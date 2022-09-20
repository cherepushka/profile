<?php

namespace App\Http\Controllers;

use App\Http\Controllers\InvoiceController;

use Illuminate\Http\Request;

class DataCollectorController extends Controller
{
    public function getRequest(Request $request)
    {
        switch ($request->input('method')) {
            case 'invoice':
                new InvoiceController($request);
                break;

            default:
                return response()->json(['message' => config('constants.REQUEST_METHOD_NOT_ALLOWED_RU')]);
                break;
        }
    }
}
