<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    /**
     * @var string $table | Ассоциация с таблицей в базе данных
     */
    protected $table = 'invoice_payment';

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

//    /**
//     * @var string[] $validatedArray | Массив необходимых полей для сохранения в базе данных
//     */
//    protected array $validated_array = [
//        'paid_amount' => 'amount',
//        'paid_percent' => 'percent',
//        'paid_date' => 'payment_date',
//    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'paid_amount',
        'paid_percent',
        'last_payment_date',
    ];
}
