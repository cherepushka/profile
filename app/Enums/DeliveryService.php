<?php

namespace App\Enums;

enum DeliveryService: string
{
    case MAJOR_EXPRESS = 'Major Express';

    public static function match(string $title): DeliveryService|false
    {
        if (preg_match('#^Major #u', $title)) {
            return DeliveryService::MAJOR_EXPRESS;
        }

        return false;
    }
}
