<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use MapTrait;

    protected $fillable = [
        'order_id', 'filename', 'extension', 'section', 'updated_at'
    ];
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

    protected $validated_array = [
        'order_id' => 'order_id',
        'filename' => 'filename',
        'file_name' => 'filename',
    ];
}
