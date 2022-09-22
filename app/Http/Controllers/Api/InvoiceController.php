<?php

namespace App\Http\Controllers\Api;

use App\Enums\Section;
use App\Http\Controllers\Controller;

use App\Http\Requests\InvoiceRequest;
use App\Http\Traits\MapTrait;
use App\Models\Invoice;
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
    public function getInvoice(InvoiceRequest $request)
    {
        $valid = $request->validated();

        if ($invoice = Invoice::where('order_id', $valid['order_id'])->first()) {
            /**
             * ToDo: Update method
             */
            dd($invoice->profileInternalRelation->internal_id);

        } else {
            $invoice = new Invoice;
            $relation = $invoice->profileInternalRelation;

            /**
             * Если отсутствует связь
             */
            if (!isset($relation->internal_id)) {
                $pI_id = ProfileInternal::where('internal_id', $valid['client_id'])->select('internal_id')->first();

                /**
                 * Если отсутствует запись
                 */
                if (!isset($pI_id->internal_id)) {
                    $profileInternal = $invoice->profileInternalModel();

                    /**
                     * Создание профиля или получение модели
                     */
                    $profile = Profile::firstOrCreate(
                        ['email' => $valid['email']],
                        [
                            'password' => hash('sha256', 'Добро пожаловать'),
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
            new DocumentServices(($invoice->document())->map($valid), $valid['file'], Section::INVOICE, $profile->password, $valid['filepswd']);
        }
    }
}
