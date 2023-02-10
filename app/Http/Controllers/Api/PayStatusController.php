<?php

namespace App\Http\Controllers\Api;

use App\Enums\Section;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayStatusRequest;
use App\Http\Traits\MapTrait;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\InvoicePaymentItem;
use App\Models\InvoiceShipment;
use App\Models\InvoiceShipmentDetail;
use App\Models\InvoiceShipmentDetailItem;
use App\Models\Profile;
use App\Models\ProfileInternal;
use App\Services\DocumentServices;

class PayStatusController extends Controller
{
    use MapTrait;

    /**
     * Получение json об обновлении статуса заказа
     *
     * @param PayStatusRequest $request
     * @return void
     */
    public function updateStatus(PayStatusRequest $request)
    {
        /**
         * Получение валидированных параметров запроса
         * @var PayStatusRequest $payStatusRequest
         */
        $payStatusRequest = $request->validated();

        $superIsPaid = $payStatusRequest['super_is_paid'];

//        Уточнить необходимость параметра super_is_paid
//
//        switch ($superIsPaid) {
//            case true:
//                /* ... */
//                break;
//
//            case false:
//                /* ... */
//                break;
//        }

        $this->invoicePaymentCreate($payStatusRequest['data']);
        $this->invoiceShipmentCreate($payStatusRequest['data_shipment']);
    }

    /**
     * Создание invoicePayment и его invoicePaymentItem
     *
     * @param $paymentData
     * @return void
     */
    private function invoicePaymentCreate($paymentData)
    {
        foreach ($paymentData as $dataPayment) {
            $invoice = Invoice::where('order_id', $dataPayment['order_id'])->first();

            /**
             * Пропустить элемент при отсутствии заказа в базе данных
             */
            if (is_null($invoice)) {
                continue;

            } else {
                if ($invoice->contract_date == strtotime("Y-m-d H:i:s", 0)) {
                    Invoice::update(['order_id' => $dataPayment['order_id']],
                        [
                            'contract_date' => $dataPayment['contract_date'],
                        ]
                    );
                }
            }

            $paymentResource = $this->viewPaymentItem($dataPayment);

            InvoicePayment::updateOrCreate(
                ['order_id' => $dataPayment['order_id']], // Необходимо уточнение, что именно являеся уникальным атрибутом таблицы
                [
                    'order_id' => $dataPayment['order_id'],
                    'paid_amount' => (double)$this->replaceSpaces($dataPayment['paid_amount']),
                    'paid_percent' => (int)$dataPayment['paid_percent'],
                    'last_payment_date' => date('Y-m-d H:i:s', $paymentResource['last_payment_date']),
                ]
            );

            /**
             * Удаление истории прошлых оплат
             */
            InvoicePaymentItem::where('order_id', $dataPayment['order_id'])->delete();

            foreach ($paymentResource['paymentItems'] as $item) {
                $item->save();
            }
        }
    }

    /**
     * @param $paymentData | Массив действий в заказе - paid_detail
     * @return array
     */
    private function viewPaymentItem($paymentData)
    {
        $lastPaymentDate = 0;
        $paymentItemArray = [];

        $detail = $paymentData['paid_detail'];

        foreach ($detail as $detailData) {
            $invoicePI = new InvoicePaymentItem;
            $invoicePI->order_id = $paymentData['order_id'];
            $invoicePI->amount = (double)$invoicePI->replaceSpaces($detailData['paid_amount']);
            $invoicePI->percent = (int)$detailData['paid_percent'];
            $invoicePI->payment_date = $detailData['paid_date'];
            array_push($paymentItemArray, $invoicePI);

            if ($lastPaymentDate < strtotime($detailData['paid_date'])) {
                $lastPaymentDate = strtotime($detailData['paid_date']);
            }
        }

        return ['last_payment_date' => $lastPaymentDate, 'paymentItems' => $paymentItemArray];
    }

    /**
     * Создание отгрузки товара invoiceShipment
     *
     * @param $shipmentData
     * @return void
     */
    private function invoiceShipmentCreate($shipmentData)
    {
        foreach ($shipmentData as $dataShipment) {

            $invoice = Invoice::where(['order_id' => $dataShipment['order_id']])->first();

            if (is_null($invoice)) {
                continue;
            }

            InvoiceShipment::updateOrCreate(['order_id' => $dataShipment['order_id']],
                [
                    'order_id' => $dataShipment['order_id'],
                    'currency' => $dataShipment['selling_currency'],
                    'amount' => (double)$this->replaceSpaces($dataShipment['selling_amount']),
                ]
            );

            foreach ($dataShipment['selling_detail'] as $details) {
                $this->createShipmentDetail($dataShipment['order_id'], $details);
            }

            $this->createShipmentFiles($dataShipment['order_id'], $dataShipment['shipment_file']);
        }
    }

    /**
     * Создание отгрузки товара invoiceShipmentDetail
     *
     * @param $order_id | Номер заказа
     * @param $details | Детали заказа
     * @return void
     */
    private function createShipmentDetail($order_id, $details)
    {
        InvoiceShipmentDetail::updateOrCreate(['realization_id' => $details['selling_id']],
            [
                'order_id' => $order_id,
                'realization_id' => $details['selling_id'],
                'realization_number' => (int)$details['selling_number'],
                'amount' => (double)$this->replaceSpaces($details['selling_sum']),
                'transport_company' => $details['transport_company'],

                //'transport_company_id' => $details['transport_company_number'],
                'transport_company_id' => 0, // Debug value

                'shipment_date' => $details['selling_date'],
            ]
        );

        InvoiceShipmentDetailItem::where(['order_id' => $order_id])->delete();

        foreach ($details['shipment_detail'] as $detail) {
            $this->createShipmentDetailItem($order_id, $detail);
        }
    }

    /**
     * Создание отгрузки товара invoiceShipmentDetailItem
     *
     * @param $order_id | Номер заказа
     * @param $detail | Товары в заказе
     * @return void
     */
    private function createShipmentDetailItem($order_id, $detail)
    {
        $detailItem = new InvoiceShipmentDetailItem;
        $invoiceItem = InvoiceItem::where(['internal_id' => $detail['product_id'], 'order_id' => $order_id])->first();

        if (!is_null($invoiceItem)) {
            $detailItem->order_id = $order_id;
            $detailItem->invoice_product_id = $invoiceItem->id;
            $detailItem->product_quantity = $detail['product_qty'];
            $detailItem->save();
        }
    }

    /**
     * Создание файлов отгрузки
     *
     * @param $order_id
     * @param $filesData
     * @return void
     */
    private function createShipmentFiles($order_id, $filesData)
    {
        $hash = $this->getUserHash($order_id);

        if (!is_null($hash)) {
            $docService = new DocumentServices();
            $document = new Document;

            $array = $filesData += ['order_id' => $order_id];
            $docService->getData($document->map($array), $filesData['file_data'], Section::SHIPMENT,  $hash, $filesData['file_pswd']);
        }
    }

    /**
     * Получение Хэша пользователя для создания архива отгрузки
     *
     * @param $order_id
     */
    private function getUserHash($order_id)
    {
        $invoice = Invoice::where(['order_id' => $order_id])->first();

        if (!is_null($invoice)) {
            $profileInternal = ProfileInternal::where(['internal_id' => $invoice->user_id])->first();

            if (!is_null($profileInternal)) {
                $profile = Profile::where(['id' => $profileInternal->profile_id])->first();

                if (!is_null($profile)) {
                    return $profile->password;
                }
            }
        }
    }
}
