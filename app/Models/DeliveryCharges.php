<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryCharges extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    protected $table = 'delivery_charges';
    protected $fillable = [
        'id_vehicle_type',
        'zone_id',
        'delivery_charges_per_km',
        'minimum_delivery_charges',
        'minimum_delivery_charges_within_km',
        'created',
        'modifier',
    ];

 
}
