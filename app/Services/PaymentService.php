<?php

namespace App\Services;

use App\Http\Traits\MapTrait;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoicePaymentItem;

class PaymentService
{
    use MapTrait;

    public function setIsPaid(string $order_id): void
    {
        $payment = InvoicePayment::where('order_id', $order_id)->first();

        if($payment !== null) {
            $payment->paid_percent = 100;
            $payment->save();
        }
    }

    /**
     * Создание invoicePayment и его invoicePaymentItem
     *
     * @param array $dataPayment
     * @return void
     */
    public function savePaymentInfo(array $dataPayment): void
    {
        $invoice = Invoice::where('order_id', $dataPayment['order_id'])->lockForUpdate()->first();
        if (is_null($invoice)) {
            return;
        }

        if ($invoice->contract_date == strtotime("Y-m-d H:i:s", 0)) {
            $invoice->contract_date =  $dataPayment['contract_date'];
            $invoice->save();
        }

        $paymentResource = $this->viewPaymentItem($dataPayment['order_id'], $dataPayment['paid_detail']);

        InvoicePayment::updateOrCreate(
            ['order_id' => $dataPayment['order_id']], // Необходимо уточнение, что именно являеся уникальным атрибутом таблицы
            [
                'order_id' => $dataPayment['order_id'],
                'paid_amount' => (float)str_replace(',', '.', $this->replaceSpaces($dataPayment['paid_amount'])),
                'paid_percent' => (int)$dataPayment['paid_percent'],
                'last_payment_date' => date('Y-m-d H:i:s', $paymentResource['last_payment_date']),
            ]
        );

        // Удаление истории прошлых оплат
        InvoicePaymentItem::where('order_id', $dataPayment['order_id'])->delete();

        foreach ($paymentResource['paymentItems'] as $item) {
            $item->save();
        }
    }

    /**
     * @param string $orderId
     * @param array $details
     * @return array
     */
    private function viewPaymentItem(string $orderId, array $details): array
    {
        $lastPaymentDate = 0;
        $paymentItemArray = [];

        foreach ($details as $detailData) {

            $invoicePI = new InvoicePaymentItem();
            $invoicePI->order_id = $orderId;
            $invoicePI->amount = (float)$invoicePI->replaceSpaces($detailData['paid_amount']);
            $invoicePI->percent = (int)$detailData['paid_percent'];
            $invoicePI->payment_date = $detailData['paid_date'];

            $paymentItemArray[] = $invoicePI;

            if ($lastPaymentDate < strtotime($detailData['paid_date'])) {
                $lastPaymentDate = strtotime($detailData['paid_date']);
            }
        }

        return [
            'last_payment_date' => $lastPaymentDate,
            'paymentItems' => $paymentItemArray
        ];
    }

}
