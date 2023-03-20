<?php

namespace App\Http\Actions\OrderList\Sorters\Items;

use App\Http\Actions\OrderList\Sorters\SorterInterface;
use Illuminate\Database\Query\Builder;

final class LastPaymentDate implements SorterInterface{

    private string $sortValue;

    public function setValue(string $userValue): void
    {
        $this->sortValue = $userValue;
    }

    public function modifyQuery(Builder $qb): Builder
    {
        $qb = $qb->orderBy('invoice_payment.last_payment_date', $this->sortValue);

        return $qb;
    }

}