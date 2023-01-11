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
use App\Services\DocumentServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Services\UserService;
use mysql_xdevapi\TableUpdate;

class InvoiceController extends Controller
{
    use MapTrait;

    private string $password_hash = "";

    public function __construct(
        private readonly UserService $userService,
    )
    {}

    /**
     * @param  $invoiceRequest : $request полученные данные из 1С
     * @return ProfileInternal
     */
    public function getProfileInternal($request): ProfileInternal {

        /**
         * Удалить параметры ниже, когда будут заданны параметры
         */
        if (!isset($request['phone'])) {
            $request['phone'] = rand(70000000000, 79999999999); // Required in prod -- debug value
        }
        if (!isset($request['internal_code'])) {
            $request['internal_code'] = 0; // Required in prod -- debug value
        }

        $request['email'] .= "-debug"; // Edited debug value

        $profileInternal = ProfileInternal::where('internal_id', $request['client_id'])->select('internal_id')->first();

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
                    'phone' => $request['phone'],
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
            return $profileInternal;

        } else {
            return $profileInternal;
        }
    }

    /**
     * Обработка json о регистрации заказа
     *
     * @param InvoiceRequest $request
     * @return void
     */
    public function getInvoice(DocumentServices $docs, InvoiceRequest $request)
    {
        /**
         * Получение валидированных параметров запроса
         */
        $invoiceRequest = $request->validated();

        /**
         * Создание и получение профиля
         */
        $profileInternal = $this->getProfileInternal($request); // Заменить $request на $invoiceRequest в версии prod

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

            $invoiceRequest['Invoice_price'] = $this->replaceSpaces($invoiceRequest['Invoice_price']);

            $invoice->map($invoiceRequest)->save();

        } else {
            /**
             * ToDo: Update method
             */
//            $file = $valid['file'];
//            $docs->getData($invoice->document()->map($valid), $file, Section::INVOICE, hash('sha256', 'Добро'), $valid['filepswd']);
        }
        if (is_array($invoiceRequest['Invoice_data'])) {
            $invoiceData = $invoiceRequest['Invoice_data'];
            for ($i = 0; $i < count($invoiceData); $i++) {
                $item = current($invoiceData);

                if ($item['product_gcategory'] = "") {
                    $item['product_category'] = "NaN";
                }

                InvoiceItem::updateOrCreate(
                    ['internal_id' => $item['product_id']], // Необходимо уточнение, что именно являеся уникальным атрибутом таблицы
                    [
                        'order_id' => $invoiceRequest['order_id'],
                        'vendor_code' => $item['vendor_code'],
                        'internal_id' => $item['product_id'],
                        'title' => json_encode($item['product_name']),
                        'category' => $item['product_category'],
                        'unit' => $item['product_unit'],
                        'quantity' => $item['product_qty'],
                        'pure_price' => (double)$this->replaceSpaces($item['product_price']),
                        'full_price' => (double)$this->replaceSpaces($item['product_sum']),
                        'VAT_rate' => (int)$item['product_vat'],
                        'VAT_sum' => (double)$this->replaceSpaces($item['sum_vat']),
                        'final_price' => (double)$this->replaceSpaces($item['product_sum_vat']),
                    ]
                );
                next($invoiceData);
            }
        }

        /**
         * ToDO: api cloudpaymants
         */

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
}
