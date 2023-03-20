<?php

namespace App\Packages\Payments\Cloudpayments\Dto;

/**
 * Документация:
 * 
 * https://developers.cloudpayments.ru/#check
 */
final class Payment{

    private float $amount;
    private string $invoiceId;
    private string $accountId;
    private string $currency = 'RUB';
    private bool $sendEmail = true;
    private string $description = 'Оплата на сайте';
    
    /**
     * @var $items []PaymentRecieptItem
     */
    private array $items;
    
    private int $taxationSystem = 0;
    private string $email;
    private string $phone = '';
    private float $amountElectronic;

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return Payment
     */
    public function setAmount(float $amount): Payment
    {
        $this->amount = $amount;
        $this->amountElectronic = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceId(): string
    {
        return $this->invoiceId;
    }

    /**
     * @param string $invoiceId
     * @return Payment
     */
    public function setInvoiceId(string $invoiceId): Payment
    {
        $this->invoiceId = $invoiceId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->accountId;
    }

    /**
     * @param string $accountId
     * @return Payment
     */
    public function setAccountId(string $accountId): Payment
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return bool
     */
    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return Payment
     */
    public function addItem(PaymentRecieptItem $item): Payment
    {
        $this->items[] = $item;
        
        return $this;
    }

    /**
     * @return int
     */
    public function getTaxationSystem(): int
    {
        return $this->taxationSystem;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Payment
     */
    public function setEmail(string $email): Payment
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return float
     */
    public function getAmountElectronic(): float
    {
        return $this->amountElectronic;
    }

}