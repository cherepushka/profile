<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    public function getOrderId(): JsonResponse
    {
        return response()->json([
            "offer_docs" => [
                "id" => 0,
                "title" => "Коммерческое предложение и счет на оплату №28080 от 16 августа 2022 г..pdf",
                "file_extension" => "pdf"
            ],
            "shipment_docs" => [
                "id" => 0,
                "title" => "Коммерческое предложение и счет на оплату №28080 от 16 августа 2022 г..pdf",
                "file_extension" => "pdf"
            ],
            "currency" => "RUB",
            "products" => [
                "title" => "ACBU-6M; Соединитель с креплением на панель из нержавеющей стали O.D. 6мм, серия CBU",
                "count" => 0,
                "unit" => "string",
                "pure_price" => 0,
                "vat_price" => 0,
                "shipped_count" => 0
            ],
            "items_count" => [
                "count" => 0,
                "unit" => "string"
            ],
            "pure_price" => 0,
            "vat_price" => 0,
            "shipped_count" => [
                "count" => 0,
                "unit" => "string"
            ]
        ]);
    }
}
