<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use MapTrait;
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

    /**
     * Возвращает модель менеджера
     *
     * @return Manager
     */
    public function managerRelation()
    {
        return $this->belongsTo(Manager::class, 'responsible_email', 'email');
    }

    public function profileInternalRelation()
    {
        return $this->belongsTo(ProfileInternal::class, 'user_id', 'internal_id');
    }

    public function document()
    {
        return new Document;
    }

    /**
     * Массив для валидации полей
     *
     * ToDo: Вынести за пределы модели
     *
     * @var string[]
     */
    protected array $validated_array = [
        'order_id' => 'order_id',
        'InvoiceId' => 'invoice_id',
        'client_id' => 'user_id',
        'ToDo: cloud_api' => 'pay_link',
        'entity' => 'entity',
        'responsible' => 'responsible_email',
        'pay_block' => 'pay_block',
        'InvoiceData' => 'custom_field',
        'InvoiceDate' => 'contract_date',
        'Invoice_currency' => 'currency',
        'Invoice_price' => 'order_amount',
        'contract_date' => 'contract_date',
        'order_amount' => 'order_amount',
    ];
}
