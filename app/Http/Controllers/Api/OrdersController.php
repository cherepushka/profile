<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Список заказов пользователя. Содержит максимум 30 позиций
     *
     * @param Request $request
     * @param $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderList(Request $request, $page): JsonResponse
    {
        return response()->json([
            "id" => 0,
            "orderDate" => 0,
            "items" => 0,
            "full_price" => 0,
            "manager" => [
                "id" => 0,
                "name" => "string",
            ],
            "mail_trigger" => "string",
            "pay_link" => "string",
            "order_status" => "оплачен",
            "shipment_status" => "доставлен",
            "last_shipment_date" => 0,
            "last_payment_date" => 0,
            "custom_field_value" => "string"
        ]);
    }

    /**
     * Возвращает всю информацию о заказе
     *
     * @return JsonResponse
     */
    public function getOrderId($order_id): JsonResponse
    {
        $response = ['offer_docs' => [], 'shipment_docs' => [], 'products' => []];

        $invoice = Invoice::where('order_id', $order_id)->first();
        $documents = Document::where('order_id', $order_id)->get();

        $response['currency'] = $invoice->currency;

        $response["products"] = [
            "title" => "ACBU-6M; Соединитель с креплением на панель из нержавеющей стали O.D. 6мм, серия CBU",
            "count" => 0,
            "unit" => "string",
            "pure_price" => 0,
            "vat_price" => 0,
            "shipped_count" => 0
        ];

        $response["pure_price"] = $response['vat_price'] = 0;
        $response["shipped_count"] = [
            "count" => 0,
            "unit" => "string"
        ];

        $response["items_count"] = [
            "count" => 0,
            "unit" => "string"
        ];

        foreach ($documents as $doc) {
            if ($doc->section == "Коммерческое предложение") {
                array_push($response['offer_docs'], ['id' => $doc->id, 'title' => $doc->filename, 'file_extension' => $doc->extension]);
            } elseif ($doc->section == "Отгрузка") {
                array_push($response['shipment_docs'], ['id' => $doc->id, 'title' => $doc->filename, 'file_extension' => $doc->extension]);
            }
        }

        return response()->json($response);
    }
}
