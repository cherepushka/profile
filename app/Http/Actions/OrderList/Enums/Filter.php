<?php

namespace App\Http\Actions\OrderList\Enums;

use App\Http\Actions\OrderList\Filters\FilterInterface;
use App\Http\Actions\OrderList\Filters\Items\CommercialOfferNumberFilter;
use App\Http\Actions\OrderList\Filters\Items\DeliveryDateFilter;
use App\Http\Actions\OrderList\Filters\Items\DeliveryStatus;
use App\Http\Actions\OrderList\Filters\Items\DeliveryTrackNumber;
use App\Http\Actions\OrderList\Filters\Items\InvoiceAmount;
use App\Http\Actions\OrderList\Filters\Items\InvoiceDateFilter;
use App\Http\Actions\OrderList\Filters\Items\WaybillNumber;

/**
 * Доступные фильтры
 */
enum Filter: string
{
    case INVOICE_DATE = 'invoiceDate';
    case COMMERCIAL_OFFER_NUMBER = 'commercialOfferNumber';
    case WAYBILL_NUMBER = 'waybillNumber';
    case DELIVERY_TRACK_NUMBER = 'deliveryTrackNumber';
    case DELIVERY_STATUS = 'deliveryStatus';
    case DELIVERY_DATE = 'deliveryDate';
    case INVOICE_AMOUNT = 'invoiceAmount';

    public function getFilter(): FilterInterface
    {
        return match($this) {
            Filter::INVOICE_DATE => new InvoiceDateFilter(),
            Filter::COMMERCIAL_OFFER_NUMBER => new CommercialOfferNumberFilter(),
            Filter::WAYBILL_NUMBER => new WaybillNumber(),
            Filter::DELIVERY_TRACK_NUMBER => new DeliveryTrackNumber(),
            Filter::DELIVERY_STATUS => new DeliveryStatus(),
            Filter::DELIVERY_DATE => new DeliveryDateFilter(),
            Filter::INVOICE_AMOUNT => new InvoiceAmount(),
        };
    }
}
