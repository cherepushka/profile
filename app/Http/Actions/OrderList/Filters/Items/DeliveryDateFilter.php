<?php

namespace App\Http\Actions\OrderList\Filters\Items;

use App\Http\Actions\OrderList\Filters\FilterInterface;
use DateTimeImmutable;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

final class DeliveryDateFilter implements FilterInterface
{
    private ?DateTimeImmutable $dateStart = null;
    private ?DateTimeImmutable $dateEnd = null;

    public function setInfo(string $userValue): void
    {
        [$timestampMsStart, $timestampMsEnd] = explode(';', $userValue);

        $timestampMsStart = (int)$timestampMsStart;
        $timestampMsEnd = (int)$timestampMsEnd;

        if($timestampMsStart > 0) {
            $this->dateStart = (new DateTimeImmutable())->setTimestamp($timestampMsStart / 1000);
        }

        if($timestampMsEnd > 0) {
            $this->dateEnd = (new DateTimeImmutable())->setTimestamp($timestampMsEnd / 1000);
        }

        if($this->dateStart !== null && $this->dateEnd !== null) {
            if($this->dateStart > $this->dateEnd) {
                throw new InvalidArgumentException("Дата начала фильтрации больше даты конца");
            }
        } elseif($this->dateStart === null && $this->dateEnd === null) {
            throw new InvalidArgumentException("Оба значения фильтрации пусты");
        }
    }

    public function modifyQuery(Builder $qb): Builder
    {
        $qb = $this->dateEnd !== null
            ? $qb->where('invoice_shipment_detail.delivery_date <= ?', [$this->dateStart->format('Y-m-d H:i:s')])
            : $qb;

        return $this->dateEnd !== null
            ? $qb->where('invoice_shipment_detail.delivery_date <= ?', [$this->dateEnd->format('Y-m-d H:i:s')])
            : $qb;
    }

}
