<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePaymentItem extends Model
{
    use MapTrait;

    /**
     * @var string $table | Ассоциация с таблицей в базе данных
     */
    protected $table = 'invoice_payment_item';

    /**
     * @var string $primaryKey | Табличный атрибут для первичного ключа
     */
    protected $primaryKey = 'id';

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

    /**
     * @var string[] $validatedArray | Массив необходимых полей для сохранения в базе данных
     */
    protected $validated_array = [
        'paid_amount' => 'amount',
        'paid_percent' => 'percent',
        'paid_date' => 'payment_date',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
      'order_id', 'amount', 'percent', 'payment_date'
    ];
}
