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

class InvoiceController extends Controller
{
    use MapTrait;
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
//            dd($invoice->profileInternalRelation->internal_id);

        } else {
            $invoice = new Invoice;
            $pI_id = ProfileInternal::where('internal_id', $valid['client_id'])->select('internal_id')->first();

            /**
             * Если отсутствует запись
             */
            if (!isset($pI_id->internal_id)) {
                $profileInternal = new ProfileInternal;

                /**
                 * Создание профиля или получение модели
                 */
                $profile = Profile::firstOrCreate(
                    ['email' => $valid['email']],
                    [
                        'password' => hash('sha256', 'Добро'),
                        'phone' => '',
                        'email' => $valid['email'],
                        'remember_token' => '',
                        'status' => 'NOT_AUTH'
                    ]
                );

                $profileInternal->profile_id = $profile->getKey();
                $profileInternal->internal_id = $valid['client_id'];
                $profileInternal->internal_code = 0;
                $profileInternal->company = '';
                $profileInternal->save();
            }

            foreach ($valid['Invoice_data'] as $item_content) {
                InvoiceItem::updateOrCreate(
                    ['order_id' => $valid['order_id'], 'internal_id' => $item_content['product_id']],
                    [
                        'order_id' => $valid['order_id'],
                        'vendor_code' => $item_content['vendor_code'],
                        'internal_id' => $item_content['product_id'],
                        'title' => json_encode($item_content['product_name']), //Json string quotes
                        'category' => $item_content['product_category'],
                        'unit' => $item_content['product_unit'],
                        'quantity' => $item_content['product_qty'],
                        'pure_price' => (double)$item_content['product_price'], //Json string
                        'full_price' => (double)$item_content['product_sum'], //Json string
                        'VAT_rate' => (int)$item_content['product_vat'], //Json string
                        'VAT_sum' => (double)$item_content['sum_vat'], //Json string
                        'final_price' => (double)$item_content['product_sum_vat'], //Json string
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

            $docs->getData($invoice->document()->map($valid), $file, Section::INVOICE, hash('sha256', 'Добро'), $valid['filepswd']);
        }
    }
}
