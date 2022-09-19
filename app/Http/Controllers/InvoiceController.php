<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\InvoiceModel;

class InvoiceController extends Controller
{
    public function collectInvoiceOrder(Request $request): JsonResponse
    {
        $invoice = new InvoiceModel;

//        $invoice->name = $request->name;
//        $invoice->save();

        return response()->json($request->input('data'));
    }
}
