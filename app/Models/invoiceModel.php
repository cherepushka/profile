<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoiceModel extends Model
{
    /**
     * Имена в таблице базы данных
     *
     * @const CREATED_AT, UPDATED_AT
     */
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'updated_date';

    /**
     * Ассоциация с таблицей в базе данных
     *
     * @var string
     */
    protected $table = "invoice";

    /**
     * Первичный ключ модели invoice
     *
     * @var string
     */
    protected $primaryKey = "order_id";

    /**
     * Автоинкримент модели invoice
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Тип автоинкримента
     *
     * @var string
     */
    protected $keyType = "int";

    /**
     * Автозаполнение created_at, updated_at
     *
     * @var bool
     */
    public $timestamps = true;
    //use HasFactory;

    /**
     * Подключение, которое использует модель
     *
     * @var string
     */
    protected $connection = "mariadb";
}
