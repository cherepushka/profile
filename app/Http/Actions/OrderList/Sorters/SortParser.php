<?php

namespace App\Http\Actions\OrderList\Sorters;

use App\Http\Actions\OrderList\Enums\Sort;
use InvalidArgumentException;

final class SortParser
{
    private string $regexp = '#(?<sort_name>[a-zA-Z]+)_(?<sort_value>[a-z]+)#';
    private array $availableValues = ['asc', 'desc'];

    public function parse(string $order_params): SorterInterface
    {
        if(!preg_match($this->regexp, trim($order_params), $matches)) {
            throw new InvalidArgumentException('Неизвестный формат сортировки');
        }

        if(!isset($matches['sort_name'])) {
            throw new InvalidArgumentException('Неизвестный формат имени сортировки');
        }

        if(!isset($matches['sort_value']) || !in_array($matches['sort_value'], $this->availableValues)) {
            throw new InvalidArgumentException('Неизвестный формат значения сортировки');
        }

        //

        $sorterMatch = Sort::from($matches['sort_name']);
        $sorter = $sorterMatch->getSorter();

        $sorter->setValue(mb_strtoupper($matches['sort_value']));

        return $sorter;
    }

}
