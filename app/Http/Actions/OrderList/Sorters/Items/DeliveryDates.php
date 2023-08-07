<?php

namespace App\Http\Actions\OrderList\Sorters\Items;

use App\Http\Actions\OrderList\Sorters\SorterInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DeliveryDates implements SorterInterface
{

    private string $sortValue;

    public function setValue(string $userValue): void
    {
        $this->sortValue = $userValue;
    }

    public function modifyQuery(Builder $qb): Builder
    {
        if (mb_strtolower($this->sortValue) === 'asc'){
            return $qb->orderBy(DB::raw('MIN(`invoice_shipment_detail`.`delivery_date`)'), 'asc');
        }

        return $qb->orderBy(DB::raw('MAX(`invoice_shipment_detail`.`delivery_date`)'), 'desc');
    }
}
