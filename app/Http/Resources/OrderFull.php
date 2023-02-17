<?php

namespace App\Http\Resources;

use App\Enums\Section;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderFull extends JsonResource
{

    private array $shipmentsArrayAssoc = [];

    private array $shipmentsTotalArrayCount = [];
    private array $totalItemsUnitsCount = [];

    private array $products = [];

    private array $offerDocs = [];
    private array $shipmentDocs = [];

    private int $totalPurePrice = 0;
    private int $totalVatPrice = 0;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->setShipmentsArrayAssoc();
        $this->setProductsInfo();
        $this->setDocsinfo();

        return [
            'offerDocs' => $this->offerDocs,
            'shipmentDocs' => $this->shipmentDocs,
            'currency' => $this->currency,
            'products' => $this->products,
            'itemsCount' => $this->totalItemsUnitsCount,
            'purePrice' => $this->totalPurePrice,
            'vatPrice' => $this->totalVatPrice,
            'shippedCount' => $this->shipmentsTotalArrayCount,
        ];
    }

    private function setDocsinfo(): void
    {
        if(!$this->whenLoaded('documentRelation')){
            return;
        }

        foreach($this->whenLoaded('documentRelation') as $document){

            $docResource = new Document($document);

            switch($document->section){
                case Section::INVOICE->getSection():
                    $this->offerDocs[] = $docResource;
                    break;
                case Section::SHIPMENT->getSection():
                    $this->shipmentDocs[] = $docResource;
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

            $productShippedCount = isset($this->shipmentsArrayAssoc[$product->id])
                ? $this->shipmentsArrayAssoc[$product->id]
                : 0;
            
            // Расчет всего товаров (группировка по единице измерения)
            $unitCount = isset($this->totalItemsUnitsCount[$product->unit])
                ? $this->totalItemsUnitsCount[$product->unit]
                : 0;
            $this->totalItemsUnitsCount[$product->unit] = $unitCount + $product->qty;

            // Расчет всего отгруженных товаров (группировка по единице измерения)
            $shipmentUnitCount = isset($this->shipmentsTotalArrayCount[$product->unit])
                ? $this->shipmentsTotalArrayCount[$product->unit]
                : 0;
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
