<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Profile extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    use MapTrait;

    protected $fillable = [
        'email', 'phone', 'password', 'auth_sms_code', 'remember_token',
    ];
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
