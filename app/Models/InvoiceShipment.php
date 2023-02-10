<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceShipment extends Model
{
    /**
     * @var string $table | Ассоциация с таблицей в базе данных
     */
    protected $table = 'invoice_shipment';

    /**
     * @var string $primaryKey | Табличный атрибут для первичного ключа
     */
    protected $primaryKey = 'order_id';

    /**
     * @var bool $incrementing | Автоинкремент кортежей таблицы
     */
    public $incrementing = false;

    /**
     * @var bool $timestamps | Создание временных точек
     */
    public $timestamps = true;

    /**
     * @var string $connection | Стандартная схема подключения к базе данных
     */
    protected $connection = "mysql";

    /**
     * @var string[]
     */
    protected $fillable = [
        'order_id', 'currency', 'amount',
    ];
}
