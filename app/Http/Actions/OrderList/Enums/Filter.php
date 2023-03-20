<?php

namespace App\Http\Actions\OrderList\Enums;

use App\Http\Actions\OrderList\Filters\FilterInterface;
use App\Http\Actions\OrderList\Filters\Items\InvoiceDateFilter;

/**
 * Доступные фильтры
 */
enum Filter: string
{
    case INVOICE_DATE = 'invoice_date';

    public function getFilter(): FilterInterface
    {
        return match($this) {
            Filter::INVOICE_DATE => new InvoiceDateFilter()
        };
    }
}
