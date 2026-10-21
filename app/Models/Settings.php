<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Settings extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'settings';
    
    public $timestamps = false;
    
    protected $fillable = [
        'title',
        'footer',
        'email',
        'creer',
        'modifier',
        'delivery_distance',
        'minimum_deposit_amount',
        'minimum_withdrawal_amount',
        'referral_amount',
        'subscription_model',
        'commission_model',
        'active_services',
        'driver_doc_verification',
        'owner_doc_verification',
        'serviceJsonData',
        'website_color',
        'web_panel_url',
        'web_footer_description',
        'web_map_iframe',
        'web_header_logo',
        'web_footer_logo',
        'web_favicon',
        'web_social',
        'web_cookie_consent',
    ];

    protected $casts = [
        'id'=>'string',
        'pusher_settings' => 'array',
        'web_social' => 'array',
        'web_cookie_consent' => 'array',
    ];
}
