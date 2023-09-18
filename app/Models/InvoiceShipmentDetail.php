<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceShipmentDetail extends Model
{
    /**
     * @var string $table | Ассоциация с таблицей в базе данных
     */
    protected $table = 'invoice_shipment_detail';

    /**
     * @var string $primaryKey | Табличный атрибут для первичного ключа
     */
    protected $primaryKey = 'id';

    /**
     * @var bool $incrementing | Автоинкремент кортежей таблицы
     */
    public $incrementing = false;

    /**
     * @var bool $timestamps | Создание временных точек
     */
    public $timestamps = true;

    /**
     * @var string $connection | Стандартная схема подключения к базе данных
     */
    protected $connection = "mysql";

    /**
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'realization_id',
        'realization_number',
        'amount',
        'transport_company',
        'transport_company_id',
        'date',
        'facture_number',
        'facture_date',
        'last_event_group',
        'delivery_date'
    ];

    public function itemRelation(): HasMany
    {
        return $this->hasMany(InvoiceShipmentDetailItem::class, 'order_id', 'order_id');
    }

    public function getItemRelationCountAttribute(): int
    {
        return $this->itemRelation()->count();
    }
}
