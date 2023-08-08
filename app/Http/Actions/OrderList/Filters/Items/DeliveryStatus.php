<?php

namespace App\Http\Actions\OrderList\Filters\Items;

use App\Enums\Shipment\EventGroup;
use App\Http\Actions\OrderList\Filters\FilterInterface;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

class DeliveryStatus implements FilterInterface
{
    private EventGroup $group;

    public function setInfo(string $userValue): void
    {
        $userValue = trim($userValue);

        if(!$userValue) {
            throw new InvalidArgumentException("Значение фильтра не может быть пустым");
        }

        $this->group = constant(EventGroup::class . '::' . $userValue);
    }

    public function modifyQuery(Builder $qb): Builder
    {
        return $qb->having('last_event_groups', 'LIKE', '%'.$this->group->value.'%');
    }
}
