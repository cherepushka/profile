<?php

namespace App\Models;

use App\Http\Traits\MapTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
     * @return BelongsTo
     */
    public function managerRelation(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'responsible_email', 'email');
    }

    public function invoiceShipmentRelation(): HasOne
    {
        return $this->hasOne(InvoiceShipment::class, 'order_id', 'order_id');
    }

    public function invoicePaymentRelation(): HasOne
    {
        return $this->hasOne(InvoicePayment::class, 'order_id', 'order_id');
    }

    public function invoiceItemRelation(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'order_id', 'order_id');
    }

    public function documentRelation(): HasMany
    {
        return $this->hasMany(Document::class, 'order_id', 'order_id');
    }

    /**
     * @var array|string[] $validated_array | Массив для валидации полей
     */
    protected array $validated_array = [
        'order_id' => 'order_id',
        'InvoiceId' => 'invoice_id',
        'client_id' => 'user_id',
        'entity' => 'entity',
        'responsible' => 'responsible_email',
        'pay_link' => 'pay_link',
        'pay_block' => 'pay_block',
        'InvoiceData' => 'custom_field',
        'Invoice_currency' => 'currency',
        'Invoice_price' => 'order_amount',
        'roistat_id' => 'roistat_id',
        'deal_source' => 'deal_source',
        'InvoiceDate' => 'date',
        'trigger' => 'mail_trigger',
    ];

    /**
     * @var array|string[] $superfluous | Массив лишних полей, которые (необходимо удалить/учавствуют в обработке)
     */
    protected array $superfluous = [
        ''
    ];

    protected $fillable = [
        'contract_date',
    ];
}
