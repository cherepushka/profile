<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderListRequest;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceShipment;
use App\Models\InvoiceShipmentDetail;
use App\Models\InvoiceShipmentDetailItem;
use App\Models\Manager;
use App\Models\ProfileInternal;
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
    public function orderList(OrderListRequest $request, int $page = 0): JsonResponse
    {
        $limit = 5;
        $offset = $this->getOffset($limit, $page);

        $orderRequest = $request->validated();

        $profileInternal = ProfileInternal::where(['profile_id' => $orderRequest['user_id']])->first();

        if (!is_null($profileInternal)) {
            $invoiceArray = Invoice::where(['user_id' => $profileInternal->internal_id])
                ->limit($limit)
                ->offset($offset)
//                ->leftJoin('manager', 'invoice.responsible_email', '=', 'manager.email')
                ->get()
                ->toArray();

            foreach ($invoiceArray as $invoiceIndex => $invoice) {
                $manager = Manager::where(['email' => $invoice['responsible_email']])->first()->toArray();

                if (!is_null($manager)) {
                    $invoiceArray[$invoiceIndex] += ['manager' => $manager];
                    unset($invoiceArray[$invoiceIndex]['responsible_email']);
                }
            }

            return new JsonResponse(['orders' => $invoiceArray]);

        } else {
            return new JsonResponse(['error' => 'Internal is undefined']);
        }

        return new JsonResponse(['error' => 'Crash happened :^(']);
    }

    private function getOffset($limit, $page) {
        if ($page > 0) {
            return $limit * ($page - 1);
        }

        return 0;
    }

    /**
     * Возвращает всю информацию о заказе
     *
     * @return JsonResponse
     */
    public function orderInfo($orderId): JsonResponse
    {
        $documents = Document::where(['order_id' => $orderId])
            ->where('extension', '!=', 'zip')
            ->get()
            ->toArray();
        if (!is_null($documents)) {
            $shipment = InvoiceShipment::where(['order_id' => $orderId])
                ->get()
                ->toArray();

            $shipment += [
                'shipment_detail' => InvoiceShipmentDetail::where(['order_id' => $orderId])
                    ->get()
                    ->toArray()
            ];

            $shipment += [
                'shipment_detail_item' => InvoiceShipmentDetailItem::where(['invoice_shipment_detail_item.order_id' => $orderId])
                    ->join('invoice_item', 'invoice_product_id', '=', 'invoice_item.id')
                    ->get()
                    ->toArray()
            ];

            return new JsonResponse(['response' => ['documents' => $documents, 'shipment' => $shipment]]);

        } else {
            return new JsonResponse(['error' => 'Any document is not found']);
        }

        return new JsonResponse(['error' => 'Bad day for response :\'(']);
    }
}
