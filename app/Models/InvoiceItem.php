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
     * Ассоциация с таблицей в базе данных
     *
     * @var string
     */
    protected $table = "invoice_item";

    /**
     * Первичный ключ модели invoice
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * Автоинкримент модели invoice
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Автозаполнение created_at, updated_at
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Подключение, которое использует модель
     *
     * @var string
     */
    protected $connection = "mysql";
}
