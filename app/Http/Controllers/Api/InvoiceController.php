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
    public function createProfileInternal($request) : ProfileInternal
    {
        $profileInternal = ProfileInternal::where('internal_id', $request['client_id'])->first();

        /**
         * Создание пароля для пользователя
         */
        $user_password = $this->userService->generatePassword();
        $this->password_hash = $password_hash = $this->userService->encryptUserData($user_password);
        $email_hash = $this->userService->encryptUserData($request['email']);
        $phone_hash = $this->userService->encryptUserData($request['phone']);

        /**
         * Если не существует запись ProfileInternal
         */
        if (is_null($profileInternal)) {
            $profileInternal = new ProfileInternal;
            $profileInternal->internal_id = $request['client_id'];
            $profileInternal->internal_code = $request['client_code'];
//            $profileInternal->company = $request[''];
            $profileInternal->save();

        }

        $profile = $this->createProfile($email_hash, $password_hash, $phone_hash, $profileInternal->internal_id);

        if ($profile->wasRecentlyCreated === true) {
            Mail::to($request['email'])->send(new UserCreated([
                'user_email' => $request['email'],
                'user_phone' => $request['phone'],
                'user_password' => $user_password,
            ]));
        }

        return $profileInternal;
    }

    private function createProfile(string $email, string $password, string $phone, string $internal_id)
    {
        $findBy = ['email' => $email, 'phone' => $phone];

        $profile = Profile::where($findBy)->first();

        /**
         * Создание профиля или получение модели
         */
        if (is_null($profile)) {
            $profile = Profile::firstOrCreate($findBy, [
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'remember_token' => '',
                'internal_id' => $internal_id,
            ]);
        }

        return $profile;
    }

    /**
     * Обработка json о регистрации заказа
     *
     * @param InvoiceRequest $request
     */
    public function getInvoice(DocumentServices $docs, InvoiceRequest $request)
    {
        /**
         * Получение валидированных параметров запроса
         */
        $invoiceRequest = $request->validated();

        /**
         * Удалить параметры ниже, когда будут заданны параметры
         */
        /* DEBUG VARIABLES */
        if (!isset($invoiceRequest['phone'])) {
            $invoiceRequest['phone'] = 79001234567;
        }

        if (!isset($invoiceRequest['internal_code'])) {
            $invoiceRequest['internal_code'] = 0;
        }

        if (env('APP_DEBUG')) {
            $invoiceRequest['email'] = "fluidmi@rambler.ru";
        }
        /* END DEBUG VARIABLES */

        /**
         * Создание и получение профиля
         */
        $profileInternal = $this->createProfileInternal($invoiceRequest);

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
