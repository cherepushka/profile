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
use App\Services\DocumentServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\UserService;

class InvoiceController extends Controller
{
    use MapTrait;

    public function __construct(
        private readonly UserService $userService
    )
    {}

    /**
     * Обработка json о регистрации заказа
     *
     * @param InvoiceRequest $request
     * @return void
     */
    public function getInvoice(DocumentServices $docs, InvoiceRequest $request)
    {
        $valid = $request->validated();

        if ($invoice = Invoice::where('order_id', $valid['order_id'])->first()) {

            /**
             * ToDo: Update method
             */
            $file = $valid['file'];
            $docs->getData($invoice->document()->map($valid), $file, Section::INVOICE, hash('sha256', 'Добро'), $valid['filepswd']);
        } else {
            $invoice = new Invoice;
            $profile_internal = ProfileInternal::where('internal_id', $valid['client_id'])->select('internal_id')->first();

            /**
             * Если отсутствует запись
             */
            if (!isset($profile_internal->internal_id)) {
                $profileInternal = new ProfileInternal;

                $user_password = $this->userService->generatePassword();

                $password_hash = $this->userService->encryptUserData($user_password);
                $email_hash = $this->userService->encryptUserData($valid['email']);

                /**
                 * Создание профиля или получение модели
                 */
                $profile = Profile::firstOrCreate(
                    ['email' => $valid['email']],
                    [
                        'password' => $password_hash,
                        'phone' => '',
                        'email' => $email_hash,
                        'remember_token' => '',
                        'status' => 'NOT_AUTH'
                    ]
                );

                $profileInternal->profile_id = $profile->getKey();
                $profileInternal->internal_id = $valid['client_id'];
                $profileInternal->internal_code = 0;
                $profileInternal->company = ''; //TODO добаить запись компании
                $profileInternal->save();

                // TODO сделать отправку имейла с сообщением успешной регистрации
            }

            foreach ($valid['Invoice_data'] as $item_content) {
                InvoiceItem::updateOrCreate(
                    ['order_id' => $valid['order_id']],
                    [
                        'order_id' => $valid['order_id'],
                        'vendor_code' => $item_content['vendor_code'],
                        'internal_id' => $item_content['product_id'],
                        'title' => json_encode($item_content['product_name']),
                        'category' => $item_content['product_category'],
                        'unit' => $item_content['product_unit'],
                        'quantity' => $item_content['product_qty'],
                        'pure_price' => (double)$item_content['product_price'],
                        'full_price' => (double)$item_content['product_sum'],
                        'VAT_rate' => (int)$item_content['product_vat'],
                        'VAT_sum' => (double)$item_content['sum_vat'],
                        'final_price' => (double)$item_content['product_sum_vat'],
                    ]
                );
            }

            /**
             * ToDO: api cloudpaymants
             */

            /**
             * Сохранение заказа в invoice
             */
            $invoice->map($valid)->save();

            /**
             * Переход к обработке документа
             */
//            $document = new Document;
            $file = $valid['file'];

            $docs
                ->getData($invoice->document()->map($valid), $file, Section::INVOICE, hash('sha256', 'Добро'), $valid['filepswd']);
        }
    }
}
