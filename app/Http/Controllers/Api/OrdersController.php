<?php

namespace App\Http\Controllers\Api;

use App\Enums\Shipment\EventGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditCustomValueRequest;
use App\Http\Requests\OrderListRequest;
use App\Http\Resources\OrderFull;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\OrderListItem;
use App\Http\Actions\OrderList\Filters\FilterParser;
use App\Http\Actions\OrderList\Sorters\SortParser;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Список заказов пользователя. Содержит максимум 30 позиций
     *
     * @param OrderListRequest $request
     * @param int $page
     * @return JsonResponse
     */
    public function orderList(OrderListRequest $request, int $page = 0): JsonResponse
    {
        $limit = 20;
        $offset = 0;
        if ($page > 0) {
            $offset = $limit * ($page - 1);
        }

        $internalIds = $this->getUserInternalIds();

        $qb = DB::table('invoice')
            ->select([
                // works because functional depend on `order_id` in GROUP BY (order_id is PRIMARY KEY)
                'invoice.order_id',
                'invoice.invoice_id',
                'invoice.user_id',
                'invoice.entity',
                'invoice.pay_link',
                'invoice.pay_block',
                'invoice.custom_field',
                'invoice.contract_date',
                'invoice.currency',
                'invoice.order_amount',
                'invoice.roistat_id',
                'invoice.deal_source',
                'invoice.date',
                'invoice.mail_trigger',

                'manager.id',
                'manager.name',
                'manager.surname',
                'manager.position',
                'manager.email',
                'manager.phone',
                'manager.whats_app',
                'manager.image',
                'manager.status',

                'last_shipment_date' => function ($query) {
                    $query->selectRaw('MAX(`date`)')
                        ->from('invoice_shipment_detail')
                        ->whereRaw('order_id = `invoice`.`order_id`');
                },
                'position_count' => function ($query) {
                    $query->selectRaw('COUNT(*)')
                        ->from('invoice_item')
                        ->whereRaw('`order_id` = `invoice`.`order_id`');
                },
                'products_qty_count' => function ($query) {
                    $query->selectRaw('SUM(qty)')
                        ->from('invoice_item')
                        ->whereRaw('`order_id` = `invoice`.`order_id`');
                },
                'shipped_qty_count' => function ($query) {
                    $query->selectRaw('SUM(product_qty)')
                        ->from('invoice_shipment_detail_item')
                        ->whereRaw('`order_id` = `invoice`.`order_id`');
                },
                DB::raw("GROUP_CONCAT(`invoice_shipment_detail`.`last_event_group` SEPARATOR ', ') as `last_event_groups`"),
                DB::raw("GROUP_CONCAT(`invoice_shipment_detail`.`delivery_date` SEPARATOR ', ') as `last_delivery_dates`"),
                'invoice_payment.last_payment_date',
                'invoice_payment.paid_percent',
            ])
            ->whereIn('invoice.user_id', $internalIds)
            ->leftJoin('manager', 'invoice.responsible_email', '=', 'manager.email')
            ->leftJoin('invoice_payment', 'invoice.order_id', '=', 'invoice_payment.order_id')
            ->leftJoin('invoice_shipment_detail', 'invoice.order_id', '=', 'invoice_shipment_detail.order_id')
            ->groupBy('invoice.order_id')
            ->limit($limit)
            ->offset($offset);

        if($request->validated('sort') !== null) {
            foreach($request->validated('sort') as $filterParams) {
                (new FilterParser())->parse($filterParams)->modifyQuery($qb);
            }
        }

        if($request->validated('order') !== null) {
            (new SortParser())->parse($request->validated('order'))->modifyQuery($qb);
        }

        $invoices = $qb->get();
        $invoicesCountAll = $qb->getCountForPagination();

        return new JsonResponse([
            'items' => OrderListItem::collection($invoices),
            'count' => $invoicesCountAll,
        ]);
    }

    /**
     * Возвращает всю информацию о заказе
     */
    public function orderInfo(string $orderId): OrderFull|JsonResponse
    {
        $internalIds = $this->getUserInternalIds();

        $invoice = Invoice::whereIn('user_id', $internalIds)
            ->where('order_id', $orderId)
            ->with('documentRelation')
            ->with('invoiceShipmentRelation')
            ->with('invoicePaymentRelation')
            ->with('invoiceItemRelation')
            ->with('allShipmentTrackingInfoRelation')
            ->first();

        if (is_null($invoice)) {
            return new JsonResponse(['error' => 'Can`t find invoice'], 500);
        }

        return new OrderFull($invoice);
    }

    /**
     * Изменение кастомного поля в заказе
     */
    public function editCustomValue(EditCustomValueRequest $request, string $orderId)
    {
        $newValue = $request->validated('value');

        $internalIds = $this->getUserInternalIds();

        $invoice = Invoice::whereIn('user_id', $internalIds)
            ->where('order_id', $orderId)
            ->first();

        if (is_null($invoice)) {
            return new JsonResponse(['error' => 'Can`t find invoice'], 500);
        }

        $invoice->custom_field = $newValue;
        $invoice->save();

        return response('', 201);
    }

    public function deliveryStatuses()
    {
        return response()->json(EventGroup::array());
    }
}
