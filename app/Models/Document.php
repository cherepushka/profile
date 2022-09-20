<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /**
     * Ассоциация с таблицей в базе данных
     *
     * @var string
     */
    protected $table = "document";

    /**
     * Первичный ключ модели document
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * Автоинкримент модели document
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Тип автоинкримента
     *
     * @var string
     */
    protected $keyType = "integer";

    /**
     * Подключение, которое использует модель
     *
     * @var string
     */
    protected $connection = "mysql";
}
