<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParcelDeliveryCharges extends Model
{
    protected $table = 'parcel_delivery_charges';

    protected $fillable = [
        'zone_id',
        'delivery_charge_parcel',
        'parcel_per_weight_charge',
    ];

    protected $casts = [
        'zone_id' => 'integer',
        'delivery_charge_parcel' => 'float',
        'parcel_per_weight_charge' => 'float',
    ];
}
