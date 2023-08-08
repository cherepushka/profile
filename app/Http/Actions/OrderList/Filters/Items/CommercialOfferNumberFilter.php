<?php

namespace App\Http\Actions\OrderList\Filters\Items;

use App\Http\Actions\OrderList\Filters\FilterInterface;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

class CommercialOfferNumberFilter implements FilterInterface
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
        return $qb->where('invoice.invoice_id', '=', $this->value);
    }
}
