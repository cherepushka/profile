<?php

namespace App\Http\Controllers\Api;

use App\Enums\Section;
use App\Http\Controllers\Controller;

use App\Http\Requests\InvoiceRequest;
use App\Http\Traits\MapTrait;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Profile;
use App\Models\ProfileInternal;
use App\Packages\Crypto\User;
use App\Packages\Payments\Cloudpayments\Cloudpayments;
use App\Packages\Payments\Cloudpayments\Dto\Payment;
use App\Packages\Payments\Cloudpayments\Dto\PaymentRecieptItem;
use App\Services\DocumentServices;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    use MapTrait;

    private readonly Cloudpayments $cloudPayments;

    public function __construct(
        private readonly User $userCrypto
    ) {
        $cloudPayments_id = config('services.cloudpayments.id');
        $cloudPayments_key = config('services.cloudpayments.key');

        $this->cloudPayments = new CloudPayments($cloudPayments_id, $cloudPayments_key);
    }

    /**
     * Обработка json о регистрации заказа:q
     *
     * @param DocumentServices $docs
     * @param InvoiceRequest $request
     * @throws Exception
     */
    public function getInvoice(DocumentServices $docs, InvoiceRequest $request): void
    {
        $invoiceRequest = $request->validated();

        if (!isset($invoiceRequest['internal_code'])) {
            $invoiceRequest['internal_code'] = 0;
        }

        /* DEBUG VARIABLES */
        if (config('app.env') !== 'production') {
            $invoiceRequest['phone'] = 79182319532;
            $invoiceRequest['email'] = "kulpovvvan@gmail.com";
        }
        /* END DEBUG VARIABLES */

        $profile = $this->getOrCreateProfileInternal($invoiceRequest)->profile;

        $invoiceRequest['InvoiceDate'] = (new DateTime($invoiceRequest['InvoiceDate']))->format('Y-m-d');

        DB::beginTransaction();

        try {
            $invoice = Invoice::where('order_id', $invoiceRequest['order_id'])->lockForUpdate()->first();

            // Создание заказа в invoice
            if (is_null($invoice)) {
                $invoice = new Invoice();

                /**
                 * Удалить параметры ниже, когда будут заданны параметры
                 */
                if (!isset($invoiceRequest['contract_date'])) {
                    $invoiceRequest['contract_date'] = date("Y-m-d H:i:s", 0);
                }

                if (isset($invoiceRequest['link'])) {

                    try {
                        $payment = $this->getPaymentFrom1cLink($invoiceRequest['link']);
                        $invoiceRequest['pay_link'] = $this->cloudPayments->orders()->create($payment);
                    } catch (Exception $e) {
                        Log::error($e);
                        $invoiceRequest['pay_link'] = null;
                    }
                }

                $invoiceRequest['Invoice_price'] = $this->replaceSpaces($invoiceRequest['Invoice_price']);

                $invoice->map($invoiceRequest)->save();

                $this->createInvoiceItems($invoiceRequest['Invoice_data'], $invoiceRequest['order_id']);

            } else {
                $invoice->pay_block = $invoiceRequest['pay_block'];
                $invoice->currency = $invoiceRequest['Invoice_currency'];
                $invoice->order_amount = $invoiceRequest['Invoice_price'];
                $invoice->roistat_id = $invoiceRequest['roistat_id'];
                $invoice->deal_source = $invoiceRequest['deal_source'];
                $invoice->client_destination = $invoiceRequest['client_destination'];
                $invoice->save();

                $invoiceItems = InvoiceItem::where('order_id', $invoice->order_id)->get('id');

                $invoiceResources = [];
                foreach ($invoiceItems as $iItem) {
                    $invoiceResources[] = $iItem->id;
                }

                $this->updateInvoiceItems($invoiceRequest['Invoice_data'], $invoice->order_id, $invoiceResources);
            }

            // Переход к обработке документа
            $docs->getData(
                (new Document())->map($invoiceRequest), // Валидированный массив для модели Document
                $invoiceRequest['file'],    // Файл base64
                Section::INVOICE,    // Перечисление для выбора
                $profile->password,         // Хэш для пользователя
                $invoiceRequest['filepswd'] // Пароль для архива
            );

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param array $request - $request полученные данные из 1С
     * @return ProfileInternal
     */
    private function getOrCreateProfileInternal(array $request): ProfileInternal
    {
        /**
         * Создание пароля для пользователя
         */
        $user_password = $this->userCrypto->generatePassword();
        $password_hash = $this->userCrypto->encryptUserData($user_password);
        $email_hash = $this->userCrypto->encryptUserData($request['email']);

        $profile = Profile::firstOrCreate(
            ['email' => $email_hash],
            [
                'password' => $password_hash,
                'remember_token' => '',
            ]
        );

        if ($profile->wasRecentlyCreated === true) {

            Log::build([
                'driver' => 'single',
                'path' => storage_path('/logs/registrations.log'),
            ])->debug('registration: ' . $request['email'] . '; pass: ' . $user_password);

            // Mail::to($request['email'])->send(new UserCreated([
            //     'user_email' => $request['email'],
            //     'user_phone' => $request['phone'],
            //     'user_password' => $user_password,
            // ]));
        }

        return ProfileInternal::firstOrCreate(
            [
                'profile_id' => $profile->id,
                'internal_id' => $request['client_id']
            ],
            ['internal_code' => $request['client_code']]
        );
    }

    private function updateInvoiceItems(array $invoiceData, string $order_id, array $invoiceResources): void
    {

        foreach ($invoiceData as $item) {

            $findBy = ['order_id' => $order_id, 'internal_id' => $item['product_id']];

            $invoiceItem = InvoiceItem::where($findBy)->first('id');

            if (!is_null($invoiceItem)) {
                $key = array_search($invoiceItem->id, $invoiceResources);

                if ($key !== false) {
                    unset($invoiceResources[$key]);
                }
            }

            InvoiceItem::updateOrCreate(
                $findBy,
                [
                    'order_id' => $order_id,
                    'vendor_code' => $item['vendor_code'] ?? null,
                    'internal_id' => $item['product_id'],
                    'title' => $item['product_name'],
                    'category' => $item['product_category'],
                    'unit' => $item['product_unit'],
                    'qty' => $item['product_qty'],
                    'pure_price' => (float)str_replace(',', '.', $this->replaceSpaces($item['product_price'])),
                    'VAT_rate' => (int)$item['product_vat'],
                    'VAT_sum' => (float)str_replace(',', '.', $this->replaceSpaces($item['sum_vat'])),
                    'final_price' => (float)str_replace(',', '.', $this->replaceSpaces($item['product_sum_vat'])),
                ]
            );
        }

        // Удаляем позиции, которые сохранены в базе, но нет в выгрузке
        if (count($invoiceResources) > 0) {
            InvoiceItem::whereIn('id', $invoiceResources)->delete();
        }
    }

    private function createInvoiceItems(array $invoiceData, string $order_id): void
    {
        foreach ($invoiceData as $item) {

            InvoiceItem::create([
                'order_id' => $order_id,
                'vendor_code' => $item['vendor_code'] ?? null,
                'internal_id' => $item['product_id'],
                'title' => $item['product_name'],
                'category' => $item['product_category'],
                'unit' => $item['product_unit'],
                'qty' => $item['product_qty'],
                'pure_price' => (float)str_replace(',', '.', $this->replaceSpaces($item['product_price'])),
                'VAT_rate' => (int)$item['product_vat'],
                'VAT_sum' => (float)str_replace(',', '.', $this->replaceSpaces($item['sum_vat'])),
                'final_price' => (float)str_replace(',', '.', $this->replaceSpaces($item['product_sum_vat'])),
            ]);
        }
    }

    private function getPaymentFrom1cLink(string $link): Payment
    {
        $queryString = urldecode(trim(strval($link), '?'));
        $invoice = [];

        foreach (explode('&', $queryString) as $rowInner) {
            $exp = explode('=', $rowInner);
            $invoice[lcfirst($exp[0])] = $exp[1];
        }

        $payment = (new Payment())
            ->setAmount((float)$invoice['amount'])
            ->setInvoiceId($invoice['invoiceId'])
            ->setAccountId($invoice['accountId'])
            ->setEmail($invoice['email']);

        // Позиции в заказе
        foreach(explode('//', $invoice['invoice']) as $val) {

            // отделяем товар от количества
            $val_ex = explode('$', $val);

            // формируем массив товаров
            if (count($val_ex) == 4) {

                $item = (new PaymentRecieptItem())
                    ->setLabel($val_ex[0])
                    ->setQuantity((float)$val_ex[1])
                    ->setPrice((float)$val_ex[2])
                    ->setAmount((float)$val_ex[3]);

                $payment->addItem($item);
            }
        }

        return $payment;
    }
}
