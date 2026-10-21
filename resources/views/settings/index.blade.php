@extends('layouts.app')
@section('content')
    {{-- Settings hub: one card per settings area rather than a flat menu. Each
         entry names a route that already exists; the grid is driven from this
         array so adding a section is a one-line change. --}}
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">

            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.settings') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.settings_hub_text') }}</p>
                </div>
            </div>

            @php
                // [route name, icon, title key, description key]
                // Reorder this array to reorder the grid — every card is driven from it.
                // The sections split out of the old general settings page. The
                // route takes the section slug, which the controller checks
                // against its own map, so a typo here shows up as a 404 rather
                // than a blank page.
                $ec_general = [
                    ['branding',      'mdi-palette',                   'branding',              'settings_branding_desc'],
                    ['web',           'mdi-web',                       'web_settings',          'settings_web_desc'],
                    ['ride',          'mdi-car',                       'ride_settings',         'settings_ride_desc'],
                    ['map',           'mdi-map-outline',               'map_settings',          'settings_map_desc'],
                    ['surge',         'mdi-trending-up',               'surge_zone_settings',   'settings_surge_desc'],
                    ['wallet',        'mdi-wallet-outline',            'wallet_settings',       'settings_wallet_desc'],
                    ['referral',      'mdi-gift-outline',              'referral_settings',     'settings_referral_desc'],
                    ['services',      'mdi-view-grid-outline',         'service_type_settings', 'settings_services_desc'],
                    ['documents',     'mdi-file-check-outline',        'document_verification', 'settings_verification_desc'],
                    ['contact',       'mdi-card-account-phone-outline','contact_us',            'settings_contact_desc'],
                    ['notifications', 'mdi-bell-outline',              'notification_setting',  'settings_notifications_desc'],
                    ['version',       'mdi-cellphone-arrow-down',      'version',               'settings_version_desc'],
                ];

                $ec_settings = [
                    ['maintenance-settings.edit', 'mdi-wrench-outline',          'maintenance_mode',      'settings_maintenance_desc'],
                    ['commission.edit',           'mdi-sale',                    'admin_commission',      'settings_commission_desc'],
                    ['parcel-charges.index',      'mdi-truck-delivery-outline',  'parcel_charges',        'parcel_charges_desc'],
                    ['terms-condition.edit',      'mdi-file-document-outline',   'terms_and_conditions',  'settings_terms_desc'],
                    ['privacy-policy.edit',       'mdi-shield-lock-outline',     'privacy_policy',        'settings_privacy_desc'],
                ];

                // The general sections carry their slug rather than a route name;
                // resolve them to the shared section route so both lists render
                // through the same loop.
                foreach ($ec_general as [$ec_slug, $ec_icon, $ec_title, $ec_desc]) {
                    $ec_settings[] = [['general-settings.section', $ec_slug], $ec_icon, $ec_title, $ec_desc];
                }

                // Alphabetical by the title shown on the card, so the grid stays
                // ordered no matter what order entries are added to the array.
                usort($ec_settings, fn($a, $b) => strcmp(trans('lang.' . $a[2]), trans('lang.' . $b[2])));
            @endphp

            <div class="row">
                @foreach ($ec_settings as [$ec_route, $ec_icon, $ec_title, $ec_desc])
                    @php
                        $ec_name = is_array($ec_route) ? $ec_route[0] : $ec_route;
                        $ec_href = Route::has($ec_name)
                            ? (is_array($ec_route) ? route($ec_name, $ec_route[1]) : route($ec_name))
                            : null;
                    @endphp
                    @if ($ec_href)
                        <div class="col-xl-4 col-md-6 mb-3">
                            <a href="{{ $ec_href }}" class="settings-card-link">
                                <div class="card ec-card h-100 settings-card">
                                    <div class="card-body d-flex align-items-start">
                                        <span class="settings-card-icon mr-3"><i class="mdi {{ $ec_icon }}"></i></span>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ trans('lang.'.$ec_title) }}</h5>
                                            <p class="mb-0 text-muted small">{{ trans('lang.'.$ec_desc) }}</p>
                                        </div>
                                        <span class="settings-card-arrow ml-2"><i class="mdi mdi-chevron-right"></i></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>
    </div>
@endsection
