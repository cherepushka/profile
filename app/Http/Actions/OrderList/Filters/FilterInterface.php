<?php

namespace App\Http\Actions\OrderList\Filters;

use Illuminate\Database\Query\Builder;

interface FilterInterface{

    public function setInfo(string $userValue): void;

    public function modifyQuery(Builder $qb): Builder;
}