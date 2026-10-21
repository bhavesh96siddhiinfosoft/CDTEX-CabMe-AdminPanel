@extends('layouts.app')

@section('content')
    <div id="main-wrapper" class="page-wrapper dashboard-page">
        <div class="container-fluid">

            <div class="ec-hero-banner mb-4">
                <div class="ec-hero-text">
                    <h3>{{ trans('lang.welcome_admin_note') }}{{ Auth::user()->name }} 👋</h3>
                    <p>{{ trans('lang.welcome_admin_note2') }}</p>
                </div>
                <div class="ec-hero-actions">
                    @can('rides.index')
                        <a href="{{ route('rides.index') }}" class="ec-hero-btn">
                            <i class="mdi mdi-map-marker-multiple"></i> {{ trans('lang.all_rides') }}
                        </a>
                    @endcan
                    @can('drivers.index')
                        <a href="{{ route('drivers.index') }}" class="ec-hero-btn ec-hero-btn-outline">
                            <i class="mdi mdi-account-card-details"></i> {{ trans('lang.driver_plural') }}
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Period filter. The three control groups are shown and hidden by the
                 viewFilter handler in @section('scripts'); the ids it keys off
                 (viewFilter, monthYearFilters, customDateFilters) are unchanged. --}}
            <div class="card ec-card ec-filter-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-filter-variant"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.filter_by') }}</h5>
                    </div>
                    <div class="ec-filter-actions">
                        <a href="{{ route('home') }}" class="btn ec-btn-ghost">
                            <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('home') }}" id="dashboardFilterForm">
                        {{-- ec-filter-row-compact, not the listing layout: these controls
                             are short and fixed-purpose, so they sit at their own width
                             and Apply stays next to the last one instead of every column
                             dividing the row equally.

                             The *Wrap divs are what updateFilterVisibility() toggles;
                             hiding a bare <select> would leave its label stranded. --}}
                        <div class="row ec-filter-row ec-filter-row-compact align-items-end">
                            <div class="col-auto ec-filter-w-lg mb-2">
                                <label class="ec-filter-label" for="viewFilter">{{ trans('lang.filter_by') }}</label>
                                <select id="viewFilter" name="view" class="form-control">
                                    <option value="year" {{ request('view') == 'year' ? 'selected' : '' }}>{{ trans('lang.view_full_year') }}</option>
                                    <option value="month" {{ request('view') == 'month' ? 'selected' : '' }}>{{ trans('lang.view_by_month') }}</option>
                                    <option value="custom" {{ request('view') == 'custom' ? 'selected' : '' }}>{{ trans('lang.custom_date_range') }}</option>
                                </select>
                            </div>

                            <div class="col-auto ec-filter-w-md mb-2" id="monthFilterWrap" style="display: none;">
                                <label class="ec-filter-label" for="monthFilter">{{ trans('lang.view_by_month') }}</label>
                                <select id="monthFilter" name="month" class="form-control">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-auto ec-filter-w-sm mb-2" id="yearFilterWrap">
                                <label class="ec-filter-label" for="yearFilter">{{ trans('lang.view_full_year') }}</label>
                                <select id="yearFilter" name="year" class="form-control">
                                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-auto ec-filter-w-md mb-2" id="customDateFilters" style="display: none;">
                                <div class="d-flex" style="gap: 10px;">
                                    <div>
                                        <label class="ec-filter-label" for="startDate">{{ trans('lang.from') }}</label>
                                        <input class="form-control" type="date" name="start_date" id="startDate" value="{{ request('start_date') }}">
                                    </div>
                                    <div>
                                        <label class="ec-filter-label" for="endDate">{{ trans('lang.to_small') }}</label>
                                        <input class="form-control" type="date" name="end_date" id="endDate" value="{{ request('end_date') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-auto ec-filter-action mb-2">
                                <button type="submit" class="btn ec-btn-brand">
                                    <i class="mdi mdi-check"></i> {{ trans('lang.apply') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Business analytics. The ids (total_earnings, admin_commission_text,
                 active_users, total_rides) and the hidden total_earning /
                 admin_commission inputs are read by the chart scripts below —
                 each appears exactly once. --}}
            @php
                $ec_money = function ($amount) use ($currency) {
                    $formatted = number_format($amount, $currency->decimal_digit);
                    return $currency->symbol_at_right == 'true'
                        ? $formatted . ' ' . $currency->symbole
                        : $currency->symbole . ' ' . $formatted;
                };
            @endphp
            <div class="row ec-kpi-row">
                <div class="col-xl-3 col-lg-6 mb-3">
                    <div class="ec-kpi kpi-brand">
                        <div class="ec-kpi-head">
                            <span class="ec-kpi-icon"><i class="mdi mdi-cash-multiple"></i></span>
                            <span class="ec-kpi-label">{{ trans('lang.dashboard_total_earnings') }}</span>
                        </div>
                        <div class="ec-kpi-value total_earning" id="total_earnings">{{ $ec_money($total_earnings) }}</div>
                        <input type="hidden" id="total_earning" value="{{ $total_earnings }}">
                        <div class="ec-kpi-trend {{ $earnings_percentage_change > 0 ? 'is-up' : 'is-down' }}">
                            <i class="fa fa-arrow-{{ $earnings_percentage_change > 0 ? 'up' : 'down' }}"></i>
                            {{ $earnings_percentage_change }}% {{ trans('lang.vs_last_month') }}
                        </div>
                        <ul class="ec-kpi-breakdown">
                            <li><span>{{ trans('lang.dashboard_ride_total_earnings') }}</span><strong>{{ $ec_money($total_ride_earnings) }}</strong></li>
                            <li><span>{{ trans('lang.dashboard_parcel_total_earnings') }}</span><strong>{{ $ec_money($total_parcel_earnings) }}</strong></li>
                            <li><span>{{ trans('lang.dashboard_rental_total_earnings') }}</span><strong>{{ $ec_money($total_rental_earnings) }}</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 mb-3">
                    <div class="ec-kpi kpi-purple">
                        <div class="ec-kpi-head">
                            <span class="ec-kpi-icon"><i class="mdi mdi-sale"></i></span>
                            <span class="ec-kpi-label">{{ trans('lang.dashboard_total_admin_commission') }}</span>
                        </div>
                        <div class="ec-kpi-value admin_commission" id="admin_commission_text">{{ $ec_money($total_admin_commission) }}</div>
                        <input type="hidden" id="admin_commission" value="{{ $total_admin_commission }}">
                        <div class="ec-kpi-trend {{ $percentage_change_admin_commission > 0 ? 'is-up' : 'is-down' }}">
                            <i class="fa fa-arrow-{{ $percentage_change_admin_commission > 0 ? 'up' : 'down' }}"></i>
                            {{ round($percentage_change_admin_commission, 2) }}% {{ trans('lang.vs_last_month') }}
                        </div>
                        <ul class="ec-kpi-breakdown">
                            <li><span>{{ trans('lang.dashboard_ride_total_earnings') }}</span><strong>{{ $ec_money($total_ride_commission) }}</strong></li>
                            <li><span>{{ trans('lang.dashboard_parcel_total_earnings') }}</span><strong>{{ $ec_money($total_parcel_admin_commission) }}</strong></li>
                            <li><span>{{ trans('lang.dashboard_rental_total_earnings') }}</span><strong>{{ $ec_money($total_rental_admin_commission) }}</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 mb-3">
                    <div class="ec-kpi kpi-blue">
                        <div class="ec-kpi-head">
                            <span class="ec-kpi-icon"><i class="mdi mdi-account-multiple"></i></span>
                            <span class="ec-kpi-label">{{ trans('lang.dashboard_active_users') }}</span>
                        </div>
                        <div class="ec-kpi-value" id="active_users">{{ ($total_users ?? 0) + ($total_drivers ?? 0) + ($total_owners ?? 0) + ($total_fleet_drivers ?? 0) }}</div>
                        <div class="ec-kpi-trend {{ $total_users_change > 0 ? 'is-up' : 'is-down' }}">
                            <i class="fa fa-arrow-{{ $total_users_change > 0 ? 'up' : 'down' }}"></i>
                            {{ $total_users_change }}% {{ trans('lang.vs_last_month') }}
                        </div>
                        <ul class="ec-kpi-breakdown">
                            <li><span>{{ trans('lang.dashboard_total_users') }}</span><strong>{{ $total_users ?? 0 }}</strong></li>
                            <li><span>{{ trans('lang.dashboard_total_drivers') }}</span><strong>{{ $total_drivers ?? 0 }}</strong></li>
                            <li><span>{{ trans('lang.owner_plural') }}</span><strong>{{ $total_owners ?? 0 }}</strong></li>
                            <li><span>{{ trans('lang.fleet_drivers') }}</span><strong>{{ $total_fleet_drivers ?? 0 }}</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 mb-3">
                    <div class="ec-kpi kpi-green">
                        <div class="ec-kpi-head">
                            <span class="ec-kpi-icon"><i class="mdi mdi-map-marker-multiple"></i></span>
                            <span class="ec-kpi-label">{{ trans('lang.dashboard_totals_trip') }}</span>
                        </div>
                        <div class="ec-kpi-value" id="total_rides">{{ ($total_rides ?? 0) + ($total_parcel ?? 0) + ($total_rental ?? 0) }}</div>
                        <div class="ec-kpi-trend {{ $rides_parcels_rentals_change > 0 ? 'is-up' : 'is-down' }}">
                            <i class="fa fa-arrow-{{ $rides_parcels_rentals_change > 0 ? 'up' : 'down' }}"></i>
                            {{ $rides_parcels_rentals_change }}% {{ trans('lang.vs_last_month') }}
                        </div>
                        <ul class="ec-kpi-breakdown">
                            <li><span>{{ trans('lang.rides') }}</span><strong>{{ $total_rides ?? 0 }}</strong></li>
                            <li><span>{{ trans('lang.parcel') }}</span><strong>{{ $total_parcel ?? 0 }}</strong></li>
                            <li><span>{{ trans('lang.rental') }}</span><strong>{{ $total_rental ?? 0 }}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Charts. The canvas ids (sales-chart, visitors, commissions) are the
                 handles the Chart.js setup in @section('scripts') looks up. --}}
            <div class="row ec-equal-row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card ec-card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ trans('lang.total_sales') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="ec-chart-box ec-chart-box-sm">
                                <canvas id="sales-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- The doughnuts switch Chart.js' own legend off: it can only show
                     labels, and these cards want label + value + share per row, which
                     ecRenderLegend draws into the ec-status-list below each ring. --}}
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card ec-card ec-status-card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ trans('lang.service_overview') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="ec-chart-box ec-chart-box-sm">
                                <canvas id="visitors"></canvas>
                            </div>
                            <ul class="ec-status-list" id="visitors_legend"></ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 mb-3">
                    <div class="card ec-card ec-status-card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ trans('lang.sales_overview') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="ec-chart-box ec-chart-box-sm">
                                <canvas id="commissions"></canvas>
                            </div>
                            <ul class="ec-status-list" id="commissions_legend"></ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ride status + the admin's action queue. Both read counts the
                 controller already gathers, so neither adds a page-load query
                 beyond the four in $attention. --}}
            <div class="row ec-equal-cards">
                <div class="col-xl-7 col-lg-12">
                    <div class="card ec-card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ trans('lang.ride_status_breakdown') }}</h5>
                            @php
                                $ec_status_total = ($new_rides ?? 0) + ($on_rides ?? 0) + ($confirmed_rides ?? 0)
                                    + ($completed_rides ?? 0) + ($canceled_rides ?? 0);
                                $ec_cancel_pct = $ec_status_total > 0
                                    ? round((($canceled_rides ?? 0) / $ec_status_total) * 100)
                                    : 0;
                            @endphp
                            {{-- Surfaced next to the title rather than buried in the list:
                                 it is the number that decides whether anything here is wrong. --}}
                            <span class="ec-pill {{ $ec_cancel_pct >= 30 ? 'ec-pill-danger' : '' }}">
                                {{ $ec_cancel_pct }}% {{ trans('lang.cancellation_rate') }}
                            </span>
                        </div>
                        <div class="card-body">
                            @php
                                $ec_statuses = [
                                    ['label' => trans('lang.new'),       'count' => $new_rides ?? 0,       'color' => '#6366f1'],
                                    ['label' => trans('lang.confirmed'), 'count' => $confirmed_rides ?? 0, 'color' => '#0ea5e9'],
                                    ['label' => trans('lang.on_ride'),   'count' => $on_rides ?? 0,        'color' => '#f59e0b'],
                                    ['label' => trans('lang.completed'), 'count' => $completed_rides ?? 0, 'color' => '#16a34a'],
                                    ['label' => trans('lang.canceled'),  'count' => $canceled_rides ?? 0,  'color' => '#ef4444'],
                                ];
                            @endphp
                            <ul class="ec-status-list mb-0">
                                @foreach ($ec_statuses as $ec_s)
                                    @php
                                        $ec_pct = $ec_status_total > 0 ? round(($ec_s['count'] / $ec_status_total) * 100) : 0;
                                    @endphp
                                    <li>
                                        <span class="dot" style="background: {{ $ec_s['color'] }}"></span>
                                        <span class="ec-status-name">{{ $ec_s['label'] }}</span>
                                        <span class="ec-status-bar">
                                            <span style="width: {{ $ec_pct }}%; background: {{ $ec_s['color'] }}"></span>
                                        </span>
                                        <span class="ec-status-count">{{ $ec_s['count'] }}</span>
                                        <span class="ec-status-pct">{{ $ec_pct }}%</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5 col-lg-12">
                    <div class="card ec-card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ trans('lang.needs_attention') }}</h5>
                            <small class="text-muted">{{ trans('lang.needs_attention_sub') }}</small>
                        </div>
                        <div class="card-body">
                            @php $ec_attention_total = collect($attention ?? [])->sum('count'); @endphp
                            @if ($ec_attention_total === 0)
                                <div class="ec-attention-clear">
                                    <i class="mdi mdi-check-circle-outline"></i>
                                    {{ trans('lang.all_clear') }}
                                </div>
                            @else
                                {{-- <div onclick>, deliberately not <a>: as anchors the browser
                                     adds its own link affordances (drag ghost / selection layer)
                                     which render as a grey wash over the tile and cannot be
                                     overridden with CSS. The KPI tiles above do the same. --}}
                                <div class="ec-attention-grid">
                                    @foreach ($attention ?? [] as $ec_a)
                                        <div class="ec-attention-item tone-{{ $ec_a['tone'] }} {{ $ec_a['count'] > 0 ? 'has-items' : '' }}"
                                             onclick="location.href='{{ $ec_a['url'] }}'">
                                            <span class="ec-attention-icon"><i class="mdi {{ $ec_a['icon'] }}"></i></span>
                                            <span class="ec-attention-count">{{ $ec_a['count'] }}</span>
                                            <span class="ec-attention-label">{{ $ec_a['label'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-12">

                    <div class="card ec-card ec-list-card">

                        <div class="card-header">

                            <div class="d-flex justify-content-between align-items-center">

                                <h5 class="mb-0">{{ trans('lang.latest_rides') }}</h5>

                            </div>

                        </div>

                        <div class="card-body">
                            <div class="table-responsive m-t-10">

                                <table id="example24"
                                    class="display nowrap table table-hover ec-list-table"
                                    cellspacing="0" width="100%">

                                    <thead>

                                        <tr>

                                            <th>{{ trans('lang.ride_id') }}</th>

                                            <th>{{ trans('lang.order_number') }}</th>

                                            <th>{{ trans('lang.userName') }}</th>

                                            <th>{{ trans('lang.driver_name') }}</th>

                                            <th>{{ trans('lang.cost_amount') }}</th>

                                            <th>{{ trans('lang.status') }}</th>

                                            <th>{{ trans('lang.created') }}</th>

                                        </tr>

                                    </thead>

                                    <tbody id="append_list12">

                                        @if (count($rides) > 0)
                                            @foreach ($rides as $ride)
                                                                            <tr>

                                                                                <td><a href="{{ route('ride.show', ['id' => $ride->id]) }}">{{ $ride->id }}</a>

                                                                                <td><a
                                                                                        href="{{ route('ride.show', ['id' => $ride->id]) }}">{{ $ride->booking_number }}</a>

                                                                                </td>

                                                                                <td>

                                                                                    @if ($ride->user_id != null)
                                                                                        <a href="{{ route('users.show', ['id' => $ride->user_id]) }}">{{ $ride->userPrenom }}
                                                                                            {{ $ride->userNom }}

                                                                                        </a>
                                                                                    @else
                                                                                        @php

                                                                                            $userInfo = json_decode($ride->user_info, true);

                                                                                        @endphp

                                                                                        {{ $userInfo ? $userInfo['name'] : '' }}
                                                                                    @endif

                                                                                </td>

                                                                                <td>
                                                                                    @if($ride->driver_id)
                                                                                        <a href="{{ route('driver.show', ['id' => $ride->driver_id]) }}">{{ $ride->driverPrenom }}
                                                                                            {{ $ride->driverNom }}</a>
                                                                                    @endif
                                                                                </td>


                                                                                <?php        $montant = floatval($ride->montant);

                                                $total_price = $montant;

                                                $discount = floatval($ride->discount);

                                                if ($discount) {
                                                    $total_price = $montant - $discount;
                                                }

                                                $tax = json_decode($ride->tax, true);

                                                $totalTaxAmount = 0;

                                                if (!empty($tax)) {
                                                    for ($i = 0; $i < sizeof($tax); $i++) {
                                                        $data = $tax[$i];

                                                        if ($data['type'] == 'Percentage') {
                                                            $taxValue = (floatval($data['value']) * $total_price) / 100;
                                                        } else {
                                                            $taxValue = floatval($data['value']);
                                                        }

                                                        $totalTaxAmount += floatval(number_format($taxValue, $currency->decimal_digit));
                                                    }

                                                    $total_price = floatval($total_price) + $totalTaxAmount;
                                                }

                                                if ($ride->tip_amount) {
                                                    $total_price = floatval($total_price) + floatval($ride->tip_amount);
                                                }

                                                                                                                                                                                                                ?>

                                                                                <td>

                                                                                    @if ($currency->symbol_at_right == 'true')
                                                                                        {{ number_format(floatval($total_price), $currency->decimal_digit) . '' . $currency->symbole }}
                                                                                    @else
                                                                                        {{ $currency->symbole . '' . number_format(floatval($total_price), $currency->decimal_digit) }}
                                                                                    @endif

                                                                                    </span>

                                                                                </td>

                                                                                <td>

                                                                                    @if ($ride->statut == 'completed')
                                                                                        <span class="badge badge-success">{{ $ride->statut }}<span>
                                                                                    @elseif($ride->statut == 'confirmed')
                                                                                                <span class="badge badge-secondary">{{ $ride->statut }}<span>
                                                                                            @elseif($ride->statut == 'new')
                                                                                                        <span class="badge badge-primary">{{ $ride->statut }}<span>
                                                                                                    @elseif($ride->statut == 'rejected')
                                                                                                                <span
                                                                                                                    class="badge badge-danger">{{ $ride->statut }}<span>
                                                                                                            @elseif($ride->statut == 'driver_rejected')
                                                                                                                        <span
                                                                                                                            class="badge badge-danger">{{ trans('lang.driver_rejected') }}<span>
                                                                                                                    @else
                                                                                                                                <span
                                                                                                                                    class="badge badge-warning">{{ $ride->statut }}<span>
                                                                                                                            @endif

                                                                                </td>

                                                                                <td class="dt-time"><span
                                                                                        class="date">{{ date('d F Y', strtotime($ride->creer)) }}</span>

                                                                                    <span class="time">{{ date('h:i A', strtotime($ride->creer)) }}</span>

                                                                                </td>

                                                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" align="center">{{ trans('lang.no_result') }}</td>
                                            </tr>
                                        @endif

                                    </tbody>

                                </table>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-xl-6 col-lg-12">
                    <div class="card ec-card ec-list-card">
                        <div class="card-header">

                            <div class="d-flex justify-content-between align-items-center">

                                <h5 class="mb-0">{{ trans('lang.top_drivers') }}</h5>

                            </div>

                        </div>

                        <div class="card-body">
                            <div class="table-responsive m-t-10">

                                <table id="example24"
                                    class="display nowrap table table-hover ec-list-table"
                                    cellspacing="0" width="100%">

                                    <thead>

                                        <tr>

                                            <th>{{trans('lang.driver_name')}}</th>

                                            <th>{{trans('lang.email')}}</th>

                                            <th>{{trans('lang.user_phone')}}</th>

                                            <th>{{trans('lang.total_ride')}}</th>


                                        </tr>

                                    </thead>

                                    <tbody id="append_list12">

                                        @if(count($topDrivers) > 0)

                                            @foreach($topDrivers as $driver)
                                                <tr>

                                                    <td>
                                                        @if (file_exists(public_path('assets/images/driver' . '/' . $driver->photo_path)) && !empty($driver->photo_path))
                                                            <img class="rounded" style="width:50px"
                                                                src="{{ asset('assets/images/driver') . '/' . $driver->photo_path }}"
                                                                alt="image">
                                                        @else
                                                            <img class="rounded" style="width:50px"
                                                                src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">
                                                        @endif
                                                        <a href="{{ route('driver.show', ['id' => $driver->id]) }}">
                                                            {{ $driver->prenom }} {{ $driver->nom }}
                                                        </a>
                                                        @if($driver->is_verified)
                                                            <i class="mdi mdi-verified verified-icon" title="{{ trans('lang.verified') }}"></i>
                                                        @endif
                                                    </td>
                                                    <td>{{ $driver->email}}</td>
                                                    <td>{{ $driver->phone}}</td>
                                                    <td>{{ $driver->total_rides }}</td>
                                                </tr>

                                            @endforeach

                                        @else

                                            <tr>
                                                <td colspan="4" align="center">{{ trans('lang.no_result') }}</td>
                                            </tr>

                                        @endif

                                    </tbody>

                                </table>

                            </div>
                        </div>

                    </div>
                </div>

                {{-- Right-now supply, so this one ignores the period filter — a
                     driver is either online at this moment or not. --}}
                <div class="col-xl-6 col-lg-12">
                    <div class="card ec-card ec-list-card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ trans('lang.online_drivers') }}</h5>
                            <span class="ec-pill">
                                {{ $online_drivers_count ?? 0 }} / {{ $total_active_drivers ?? 0 }}
                                {{ trans('lang.online_now') }}
                            </span>
                        </div>
                        <div class="card-body">
                            @if (($online_drivers ?? collect())->isEmpty())
                                <div class="ec-attention-clear">
                                    <i class="mdi mdi-car-off"></i>
                                    {{ trans('lang.no_drivers_online') }}
                                </div>
                            @else
                                <div class="table-responsive m-t-10">
                                    <table class="display nowrap table table-hover ec-list-table"
                                           cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('lang.driver_name') }}</th>
                                                <th>{{ trans('lang.user_phone') }}</th>
                                                <th>{{ trans('lang.rating') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($online_drivers as $ec_d)
                                                <tr>
                                                    <td>
                                                        <span class="ec-online-dot" title="{{ trans('lang.online_now') }}"></span>
                                                        <a href="{{ route('driver.show', ['id' => $ec_d->id]) }}">{{ $ec_d->prenom }} {{ $ec_d->nom }}</a>
                                                    </td>
                                                    <td>{{ $ec_d->phone }}</td>
                                                    <td>
                                                        <i class="mdi mdi-star" style="color:#f59e0b"></i>
                                                        {{ number_format((float) $ec_d->average_rating, 1) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- end container -->

    </div>

    <!-- end page-wrapper -->
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script type="text/javascript">
        var currency = '<?php echo $currency->symbole . ' '; ?>';

        var decimal_point = '<?php echo $currency->decimal_digit . ' '; ?>';

        var symbol_at_right = '<?php echo $currency->symbol_at_right; ?>';
        let myChart = null;
        $(document).ready(function () {
            setVisitors();
            setCommision();
            getTotalSales();
        });

        function getTotalSales() {
            const view = viewFilter.value;
            const year = parseInt(yearFilter.value);
            const month = parseInt(monthFilter.value);
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();

            $.ajax({

                url: "home/sales_overview",

                method: "GET",
                data: {
                    view: view,
                    year: year,
                    month: month,
                    start_date: startDate,
                    end_date: endDate
                },

                success: function (response) {
                    var rideData = [];
                    var parcelData = [];
                    var rentalData = [];
                    for (let i = 1; i <= 12; i++) {
                        rideData.push(parseFloat(response.ride['v' + i]));
                        parcelData.push(parseFloat(response.parcel['v' + i]));
                        rentalData.push(parseFloat(response.rental['v' + i]));
                    }

                    var labels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

                    var $salesChart = $('#sales-chart');

                    renderChart($salesChart, rideData, parcelData, rentalData, labels);

                }



            });

        }



        /* ── Chart styling ──────────────────────────────────────────────────
           Shared tokens and helpers, matching the panel's design system. The
           accent comes from --admin-panel-color, so the charts follow whatever
           colour the settings screen is set to. */
        var EC_GRID      = 'rgba(15, 23, 42, .06)';
        var EC_INK       = '#0C111C';
        var EC_INK_MUTED = '#8a90a6';

        function ecBrand() {
            return (getComputedStyle(document.documentElement)
                .getPropertyValue('--admin-panel-color') || '#facc15').trim();
        }

        function ecHexToRgba(hex, alpha) {
            var h = String(hex).replace('#', '').trim();
            if (h.length === 3) h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
            if (!/^[0-9a-f]{6}$/i.test(h)) h = 'facc15';
            return 'rgba(' + parseInt(h.substr(0, 2), 16) + ',' +
                             parseInt(h.substr(2, 2), 16) + ',' +
                             parseInt(h.substr(4, 2), 16) + ',' + alpha + ')';
        }

        function ecMoney(value) {
            var formatted = parseFloat(value || 0).toLocaleString(undefined, {
                minimumFractionDigits: parseInt(decimal_point, 10) || 0,
                maximumFractionDigits: parseInt(decimal_point, 10) || 0
            });
            return (symbol_at_right === 'true') ? formatted + ' ' + currency : currency + formatted;
        }

        // Compact axis labels: 12.5k rather than 12,500.
        function ecMoneyShort(value) {
            var n = parseFloat(value || 0), abs = Math.abs(n), out;
            if (abs >= 1e9)      out = (n / 1e9).toFixed(1).replace(/\.0$/, '') + 'B';
            else if (abs >= 1e6) out = (n / 1e6).toFixed(1).replace(/\.0$/, '') + 'M';
            else if (abs >= 1e3) out = (n / 1e3).toFixed(1).replace(/\.0$/, '') + 'k';
            else                 out = String(Math.round(n));
            return (symbol_at_right === 'true') ? out + currency : currency + out;
        }

        var EC_TOOLTIP = {
            backgroundColor: 'rgba(12, 17, 28, .92)',
            titleColor: '#fff',
            bodyColor: '#e5e7eb',
            borderColor: 'rgba(255,255,255,.08)',
            borderWidth: 1,
            padding: 12,
            cornerRadius: 10,
            displayColors: true,
            boxWidth: 8,
            boxHeight: 8,
            boxPadding: 6,
            usePointStyle: true
        };

        // Chart.js 4 throws if a canvas already hosts a chart, and the sales chart
        // is rebuilt whenever the period filter changes.
        function ecResetCanvas(el) {
            if (!el) return null;
            var existing = Chart.getChart(el);
            if (existing) existing.destroy();
            return el;
        }

        // Writes the total into the middle of a doughnut.
        var ecDoughnutCenter = {
            id: 'ecDoughnutCenter',
            afterDraw: function (chart) {
                var opts = chart.options.plugins.ecDoughnutCenter;
                if (!opts || !opts.display) return;

                var meta = chart.getDatasetMeta(0);
                if (!meta || !meta.data || !meta.data.length) return;

                var ctx = chart.ctx;
                var x = meta.data[0].x;
                var y = meta.data[0].y;

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                ctx.font = '800 22px Urbanist, Nunito, sans-serif';
                ctx.fillStyle = EC_INK;
                ctx.fillText(opts.value || '', x, y - 6);

                ctx.font = '600 12px Urbanist, Nunito, sans-serif';
                ctx.fillStyle = EC_INK_MUTED;
                ctx.fillText(opts.label || '', x, y + 15);

                ctx.restore();
            }
        };
        Chart.register(ecDoughnutCenter);

        // Chart.js has no "space below the legend" option, and layout.padding pads
        // the whole canvas rather than the gap between legend and plot — so the top
        // y-axis tick ends up directly under the legend row. Growing the legend box
        // in its own fit() is the supported way to reserve that space.
        var ecLegendSpacing = {
            id: 'ecLegendSpacing',
            beforeInit: function (chart) {
                var legend = chart.legend;
                if (!legend || legend.__ecSpaced) return;
                legend.__ecSpaced = true;
                var origFit = legend.fit;
                legend.fit = function () {
                    origFit.call(this);
                    this.height += 20;
                };
            }
        };
        Chart.register(ecLegendSpacing);

        function ecDoughnutOptions(centerValue, centerLabel, moneyTooltip) {
            return {
                maintainAspectRatio: false,
                responsive: true,
                cutout: '72%',
                layout: { padding: 4 },
                plugins: {
                    legend: { display: false },
                    tooltip: Object.assign({}, EC_TOOLTIP, {
                        callbacks: {
                            label: function (ctx) {
                                var v = ctx.parsed;
                                return ' ' + ctx.label + ': ' + (moneyTooltip ? ecMoney(v) : v);
                            }
                        }
                    }),
                    ecDoughnutCenter: { display: true, value: centerValue, label: centerLabel }
                }
            };
        }

        // Legend rows under a doughnut: colour dot, label, value, share of total.
        function ecRenderLegend(targetId, labels, values, colors, isMoney) {
            var el = document.getElementById(targetId);
            if (!el) return;

            var total = values.reduce(function (a, b) { return a + b; }, 0);
            el.innerHTML = labels.map(function (label, i) {
                var value = values[i] || 0;
                var pct = total > 0 ? Math.round((value / total) * 100) : 0;
                return '<li>' +
                           '<span class="dot" style="background:' + colors[i] + '"></span>' +
                           '<span class="ec-status-name">' + label + '</span>' +
                           '<span class="ec-status-count">' + (isMoney ? ecMoney(value) : value.toLocaleString()) + '</span>' +
                           '<span class="ec-status-pct">' + pct + '%</span>' +
                       '</li>';
            }).join('');
        }

        /* ── Sales: three smooth series with gradient fills ────────────────── */
        function renderChart(chartNode, rideData, parcelData, rentalData, labels) {
            var el = (chartNode && chartNode.jquery) ? chartNode[0] : chartNode;
            el = ecResetCanvas(el);
            if (!el) return null;

            var ctx = el.getContext('2d');
            var h = el.clientHeight || 260;

            // A vertical gradient per series, so overlapping areas stay readable.
            function area(color) {
                var g = ctx.createLinearGradient(0, 0, 0, h);
                g.addColorStop(0, ecHexToRgba(color, .26));
                g.addColorStop(1, ecHexToRgba(color, 0));
                return g;
            }

            var series = [
                { label: "{{ trans('lang.ride_sales') }}",   data: rideData,   color: ecBrand() },
                { label: "{{ trans('lang.parcel_sales') }}", data: parcelData, color: '#3b82f6' },
                { label: "{{ trans('lang.rental_sales') }}", data: rentalData, color: '#22c55e' }
            ];

            myChart = new Chart(el, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: series.map(function (s) {
                        return {
                            label: s.label,
                            data: s.data,
                            borderColor: s.color,
                            backgroundColor: area(s.color),
                            borderWidth: 2.5,
                            fill: true,
                            tension: .4,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: s.color,
                            pointBorderWidth: 2,
                            pointHoverBorderWidth: 3
                        };
                    })
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    // Chart.js draws the legend inside the canvas, so without this the
                    // topmost y-axis tick sits directly beneath the legend row.
                    layout: { padding: { top: 4 } },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'start',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 14,
                                color: EC_INK_MUTED,
                                font: { size: 12, weight: '600', family: 'Urbanist, Nunito, sans-serif' }
                            }
                        },
                        tooltip: Object.assign({}, EC_TOOLTIP, {
                            callbacks: {
                                label: function (c) { return ' ' + c.dataset.label + ': ' + ecMoney(c.parsed.y); }
                            }
                        })
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: EC_GRID, drawTicks: false },
                            ticks: {
                                padding: 10,
                                maxTicksLimit: 5,
                                color: EC_INK_MUTED,
                                font: { size: 11.5, weight: '600', family: 'Urbanist, Nunito, sans-serif' },
                                callback: function (value) { return ecMoneyShort(value); }
                            }
                        },
                        x: {
                            border: { display: false },
                            grid: { display: false },
                            ticks: {
                                padding: 6,
                                color: EC_INK_MUTED,
                                font: { size: 11.5, weight: '600', family: 'Urbanist, Nunito, sans-serif' }
                            }
                        }
                    }
                }
            });

            return myChart;
        }

        /* ── Service overview ──────────────────────────────────────────────── */
        function setVisitors() {
            var el = ecResetCanvas(document.getElementById('visitors'));
            if (!el) return null;

            var labels = [
                "{{ trans('lang.dashboard_total_users') }}",
                "{{ trans('lang.individual_drivers') }}",
                "{{ trans('lang.owner_plural') }}",
                "{{ trans('lang.fleet_drivers') }}"
            ];
            var values = [
                {{ $total_users ?? 0 }},
                {{ $total_drivers ?? 0 }},
                {{ $total_owners ?? 0 }},
                {{ $total_fleet_drivers ?? 0 }}
            ];
            var colors = [ecBrand(), '#3b82f6', '#22c55e', '#8b5cf6'];
            var total  = values.reduce(function (a, b) { return a + b; }, 0);

            ecRenderLegend('visitors_legend', labels, values, colors, false);

            return new Chart(el, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0,
                        borderRadius: 6,
                        hoverOffset: 6
                    }]
                },
                options: ecDoughnutOptions(total.toLocaleString(), "{{ trans('lang.dashboard_active_users') }}", false)
            });
        }

        /* ── Earnings vs commission ────────────────────────────────────────── */
        function setCommision() {
            var el = ecResetCanvas(document.getElementById('commissions'));
            if (!el) return null;

            var labels = [
                "{{ trans('lang.dashboard_total_earnings') }}",
                "{{ trans('lang.admin_commission') }}"
            ];
            var values = [
                parseFloat(jQuery('#total_earning').val()) || 0,
                parseFloat(jQuery('#admin_commission').val()) || 0
            ];
            var colors = [ecBrand(), '#8b5cf6'];
            var total  = values.reduce(function (a, b) { return a + b; }, 0);

            ecRenderLegend('commissions_legend', labels, values, colors, true);

            return new Chart(el, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0,
                        borderRadius: 6,
                        hoverOffset: 6
                    }]
                },
                options: ecDoughnutOptions(ecMoneyShort(total), "{{ trans('lang.total_sales') }}", true)
            });
        }

