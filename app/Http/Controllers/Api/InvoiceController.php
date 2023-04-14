<?php

namespace App\Http\Controllers\Api;

use App\Enums\Section;
use App\Http\Controllers\Controller;

use App\Http\Requests\InvoiceRequest;
use App\Http\Traits\MapTrait;
use App\Mail\UserCreated;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Profile;
use App\Models\ProfileInternal;
use App\Packages\Payments\Cloudpayments\Cloudpayments;
use App\Packages\Payments\Cloudpayments\Dto\Payment;
use App\Packages\Payments\Cloudpayments\Dto\PaymentRecieptItem;
use App\Services\DocumentServices;
use Illuminate\Support\Facades\Mail;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    use MapTrait;

    private readonly Cloudpayments $cloudPayments;

    public function __construct(
        private readonly UserService $userService
    ){
        $cloudPayments_id = config('services.cloudpayments.id');
        $cloudPayments_key = config('services.cloudpayments.key');

        $this->cloudPayments = new CloudPayments($cloudPayments_id, $cloudPayments_key);
    }

    /**
     * Обработка json о регистрации заказа
     *
     * @param InvoiceRequest $request
     */
    public function getInvoice(DocumentServices $docs, InvoiceRequest $request)
    {
        $invoiceRequest = $request->validated();

        // Удалить параметры ниже, когда будут заданны параметры
        /* DEBUG VARIABLES */
        if (!isset($invoiceRequest['internal_code'])) {
            $invoiceRequest['internal_code'] = 0;
        }

        if (env('APP_DEBUG')) {
            $invoiceRequest['phone'] = 79182319532;
            $invoiceRequest['email'] = "kulpovvvan@gmail.com";
        }
        /* END DEBUG VARIABLES */

        $profile = $this->getOrCreateProfileInternal($invoiceRequest)
            ->profile;

        $invoiceRequest['InvoiceDate'] = (new \DateTime($invoiceRequest['InvoiceDate']))->format('Y-m-d');

        $invoice = Invoice::where('order_id', $invoiceRequest['order_id'])->first();

        // Создание заказа в invoice
        if (is_null($invoice)) {
            $invoice = new Invoice;

            /**
             * Удалить параметры ниже, когда будут заданны параметры
             */
            if (!isset($invoiceRequest['contract_date'])) {
                $invoiceRequest['contract_date'] = date("Y-m-d H:i:s", 0);
            }

            if (isset($invoiceRequest['link'])) {

                try{
                    $payment = $this->getPaymentFrom1cLink($invoiceRequest['link']);
                    $invoiceRequest['pay_link'] = $this->cloudPayments->orders()->create($payment);
                } catch (Exception $e){
                    Log::error($e);
                    $invoiceRequest['pay_link'] = null;
                }
            }

            $invoiceRequest['Invoice_price'] = $this->replaceSpaces($invoiceRequest['Invoice_price']);

            $invoice->map($invoiceRequest)->save();

            $this->createInvoiceItems($invoiceRequest['Invoice_data'], $invoiceRequest['order_id']);

        } else {
            $invoiceItems = InvoiceItem::where('order_id', $invoice->order_id)->get('id');

            $invoiceResources = [];
            foreach ($invoiceItems as $iItem) {
                array_push($invoiceResources, $iItem->id);
            }

            $this->updateInvoiceItems($invoiceRequest['Invoice_data'], $invoice->order_id, $invoiceResources);
        }

        // Переход к обработке документа
        $docs->getData(
            (new Document())->map($invoiceRequest), // Валидированный массив для модели Document
            $invoiceRequest['file'],    // Файл base64
            Section::INVOICE,           // Перечисление для выбора
            $profile->password,         // Хэш для пользователя
            $invoiceRequest['filepswd'] // Пароль для архива
        );
    }

    /**
     * @param  $invoiceRequest : $request полученные данные из 1С
     * @return ProfileInternal
     */
    private function getOrCreateProfileInternal($request): ProfileInternal
    {
        /**
         * Создание пароля для пользователя
         */
        $user_password = $this->userService->generatePassword();
        $password_hash = $this->userService->encryptUserData($user_password);
        $email_hash = $this->userService->encryptUserData($request['email']);
        $phone_hash = isset($request['phone']) ? $this->userService->encryptUserData($request['phone']) : null;

        $profile = Profile::firstOrCreate(
            ['email' => $email_hash, 'phone' => $phone_hash],
            [
                'password' => $password_hash,
                'remember_token' => '',
            ]
        );

        if ($profile->wasRecentlyCreated === true) {
            // Mail::to($request['email'])->send(new UserCreated([
            //     'user_email' => $request['email'],
            //     'user_phone' => $request['phone'],
            //     'user_password' => $user_password,
            // ]));
        }
        
        $profileInternal = ProfileInternal::firstOrCreate(
            [
                'profile_id' => $profile->id, 
                'internal_id' => $request['client_id']
            ],
            ['internal_code' => $request['client_code']]
        );

        return $profileInternal;
    }

    private function updateInvoiceItems(array $invoiceData, string $order_id, array $invoiceResources) {

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
                    'vendor_code' => isset($item['vendor_code']) ? $item['vendor_code'] : null,
                    'internal_id' => $item['product_id'],
                    'title' => json_encode($item['product_name']),
                    'category' => $item['product_category'],
                    'unit' => $item['product_unit'],
                    'qty' => $item['product_qty'],
                    'pure_price' => (double)str_replace(',', '.', $this->replaceSpaces($item['product_price'])),
                    'VAT_rate' => (int)$item['product_vat'],
                    'VAT_sum' => (double)str_replace(',', '.', $this->replaceSpaces($item['sum_vat'])),
                    'final_price' => (double)str_replace(',', '.', $this->replaceSpaces($item['product_sum_vat'])),
                ]
            );
        }

        // Удаляем позиции, которые сохранены в базе, но нет в выгрузке
        if (count($invoiceResources) > 0) {
            InvoiceItem::whereIn('id', $invoiceResources)->delete();
        }
    }

    private function createInvoiceItems(array $invoiceData, string $order_id) {
        foreach ($invoiceData as $item) {

            InvoiceItem::create([
                'order_id' => $order_id,
                'vendor_code' => isset($item['vendor_code']) ? $item['vendor_code'] : null,
                'internal_id' => $item['product_id'],
                'title' => $item['product_name'],
                'category' => $item['product_category'],
                'unit' => $item['product_unit'],
                'qty' => $item['product_qty'],
                'pure_price' => (double)str_replace(',', '.', $this->replaceSpaces($item['product_price'])),
                'VAT_rate' => (int)$item['product_vat'],
                'VAT_sum' => (double)str_replace(',', '.', $this->replaceSpaces($item['sum_vat'])),
                'final_price' => (double)str_replace(',', '.', $this->replaceSpaces($item['product_sum_vat'])),
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
        foreach(explode('//', $invoice['invoice']) as $key => $val){

            // отделяем товар от количества 
            $val_ex = explode('$', $val);

            // формируем массив товаров 
            if (count($val_ex) == 4) {

                $item = (new PaymentRecieptItem)
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
