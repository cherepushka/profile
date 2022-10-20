<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaystatusController extends Controller
{
    /**
     * Получение json об обновлении статуса заказа
     *
     * @param Request $request
     * @return void
     */
    public function updateStatus(Request $request)
    {
        foreach ($request->input() as $key => $value) {
            var_dump("/ key: $key");
        }
    }
}
