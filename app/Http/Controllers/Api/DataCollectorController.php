<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
