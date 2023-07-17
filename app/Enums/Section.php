<?php

namespace App\Enums;

/**
 * Перечисление для секций в загрузках
 */
enum Section
{
    case INVOICE;
    case SHIPMENT;

    /**
     * Выбор секции
     *
     * @return string
     */
    public function getSection(): string
    {
        return match ($this) {
            Section::INVOICE => 'Коммерческое предложение',
            Section::SHIPMENT => 'Отгрузка',
        };
    }
}
