<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Model;

class ProfileInternal extends Model
{
    use MapTrait;
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
    protected $primaryKey = "profile_id";

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

    public function profile()
    {
        $response = $this->hasOne(Profile::class, 'id');

        if (is_null($response->profile_id)) {
            return new Profile;

        } else {
            return $response;
        }
    }
}
