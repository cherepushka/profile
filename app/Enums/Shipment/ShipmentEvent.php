<?php

namespace App\Enums\Shipment;

interface ShipmentEvent
{

    /**
     * Поиск ивента по названию
     *
     * @param string $eventTitle
     * @return self
     */
    public static function matchEvent(string $eventTitle): self;

    /**
     * Является ли текущее состояние конечным в цикле доставки
     *
     * @return bool
     */
    public function isFinal(): bool;

    /**
     * Получить группу для ивента
     *
     * @return EventGroup
     */
    public function getEventGroup(): EventGroup;

}
