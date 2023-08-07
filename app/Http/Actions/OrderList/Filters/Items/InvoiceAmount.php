<?php

namespace App\Http\Actions\OrderList\Filters\Items;

use App\Http\Actions\OrderList\Filters\FilterInterface;
use DateTimeImmutable;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

final class InvoiceAmount implements FilterInterface
{
    private ?int $from = null;
    private ?int $to = null;

    public function setInfo(string $userValue): void
    {
        [, $values] = explode(':', $userValue);
        [$from, $to] = explode(';', $values);

        if(!$from && !$to) {
            throw new InvalidArgumentException("Вы должны ввести хотя бы одно значение");
        }

        if ($from !== ''){
            $this->from = (int)$from;
        }

        if ($to !== ''){
            $this->to = (int)$to;
        }

        if($this->from && $this->to && $this->from > $this->to) {
            throw new InvalidArgumentException("Максимум суммы заказа больше минимальной суммы");
        }
    }

    public function modifyQuery(Builder $qb): Builder
    {
        $qb = $this->from !== null
            ? $qb->where('invoice.order_amount', '>=', $this->from)
            : $qb;

        $qb = $this->to !== null
            ? $qb->where('invoice.order_amount', '<=', $this->to)
            : $qb;

        return $qb;
    }

}
