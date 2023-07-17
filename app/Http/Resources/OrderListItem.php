<?php

namespace App\Http\Resources;

use App\Models\Manager;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class OrderListItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->order_id,
            'orderDate' => strtotime($this->date),
            'items' => $this->position_count,
            'fullPrice' => $this->order_amount,
            'currency' => $this->currency,
            'manager' => new ManagerInfo($this->getManager()),
            'mail_trigger' => $this->mail_trigger,
            'payLink' => $this->displayLink(),
            'paymentStatus' => $this->displayPaymentStatus(),
            'shipmentStatus' => $this->displayShipmentStatus(),
            'lastPaymentDate' => $this->last_payment_date !== null ? strtotime($this->last_payment_date) : null,
            'lastShipmentDate' => $this->last_shipment_date !== null ? strtotime($this->last_shipment_date) : null,
            'customFieldValue' => (string)$this->custom_field,
            'commercialOfferNumber' => (int)$this->invoice_id,
        ];
    }

    private function getManager(): stdClass
    {
        $manager = new stdClass();

        $manager->id = $this->id;
        $manager->name = $this->name;
        $manager->surname = $this->surname;

        return $manager;
    }

    private function displayPaymentStatus(): string
    {
        if(!$this->paid_percent || $this->paid_percent === 0) {
            return 'Не оплачен';
        }

        if($this->paid_percent >= 100) {
            return 'Оплачен';
        }

        return 'Частично плачен';
    }

    private function displayShipmentStatus(): string
    {
        $shippedCount = (int)$this->shipped_qty_count;

        if($shippedCount === 0) {
            return 'Не отгружен';
        }

        $orderedCount = (int)$this->products_qty_count;

        if($shippedCount >= $orderedCount) {
            return 'Отгружен';
        }

        return 'Частично отгружен';
    }

    private function displayLink(): ?string
    {
        // Если в 1С оплата заблокирована
        if($this->pay_block !== 0) {
            return null;
        }

        // Если Юр. лицо, то не показываем
        if($this->entity !== 0) {
            return null;
        }

        return $this->pay_link;
    }
}
