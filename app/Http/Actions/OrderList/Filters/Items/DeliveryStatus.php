<?php

namespace App\Http\Actions\OrderList\Filters\Items;

use App\Http\Actions\OrderList\Filters\FilterInterface;
use App\Models\InvoiceShipmentDetail;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

class DeliveryStatus implements FilterInterface
{
    private string $value;

    public function setInfo(string $userValue): void
    {
        [, $value] = explode(':', $userValue);
        $value = trim($value);

        if(!$value) {
            throw new InvalidArgumentException("Значение фильтра не может быть пустым");
        }

        $this->value = $value;
    }

    public function modifyQuery(Builder $qb): Builder
    {
        return $qb->having('last_event_groups', 'LIKE', '%'.$this->value.'%');
    }
}
