<?php

namespace App\Enums\Shipment\MajorExpress;

use App\Enums\Shipment\EventGroup;
use App\Enums\Shipment\ShipmentEvent;

enum Event implements ShipmentEvent
{
    case UNKNOWN;

    case INVOICE_INPUT_BY_CUSTOMER;
    case DIGITAL_INVOICE_INPUT_BY_CUSTOMER;

    case TAKEN_FROM_SENDER;

    case ACCEPTED_BY_AGENT;
    case ACCEPTED_AT_THE_CENTRAL_WAREHOUSE;
    case AUTOMATIC_CARGO_MEASUREMENT;
    case SCANNED_FOR_DEPARTURE;
    case MANIFESTED;
    case MOVED_TO_ANOTHER_MANIFEST;
    case HANDED_OVER_TO_THE_SUPPLIER;
    case BOOKING;
    case IN_THE_WAREHOUSE;

    case SENT;

    case RECEIVED_FROM_SUPPLIER;
    case RESTANTE;
    case HANDED_OVER_TO_COURIER;
    case TRANSIT_ACROSS_THE_COUNTRY;
    case TRANSIT_ACROSS_THE_REGION;
    case DELIVERY_POSTPONED_BY_RECIPIENT;
    case TRACE_OPENED;
    case TRACE_CLOSED;
    case FORWARDING;
    case COORDINATION_OF_ADDITIONAL_COSTS;
    case HOLIDAYS;
    case CARRIER_PROBLEMS;
    case REFINEMENT_OF_DATA_TO_DELIVERY;

    case NO_RECIPIENT;
    case WRONG_ADDRESS;
    case REFUSAL_TO_RECEIVE;

    case RETURN_TO_SENDER;
    case RESCHEDULING_FOR_A_NEW_INVOICE;

    case DELIVERED;

    public static function matchEvent(string $eventTitle): ShipmentEvent
    {
        return match (trim($eventTitle)){
            'Ввод накладной клиентом' => self::INVOICE_INPUT_BY_CUSTOMER,
            'Электронный ввод' => self::DIGITAL_INVOICE_INPUT_BY_CUSTOMER,

            'Груз забран у отправителя' => self::TAKEN_FROM_SENDER,

            'Груз принят агентом' => self::ACCEPTED_BY_AGENT,
            'Груз принят на центральный склад' => self::ACCEPTED_AT_THE_CENTRAL_WAREHOUSE,
            'Автоматическое измерение груза' => self::AUTOMATIC_CARGO_MEASUREMENT,
            'Груз отсканирован на убытие' => self::SCANNED_FOR_DEPARTURE,
            'Груз манифестирован' => self::MANIFESTED,
            'Груз перенесен в другой манифест' => self::MOVED_TO_ANOTHER_MANIFEST,
            'Груз сдан поставщику' => self::HANDED_OVER_TO_THE_SUPPLIER,
            'Бронирование груза' => self::BOOKING,
            'Груз на складе' => self::IN_THE_WAREHOUSE,

            'Груз отправлен' => self::SENT,

            'Груз получен от поставщика' => self::RECEIVED_FROM_SUPPLIER,
            'До востребования' => self::RESTANTE,
            'Передано на доставку курьеру' => self::HANDED_OVER_TO_COURIER,
            'Транзит по стране' => self::TRANSIT_ACROSS_THE_COUNTRY,
            'Транзит по области' => self::TRANSIT_ACROSS_THE_REGION,
            'Доставка перенесена получателем' => self::DELIVERY_POSTPONED_BY_RECIPIENT,
            'Трейс открыт' => self::TRACE_OPENED,
            'Трейс закрыт' => self::TRACE_CLOSED,
            'Переадресация' => self::FORWARDING,
            'Согласование дополнительных расходов' => self::COORDINATION_OF_ADDITIONAL_COSTS,
            'Праздничные дни' => self::HOLIDAYS,
            'Проблемы перевозчика' => self::CARRIER_PROBLEMS,
            'Уточнение данных для доставки' => self::REFINEMENT_OF_DATA_TO_DELIVERY,

            'Отсутствие получателя' => self::NO_RECIPIENT,
            'Неправильный адрес' => self::WRONG_ADDRESS,
            'Отказ от получения' => self::REFUSAL_TO_RECEIVE,

            'Возврат отправителю' => self::RETURN_TO_SENDER,
            'Переоформление на новую накладную' => self::RESCHEDULING_FOR_A_NEW_INVOICE,

            'Груз доставлен получателю' => self::DELIVERED,

            default => self::UNKNOWN,
        };
    }

    public function isFinal(): bool
    {
        return match($this) {
            self::DELIVERED, self::RESCHEDULING_FOR_A_NEW_INVOICE => true,
            default => false,
        };
    }

    /**
     * @return EventGroup
     * @throws \UnhandledMatchError
     */
    public function getEventGroup(): EventGroup
    {
        return match($this) {
            self::UNKNOWN => EventGroup::UNKNOWN,
            //
            self::INVOICE_INPUT_BY_CUSTOMER,
            self::DIGITAL_INVOICE_INPUT_BY_CUSTOMER => EventGroup::IS_NOT_SENT,
            //
            self::TAKEN_FROM_SENDER                 => EventGroup::TAKEN_FROM_SENDER,
            //
            self::ACCEPTED_BY_AGENT,
            self::ACCEPTED_AT_THE_CENTRAL_WAREHOUSE,
            self::SCANNED_FOR_DEPARTURE,
            self::MANIFESTED,
            self::AUTOMATIC_CARGO_MEASUREMENT,
            self::MOVED_TO_ANOTHER_MANIFEST,
            self::HANDED_OVER_TO_THE_SUPPLIER,
            self::BOOKING,
            self::IN_THE_WAREHOUSE                  => EventGroup::AT_AN_INTERMEDIATE_POINT,
            //
            self::SENT                              => EventGroup::SHIPPED_FROM_AN_INTERMEDIATE_POINT,
            //
            self::RECEIVED_FROM_SUPPLIER,
            self::RESTANTE,
            self::HANDED_OVER_TO_COURIER,
            self::TRANSIT_ACROSS_THE_COUNTRY,
            self::TRANSIT_ACROSS_THE_REGION,
            self::DELIVERY_POSTPONED_BY_RECIPIENT,
            self::TRACE_OPENED,
            self::TRACE_CLOSED,
            self::FORWARDING,
            self::COORDINATION_OF_ADDITIONAL_COSTS,
            self::HOLIDAYS,
            self::CARRIER_PROBLEMS,
            self::REFINEMENT_OF_DATA_TO_DELIVERY        => EventGroup::IN_TRANSIT,
            //
            self::NO_RECIPIENT,
            self::WRONG_ADDRESS,
            self::REFUSAL_TO_RECEIVE                    => EventGroup::DELIVERY_ERROR,
            //
            self::RETURN_TO_SENDER,
            self::RESCHEDULING_FOR_A_NEW_INVOICE        => EventGroup::DELIVERY_CANCELLATION,
            //
            self::DELIVERED                             => EventGroup::DELIVERED,
        };
    }
}
