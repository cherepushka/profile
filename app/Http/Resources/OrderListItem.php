<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderListItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->order_id,
            'orderDate' => strtotime($this->date),
            'items' => $this->whenCounted('invoiceItemRelation'),
            'fullPrice' => $this->order_amount,
            'currency' => $this->currency,
            'manager' => new ManagerInfo($this->whenLoaded('managerRelation')),
            'mail_trigger' => $this->mail_trigger,
            'pay_link' => $this->displayLink(),
            'paymentStatus' => $this->displayPaymentStatus(),
            'shipmentStatus' => $this->displayShipmentStatus(),
            'lastShipmentDate' => $this->whenLoaded('invoicePaymentRelation')
                ? strtotime($this->whenLoaded('invoicePaymentRelation')->last_payment_date)
                : null,
            'lastPaymentDate' => $this->whenLoaded('invoiceShipmentRelation')?->latestShipmentDetailRelation
                ? strtotime($this->whenLoaded('invoiceShipmentRelation')->latestShipmentDetailRelation->date)
                : null,
            'customFieldValue' => (string)$this->custom_field,
        ];
    }

    private function displayPaymentStatus(): string
    {
        if(!$this->whenLoaded('invoicePaymentRelation')){
            return 'Не оплачен';
        }

        if($this->whenLoaded('invoicePaymentRelation')->paid_percent / 100 >= 1){
            return 'Оплачен';
        }

        return 'Частично плачен';
    }

    private function displayShipmentStatus(): string
    {
        $shippedItems = $this->whenLoaded('invoiceShipmentRelation')
            ?->latestShipmentDetailRelation
            ?->itemRelation;

        if(!$shippedItems){
            return 'Не отгружен';
        }

        $shippedCount = 0;
        foreach($shippedItems as $item){
            $shippedCount += $item->product_qty;
        }

        $orderedCount = 0;
        foreach($this->whenLoaded('invoiceItemRelation') as $item){
            $orderedCount += $item->qty;
        }

        if($shippedCount >= $orderedCount){
            return 'Отгружен';
        }

        return 'Частично отгружен';
    }

    private function displayLink(): ?string
    {
        // Если в 1С оплата заблокирована
        if($this->pay_block !== 0){
            return null;
        }

        // Если Юр. лицо, то не показываем
        if($this->entity !== 0){
            return null;
        }

        return $this->pay_link;
    }
}
