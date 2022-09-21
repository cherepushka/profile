<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    use MapTrait;
    /**
     * Ассоциация с таблицей в базе данных
     *
     * @var string
     */
    protected $table = "manager";

    /**
     * Первичный ключ модели manager
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * Автоинкримент модели manager
     *
     * @var bool
     */
    public $incrementing = false;

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
