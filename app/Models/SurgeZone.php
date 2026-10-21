<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurgeZone extends Model
{
    protected $table = 'surge_zones';

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius_km',
        'active_requests',
        'online_drivers',
        'surge_multiplier',
        'manual_override',
        'order_ids',
        'publish',
    ];

    protected $casts = [
        'order_ids' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_km' => 'float',
        'surge_multiplier' => 'float',
        'manual_override' => 'boolean',
    ];
}
