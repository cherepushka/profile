<?php

namespace App\Http\Actions\OrderList\Enums;

use App\Http\Actions\OrderList\Sorters\SorterInterface;
use App\Http\Actions\OrderList\Sorters\Items\InvoiceDate;
use App\Http\Actions\OrderList\Sorters\Items\LastShipmentDate;
use App\Http\Actions\OrderList\Sorters\Items\LastPaymentDate;

/**
 * Доступные сортировки
 */
enum Sort: string
{
    case INVOICE_DATE = 'invoiceDate';
    case LAST_SHIPMENT_DATE = 'lastShipmentDate';
    case LAST_PAYMENT_DATE = 'lastPaymentDate';

    public function getSorter(): SorterInterface
    {
        return match($this) {
            Sort::INVOICE_DATE => new InvoiceDate(),
            Sort::LAST_SHIPMENT_DATE => new LastShipmentDate(),
            Sort::LAST_PAYMENT_DATE => new LastPaymentDate(),
        };
    }
}
