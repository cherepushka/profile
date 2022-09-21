<?php

namespace App\Http\Controllers\Api;

use App\Enums\Section;
use App\Http\Controllers\Controller;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Profile;
use App\Services\DocumentServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
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
            dd("not null");
        } else {
            $invoice = new Invoice;

//            $profile = $invoice->profile();
//            $profile->password = '';
//            $profile->phone = '';
//            $profile->email = $valid['email'];
//            $profile->remember_token = '';
//            $profile->status = 'NOT_AUTH';
//            $profile->save();

//            $id = (Profile::where('email', $valid['email'])->select('id')->first())->id;

//            $profileInternal = $invoice->profileInternal();
//            $profileInternal->profile_id = $id;
//            $profileInternal->internal_id = $valid['client_id'];
//            $profileInternal-> internal_code = 0;
//            $profileInternal->company = '';
//            $profileInternal->save();

            $manager = $invoice->manager();
            $manager->name = '';
            $manager->surname = '';
            $manager->position = '';
            $manager->email = $valid['responsible'];
            $manager->phone = '';
            $manager->whats_app = '';
            $manager->image = '';
            $manager->status = 0;
            $manager->save();

            $document = $invoice->document();

            /**
             * ToDO: api cloudpaymants
             */
             $invoice->map($valid)->save();

             new DocumentServices($document->map($valid), $valid['file'], Section::INVOICE);
        }
    }
}
