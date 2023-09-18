<?php

namespace App\Packages\Payments\Cloudpayments\Dto;

final class PaymentRecieptItem
{
    private string $label;
    private float $quantity;
    private float $price;
    private float $amount;
    private int $vat = 18;
    private int $method = 1;
    private int $object = 1;
    private string $measurementUnit = 'шт';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getVat(): int
    {
        return $this->vat;
    }

    public function getMethod(): int
    {
        return $this->method;
    }

    public function getObject(): int
    {
        return $this->object;
    }

    public function getMeasurementUnit(): string
    {
        return $this->measurementUnit;
    }

}
