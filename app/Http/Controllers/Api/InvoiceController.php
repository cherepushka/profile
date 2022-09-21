<?php

namespace App\Http\Controllers\Api;

use App\Enums\Section;
use App\Http\Controllers\Controller;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
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
            $document = $invoice->document();

            /**
             * ToDO: api cloudpaymants
             */
             $invoice->map($valid)->save();

             new DocumentServices($document->map($valid), $valid['file'], Section::INVOICE);
        }
    }
}
