<?php

namespace App\Http\Controllers;

use App\Models\PaymentSettings;
use App\Models\Settings;
use App\Models\Currency;
use App\Models\Driver;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Validator;

class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * The settings columns each section owns, keyed by the section slug used in
     * the url. Everything about the split pages is driven from this map: what a
     * page may write, which rules apply to it, and the checkbox groups that need
     * presence rather than value.
     */
    public const GENERAL_SECTIONS = [
        // Each surface owns its own colour: `website_color` is the customer
        // APP's, `web_panel_color` the customer WEB panel's. They shared one
        // column before, so editing either moved both — and branding could not
        // write it at all, which is why "Customer App Color" saved nothing.
        'branding'      => ['title', 'footer', 'website_color', 'driverapp_color', 'adminpanel_color', 'adminpanel_sec_color', 'app_logo', 'app_logo_small', 'app_logo_favicon'],
        'web'           => ['web_panel_color', 'web_panel_url', 'web_app_store_link', 'web_play_store_link', 'web_footer_description', 'web_map_iframe', 'web_header_logo', 'web_footer_logo', 'web_favicon', 'web_social', 'web_cookie_consent'],
        'ride'          => ['driver_radios', 'trip_accept_reject_by_driver', 'driver_order_assignment', 'show_ride_otp', 'delivery_distance'],
        'surge'         => ['surge_zone_radius', 'surge_zone_ride_count', 'surge_zone_selection_minutes', 'surge_zone_duration_minutes', 'surge_zone_surge_percentage'],
        'wallet'        => ['minimum_deposit_amount', 'minimum_withdrawal_amount'],
        'referral'      => ['referral_amount'],
        'services'      => ['active_services'],
        'documents'     => ['driver_doc_verification', 'owner_doc_verification'],
        'map'           => ['map_key', 'map_for_app', 'map_type', 'driver_location_update'],
        'contact'       => ['contact_us_email', 'contact_us_phone', 'contact_us_address', 'country'],
        'notifications' => ['senderId', 'serviceJson'],
        'version'       => ['app_version', 'web_version'],
    ];

    /** Validation for a single field, applied only when its section is saved. */
    private const GENERAL_RULES = [
        'title' => 'required',
        'footer' => 'required',
        'driver_radios' => 'required',
        'map_key' => 'required',
        'referral_amount' => 'required',
        'minimum_deposit_amount' => 'required',
        'minimum_withdrawal_amount' => 'required',
        'driver_location_update' => 'required',
        'map_type' => 'required',
        'senderId' => 'required',
        'map_for_app' => 'required',
        'active_services' => 'required|array|min:1',
        'web_panel_url' => 'nullable|url',
        'web_app_store_link' => 'nullable|url',
        'web_play_store_link' => 'nullable|url',
        'web_header_logo' => 'nullable|image',
        'web_footer_logo' => 'nullable|image',
        'web_favicon' => 'nullable|image',
    ];

    /** One section of the old general settings page. */
    public function generalSection($section)
    {
        if (!isset(self::GENERAL_SECTIONS[$section])) {
            abort(404);
        }

        $settings = Settings::first();
        $currency = Currency::where('statut', 'yes')->first();
        $active_services = $settings->active_services ? explode(',', $settings->active_services) : [];
        $countries = Country::where('statut', 'yes')->select('id', 'code', 'libelle', 'phone', 'statut')->get();

        return view('settings.' . $section, compact('settings', 'currency', 'active_services', 'countries'))
            ->with('section', $section);
    }


    /**
     * Save one section. Only the columns that section owns are touched, so the
     * rest of the settings row is left exactly as it was.
     */
    private function updateGeneralSection(Request $request, $id, $section)
    {
        $fields = self::GENERAL_SECTIONS[$section];
        $rules = array_intersect_key(self::GENERAL_RULES, array_flip($fields));

        // the credentials file is only required while none is stored
        if (in_array('serviceJson', $fields, true) && !Storage::disk('local')->has('firebase/credentials.json')) {
            $rules['serviceJson'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules, [
            'active_services.required' => trans('lang.you_must_select_at_least_one_service_type'),
            'serviceJson.required' => trans('lang.the_credentials_file_field_is_required'),
        ]);

        if ($validator->fails()) {
            return redirect()->route('general-settings.section', $section)->withErrors($validator)->withInput();
        }

        $settings = Settings::find($id);
        if (!$settings) {
            return redirect()->route('settings')->with('error', trans('lang.no_result'));
        }

        // request field => settings column, for the plain text/select inputs
        $columns = [
            'title' => 'title',
            'footer' => 'footer',
            'website_color' => 'website_color',
            'web_panel_color' => 'web_panel_color',
            'driverapp_color' => 'driverapp_color',
            'adminpanel_color' => 'adminpanel_color',
            'adminpanel_sec_color' => 'adminpanel_sec_color',
            'driver_radios' => 'driver_radios',
            'trip_accept_reject_by_driver' => 'trip_accept_reject_driver_time_sec',
            'driver_order_assignment' => 'driver_order_assignment',
            'show_ride_otp' => 'show_ride_otp',
            'delivery_distance' => 'delivery_distance',
            'surge_zone_radius' => 'surge_zone_radius',
            'surge_zone_ride_count' => 'surge_zone_ride_count',
            'surge_zone_selection_minutes' => 'surge_zone_selection_minutes',
            'surge_zone_duration_minutes' => 'surge_zone_duration_minutes',
            'surge_zone_surge_percentage' => 'surge_zone_surge_percentage',
            'minimum_deposit_amount' => 'minimum_deposit_amount',
            'minimum_withdrawal_amount' => 'minimum_withdrawal_amount',
            'referral_amount' => 'referral_amount',
            'map_key' => 'google_map_api_key',
            'map_for_app' => 'map_for_application',
            'map_type' => 'mapType',
            'driver_location_update' => 'driverLocationUpdate',
            'contact_us_email' => 'contact_us_email',
            'contact_us_phone' => 'contact_us_phone',
            'contact_us_address' => 'contact_us_address',
            'country' => 'default_country_code',
            'senderId' => 'senderId',
            'app_version' => 'app_version',
            'web_version' => 'web_version',
            'web_panel_url' => 'web_panel_url',
            'web_app_store_link' => 'web_app_store_link',
            'web_play_store_link' => 'web_play_store_link',
            'web_footer_description' => 'web_footer_description',
            'web_map_iframe' => 'web_map_iframe',
        ];

        foreach ($fields as $field) {
            if (isset($columns[$field]) && $request->has($field)) {
                $settings->{$columns[$field]} = $request->input($field);
            }
        }

        if (in_array('active_services', $fields, true)) {
            $settings->active_services = implode(',', $request->get('active_services', []));
        }

        // A cleared checkbox is simply absent from the request, so these are read
        // by presence and only within the section that owns them.
        if (in_array('driver_doc_verification', $fields, true)) {
            $driver_doc_verification = $request->has('driver_doc_verification') ? 'yes' : 'no';
            if ($driver_doc_verification == 'yes' && $settings->driver_doc_verification == 'no') {
                Driver::where('role', 'driver')->where('isOwner', 'false')->where('ownerId', null)
                    ->update(['is_verified' => 0, 'online' => 'no']);
            }
            $settings->driver_doc_verification = $driver_doc_verification;

            $owner_doc_verification = $request->has('owner_doc_verification') ? 'yes' : 'no';
            if ($owner_doc_verification == 'yes' && $settings->owner_doc_verification == 'no') {
                Driver::where('role', 'owner')->where('isOwner', 'true')
                    ->update(['is_verified' => 0, 'online' => 'no']);
            }
            $settings->owner_doc_verification = $owner_doc_verification;
        }

        // The six social urls and the cookie-consent block are stored as JSON so
        // the settings row doesn't gain ten more flat columns.
        if (in_array('web_social', $fields, true)) {
            $social = [];
            foreach (['facebook', 'instagram', 'x', 'linkedin', 'pinterest', 'youtube'] as $network) {
                $social[$network] = trim((string) $request->input('social_' . $network, ''));
            }
            $settings->web_social = $social;
        }

        if (in_array('web_cookie_consent', $fields, true)) {
            $settings->web_cookie_consent = [
                // An unchecked box is absent from the request, so read by presence.
                'enabled' => $request->has('cookie_consent_enabled'),
                'buttonText' => trim((string) $request->input('cookie_button_text', '')),
                'message' => trim((string) $request->input('cookie_message', '')),
                'policyUrl' => trim((string) $request->input('cookie_policy_url', '')),
            ];
        }

        foreach ([
            'app_logo' => 'app_logo.png',
            'app_logo_small' => 'app_logo_small.png',
            'app_logo_favicon' => 'app_logo_favicon.png',
            'web_header_logo' => 'web_header_logo.png',
            'web_footer_logo' => 'web_footer_logo.png',
            'web_favicon' => 'web_favicon.png',
        ] as $input => $filename) {
            if (!in_array($input, $fields, true) || !$request->hasfile($input)) {
                continue;
            }
            $destination = public_path('assets/images/' . $filename);
            if (File::exists($destination)) {
                File::delete($destination);
            }
            $request->file($input)->move(public_path('assets/images/'), $filename);
            $settings->{$input} = $filename;
            $dispatcherColumn = $input . '_dispatcher';
            if (Schema::hasColumn($settings->getTable(), $dispatcherColumn)) {
                $settings->{$dispatcherColumn} = asset('assets/images/' . $filename);
            }
        }

        if (in_array('serviceJson', $fields, true) && $request->hasfile('serviceJson')) {
            $destination = storage_path('app/firebase/credentials.json');
            if (File::exists($destination)) {
                File::delete($destination);
            }
            $file = $request->file('serviceJson');
            $jsonContent = file_get_contents($file->getRealPath());
            $file->move(storage_path('app/firebase/'), 'credentials.json');
            $settings->serviceJson = asset('storage/app/firebase/credentials.json');
            $settings->serviceJsonData = $jsonContent;
        }

        $settings->modifier = date('Y-m-d H:i:s');
        $settings->save();

        return redirect()->back()->with('message', trans('lang.settings_have_been_saved_successfully'));
    }
    public function updateGeneral(Request $request, $id)
    {
        // Every settings screen posts one section, naming it in _section, and
        // only the columns that section owns are written.
        $section = $request->input('_section');
        if (! isset(self::GENERAL_SECTIONS[$section])) {
            abort(404);
        }

        return $this->updateGeneralSection($request, $id, $section);
    }
   
    public function cod()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();

        return view('settings.payment.cod')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('xendit', $xendit)->with('midtrans', $midtrans)
            ->with('orangepay', $orangepay);
    }

    public function codUpdate(Request $request, $id)
    {
        $isEnabled = $request->isEnabled;

        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);

        if ($settings) {
            $settings->isEnabled = $isEnabled;
            $settings->modifier = $modifier;

        }
        $settings->save();
    }

    public function applePay()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();

        return view('settings.payment.applepay')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('xendit', $xendit)->with('midtrans', $midtrans)
            ->with('orangepay', $orangepay);
    }
    public function applepayUpdate(Request $request,$id)  {
        $isEnabled = $request->isEnabled;
        $merchantId = $request->merchantId;
        $secretKey = $request->secretKey;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);

        if ($settings) {
            $settings->isEnabled = $isEnabled;
            $settings->merchant_Id = $merchantId;
            $settings->secret_key = $secretKey;
            $settings->modifier = $modifier;

        }
        $settings->save();

    }

    public function stripe()
    {

        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();

        return view('settings.payment.stripe')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('xendit', $xendit)->with('midtrans', $midtrans)
            ->with('orangepay', $orangepay);
    }

    public function stripeUpdate(Request $request, $id)
    {
        $isEnabled = $request->isEnabled;
        $stripekey = $request->stripekey;
        $stripesecret = $request->stripeSecret;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);

        if ($settings) {
            $settings->isEnabled = $isEnabled;
            $settings->key = $stripekey;
            $settings->secret_key = $stripesecret;
            $settings->modifier = $modifier;

        }
        $settings->save();
    }

    public function razorpay()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();

        return view('settings.payment.razorpay')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('xendit', $xendit)->with('midtrans', $midtrans)
            ->with('orangepay', $orangepay);
    }

    public function razorpayUpdate(Request $request, $id)
    {
        $isRazorpayenabled = $request->isRazorpayenabled;
        $razorpayKey = $request->razorpayKey;
        $razorpaySecret = $request->razorpaySecret;
        $sendboxmode = $request->sendboxmode;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);

        if ($settings) {
            $settings->isEnabled = $isRazorpayenabled;
            $settings->key = $razorpayKey;
            $settings->secret_key = $razorpaySecret;
            $settings->isSandboxEnabled = $sendboxmode;
            $settings->modifier = $modifier;

        }
        $settings->save();
    }


   


    public function paypal()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();

        return view('settings.payment.paypal')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('xendit', $xendit)->with('midtrans', $midtrans)
            ->with('orangepay', $orangepay);
    }

    public function paypalUpdate(Request $request, $id)
    {
        $isEnabled = $request->isEnabled;
        $isLive = $request->isLive;
        $app_id = $request->app_id;
        $secret_key = $request->secret_key;
        $username = $request->username;
        $password = $request->password;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);
        if ($settings) {
            $settings->isEnabled = $isEnabled;
            $settings->isLive = $isLive;
            $settings->app_id = $app_id;
            $settings->secret_key = $secret_key;
            $settings->username = $username;
            $settings->password = $password;
            $settings->modifier = $modifier;

        }
        $settings->save();
    }

    public function payfast()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();

        return view('settings.payment.payfast')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('xendit', $xendit)->with('midtrans', $midtrans)
            ->with('orangepay', $orangepay);
    }

    public function payfastUpdate(Request $request, $id)
    {
        $merchant_Id = $request->merchant_Id;
        $merchant_key = $request->merchant_key;
        $cancel_url = $request->cancel_url;
        $notify_url = $request->notify_url;
        $return_url = $request->return_url;
        $isEnabled = $request->isEnabled;
        $isSandboxEnabled = $request->isSandboxEnabled;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);
        
        if ($settings) {
            $settings->merchant_Id = $merchant_Id;
            $settings->merchant_key = $merchant_key;
            $settings->cancel_url = $cancel_url;
            $settings->notify_url = $notify_url;
            $settings->return_url = $return_url;
            $settings->isEnabled = $isEnabled;
            $settings->isSandboxEnabled = $isSandboxEnabled;
            $settings->modifier = $modifier;

        }
        $settings->save();

    }

    public function paystack()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();

        return view('settings.payment.paystack')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('xendit', $xendit)->with('midtrans', $midtrans)
            ->with('orangepay', $orangepay);
    }

    public function paystackUpdate(Request $request, $id)
    {
        $secret_key = $request->secret_key;
        $public_key = $request->public_key;
        $callback_url = $request->callback_url;
        $webhook_url = $request->webhook_url;
        $isEnabled = $request->isEnabled;
        $isSandboxEnabled = $request->isSandboxEnabled;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);
        
        if ($settings) {
            $settings->secret_key = $secret_key;
            $settings->public_key = $public_key;
            $settings->callback_url = $callback_url;
            $settings->webhook_url = $webhook_url;
            $settings->isEnabled = $isEnabled;
            $settings->isSandboxEnabled = $isSandboxEnabled;
            $settings->modifier = $modifier;

        }
        $settings->save();

    }

    public function flutterwave()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();

        return view('settings.payment.flutterwave')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('xendit', $xendit)->with('midtrans', $midtrans)
            ->with('orangepay', $orangepay);
    }

    public function flutterUpdate(Request $request, $id)
    {
        $secret_key = $request->secret_key;
        $public_key = $request->public_key;
        $encryption_key = $request->encryption_key;
        $isEnabled = $request->isEnabled;
        $isSandboxEnabled = $request->issandboxEnabled;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);
        if ($settings) {
            $settings->secret_key = $secret_key;
            $settings->public_key = $public_key;
            $settings->encryption_key = $encryption_key;
            $settings->isEnabled = $isEnabled;
            $settings->isSandboxEnabled = $isSandboxEnabled;
            $settings->modifier = $modifier;

        }
        $settings->save();
    }

    public function wallet()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();

        return view('settings.payment.wallet')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('xendit', $xendit)->with('midtrans', $midtrans)
            ->with('orangepay', $orangepay);
    }

    public function walletUpdate(Request $request, $id)
    {
        $isEnabled = $request->isEnabled;

        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);

        if ($settings) {
            $settings->isEnabled = $isEnabled;
            $settings->modifier = $modifier;

        }
        $settings->save();
    }

    public function mercadopago()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();

        return view('settings.payment.mercadopago')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('xendit', $xendit)->with('midtrans', $midtrans)
            ->with('orangepay', $orangepay);
    }

    public function mercadopagoUpdate(Request $request, $id)
    {
        $mercadopagoKey = $request->mercadopagoKey;
        $mercadopago_accesstoken = $request->mercadopago_accesstoken;
        $ismercadopagoEnabled = $request->ismercadopagoEnabled;
        $isSandboxEnabled = $request->isSandboxEnabled;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);
        if ($settings) {
            $settings->public_key = $mercadopagoKey;
            $settings->accesstoken = $mercadopago_accesstoken;
            $settings->isEnabled = $ismercadopagoEnabled;
            $settings->isSandboxEnabled = $isSandboxEnabled;
            $settings->modifier = $modifier;

        }
        $settings->save();
    }

    public function xendit()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();
        return view('settings.payment.xendit')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('orangepay', $orangepay)->with('xendit', $xendit)
            ->with('midtrans', $midtrans);
    }

    public function xenditUpdate(Request $request, $id)
    {
        $xenditKey = $request->apiKey;
        $isEnabled = $request->isEnabled;
        $isSandboxEnabled = $request->isSandboxEnabled;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);
        if ($settings) {
            $settings->key = $xenditKey;
            $settings->isEnabled = $isEnabled;
            $settings->isSandboxEnabled = $isSandboxEnabled;
            $settings->modifier = $modifier;

        }
        $settings->save();
    }
    public function orangepay()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();
        return view('settings.payment.orangepay')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('orangepay', $orangepay)->with('xendit', $xendit)
            ->with('midtrans', $midtrans);
    }

    public function orangepayUpdate(Request $request, $id)
    {
        $apikey = $request->apiKey;
        $clientId = $request->clientId;
        $secretKey = $request->clientSecret;
        $isEnabled = $request->isEnabled;
        $isSandboxEnabled = $request->isSandboxEnabled;
        $cancelUrl = $request->cancelUrl;
        $merchatKey = $request->merchatKey;
        $notifyUrl = $request->notifyUrl;
        $returnUrl = $request->returnUrl;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);
        if ($settings) {
            $settings->key = $apikey;
            $settings->isEnabled = $isEnabled;
            $settings->isSandboxEnabled = $isSandboxEnabled;
            $settings->clientpublishableKey = $clientId;
            $settings->secret_key = $secretKey;
            $settings->merchant_key = $merchatKey;
            $settings->cancel_url = $cancelUrl;
            $settings->notify_url = $notifyUrl;
            $settings->return_url = $returnUrl;
            $settings->modifier = $modifier;

        }
        $settings->save();
    }
    public function midtrans()
    {
        $stripe = PaymentSettings::where('id_payment_method', 10)->get();
        $razorpay = PaymentSettings::where('id_payment_method', 13)->get();
        $cods = PaymentSettings::where('id_payment_method', 5)->get();
        $paypal = PaymentSettings::where('id_payment_method', 15)->get();
        $payfast = PaymentSettings::where('id_payment_method', 7)->get();
        $paystack = PaymentSettings::where('id_payment_method', 11)->get();
        $flutterwave = PaymentSettings::where('id_payment_method', 12)->get();
        $wallet = PaymentSettings::where('id_payment_method', 9)->get();
        $mercadopago = PaymentSettings::where('id_payment_method', 16)->get();
        $applePay = PaymentSettings::where('id_payment_method', 17)->get();
        $xendit = PaymentSettings::where('id_payment_method', 20)->get();
        $orangepay = PaymentSettings::where('id_payment_method', 19)->get();
        $midtrans = PaymentSettings::where('id_payment_method', 18)->get();
        return view('settings.payment.midtrans')->with('stripe', $stripe)
            ->with('razorpay', $razorpay)->with('cods', $cods)
            ->with('payfast', $payfast)->with('paystack', $paystack)
            ->with('paypal', $paypal)
            ->with('flutterwave', $flutterwave)->with('wallet', $wallet)
            ->with('applePay', $applePay)->with('mercadopago', $mercadopago)
            ->with('orangepay', $orangepay)->with('xendit', $xendit)
            ->with('midtrans', $midtrans);
    }

    /**
     * The gateways added as payment methods 21-26, with the payment_settings
     * columns each one stores its credentials in. The pair of actions below is
     * driven entirely from here, so a page can only ever write the columns its
     * own gateway declares, and adding another gateway is one entry plus a view.
     */
    public const EXTRA_GATEWAYS = [
        'cashfree'  => ['id' => 21, 'fields' => ['clientpublishableKey', 'secret_key']],
        'foloosi'   => ['id' => 22, 'fields' => ['merchant_key']],
        'instamojo' => ['id' => 23, 'fields' => ['clientpublishableKey', 'secret_key']],
        'mtnmomo'   => ['id' => 24, 'fields' => ['key', 'secret_key', 'callback_url', 'target_environment', 'expiry_time_seconds']],
        'paymongo'  => ['id' => 25, 'fields' => ['secret_key']],
        'phonepe'   => ['id' => 26, 'fields' => ['merchant_Id', 'clientpublishableKey', 'secret_key', 'salt_key', 'flow_id']],
    ];

    public function gateway($gateway)
    {
        if (! isset(self::EXTRA_GATEWAYS[$gateway])) {
            abort(404);
        }

        $rows = PaymentSettings::where('id_payment_method', self::EXTRA_GATEWAYS[$gateway]['id'])->get();

        return view('settings.payment.' . $gateway)->with('gateway', $rows);
    }

    public function gatewayUpdate(Request $request, $gateway, $id)
    {
        if (! isset(self::EXTRA_GATEWAYS[$gateway])) {
            abort(404);
        }

        $settings = PaymentSettings::find($id);
        if (! $settings) {
            abort(404);
        }

        // Only this gateway's own columns, so a crafted post cannot reach
        // another gateway's credentials.
        foreach (self::EXTRA_GATEWAYS[$gateway]['fields'] as $column) {
            $settings->{$column} = $request->input($column);
        }
        $settings->isEnabled = $request->isEnabled;
        $settings->isSandboxEnabled = $request->isSandboxEnabled;
        $settings->modifier = date('Y-m-d H:i:s');
        $settings->save();
    }

    public function midtransUpdate(Request $request, $id)
    {
        $serverKey = $request->serverKey;
        $isEnabled = $request->isEnabled;
        $isSandboxEnabled = $request->isSandboxEnabled;
        $modifier = date('Y-m-d H:i:s');

        $settings = PaymentSettings::find($id);
        if ($settings) {
            $settings->key = $serverKey;
            $settings->isEnabled = $isEnabled;
            $settings->isSandboxEnabled = $isSandboxEnabled;
            $settings->modifier = $modifier;

        }
        $settings->save();
    }

    public function maintenance()
    {
        $settings = Settings::first();        
        return view("settings.maintenance")->with("settings", $settings);
    }

    public function updateMaintenance(Request $request, $id)
    {
        $settings = Settings::findOrFail($id);

        $settings->is_customer_maintenance_mode   = $request->has('customer_maintenance_mode') ? 'true' : 'false';
        $settings->is_driver_maintenance_mode     = $request->has('driver_maintenance_mode') ? 'true' : 'false';
        $settings->is_dispatcher_maintenance_mode = $request->has('dispatcher_maintenance_mode') ? 'true' : 'false';

        $settings->save();

        return redirect()->back()->with(
            'message',
            trans('lang.settings_have_been_saved_successfully')
        );
    }



}
