<?php

namespace App\Http\Actions\OrderList\Sorters;

use Illuminate\Database\Query\Builder;

interface SorterInterface{

    public function setValue(string $userValue): void;

    public function modifyQuery(Builder $qb): Builder;
}