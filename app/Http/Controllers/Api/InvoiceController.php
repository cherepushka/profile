<?php

namespace App\Http\Controllers\Api;

use App\Enums\Section;
use App\Http\Controllers\Controller;

use App\Http\Requests\InvoiceRequest;
use App\Http\Traits\MapTrait;
use App\Mail\UserCreated;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Profile;
use App\Models\ProfileInternal;
use App\Services\DocumentServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Services\UserService;

class InvoiceController extends Controller
{
    use MapTrait;

    private string $password_hash = "";

    public function __construct(private readonly UserService $userService)
    { }

    /**
     * @param  $invoiceRequest : $request полученные данные из 1С
     * @return ProfileInternal
     */
    public function getProfileInternal($request) : ProfileInternal
    {
        $profileInternal = ProfileInternal::where('internal_id', $request['client_id'])->first();

        /**
         * Если не существует запись ProfileInternal
         */
        if (is_null($profileInternal)) {
            $profileInternal = new ProfileInternal;

            /**
             * Создание пароля для пользователя
             */
            $user_password = $this->userService->generatePassword();
            $this->password_hash = $password_hash = $this->userService->encryptUserData($user_password);
            $email_hash = $this->userService->encryptUserData($request['email']);

            /**
             * Создание профиля или получение модели
             */
            $profile = Profile::firstOrCreate(
                ['email' => $email_hash],
                [
                    'email' => $email_hash,
                    'password' => $password_hash,
                    'user_phone' => $request['phone'],
                    'remember_token' => '',
                    'status' => 'NOT_AUTH'
                ]
            );

            $profileInternal->profile_id = $profile->getKey();
            $profileInternal->internal_id = $request['client_id'];
            $profileInternal->internal_code = $request['internal_code'];
            $profileInternal->save();

            if ($request->server('SERVER_ADDR') != "127.0.0.1") {
                /**
                 * Care to usage
                 */
                Mail::to($request['email'])->send(new UserCreated([
                    'email' => $request['email'], 'password' => $user_password
                ]));
            }
        }

        return $profileInternal;
    }

    /**
     * Обработка json о регистрации заказа
     *
     * @param InvoiceRequest $request
     */
    public function getInvoice(DocumentServices $docs, InvoiceRequest $request)
    {
        /**
         * Удалить параметры ниже, когда будут заданны параметры
         */
        /* DEBUG VARIABLES */

        if (!isset($request['phone'])) {
            $request['phone'] = rand(70000000000, 79999999999);
        }

        if (!isset($request['internal_code'])) {
            $request['internal_code'] = 0;
        }

        $request['email'] .= "-debug";
        /* END DEBUG VARIABLES */

        /**
         * Получение валидированных параметров запроса
         */
        $invoiceRequest = $request->validated();

        /**
         * Создание и получение профиля
         */
        $profileInternal = $this->getProfileInternal($request); // Заменить $request на $invoiceRequest в версии prod

        if ($this->password_hash == "") {
            if ($invoiceRequest['email_hash'] != "") {
                $profile = Profile::where(['id' => $profileInternal->profile_id])->select('password')->first();

                if (!is_null($profile)) {
                    $this->password_hash = $profile->password;

                } else {
                    return new JsonResponse(['error' => 'Profile is undefined, check your json data before use this method.']);
                }
            } else {
                return new JsonResponse(['error' => 'Profile is undefined, check your json data before use this method.']);
            }
        }

        $invoice = Invoice::where('order_id', $invoiceRequest['order_id'])->first();

        /**
         * Создание заказа в invoice
         */
        if (is_null($invoice)) {
            $invoice = new Invoice;

            /**
             * Удалить параметры ниже, когда будут заданны параметры
             */
            if (!isset($invoiceRequest['contract_date'])) {
                $invoiceRequest['contract_date'] = date("Y-m-d H:i:s", 0); // Required in prod -- debug value
            }

            /**
             * ToDO: api cloudpaymants
             */
            if (!isset($invoiceRequest['pay_link'])) {
                $invoiceRequest['pay_link'] = 'https://ToDo.tomorrow/';
            }

            $invoiceRequest['Invoice_price'] = $this->replaceSpaces($invoiceRequest['Invoice_price']);

            $invoice->map($invoiceRequest)->save();

            $this->createInvoiceItem($invoiceRequest['Invoice_data'], $invoiceRequest['order_id']);

        } else {
            $invoiceItems = InvoiceItem::where('order_id', $invoice->order_id)->get('id');

            $invoiceResources = [];

            foreach ($invoiceItems as $iItem) {
                array_push($invoiceResources, $iItem->id);
            }

            $this->updateInvoiceItem($invoiceRequest['Invoice_data'], $invoice->order_id, $invoiceResources);
        }

        /**
         * Переход к обработке документа
         */

        $docs->getData(
            $invoice->document()->map($invoiceRequest), // Валидированный массив для модели Document
            $invoiceRequest['file'], // Файл base64
            Section::INVOICE, // Перечисление для выбора
            $this->password_hash, // Хэш для пользователя

            $invoiceRequest['filepswd'] // Пароль для архива
        );
    }

