<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentTrackInfo extends Model
{
    use HasFactory;

    protected $table = 'shipment_track_info';

    protected $primaryKey = "id";

    protected $fillable = [
        'shipment_id',
        'transport_company',
        'event_title',
        'event_group',
        'event_current_geo',
        'event_date'
    ];
}
