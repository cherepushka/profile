<?php

namespace App\Enums\Shipment;

use App\Enums\EnumToArray;

enum EventGroup: string
{
    use EnumToArray;

    case UNKNOWN = 'неизвестный статус';
    case IS_NOT_SENT = 'не отправлен';
    case TAKEN_FROM_SENDER = 'груз принят';
    case AT_AN_INTERMEDIATE_POINT = 'в промежуточном пункте';
    case SHIPPED_FROM_AN_INTERMEDIATE_POINT = 'отправлен с промежуточного пункта';
    case IN_TRANSIT = 'в пути';
    case DELIVERY_ERROR = 'ошибка доставки';
    //
    case DELIVERY_CANCELLATION = 'отмена доставки';
    case DELIVERED = 'груз доставлен';
}
