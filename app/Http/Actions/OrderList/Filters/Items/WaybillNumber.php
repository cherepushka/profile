<?php

namespace App\Http\Actions\OrderList\Filters\Items;

use App\Http\Actions\OrderList\Filters\FilterInterface;
use App\Models\InvoiceShipmentDetail;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

class WaybillNumber implements FilterInterface
{
    private string $value;

    public function setInfo(string $userValue): void
    {
        [, $value] = explode(':', $userValue);
        $value = trim($value);

        if(!$value) {
            throw new InvalidArgumentException("Значение фильра не может быть пустым");
        }

        $this->value = $value;
    }

    public function modifyQuery(Builder $qb): Builder
    {
        $shipmentDetail = InvoiceShipmentDetail::where('realization_number', '=', $this->value)->first();

        $order_id = '';
        if($shipmentDetail !== null) {
            $order_id = $shipmentDetail->order_id;
        }

        return $qb->where('invoice.order_id', '=', $order_id);
    }
}
