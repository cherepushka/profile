<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'order_id', 'vendor_code', 'internal_id',
        'title', 'category', 'unit',
        'quantity', 'pure_price', 'full_price',
        'VAT_rate', 'VAT_sum', 'final_price'
    ];

    /**
     * @var string $table | Ассоциация с таблицей в базе данных
     */
    protected $table = "invoice_item";

    /**
     * @var string $primaryKey | Табличный атрибут для первичного ключа
     */
    protected $primaryKey = "id";

    /**
     * @var bool $incrementing | Автоинкремент кортежей таблицы
     */
    public $incrementing = true;

    /**
     * @var bool $timestamps | Создание временных точек
     */
    public $timestamps = true;

    /**
     * @var string $connection | Стандартная схема подключения к базе данных
     */
    protected $connection = "mysql";
}
