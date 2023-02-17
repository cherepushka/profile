<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderListRequest;
use App\Http\Resources\OrderFull;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceShipment;
use App\Models\InvoiceShipmentDetail;
use App\Models\InvoiceShipmentDetailItem;
use App\Models\Manager;
use App\Models\ProfileInternal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\OrderListItem;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Response;

class OrdersController extends Controller
{
    /**
     * Список заказов пользователя. Содержит максимум 30 позиций
     *
     * @param Request $request
     * @param $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderList(Request $request, int $page = 0): JsonResponse
    {
        $limit = 20;
        $offset = $this->getOffset($limit, $page);

        $internalIds = $this->getUserInternalIds();

        $invoices = Invoice::whereIn('user_id', $internalIds)
            ->limit($limit)
            ->offset($offset)
            ->with('invoiceShipmentRelation')
            ->with('invoicePaymentRelation')
            ->with('managerRelation')
            ->withCount('invoiceItemRelation')
            ->get();

        $invoicesCountAll = Invoice::whereIn('user_id', $internalIds)
            ->selectRaw('count(user_id) as cnt')
            ->pluck('cnt');

        // dd($invoices[0]);

        return new JsonResponse([
            'items' => OrderListItem::collection($invoices),
            'count' => $invoicesCountAll->first()
        ]);
    }

    private function getOffset($limit, $page) {
        if ($page > 0) {
            return $limit * ($page - 1);
        }

        return 0;
    }

    /**
     * Возвращает всю информацию о заказе
     */
    public function orderInfo($orderId)
    {
        $internalIds = $this->getUserInternalIds();

        $invoice = Invoice::whereIn('user_id', $internalIds)
            ->where('order_id', $orderId)
            ->with('documentRelation')
            ->with('invoiceShipmentRelation')
            ->with('invoicePaymentRelation')
            ->with('invoiceItemRelation')
            ->first();

        if (is_null($invoice)) {
            return new JsonResponse(['error' => 'Any document is not found'], 500);
        }

        return new OrderFull($invoice);
    }
}
