<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Currency;
use App\Models\Tax;
use App\Models\Commission;
use App\Models\Country;

class SettingsController extends Controller
{
    public function getData(Request $request)
    {
        $settings = Settings::first();
        $settings->active_services = explode(',',$settings->active_services);

        $currency = Currency::where('statut', '=', 'yes')->first();
        $settings->currency = $currency->symbole;
        // ISO code (currency.libelle, e.g. "USD") — gateways need this, not the symbol.
        $settings->currency_code = $currency->libelle;
        $settings->decimal_digit = $currency->decimal_digit;
        $settings->symbol_at_right = $currency->symbol_at_right;
    
        // default_country_code stores the ISO code ("IN"); the dial code lives
        // on the country row, and clients need it to show a full phone number.
        $country = $settings->default_country_code
            ? Country::where('code', $settings->default_country_code)->first()
            : null;
        $settings->default_country_phonecode = $country ? '+' . ltrim($country->phone, '+') : null;

        $taxes = Tax::where('statut', '=', 'yes')->get();
        $settings->tax = $taxes;

        $admin_commission = Commission::first();
        $settings->admin_commission = $admin_commission;

        // Web panel branding. The columns hold bare filenames, so hand the web
        // client absolute urls, and null out any with no file behind them.
        foreach (['web_header_logo', 'web_footer_logo', 'web_favicon'] as $logo) {
            $settings->{$logo} = (!empty($settings->{$logo}) && file_exists(public_path('assets/images/' . $settings->{$logo})))
                ? asset('assets/images/' . $settings->{$logo})
                : null;
        }

        // These are array-cast on the model, but be explicit so a legacy row
        // still holding a raw JSON string doesn't reach the client as one.
        foreach (['web_social', 'web_cookie_consent'] as $blob) {
            $value = $settings->{$blob};
            $settings->{$blob} = is_string($value) ? (json_decode($value, true) ?: []) : ($value ?: []);
        }

        $response['success'] = 'success';
        $response['error'] = null;
        $response['message'] = 'Settings successfully found';
        $response['data'] = $settings->toArray();

        return response()->json($response);
    }

    /**
     * Firebase server credentials for trusted panels (cabme-web).
     *
     * The service-account JSON is uploaded in Settings -> Notifications and
     * stored in settings.serviceJsonData; senderId sits alongside it. This is
     * SERVER material (it can sign FCM sends), so it lives on its own
     * endpoint rather than inside the widely-fetched v1/settings payload.
     */
    public function firebaseCredentials(Request $request)
    {
        $settings = Settings::first();

        $response["success"] = "success";
        $response["error"] = null;
        $response["message"] = "Firebase credentials";
        $response["data"] = [
            "sender_id" => $settings->senderId,
            "service_json_data" => $settings->serviceJsonData,
        ];

        return response()->json($response);
    }
}