    public function updateInvoiceItem(?array $invoiceData, string $order_id, array $invoiceResources) {
        if (is_array($invoiceData)) {
            foreach ($invoiceData as $item) {
                if ($item['product_category'] = "") {
                    $item['product_category'] = "NaN";
                }

                $findBy = ['order_id' => $order_id, 'internal_id' => $item['product_id']];

                $invoiceItem = InvoiceItem::where($findBy)->first('id');

                if (!is_null($invoiceItem)) {
                    $itemId = $invoiceItem->id;

                    $key = array_search($itemId, $invoiceResources);

                    if ($key !== false) {
                        unset($invoiceResources[$key]);
                    }
                }

                InvoiceItem::updateOrCreate(
                    $findBy,
                    [
                        'order_id' => $order_id,
                        'vendor_code' => $item['vendor_code'],
                        'internal_id' => $item['product_id'],
                        'title' => json_encode($item['product_name']),
                        'category' => $item['product_category'],
                        'unit' => $item['product_unit'],
                        'qty' => $item['product_qty'],
                        'pure_price' => (double)$this->replaceSpaces($item['product_price']),
                        'VAT_rate' => (int)$item['product_vat'],
                        'VAT_sum' => (double)$this->replaceSpaces($item['sum_vat']),
                        'final_price' => (double)$this->replaceSpaces($item['product_sum_vat']),
                    ]
                );
            }

            if (count($invoiceResources) > 0) {
                InvoiceItem::whereIn('id', $invoiceResources)->delete();
            }
        }
    }

    public function createInvoiceItem(?array $invoiceData, string $order_id) {
        if (is_array($invoiceData)) {
            foreach ($invoiceData as $item) {
                if ($item['product_category'] = "") {
                    $item['product_category'] = "NaN";
                }

                InvoiceItem::updateOrCreate(
                    ['internal_id' => $item['product_id'], 'order_id' => $order_id], // Необходимо уточнение, что именно являеся уникальным атрибутом таблицы
                    [
                        'order_id' => $order_id,
                        'vendor_code' => $item['vendor_code'],
                        'internal_id' => $item['product_id'],
                        'title' => json_encode($item['product_name']),
                        'category' => $item['product_category'],
                        'unit' => $item['product_unit'],
                        'qty' => $item['product_qty'],
                        'pure_price' => (double)$this->replaceSpaces($item['product_price']),
                        'VAT_rate' => (int)$item['product_vat'],
                        'VAT_sum' => (double)$this->replaceSpaces($item['sum_vat']),
                        'final_price' => (double)$this->replaceSpaces($item['product_sum_vat']),
                    ]
                );
            }
        }
    }
}
