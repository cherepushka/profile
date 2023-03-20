<?php

namespace App\Packages\Payments\Cloudpayments\RequestMappers;

use App\Packages\Payments\Cloudpayments\Dto\Payment;

final class OrderCreate{

    public function getRequestBody(Payment $payment): string
    {
        $requestBody = [
            'Amount' => $payment->getAmount(),
            'InvoiceId' => $payment->getInvoiceId(),
            'AccountId' => $payment->getAmount(),
            'Currency' => $payment->getCurrency(),
            'SendEmail' => $payment->isSendEmail(),
            'Description' => $payment->getDescription(),
            'JsonData' => [
                'cloudPayments' => [
                    'customerReceipt' => [
                        'taxationSystem' => $payment->getTaxationSystem(),
                        'phone' => $payment->getPhone(),
                        'amounts' => [
                            'electronic' => $payment->getAmountElectronic(),
                        ],
                    ],
                ],
            ],
        ];

        /**
         * @var $item PaymentRecieptItem
         */
        foreach($payment->getItems() as $item){
            $requestBody['JsonData']['cloudPayments']['customerReceipt']['Items'][] = [
                'label' => $item->getLabel(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getPrice(),
                'amount' => $item->getAmount(),
                'vat' => $item->getVat(),
                'method' => $item->getMethod(),
                'object' => $item->getObject(),
                'measurementUnit' => $item->getMeasurementUnit(),
            ];
        }

        return json_encode($requestBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | 128);
    }

}