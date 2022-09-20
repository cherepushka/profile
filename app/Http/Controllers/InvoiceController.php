<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\DocumentController;

use App\Models\Document;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function __construct(Request $request)
    {
        $exists = DB::table('invoice')
            ->select('order_id')
            ->where('order_id', '=', $request->input('order_id'))
            ->get();

        $invoice = new Invoice;
        $document = new Document;

        if ($exists->value('order_id') !== null) {
            var_dump('here');
        } else {
            $invoiceArray = config('constants.invoiceArray');
            $documentArray = config('constants.documentArray');

            foreach ($request->input() as $data_key => $key_content) {
                if (isset($invoiceArray[$data_key])) {
                    $key = $invoiceArray[$data_key];
                    $invoice->$key = $key_content;
                }
                if (isset($documentArray[$data_key])) {
                    $key = $documentArray[$data_key];
                    $document->$key = $key_content;
                }
            }
            if ($invoice->order_id !== false) {
                $invoice->contract_date = date('Y-m-d H:i:s', strtotime($invoice->contract_date));
                $invoice->save();
            }
            if ($document->filename !== false) {
                new DocumentController($document, $request->input('file'));
            }
        }
    }
}
