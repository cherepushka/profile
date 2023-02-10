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
        return response()->json([[
            "id" => 228,
            "orderDate" => 1675775271,
            "items" => 2,
            "fullPrice" => 03210,
            "manager" => [
                "id" => 2,
                "name" => "Евгения Каманина",
            ],
            "mailTrigger" => "#13213123213",
            "payLink" => "http://google.com",
            "orderStatus" => "не оплачен",
            "shipmentStatus" => "не доставлен",
            "lastShipmentDate" => 1675775271,
            "lastPaymentDate" => 1675775271,
            "customFieldValue" => "123213123"
        ]]);
    }

    /**
     * Возвращает всю информацию о заказе
     *
     * @return JsonResponse
     */
    public function getOrderId($order_id): JsonResponse
    {
        return response()->json([
            'offerDocs' => [
                [
                    'id' => 228,
                    'title' => 'Коммерческое предложение и счет на оплату №28080 от 16 августа 2023 г..pdf',
                    'link' => 'https://fluid-line.ru/assets/snippets/profile/Profile.class.php?invoice=28080&file=%D0%9A%D0%BE%D0%BC%D0%BC%D0%B5%D1%80%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5%20%D0%BF%D1%80%D0%B5%D0%B4%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D1%81%D1%87%D0%B5%D1%82%20%D0%BD%D0%B0%20%D0%BE%D0%BF%D0%BB%D0%B0%D1%82%D1%83%20%E2%84%9628080%20%D0%BE%D1%82%2016%20%D0%B0%D0%B2%D0%B3%D1%83%D1%81%D1%82%D0%B0%202022%C2%A0%D0%B3..pdf&method=showOfferPdf',
                    'fileExtension' => 'pdf',
                ],
                [
                    'id' => 228,
                    'title' => 'Коммерческое предложение и счет на оплату №28080 от 16 августа 2022 г..xlsx',
                    'link' => 'https://fluid-line.ru/assets/snippets/profile/Profile.class.php?invoice=28080&file=%D0%9A%D0%BE%D0%BC%D0%BC%D0%B5%D1%80%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5%20%D0%BF%D1%80%D0%B5%D0%B4%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D1%81%D1%87%D0%B5%D1%82%20%D0%BD%D0%B0%20%D0%BE%D0%BF%D0%BB%D0%B0%D1%82%D1%83%20%E2%84%9628080%20%D0%BE%D1%82%2016%20%D0%B0%D0%B2%D0%B3%D1%83%D1%81%D1%82%D0%B0%202022%C2%A0%D0%B3..pdf&method=showOfferPdf',
                    'fileExtension' => 'xlsx'
                ],
            ],
            'shipmentDocs' => [
                [
                    'id' => 228,
                    'title' => 'Реализация товаров и услуг 00000009576 от 05.09.2022.xlsx',
                    'link' => 'https://fluid-line.ru/assets/snippets/profile/Profile.class.php?invoice=28080&file=%D0%9A%D0%BE%D0%BC%D0%BC%D0%B5%D1%80%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5%20%D0%BF%D1%80%D0%B5%D0%B4%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D1%81%D1%87%D0%B5%D1%82%20%D0%BD%D0%B0%20%D0%BE%D0%BF%D0%BB%D0%B0%D1%82%D1%83%20%E2%84%9628080%20%D0%BE%D1%82%2016%20%D0%B0%D0%B2%D0%B3%D1%83%D1%81%D1%82%D0%B0%202022%C2%A0%D0%B3..pdf&method=showOfferPdf',
                    'fileExtension' => 'xlsx',
                ],
                [
                    'id' => 228,
                    'title' => 'Счет-фактура выданный 000000016004 от 05.09.2022.xlsx',
                    'link' => 'https://fluid-line.ru/assets/snippets/profile/Profile.class.php?invoice=28080&file=%D0%9A%D0%BE%D0%BC%D0%BC%D0%B5%D1%80%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5%20%D0%BF%D1%80%D0%B5%D0%B4%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D1%81%D1%87%D0%B5%D1%82%20%D0%BD%D0%B0%20%D0%BE%D0%BF%D0%BB%D0%B0%D1%82%D1%83%20%E2%84%9628080%20%D0%BE%D1%82%2016%20%D0%B0%D0%B2%D0%B3%D1%83%D1%81%D1%82%D0%B0%202022%C2%A0%D0%B3..pdf&method=showOfferPdf',
                    'fileExtension' => 'xlsx',
                ],
            ],
            'currency' => 'RUB',
            'items' => [
                [
                    'title' => 'ACBU-6M; Соединитель с креплением на панель из нержавеющей стали O.D. 6мм, серия CBU',
                    'count' => 1,
                    'purePrice' => 989.20,
                    'VatPrice' => 1184.64,
                    'shippedCount' => 1,
                ]
            ],
            'total' => [
                'itemsCount' => 28,
                'itemsLength' => 18,
                'purePrice' => 27554.40,
                'VatPrice' => 33065.28,
                'shippedCount' => 1
            ]
        ]);
    }
}
