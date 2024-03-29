<?php

namespace App\Http\Actions\OrderList\Filters;

use App\Http\Actions\OrderList\Enums\Filter;
use App\Http\Actions\OrderList\Filters\FilterInterface;

final class FilterParser
{
    /**
     * @param string $filter_params
     * @return FilterInterface
     */
    public function parse(string $filter_params): FilterInterface
    {
        [$filterName, $filterValue] = explode(':', $filter_params);

        $filterMatch = Filter::from($filterName);
        $filter = $filterMatch->getFilter();

        $filter->setInfo($filterValue);

        return $filter;
    }

}
