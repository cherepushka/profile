<?php

namespace App\Http\Actions\OrderList\Filters\Items;

use App\Http\Actions\OrderList\Filters\FilterInterface;
use App\Models\InvoiceShipmentDetail;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

class DeliveryTrackNumber implements FilterInterface
{
    private string $value;

    public function setInfo(string $userValue): void
    {
        $userValue = trim($userValue);

        if(!$userValue) {
            throw new InvalidArgumentException("Значение фильтра не может быть пустым");
        }

        $this->value = $userValue;
    }

    public function modifyQuery(Builder $qb): Builder
    {
        $shipmentDetail = InvoiceShipmentDetail::where('transport_company_id', '=', $this->value)->first();

        $order_id = '';
        if($shipmentDetail !== null) {
            $order_id = $shipmentDetail->order_id;
        }

        return $qb->where('invoice.order_id', '=', $order_id);
    }
}
