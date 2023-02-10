<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceShipmentDetailItem extends Model
{
    /**
     * @var string $table | Ассоциация с таблицей в базе данных
     */
    protected $table = 'invoice_shipment_detail_item';

    /**
     * @var string $primaryKey | Табличный атрибут для первичного ключа
     */
    protected $primaryKey = 'id';

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
        'order_id', 'invoice_product_id', 'product_quantity',
    ];
}
