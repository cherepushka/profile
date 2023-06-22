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
        [, $value] = explode(':', $userValue);
        $value = trim($value);

        if(!$value) {
            throw new InvalidArgumentException("Значение фильра не может быть пустым");
        }

        $this->value = $value;
    }

    public function modifyQuery(Builder $qb): Builder
    {
        return $qb->where('invoice.invoice_id', '=', $this->value);
    }
}
