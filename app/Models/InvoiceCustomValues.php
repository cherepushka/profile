<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCustomValues extends Model
{
    use HasFactory;

    protected $table = "invoice_custom_values";

    protected $primaryKey = "order_id";

    public $incrementing = false;

    public $timestamps = false;

    protected $connection = "mysql";

    protected $fillable = [
        'order_id',
        'web_value',
        'web_value-updated_at',
        '1C_value',
        '1C_value-updated_at',
    ];
}
