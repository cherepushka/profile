<?php

namespace App\Http\Resources;

use App\Enums\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderFull extends JsonResource
{

    private array $shipmentsArrayAssoc = [];

    private array $shipmentsTotalArrayCount = [];
    private array $totalItemsUnitsCount = [];

    private array $products = [];

    private ?Document $offerDocsZip = null;
    private array $offerDocs = [];
    private ?Document $shipmentDocsZip = null;
    private array $shipmentDocs = [];

    private float $totalPurePrice = 0;
    private float $totalVatPrice = 0;

    private array $deliveryStatuses = [];

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $this->setShipmentsArrayAssoc();
        $this->setProductsInfo();
        $this->setDocsInfo();
        $this->setDeliveryStatuses();

        return [
            'offerDocsZip' => $this->offerDocsZip,
            'offerDocs' => $this->offerDocs,
            'shipmentDocsZip' => $this->shipmentDocsZip,
            'shipmentDocs' => $this->shipmentDocs,
            'currency' => $this->currency,
            'products' => $this->products,
            'itemsCount' => $this->totalItemsUnitsCount,
            'purePrice' => $this->totalPurePrice,
            'vatPrice' => $this->totalVatPrice,
            'shippedCount' => $this->shipmentsTotalArrayCount,
            'deliveryStatuses' => $this->deliveryStatuses,
        ];
    }

    private function setDeliveryStatuses(): void
    {
        if (!$this->whenLoaded('allShipmentTrackingInfoRelation')){
            return;
        }

        foreach ($this->allShipmentTrackingInfoRelation as $info){

            $this->deliveryStatuses[$info->shipment_id]['transportCompany'] = $info->transport_company;
            $this->deliveryStatuses[$info->shipment_id]['history'][] = [
                'title' => $info->event_title,
                'geo' => $info->event_current_geo,
                'datetime' => $info->event_date,
            ];
        }

        if (!$this->whenLoaded('invoiceShipmentRelation') || count($this->allShipmentTrackingInfoRelation) === 0){
            return;
        }

        foreach ($this->invoiceShipmentRelation->shipmentDetailsRelation as $shipmentDetail){
            $this->deliveryStatuses[$shipmentDetail->id]['trackingCode'] = $shipmentDetail->transport_company_id;
            $this->deliveryStatuses[$shipmentDetail->id]['realizationNumber'] = $shipmentDetail->realization_number;
            $this->deliveryStatuses[$shipmentDetail->id]['shippingDate'] = Carbon::parse($shipmentDetail->date)->timestamp;
        }
    }

    private function setDocsInfo(): void
    {
        if(!$this->whenLoaded('documentRelation')){
            return;
        }

        foreach($this->whenLoaded('documentRelation') as $document){

            $docResource = new Document($document);

            switch($document->section){
                case Section::INVOICE->getSection():

                    if($document->extension === 'zip'){
                        $this->offerDocsZip = $docResource;
                    } else {
                        $this->offerDocs[] = $docResource;
                    }
                    break;
                case Section::SHIPMENT->getSection():

                    if($document->extension === 'zip'){
                        $this->shipmentDocsZip = $docResource;
                    } else {
                        $this->shipmentDocs[] = $docResource;
                    }
                    break;
            }
        }
    }

    private function setShipmentsArrayAssoc(): void
    {
        $shipmentItems = $this->whenLoaded('invoiceShipmentRelation')
            ?->latestShipmentDetailRelation
            ?->itemRelation;

        if(!$shipmentItems){
            return;
        }

        foreach($shipmentItems->all() as $shipment){
            $this->shipmentsArrayAssoc[$shipment->invoice_product_id] = $shipment->product_qty;
        }
    }

    private function setProductsInfo(): void
    {
        if(!$this->whenLoaded('invoiceItemRelation')){
            return;
        }

        foreach($this->whenLoaded('invoiceItemRelation') as $product){

            $productShippedCount = $this->shipmentsArrayAssoc[$product->id] ?? 0;

            // Расчет всего товаров (группировка по единице измерения)
            $unitCount = $this->totalItemsUnitsCount[$product->unit] ?? 0;
            $this->totalItemsUnitsCount[$product->unit] = $unitCount + $product->qty;

            // Расчет всего отгруженных товаров (группировка по единице измерения)
            $shipmentUnitCount = $this->shipmentsTotalArrayCount[$product->unit] ?? 0;
            $this->shipmentsTotalArrayCount[$product->unit] = $shipmentUnitCount + $productShippedCount;

            $this->totalPurePrice += $product->pure_price;
            $this->totalVatPrice += $product->final_price;

            $this->products[] = [
                'title' => $product->title,
                'count' => $product->qty,
                'unit' => $product->unit,
                'purePrice' => $product->pure_price,
                'vatPrice' => $product->final_price,
                'shippedCount' => $productShippedCount . ' ' . $product->unit,
            ];
        }
    }

}
