<?php

namespace App\Http\Actions\OrderList\Filters;

use App\Http\Actions\OrderList\Enums\Filter;
use App\Http\Actions\OrderList\Filters\FilterInterface;

final class FilterParser{

    /**
     * @return FilterInterface
     */
    public function parse(string $filter_params): FilterInterface
    {
        $filterName = explode(':', $filter_params)[0];

        $filterMatch = Filter::from($filterName);
        $filter = $filterMatch->getFilter();

        $filter->setInfo($filter_params);

        return $filter;
    }

}