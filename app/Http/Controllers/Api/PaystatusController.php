<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayStatusRequest;
use App\Models\InvoicePaymentItem;
use Illuminate\Http\Request;

class PaystatusController extends Controller
{
    /**
     * Получение json об обновлении статуса заказа
     *
     * @param PayStatusRequest $request
     * @return void
     */
    public function updateStatus(PayStatusRequest $request)
    {
        /**
         * Получение валидированных параметров запроса
         */
        $payStatusRequest = $request->validated();

        $superIsPaid = $payStatusRequest['super_is_paid'];

//        Уточнить необходимость параметра super_is_paid
//
//        switch ($superIsPaid) {
//            case true:
//                /* ... */
//                break;
//
//            case false:
//                /* ... */
//                break;
//        }

        $invoicePaymentData = $payStatusRequest['data'];
        for ($i = 0; $i < count($invoicePaymentData); $i++) {
            $item = $invoicePaymentData[$i];
            $detail = $item['paid_detail'];

            foreach ($detail as $detailData) {
                $invoicePI = new InvoicePaymentItem;
                $invoicePI->order_id = $item['order_id'];
                $invoicePI->amount = $invoicePI->replaceSpaces($item['paid_amount']);
                $invoicePI->percent = (int)$item['paid_percent'];
                $invoicePI->payment_date = $item['paid_date'];
                $invoicePI->save();
                dd($detailData);

            }
        }
    }
}
