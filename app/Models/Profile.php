<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['email', 'phone', 'password', 'remember_token', 'status'];

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
