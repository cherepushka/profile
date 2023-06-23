<?php

namespace App\Http\Actions\OrderList\Filters\Items;

use App\Http\Actions\OrderList\Filters\FilterInterface;
use DateTimeImmutable;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

final class InvoiceAmount implements FilterInterface{

    private int $from = 0;
    private int $to = 0;

    public function setInfo(string $userValue): void
    {
        [, $values] = explode(':', $userValue);
        [$from, $to] = explode(';', $values);

        if(!$from && !$to) {
            throw new InvalidArgumentException("Вы должны ввести хотя бы одно значение");
        }

        if($from > $to){
            throw new InvalidArgumentException("Максимум суммы заказа больше минимальной суммы");
        }

        $this->from = $from;
        $this->to = $to;
    }

    public function modifyQuery(Builder $qb): Builder
    {
        return $qb
            ->where('invoice.order_amount', '>=', $this->from)
            ->where('invoice.order_amount', '<=', $this->to);
    }

}