//         const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "Octomber", "November", "December"];
//         const monthFilter = document.getElementById('monthFilter');
//         const yearFilter = document.getElementById('yearFilter');
//         const viewFilter = document.getElementById('viewFilter');
//         const customDateFilters = document.getElementById('customDateFilters');
//         const monthYearFilters = document.getElementById('monthYearFilters');
//         const startDateInput = document.getElementById('startDate');
//         const endDateInput = document.getElementById('endDate');

//         const currentYear = new Date().getFullYear();
//         const currentMonth = new Date().getMonth() + 1;

//         for (let y = currentYear; y >= currentYear - 5; y--) {
//             yearFilter.innerHTML += `<option value="${y}" ${y === currentYear ? 'selected' : ''}>${y}</option>`;
//         }

//         months.forEach((m, index) => {
//             monthFilter.innerHTML += `<option value="${index + 1}" ${index + 1 === currentMonth ? 'selected' : ''}>${m}</option>`;
//         });

//         viewFilter.addEventListener('change', () => {
//             const view = viewFilter.value;

//             if (view === 'month') {
//                 monthFilter.style.display = 'inline-block';
//                 yearFilter.style.display = 'inline-block';
//                 customDateFilters.style.display = 'none';
//             } else if (view === 'year') {
//                 monthFilter.style.display = 'none';
//                 yearFilter.style.display = 'inline-block';
//                 customDateFilters.style.display = 'none';
//             } else if (view === 'custom') {
//                 monthFilter.style.display = 'none';
//                 yearFilter.style.display = 'none';
//                 customDateFilters.style.display = 'inline-block';
//             }
//             customDateFilters.style.display = view === 'custom' ? 'inline-block' : 'none';
//             getTotalSales();
//         });
//         [monthFilter, yearFilter, startDateInput, endDateInput].forEach(el => {
//             el.addEventListener('change', getTotalSales);
//         });
// $('#viewFilter, #monthFilter, #yearFilter, #startDate, #endDate').on('change', function() {
//     $('#dashboardFilterForm').submit();
// });

// Show/hide month/year/custom inputs. These target the *Wrap columns rather than
// the selects themselves, so each control's label travels with it.
function updateFilterVisibility() {
    const view = $('#viewFilter').val();
    if (view === 'month') {
        $('#monthFilterWrap').show();
        $('#yearFilterWrap').show();
        $('#customDateFilters').hide();
    } else if (view === 'year') {
        $('#monthFilterWrap').hide();
        $('#yearFilterWrap').show();
        $('#customDateFilters').hide();
    } else {
        $('#monthFilterWrap').hide();
        $('#yearFilterWrap').hide();
        $('#customDateFilters').show();
    }
}
$('#viewFilter').on('change', updateFilterVisibility);
$(document).ready(updateFilterVisibility);
       

    </script>
@endsection