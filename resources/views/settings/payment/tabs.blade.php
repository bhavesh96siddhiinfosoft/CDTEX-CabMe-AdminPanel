{{-- Payment gateway tab strip. It was copy-pasted into all twelve gateway
     views; kept here once so they cannot drift, with the active tab derived
     from the current url rather than hardcoded per page.

     The "Active" badge is resolved here too, from one query, rather than by each
     gateway page shipping a hidden checkbox per gateway and a block of jQuery to
     read them — that arrangement meant adding a tab required editing every other
     view. The legacy *_active_label classes stay on the links so the existing
     per-page scripts keep finding their target and simply re-set what is already
     rendered. --}}
@php
    // [url slug, title lang key, legacy label class, id_payment_method]
    $ec_gateways = [
        ['stripe',      'app_setting_stripe',      'stripe_active_label',      10],
        ['cod',         'app_setting_cod_short',   'cod_active_label',          5],
        ['razorpay',    'app_setting_razorpay',    'razorpay_active_label',    13],
        ['paypal',      'app_setting_paypal',      'paypal_active_label',      15],
        ['wallet',      'app_setting_wallet',      'wallet_active_label',       9],
        ['payfast',     'app_setting_payfast',     'PayFast_active_label',      7],
        ['paystack',    'app_setting_paystack',    'PayStack_active_label',    11],
        ['flutterwave', 'app_setting_flutterwave', 'FlutterWave_active_label', 12],
        ['mercadopago', 'mercadopago',             'mercadopago_active_label', 16],
        ['xendit',      'xendit',                  'xendit_active_label',      20],
        ['orangepay',   'orangepay',               'orangepay_active_label',   19],
        ['midtrans',    'midtrans',                'midtrans_active_label',    18],
        ['cashfree',    'app_setting_cashfree',    'cashfree_active_label',    21],
        ['foloosi',     'app_setting_foloosi',     'foloosi_active_label',     22],
        ['instamojo',   'app_setting_instamojo',   'instamojo_active_label',   23],
        ['mtnmomo',     'app_setting_mtnmomo',     'mtnmomo_active_label',     24],
        ['paymongo',    'app_setting_paymongo',    'paymongo_active_label',    25],
        ['phonepe',     'app_setting_phonepe',     'phonepe_active_label',     26],
    ];

    $ec_enabled = \App\Models\PaymentSettings::where('isEnabled', 'true')
        ->pluck('id_payment_method')
        ->map(fn ($id) => (string) $id)
        ->all();
@endphp
<div class="payment-top-tab mb-4">
    <ul class="nav nav-tabs ec-lang-tabs align-items-end">
        @foreach ($ec_gateways as [$ec_slug, $ec_key, $ec_label_class, $ec_method_id])
            @php $ec_is_on = in_array((string) $ec_method_id, $ec_enabled, true); @endphp
            <li class="nav-item">
                <a class="nav-link {{ request()->is('settings/payment/' . $ec_slug) ? 'active' : '' }} {{ $ec_label_class }}"
                   href="{!! url('settings/payment/' . $ec_slug) !!}">
                    <i class="mdi mdi-credit-card-outline mr-2"></i>{{ trans('lang.' . $ec_key) }}
                    <span class="badge ml-2 {{ $ec_is_on ? 'badge-success' : '' }}">{{ $ec_is_on ? trans('lang.active') : '' }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
