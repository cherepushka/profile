<?php

namespace App\Services;

use App\Http\Traits\MapTrait;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceShipment;
use App\Models\InvoiceShipmentDetail;
use App\Models\InvoiceShipmentDetailItem;
use App\Process\DeliveryStatus\Observe;

class ShipmentService
{
    use MapTrait;

    public function __construct(
        private readonly Observe $deliveryStatusObserver
    ) {
    }

    /**
     * Создание отгрузки товара invoiceShipment
     *
     * @param array $dataShipment
     * @return void
     */
    public function saveShipmentInfo(array $dataShipment): void
    {
        $invoice = Invoice::where(['order_id' => $dataShipment['order_id']])->lockForUpdate()->first();
        if (is_null($invoice)) {
            return;
        }

        InvoiceShipment::updateOrCreate(
            ['order_id' => $dataShipment['order_id']],
            [
                'order_id' => $dataShipment['order_id'],
                'currency' => $dataShipment['selling_currency'],
                'amount' => (float)$this->replaceSpaces($dataShipment['selling_amount']),
            ]
        );

        foreach ($dataShipment['selling_detail'] as $details) {
            $this->createShipmentDetail($dataShipment['order_id'], $details);

            $order_id = $dataShipment['order_id'];
            InvoiceShipmentDetailItem::where(['order_id' => $order_id])->delete();

            foreach ($details['shipment_detail'] as $detail) {

                $invoiceItem = InvoiceItem::where(['internal_id' => $detail['product_id'], 'order_id' => $order_id])->first();
                if (is_null($invoiceItem)) {
                    return;
                }

                $detailItem = new InvoiceShipmentDetailItem();
                $detailItem->order_id = $order_id;
                $detailItem->invoice_product_id = $invoiceItem->id;
                $detailItem->product_qty = $detail['product_qty'];
                $detailItem->save();
            }
        }
    }

    /**
     * Создание отгрузки товара invoiceShipmentDetail
     *
     * @param $order_id string - Номер заказа
     * @param $details array - Детали заказа
     * @return void
     */
    private function createShipmentDetail(string $order_id, array $details): void
    {
        $detail = InvoiceShipmentDetail::where('realization_id', $details['selling_id'])->first();
        $shouldObserve = false;

        if ($detail === null) {
            $shouldObserve = true;
        } else {
            if (!$detail->transport_company_id && $details['transport_company_number']){
                $shouldObserve = true;
            }
        }

        $detail = InvoiceShipmentDetail::updateOrCreate(
            ['realization_id' => $details['selling_id']],
            [
                'order_id' => $order_id,
                'realization_id' => $details['selling_id'],
                'realization_number' => (int)$details['selling_number'],
                'amount' => (float)$this->replaceSpaces($details['selling_sum']),
                'transport_company' => $details['transport_company'],
                'transport_company_id' => $details['transport_company_number'],
                'date' => $details['selling_date'],
                'facture_number' => $details['invoice_facture_number'] ?? null,
                'facture_date' => $details['invoice_facture_date'] ?? null,
            ]
        );

        if($shouldObserve) {
            $detailWithId = InvoiceShipmentDetail::where('realization_id', $detail->realization_id)->first();
            $this->deliveryStatusObserver->observe($detailWithId);
        }
    }

}
