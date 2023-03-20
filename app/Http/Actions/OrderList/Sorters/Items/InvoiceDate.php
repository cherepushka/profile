<?php

namespace App\Http\Actions\OrderList\Sorters\Items;

use App\Http\Actions\OrderList\Sorters\SorterInterface;
use App\Models\Invoice;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

final class InvoiceDate implements SorterInterface{

    private string $sortValue;

    public function setValue(string $userValue): void
    {
        $this->sortValue = $userValue;
    }

    public function modifyQuery(Builder $qb): Builder
    {
        $qb = $qb->orderBy('invoice.date', $this->sortValue);

        return $qb;
    }

}