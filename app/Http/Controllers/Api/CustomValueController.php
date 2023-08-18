<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditCustomValueRequest;
use App\Http\Requests\ExportCustomValuesRequest;
use App\Http\Resources\ExportCustomValues;
use App\Models\Invoice;
use App\Models\InvoiceCustomValues;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CustomValueController extends Controller
{

    /**
     * Изменение кастомного поля в заказе
     *
     * @param EditCustomValueRequest $request
     * @param string $orderId
     * @return Response|JsonResponse
     */
    public function edit(EditCustomValueRequest $request, string $orderId): Response|JsonResponse
    {
        $newValue = $request->validated('value');

        $internalIds = $this->getUserInternalIds();

        $invoice = Invoice::whereIn('user_id', $internalIds)
            ->where('order_id', $orderId)
            ->first();

        if (is_null($invoice)) {
            return new JsonResponse(['error' => 'Permission denied'], 403);
        }

        InvoiceCustomValues::updateOrCreate(
            ['order_id' => $invoice->order_id],
            [
                'web_value' => $newValue,
                'web_value-updated_at' => now(),
            ]
        );

        return response('', 201);
    }

    /**
     * Экспорт кастомных значений
     *
     * @param ExportCustomValuesRequest $request
     * @return JsonResponse
     */
    public function export(ExportCustomValuesRequest $request): JsonResponse
    {
        $dateFrom = $request->validated('from_timestamp');
        $dateTo = $request->validated('to_timestamp');

        $offset = $request->validated('offset') ?? 0;
        $limit = $request->validated('limit') ?? 100;

        $query = InvoiceCustomValues::select([
            'order_id',
            'web_value'
        ])
            ->where('web_value', '!=', NULL)
            ->offset($offset)
            ->limit($limit);

        if ($dateFrom) {
            $query->where('web_value-updated_at', '>=', Carbon::createFromTimestamp($dateFrom));
        }
        if ($dateTo) {
            $query->where('web_value-updated_at', '<=', Carbon::createFromTimestamp($dateTo));
        }

        $res = $query->get();

        $count = $query->select(DB::raw('COUNT(*) as count'))->first();
        $count = $count !== null ? $count->count : 0;

        return response()->json([
            'values' => ExportCustomValues::collection($res),
            'offset' => $offset,
            'limit' => $limit,
            'count_all' => $count
        ]);
    }

}
