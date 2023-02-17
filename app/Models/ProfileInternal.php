<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Model;

class ProfileInternal extends Model
{
    use MapTrait;

    protected $fillable = ['profile_id', 'internal_id', 'internal_code'];

    /**
     * Ассоциация с таблицей в базе данных
     *
     * @var string
     */
    protected $table = "profile_internal";

    /**
     * Первичный ключ модели profile_internal
     *
     * @var string
     */
    protected $primaryKey = "internal_id";

    /**
     * Автоинкримент модели profile_internal
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
