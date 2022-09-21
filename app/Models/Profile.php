<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use MapTrait;
    /**
     * Ассоциация с таблицей в базе данных
     *
     * @var string
     */
    protected $table = "profile";

    /**
     * Первичный ключ модели profile
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * Автоинкримент модели profile
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
