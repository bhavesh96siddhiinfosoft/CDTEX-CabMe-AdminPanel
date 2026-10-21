@extends('layouts.app')

@section('content')
@php
    $activeTab = request('tab', 'rides');
    $fmt = function ($v) use ($currency) {
        return $currency->symbol_at_right == 'true'
            ? number_format(floatval($v), $currency->decimal_digit) . $currency->symbole
            : $currency->symbole . number_format(floatval($v), $currency->decimal_digit);
    };
    $pageTitle = $isFleet ? trans('lang.fleet_driver_details') : trans('lang.driver_details');
@endphp
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">

        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ $pageTitle }}</h3>
                <p class="mb-0 text-muted">{{ $driver->prenom }} {{ $driver->nom }}</p>
            </div>
            <div class="d-flex align-items-center">
                @if (!$isFleet)
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#addWalletModal" class="btn ec-head-btn add-wallate"><i class="mdi mdi-plus"></i> {{ trans('lang.add_wallet_amount') }}</a>
                @endif
                <a href="{{ route('drivers.index') }}" class="btn ec-btn-ghost ml-2"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.driver_plural') }}</a>
            </div>
        </div>

        {{-- KPI strip --}}
        <div class="row ec-kpi-row">
            <div class="col-lg col-md-6 mb-3">
                <div class="ec-kpi kpi-brand ec-kpi-stat">
                    <span class="ec-kpi-icon"><i class="mdi mdi-car"></i></span>
                    <div class="ec-kpi-body">
                        <div class="ec-kpi-value">{{ $totalRides }}</div>
                        <div class="ec-kpi-label">{{ trans('lang.total_rides') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-6 mb-3">
                <div class="ec-kpi kpi-blue ec-kpi-stat">
                    <span class="ec-kpi-icon"><i class="mdi mdi-package-variant-closed"></i></span>
                    <div class="ec-kpi-body">
                        <div class="ec-kpi-value">{{ $totalParcelOrders }}</div>
                        <div class="ec-kpi-label">{{ trans('lang.total_parcel_orders') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-6 mb-3">
                <div class="ec-kpi kpi-green ec-kpi-stat">
                    <span class="ec-kpi-icon"><i class="mdi mdi-car-clock"></i></span>
                    <div class="ec-kpi-body">
                        <div class="ec-kpi-value">{{ $totalrentalOrders }}</div>
                        <div class="ec-kpi-label">{{ trans('lang.total_rental_orders') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-6 mb-3">
                <div class="ec-kpi kpi-amber ec-kpi-stat">
                    <span class="ec-kpi-icon"><i class="mdi mdi-cash-multiple"></i></span>
                    <div class="ec-kpi-body">
                        <div class="ec-kpi-value">{{ $fmt($earnings->montant ?? 0) }}</div>
                        <div class="ec-kpi-label">{{ trans('lang.earning_plural') }}</div>
                    </div>
                </div>
            </div>
            @if (!$isFleet)
                <div class="col-lg col-md-6 mb-3">
                    <div class="ec-kpi kpi-brand ec-kpi-stat">
                        <span class="ec-kpi-icon"><i class="mdi mdi-wallet-outline"></i></span>
                        <div class="ec-kpi-body">
                            <div class="ec-kpi-value wallet_balance" id="wallet_balance">
                                @if (substr($driver->amount, 0, 1) == '-')
                                    <span class="ec-amt-debit">-{{ $fmt(substr($driver->amount, 1)) }}</span>
                                @else
                                    {{ $fmt($driver->amount) }}
                                @endif
                            </div>
                            <div class="ec-kpi-label">{{ trans('lang.wallet_balance') }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row rv-eq rv-page">
            {{-- Hero --}}
            <div class="col-lg-7 mb-4">
                <div class="card ec-card ec-form-card rv-hero h-100">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-steering"></i></span>
                        <h5 class="mb-0 ml-2">{{ $pageTitle }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="rv-profile">
                            <div class="rv-avatar">
                                @if (!empty($driver->photo_path) && file_exists(public_path('assets/images/driver/' . $driver->photo_path)))
                                    <img src="{{ asset('assets/images/driver') . '/' . $driver->photo_path }}" alt="image">
                                @else
                                    <img src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">
                                @endif
                            </div>
                            <div class="rv-profile-body">
                                <div class="rv-title-row">
                                    <h4 class="rv-name mb-0">{{ $driver->prenom }} {{ $driver->nom }}</h4>
                                    @if ($driver->statut == 'yes')
                                        <span class="badge badge-success">{{ trans('lang.enabled') }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ trans('lang.disabled') }}</span>
                                    @endif
                                    @if ($isFleet)
                                        <span class="badge badge-info">{{ trans('lang.fleet_driver') }}</span>
                                    @endif
                                </div>
                                <ul class="rv-meta">
                                    <li><i class="mdi mdi-email-outline"></i><span>{{ $driver->email ?: trans('lang.not_available') }}</span></li>
                                    <li><i class="mdi mdi-phone-outline"></i><span>{{ $driver->country_code }}{{ $driver->phone }}</span></li>
                                    @if ($zone_name)
                                        <li><i class="mdi mdi-map-outline"></i><span>{{ $zone_name }}</span></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="rv-facts rv-facts-2">
                            <div class="rv-fact">
                                <span class="rv-fact-label"><i class="mdi mdi-star-outline"></i>{{ trans('lang.rating') }}</span>
                                <span class="rv-fact-value">{{ $driver->average_rating ?: '0.0' }} ({{ $driver->review_count ?: 0 }})</span>
                            </div>
                            <div class="rv-fact">
                                <span class="rv-fact-label"><i class="mdi mdi-briefcase-outline"></i>{{ trans('lang.service_type') }}</span>
                                <span class="rv-fact-value">{{ collect(explode(',', $driver->service_type))->map(fn($t) => ucfirst(trim($t)))->implode(', ') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Account details --}}
            <div class="col-lg-5 mb-4">
                <div class="card ec-card ec-form-card h-100">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-card-account-details-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.user_phone') }}</span>
                            <span class="ec-detail-value">{{ $driver->country_code }}{{ $driver->phone }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.created_at') }}</span>
                            <span class="ec-detail-value">{{ date('d F Y h:i A', strtotime($driver->creer)) }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.edited') }}</span>
                            <span class="ec-detail-value">
                                @if ($driver->modifier != '0000-00-00 00:00:00' && $driver->modifier)
                                    {{ date('d F Y h:i A', strtotime($driver->modifier)) }}
                                @endif
                            </span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.status') }}</span>
                            <span class="ec-detail-value">
                                @if ($driver->statut == 'yes')
                                    <span class="badge badge-success">{{ trans('lang.enabled') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ trans('lang.disabled') }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="mt-3">
                            @if ($driver->statut == 'no')
                                <a href="{{ route('driver.changeStatus', ['id' => $driver->id]) }}" class="btn ec-btn-brand btn-sm direct-click-btn">{{ trans('lang.enable_account') }}</a>
                            @else
                                <a href="{{ route('driver.changeStatus', ['id' => $driver->id]) }}" class="btn ec-btn-ghost btn-sm direct-click-btn">{{ trans('lang.disable_account') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row rv-eq rv-page">
            {{-- Bank details --}}
            <div class="col-lg-6 mb-4">
                <div class="card ec-card ec-form-card h-100">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-bank-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.bank_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.bank_name') }}</span>
                            <span class="ec-detail-value">{{ $driver->bank_name }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.branch_name') }}</span>
                            <span class="ec-detail-value">{{ $driver->branch_name }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.holder_name') }}</span>
                            <span class="ec-detail-value">{{ $driver->holder_name }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.account_no') }}</span>
                            <span class="ec-detail-value">{{ $driver->account_no }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.ifsc_code') }}</span>
                            <span class="ec-detail-value">{{ $driver->ifsc_code }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.Other_info') }}</span>
                            <span class="ec-detail-value">{{ $driver->other_info }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @if ($isFleet)
                {{-- Owner information --}}
                <div class="col-lg-6 mb-4">
                    <div class="card ec-card ec-form-card h-100">
                        <div class="card-header d-flex align-items-center">
                            <span class="ec-filter-badge"><i class="mdi mdi-account-tie-outline"></i></span>
                            <h5 class="mb-0 ml-2">{{ trans('lang.owner_information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.user_name') }}</span>
                                <span class="ec-detail-value">
                                    @if ($owner)<a href="{{ route('driver.show', ['id' => $owner->id]) }}">{{ $owner->prenom }} {{ $owner->nom }}</a>@else - @endif
                                </span>
                            </div>
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.user_phone') }}</span>
                                <span class="ec-detail-value">{{ $owner ? $owner->phone : '-' }}</span>
                            </div>
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.email') }}</span>
                                <span class="ec-detail-value">{{ $owner ? $owner->email : '-' }}</span>
                            </div>
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.status') }}</span>
                                <span class="ec-detail-value">
                                    @if ($owner && $owner->statut == 'yes')
                                        <span class="badge badge-success">{{ trans('lang.enabled') }}</span>
                                    @elseif ($owner && $owner->statut == 'no')
                                        <span class="badge badge-warning">{{ trans('lang.disabled') }}</span>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.rating') }}</span>
                                <span class="ec-detail-value">{{ $owner && $owner->average_rating ? $owner->average_rating : '0.0' }} ({{ $owner && $owner->review_count ? $owner->review_count : 0 }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Active subscription plan --}}
                <div class="col-lg-6 mb-4">
                    <div class="card ec-card ec-form-card h-100">
                        <div class="card-header d-flex align-items-center">
                            <span class="ec-filter-badge"><i class="mdi mdi-crown-outline"></i></span>
                            <h5 class="mb-0 ml-2">{{ trans('lang.active_subscription_plan') }}</h5>
                            <div class="ml-auto">
                                @if (!empty($driver->subscription_plan))
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#updateLimitModal" class="btn ec-btn-ghost btn-sm update-limit">{{ trans('lang.update_plan_limit') }}</a>
                                @endif
                                @if (($subscriptionModel || $commissionModel) && empty($driver->ownerId))
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#changeSubscriptionModal" class="btn ec-btn-brand btn-sm change-plan">{{ trans('lang.change_subscription_plan') }}</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.plan_name') }}</span>
                                <span class="ec-detail-value">{{ !empty($driver->subscription_plan) ? $driver->subscription_plan['name'] : trans('lang.not_available') }}</span>
                            </div>
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.plan_type') }}</span>
                                <span class="ec-detail-value">{{ !empty($driver->subscription_plan) ? $driver->subscription_plan['type'] : trans('lang.not_available') }}</span>
                            </div>
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.total_booking_limit') }}</span>
                                <span class="ec-detail-value">{{ !empty($driver->subscription_plan) ? ($driver->subscription_plan['bookingLimit'] == '-1' ? trans('lang.unlimited') : $driver->subscription_plan['bookingLimit']) : trans('lang.not_available') }}</span>
                            </div>
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.available_booking_limit') }}</span>
                                <span class="ec-detail-value">{{ !empty($driver->subscription_plan) ? ($driver->subscriptionTotalOrders == '-1' ? trans('lang.unlimited') : $driver->subscriptionTotalOrders) : trans('lang.not_available') }}</span>
                            </div>
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.plan_expires_at') }}</span>
                                <span class="ec-detail-value">{{ !empty($driver->subscription_plan) ? ($driver->subscriptionExpiryDate == null ? trans('lang.unlimited') : date('d F Y h:i A', strtotime($driver->subscriptionExpiryDate))) : trans('lang.not_available') }}</span>
                            </div>
                            <div class="ec-detail-row">
                                <span class="ec-detail-label">{{ trans('lang.available_features') }}</span>
                                <span class="ec-detail-value">
                                    @if (!empty($driver->subscription_plan) && !empty($driver->subscription_plan['plan_points']))
                                        <ul class="user-features-list mb-0 pl-3">
                                            @foreach ($driver->subscription_plan['plan_points'] as $point)
                                                <li>{{ $point }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        {{ trans('lang.not_available') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Activity tabs --}}
        <div class="row">
            <div class="col-12">
                <div class="card ec-card ec-list-card mb-4">
                    <div class="card-body">
                        <div class="menu-tab">
                            <ul>
                                <li class="{{ $activeTab == 'rides' ? 'active' : '' }}"><a href="#rides" role="tab">{{ trans('lang.rides') }}</a></li>
                                <li class="{{ $activeTab == 'parcels' ? 'active' : '' }}"><a href="#parcels" role="tab">{{ trans('lang.parcel') }}</a></li>
                                <li class="{{ $activeTab == 'rentals' ? 'active' : '' }}"><a href="#rentals" role="tab">{{ trans('lang.rental') }}</a></li>
                                <li class="{{ $activeTab == 'vehicle' ? 'active' : '' }}"><a href="#vehicle" role="tab">{{ trans('lang.vehicle') }}</a></li>
                                @if (!$isFleet)
                                    <li class="{{ $activeTab == 'transaction' ? 'active' : '' }}"><a href="#transaction" role="tab">{{ trans('lang.wallet_transaction') }}</a></li>
                                    <li class="{{ $activeTab == 'subscription_history' ? 'active' : '' }}"><a href="#subscription_history" role="tab">{{ trans('lang.subscription_history') }}</a></li>
                                @endif
                            </ul>
                        </div>

                        <div class="tab-content pt-3">
                            <div role="tabpanel" class="tab-pane {{ $activeTab == 'rides' ? 'active' : '' }}" id="rides">
                                @if (count($rides) > 0)
                                    <div class="table-responsive">
                                        <table class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('lang.ride_id') }}</th>
                                                    <th>{{ trans('lang.ride_type') }}</th>
                                                    <th>{{ trans('lang.status') }}</th>
                                                    <th>{{ trans('lang.created') }}</th>
                                                    <th>{{ trans('lang.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rides as $ride)
                                                    <tr>
                                                        <td><a href="{{ route('ride.show', ['id' => $ride->id]) }}">{{ $ride->id }}</a></td>
                                                        <td>{{ $ride->ride_type == 'dispatcher' ? trans('lang.dispatcher') : trans('lang.normal') }}</td>
                                                        <td>
                                                            @if ($ride->statut == 'completed')
                                                                <span class="badge badge-success">{{ ucfirst($ride->statut) }}</span>
                                                            @elseif ($ride->statut == 'rejected')
                                                                <span class="badge badge-danger">{{ ucfirst($ride->statut) }}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ ucfirst($ride->statut) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d F Y h:i A', strtotime($ride->creer)) }}</td>
                                                        <td class="action-btn ec-actions">
                                                            <a href="{{ route('ride.show', ['id' => $ride->id]) }}" class="ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                            <a class="do_not_delete ec-act ec-act-danger" name="user-delete" href="{{ route('ride.delete', ['rideid' => $ride->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {{ $rides->appends(['tab' => 'rides'])->links('pagination.pagination') }}
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                @endif
                            </div>

                            <div role="tabpanel" class="tab-pane {{ $activeTab == 'parcels' ? 'active' : '' }}" id="parcels">
                                @if (count($parcelOrders) > 0)
                                    <div class="table-responsive">
                                        <table class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('lang.parcel_id') }}</th>
                                                    <th>{{ trans('lang.userName') }}</th>
                                                    <th>{{ trans('lang.status') }}</th>
                                                    <th>{{ trans('lang.created') }}</th>
                                                    <th>{{ trans('lang.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($parcelOrders as $parcel)
                                                    <tr>
                                                        <td><a href="{{ route('parcel.show', ['id' => $parcel->id]) }}">{{ $parcel->id }}</a></td>
                                                        <td>
                                                            @if ($parcel->user_id)
                                                                <a href="{{ route('users.show', ['id' => $parcel->user_id]) }}">{{ $parcel->userPrenom }} {{ $parcel->userNom }}</a>
                                                            @else
                                                                {{ trans('lang.not_available') }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (in_array($parcel->status, ['completed', 'confirmed']))
                                                                <span class="badge badge-success">{{ ucfirst($parcel->status) }}</span>
                                                            @elseif ($parcel->status == 'new')
                                                                <span class="badge badge-primary">{{ ucfirst($parcel->status) }}</span>
                                                            @elseif ($parcel->status == 'rejected')
                                                                <span class="badge badge-danger">{{ ucfirst($parcel->status) }}</span>
                                                            @elseif ($parcel->status == 'driver_rejected')
                                                                <span class="badge badge-danger">{{ trans('lang.driver_rejected') }}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ ucfirst($parcel->status) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d F Y h:i A', strtotime($parcel->created_at)) }}</td>
                                                        <td class="action-btn ec-actions">
                                                            <a href="{{ route('parcel.show', ['id' => $parcel->id]) }}" class="ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                            <a class="do_not_delete ec-act ec-act-danger" name="user-delete" href="{{ route('parcel.delete', ['rideid' => $parcel->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {{ $parcelOrders->appends(['tab' => 'parcels'])->links('pagination.pagination') }}
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                @endif
                            </div>

                            <div role="tabpanel" class="tab-pane {{ $activeTab == 'rentals' ? 'active' : '' }}" id="rentals">
                                @if (count($rentalOrders) > 0)
                                    <div class="table-responsive">
                                        <table class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('lang.order_id') }}</th>
                                                    <th>{{ trans('lang.user_name') }}</th>
                                                    <th>{{ trans('lang.status') }}</th>
                                                    <th>{{ trans('lang.created') }}</th>
                                                    <th>{{ trans('lang.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rentalOrders as $rental)
                                                    <tr>
                                                        <td><a href="{{ route('rental-orders.show', ['id' => $rental->id]) }}">{{ $rental->id }}</a></td>
                                                        <td>{{ $rental->userPrenom }} {{ $rental->userNom }}</td>
                                                        <td>
                                                            @if (in_array($rental->status, ['completed', 'confirmed']))
                                                                <span class="badge badge-success">{{ ucfirst($rental->status) }}</span>
                                                            @elseif ($rental->status == 'new')
                                                                <span class="badge badge-primary">{{ ucfirst($rental->status) }}</span>
                                                            @elseif ($rental->status == 'rejected')
                                                                <span class="badge badge-danger">{{ ucfirst($rental->status) }}</span>
                                                            @elseif ($rental->status == 'driver_rejected')
                                                                <span class="badge badge-danger">{{ trans('lang.driver_rejected') }}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ ucfirst($rental->status) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d F Y h:i A', strtotime($rental->created_at)) }}</td>
                                                        <td class="action-btn ec-actions">
                                                            <a href="{{ route('rental-orders.show', ['id' => $rental->id]) }}" class="ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                            <a class="delete-btn ec-act ec-act-danger" name="user-delete" href="{{ route('rental-orders.delete', ['id' => $rental->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {{ $rentalOrders->appends(['tab' => 'rentals'])->links('pagination.pagination') }}
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                @endif
                            </div>

                            <div role="tabpanel" class="tab-pane {{ $activeTab == 'vehicle' ? 'active' : '' }}" id="vehicle">
                                @if ($vehicle)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="ec-detail-row">
                                                <span class="ec-detail-label">{{ trans('lang.brand') }}</span>
                                                <span class="ec-detail-value">{{ $vehicle->brand_name ?? '-' }}</span>
                                            </div>
                                            <div class="ec-detail-row">
                                                <span class="ec-detail-label">{{ trans('lang.vehicle_model') }}</span>
                                                <span class="ec-detail-value">{{ $vehicle->model_name ?? '-' }}</span>
                                            </div>
                                            <div class="ec-detail-row">
                                                <span class="ec-detail-label">{{ trans('lang.vehicle_type') }}</span>
                                                <span class="ec-detail-value">{{ $vehicle->vehicle_type ?? '-' }}</span>
                                            </div>
                                            <div class="ec-detail-row">
                                                <span class="ec-detail-label">{{ trans('lang.car_number') }}</span>
                                                <span class="ec-detail-value">{{ $vehicle->numberplate }}</span>
                                            </div>
                                            <div class="ec-detail-row">
                                                <span class="ec-detail-label">{{ trans('lang.number_of_pessanger') }}</span>
                                                <span class="ec-detail-value">{{ $vehicle->passenger }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="ec-detail-row">
                                                <span class="ec-detail-label">{{ trans('lang.vehicle_color') }}</span>
                                                <span class="ec-detail-value">{{ $vehicle->color }}</span>
                                            </div>
                                            <div class="ec-detail-row">
                                                <span class="ec-detail-label">{{ trans('lang.vehicle_milage') }}</span>
                                                <span class="ec-detail-value">{{ $vehicle->milage }}</span>
                                            </div>
                                            <div class="ec-detail-row">
                                                <span class="ec-detail-label">{{ trans('lang.vehicle_km') }}</span>
                                                <span class="ec-detail-value">{{ $vehicle->km }}</span>
                                            </div>
                                            <div class="ec-detail-row">
                                                <span class="ec-detail-label">{{ trans('lang.created_at') }}</span>
                                                <span class="ec-detail-value">{{ date('d F Y h:i A', strtotime($driver->creer)) }}</span>
                                            </div>
                                            <div class="ec-detail-row">
                                                <span class="ec-detail-label">{{ trans('lang.edited') }}</span>
                                                <span class="ec-detail-value">
                                                    @if ($driver->modifier != '0000-00-00 00:00:00' && $driver->modifier)
                                                        {{ date('d F Y h:i A', strtotime($driver->modifier)) }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                @endif
                            </div>

                            @if (!$isFleet)
                                <div role="tabpanel" class="tab-pane {{ $activeTab == 'transaction' ? 'active' : '' }}" id="transaction">
                                    @if (count($transactions) > 0)
                                        <div class="table-responsive">
                                            <table class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('lang.transaction_id') }}</th>
                                                        <th>{{ trans('lang.amount') }}</th>
                                                        <th>{{ trans('lang.created_at') }}</th>
                                                        <th>{{ trans('lang.payment_method') }}</th>
                                                        <th>{{ trans('lang.note') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($transactions as $transaction)
                                                        <tr>
                                                            <td>{{ $transaction->id }}</td>
                                                            <td>
                                                                @if ($transaction->is_credited == '0')
                                                                    <span class="ec-amt-debit">(-{{ $fmt($transaction->amount) }})</span>
                                                                @else
                                                                    <span class="ec-amt-credit">{{ $fmt($transaction->amount) }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ date('d F Y h:i A', strtotime($transaction->created_at)) }}</td>
                                                            <td>
                                                                @if ($transaction->image)
                                                                    <img class="rounded" style="{{ $transaction->image == 'paystack.png' ? 'width: 90px;' : 'width: 50px;' }}" src="{{ asset('/assets/images/payment_method/' . $transaction->image) }}" alt="image">
                                                                @else
                                                                    {{ $transaction->payment_method }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $transaction->note }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            {{ $transactions->appends(['tab' => 'transaction'])->links('pagination.pagination') }}
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                    @endif
                                </div>

                                <div role="tabpanel" class="tab-pane {{ $activeTab == 'subscription_history' ? 'active' : '' }}" id="subscription_history">
                                    @if (count($history) > 0)
                                        <div class="table-responsive">
                                            <table class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('lang.plan_name') }}</th>
                                                        <th>{{ trans('lang.plan_type') }}</th>
                                                        <th>{{ trans('lang.plan_expires_at') }}</th>
                                                        <th>{{ trans('lang.purchase_date') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($history as $value)
                                                        <tr>
                                                            <td>
                                                                {{ $value->subscription_plan['name'] }}
                                                                @if ($value->status == 'active')
                                                                    <span class="badge badge-success">{{ ucfirst($value->status) }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ ucfirst($value->status) }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($value->subscription_plan['type'] == 'free')
                                                                    <span class="badge badge-success">{{ trans('lang.free') }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ trans('lang.paid') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if (!empty($value->expiry_date))
                                                                    {{ date('d F Y h:i A', strtotime($value->expiry_date)) }}
                                                                @else
                                                                    {{ trans('lang.unlimited') }}
                                                                @endif
                                                            </td>
                                                            <td>{{ date('d F Y h:i A', strtotime($value->created_at)) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            {{ $history->appends(['tab' => 'subscription_history'])->links('pagination.pagination') }}
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addWalletModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered location_modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title locationModalTitle">{{ trans('lang.add_wallet_amount') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('driver.wallet', $driver->id) }}" method="post">
                    @csrf
                    <div class="row">
                        <label class="col-md-12 control-label">{{ trans('lang.amount') }}</label>
                        <div class="col-md-12">
                            <input type="number" name="amount" class="form-control" id="amount" placeholder="{{ trans('lang.enter_amount') }}">
                            <div id="wallet_error" style="color:red"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn ec-btn-brand" id="add-wallet-btn">{{ trans('lang.save') }}</button>
                        <button type="button" class="btn ec-btn-ghost" data-dismiss="modal" aria-label="Close">{{ trans('lang.close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="changeSubscriptionModal" tabindex="-1" role="dialog" aria-hidden="true" style="width: 100%">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="text-dark-2 h5 mb-0">{{ trans('lang.business_plans') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-lg-12 ml-lg-auto mr-lg-auto">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex top-title-section pb-4 mb-2 justify-content-between">
                                    <div class="d-flex top-title-left align-start-center">
                                        <div class="top-title">
                                            <h3 class="mb-0">{{ trans('lang.choose_your_business_plan') }}</h3>
                                            <p class="mb-0 text-dark-2">
                                                {{ trans('lang.choose_your_business_plan_description') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="row" id="default-plan">
                                    @foreach ($plans as $plan)
                                        @php
                                            $activeClass = $plan->id == $activeSubscriptionId ? '<span class="badge badge-success">' . trans('lang.active') . '</span>' : '';
                                            $buttonText = $plan->id == $activeSubscriptionId ? trans('lang.renew_plan') : trans('lang.select_plan');
                                        @endphp

                                        @if ($plan->id == '1')
                                            @if ($commissionModel)
                                                <div class="col-md-3 mb-3 pricing-card pricing-card-commission">
                                                    <div class="pricing-card-inner">
                                                        <div class="pricing-card-top">
                                                            <div class="d-flex align-items-center pb-4">
                                                                <span class="pricing-card-icon mr-4"><img src="{{ asset('assets/images/subscription') . '/' . $plan->image }}" alt="{{ $plan->name }}"></span>
                                                                <h2 class="text-dark-2">{!! $plan->name !!}
                                                                    {!! $activeClass !!}</h2>
                                                            </div>
                                                            <p class="text-muted">{{ $plan->description !== $plan->name ? $plan->description : '' }}</p>
                                                            <div class="pricing-card-price">
                                                                <h3 class="text-dark-2">{{ $adminCommission ?? '' }}</h3>
                                                                <span class="price-day">{{ trans('lang.commision_per_order') }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="pricing-card-content pt-3 mt-3 border-top">
                                                            <ul class="pricing-card-list text-dark-2">
                                                                <li><span class="mdi mdi-check"></span>{{ trans('lang.pay_commission_of') }}
                                                                    {{ $adminCommission ?? '' }}
                                                                    {{ trans('lang.on_each_order') }}
                                                                </li>
                                                                @foreach ($plan->plan_points as $point)
                                                                    <li><span class="mdi mdi-check"></span>{{ $point }}
                                                                    </li>
                                                                @endforeach
                                                                <li><span class="mdi mdi-check"></span>{{ trans('lang.unlimited') }}
                                                                    {{ trans('lang.bookings') }}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="pricing-card-btm">
                                                            <a href="javascript:void(0)" onClick="chooseSubscriptionPlan('{{ $plan->id }}')" class="btn rounded-full active-btn btn-primary">{{ $buttonText }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            @if ($subscriptionModel)
                                                <div class="col-md-3 mt-2 pricing-card pricing-card-subscription {{ $plan->name }}">
                                                    <div class="pricing-card-inner">
                                                        <div class="pricing-card-top">
                                                            <div class="d-flex align-items-center pb-4">
                                                                <span class="pricing-card-icon mr-4"><img src="{{ asset('assets/images/subscription') . '/' . $plan->image }}" alt="{{ $plan->name }}"></span>
                                                                <h2 class="text-dark-2">{!! $plan->name !!}
                                                                    {!! $activeClass !!}</h2>
                                                            </div>
                                                            <p class="text-muted">{{ $plan->description !== $plan->name ? $plan->description : '' }}</p>
                                                            <div class="pricing-card-price">
                                                                <h3 class="text-dark-2">

                                                                    {{ intval($plan->price == 0) ? trans('lang.free') : ($currency->symbol_at_right == 'true' ? number_format($plan->price, $currency->decimal_degit ?? 2) . $currency->symbole : $currency->symbole . number_format($plan->price, $currency->decimal_degit ?? 2)) }}

                                                                </h3>
                                                                <span class="price-day">{{ $plan->expiryDay == -1 ? trans('lang.unlimited') : $plan->expiryDay }}
                                                                    {{ trans('lang.days') }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="pricing-card-content pt-3 mt-3 border-top">
                                                            <ul class="pricing-card-list text-dark-2">
                                                                @foreach ($plan->plan_points as $point)
                                                                    <li><span class="mdi mdi-check"></span>{{ $point }}
                                                                    </li>
                                                                @endforeach
                                                                <li><span class="mdi mdi-check"></span>{{ $plan->bookingLimit == -1 ? trans('lang.unlimited') : $plan->bookingLimit }}
                                                                    {{ trans('lang.bookings') }}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="pricing-card-btm">
                                                            <a href="javascript:void(0)" onClick="chooseSubscriptionPlan('{{ $plan->id }}')" class="btn rounded-full">{{ $buttonText }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="checkoutSubscriptionModal" tabindex="-1" role="dialog" aria-hidden="true" style="width: 100%">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="text-dark-2 h5 mb-0">{{ trans('lang.shift_to_plan') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form class="">
                    <div class="subscription-section">
                        <div class="subscription-section-inner">
                            <div class="card-body">
                                <div class="row" id="plan-details"></div>
                                <div class="pay-method-section pt-4 manual_pay_div">
                                    <h6 class="text-dark-2 h6 mb-3 pb-3">{{ trans('lang.pay_via_online') }}</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="pay-method-box d-flex align-items-center">
                                                <div class="pay-method-icon">
                                                    <img src="{{ asset('images/wallet_icon_ic.png') }}">
                                                </div>
                                                <div class="form-check ">
                                                    <h6 class="text-dark-2 h6 mb-0">{{ trans('lang.manual_pay') }}
                                                    </h6>
                                                    <input type="radio" id="manual_pay" name="payment_method" value="manual_pay" checked="">
                                                    <label class="control-label mb-0" for="manual_pay"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-top">
                                <div class="align-items-center justify-content-between">
                                    <div class="edit-form-group btm-btn text-right">
                                        <div class="card-block-active-plan">
                                            <a href="" class="btn ec-btn-ghost mr-2" data-dismiss="modal">{{ trans('lang.cancel_plan') }}</a>
                                            <input type="hidden" id="plan_id" name="plan_id" value="">
                                            <button type="button" class="btn ec-btn-brand" onclick="finalCheckout()">{{ trans('lang.change_plan') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="updateLimitModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered location_modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title locationModalTitle">{{ trans('lang.update_plan_limit') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('subscription-limit.update', $driver->id) }}" method="post" enctype="multipart/form-data" id="update-limit-form">
                    @csrf
                    @method('PUT')

                    @if (!empty($driver->subscription_plan))
                        <div class="form-row">
                            <div class="form-group row">
                                <div class="form-group row width-100">
                                    <label class="control-label">{{ trans('lang.maximum_booking_limit') }}</label>
                                    <div class="form-check width-100">
                                        <input type="radio" id="unlimited_booking" name="set_booking_limit" value="unlimited" {{ !empty($driver->subscription_plan) ? ($driver->subscription_plan['bookingLimit'] == '-1' ? 'checked' : '') : '' }}>
                                        <label class="control-label" for="unlimited_booking">{{ trans('lang.unlimited') }}</label>
                                    </div>
                                    <div class="d-flex">
                                        <div class="form-check width-50 limited_booking_div">
                                            <input type="radio" id="limited_booking" name="set_booking_limit" value="limited" {{ !empty($driver->subscription_plan) ? ($driver->subscription_plan['bookingLimit'] != '-1' ? 'checked' : '') : '' }}>

                                            <label class="control-label" for="limited_booking">{{ trans('lang.limited') }}</label>
                                        </div>
                                        @if ($driver->subscription_plan['bookingLimit'] == '-1')
                                            @php $limitClass='d-none'@endphp
                                        @else
                                            @php $limitClass=''@endphp
                                        @endif
                                        <div class="form-check width-50 {{ $limitClass }} booking-limit-div">
                                            <input type="number" id="booking_limit" name="booking_limit" class="form-control" value="{{ $driver->subscription_plan['bookingLimit'] != '-1' ? $driver->subscription_plan['bookingLimit'] : '' }}" placeholder="{{ trans('lang.ex_1000') }}">
                                        </div>

                                    </div>
                                    <span class="booking_limit_err text-danger font-weight-bold"></span>

                                </div>

                            </div>
                        </div>
                    @endif
                    <div class="modal-footer">
                        <button type="submit" class="btn ec-btn-brand update-plan-limit">{{ trans('lang.save') }}</button>
                        <button type="button" class="btn ec-btn-ghost" data-dismiss="modal" aria-label="Close">{{ trans('lang.close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $("#checkoutSubscriptionModal").on('hide.bs.modal', function() {
            $("#plan-details").html('');
        });

        function chooseSubscriptionPlan(planId) {
            $("#changeSubscriptionModal").modal('hide');
            $("#checkoutSubscriptionModal").modal('show');
            showPlanDetail(planId);
        }
        $('#checkoutSubscriptionModal [data-dismiss="modal"]').on('click', function () {
            $('#checkoutSubscriptionModal').modal('hide');
        });
        async function showPlanDetail(planId) {
            $("#plan_id").val(planId);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('get-plan-detail') }}",
                method: "POST",
                data: {
                    'planId': planId,
                    'driverId': "{{ $driver->id }}"
                },
                success: function(response) {
                    var choosedPlan = response.planData;
                    var activePlan = '';
                    if (response.activePlan) {
                        activePlan = response.activePlan.subscription_plan;
                    }

                    const basePath = "{{ asset('assets/images/subscription') }}/";
                    let html = '';
                    let choosedPlan_price;
                    if (parseInt(choosedPlan.price) != 0) {
                        choosedPlan_price = "{{ $currency->symbol_at_right == 'true' }}" ? parseFloat(
                                choosedPlan.price).toFixed("{{ $currency->decimal_digit }}") +
                            "{{ $currency->symbole }}" :
                            "{{ $currency->symbole }}" + parseFloat(choosedPlan.price).toFixed(
                                "{{ $currency->decimal_digit }}");
                        $('.manual_pay_div').removeClass('d-none');
                    } else {
                        choosedPlan_price = 'Free';
                        $('.manual_pay_div').addClass('d-none');
                    }

                    if (activePlan) {
                        let activePlan_price;

                        if (parseInt(activePlan.price) != 0) {

                            activePlan_price = "{{ $currency->symbol_at_right == 'true' }}" ?
                                parseFloat(activePlan.price).toFixed("{{ $currency->decimal_digit }}") +
                                "{{ $currency->symbole }}" :
                                "{{ $currency->symbole }}" + parseFloat(activePlan.price).toFixed(
                                    "{{ $currency->decimal_digit }}");

                        } else {

                            activePlan_price = 'Free';

                        }
                        html += `
            <div class="col-md-8">
                <div class="subscription-card-left">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="subscription-card text-center">
                                <div class="d-flex align-items-center pb-3 justify-content-center">
                                    <span class="pricing-card-icon mr-4"><img src="${basePath}${activePlan.image}"></span>
                                    <h2 class="text-dark-2 mb-0 font-weight-semibold">${activePlan.id=="1"? "{{ trans('lang.commission') }}":activePlan.name}</h2>
                                </div>
                                <h3 class="text-dark-2">${activePlan.id=="1"? "{{ $adminCommission }}"+" {{ trans('lang.base_plan') }}":activePlan_price}</h3>
                                <p class="text-center">${activePlan.id=="1"? "Free":activePlan.expiryDay==-1? "{{ trans('lang.unlimited') }}":activePlan.expiryDay+" {{ trans('lang.days') }}"}</p>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <img src="{{ asset('images/left-right-arrow.png') }}">
                        </div>
                        <div class="col-md-5">
                            <div class="subscription-card text-center">
                                <div class="d-flex align-items-center pb-3 justify-content-center">
                                    <span class="pricing-card-icon mr-4"><img src="${basePath}${choosedPlan.image}"></span>
                                    <h2 class="text-dark-2 mb-0 font-weight-semibold">${choosedPlan.name}
                                    </h2>
                                </div>
                                <h3 class="text-dark-2">${choosedPlan_price}</h3>
                                <p class="text-center">${choosedPlan.expiryDay=="-1"? "{{ trans('lang.unlimited') }}":choosedPlan.expiryDay+" {{ trans('lang.days') }}"}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="subscription-card-right">
                    <div
                        class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                        <span class="font-weight-medium">{{ trans('lang.validity') }}</span>
                        <span class="font-weight-semibold">${choosedPlan.expiryDay=="-1"? "{{ trans('lang.unlimited') }}":choosedPlan.expiryDay+" {{ trans('lang.days') }}"}</span>
                    </div>
                    <div
                        class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                        <span class="font-weight-medium">{{ trans('lang.price') }}</span>
                        <span class="font-weight-semibold">${choosedPlan_price}</span>
                    </div>
                    <div
                        class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                        <span class="font-weight-medium">{{ trans('lang.bill_status') }}</span>
                        <span class="font-weight-semibold">{{ trans('lang.migrate_to_new_plan') }}</span>
                    </div>
                </div>
            </div>`;
                    } else {

                        html += `
            <div class="col-md-6">
                <div class="subscription-card-left">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="subscription-card text-center">
                                <div class="d-flex align-items-center pb-3 justify-content-center">
                                    <span class="pricing-card-icon mr-4"><img src="${basePath}${choosedPlan.image}"></span>
                                    <h2 class="text-dark-2 mb-0 font-weight-semibold">${choosedPlan.name}
                                    </h2>
                                </div>
                                <h3 class="text-dark-2">${choosedPlan_price}</h3>
                                <p class="text-center">${choosedPlan.id=="1"? "Free":choosedPlan.expiryDay=="-1"? "{{ trans('lang.unlimited') }}":choosedPlan.expiryDay+" {{ trans('lang.days') }}"}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="subscription-card-right">
                    <div
                        class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                        <span class="font-weight-medium">{{ trans('lang.validity') }}</span>
                        <span class="font-weight-semibold">${choosedPlan.id=="1"? "Unlimited":choosedPlan.expiryDay=="-1"? "{{ trans('lang.unlimited') }}":choosedPlan.expiryDay+" {{ trans('lang.days') }}"}</span>
                    </div>
                    <div
                        class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                        <span class="font-weight-medium">{{ trans('lang.price') }}</span>
                        <span class="font-weight-semibold">${choosedPlan_price}</span>
                    </div>
                    <div
                        class="d-flex justify-content-between align-items-center py-3 px-3 text-dark-2">
                        <span class="font-weight-medium">{{ trans('lang.bill_status') }}</span>
                        <span class="font-weight-semibold">{{ trans('lang.migrate_to_new_plan') }}</span>
                    </div>
                </div>
            </div>`;
                    }
                    $("#plan-details").html(html);
                },
                error: function(xhr) {

                }
            });
        }
        async function finalCheckout() {
            let planId = $("#plan_id").val();
            if (planId != undefined && planId != '' && planId != null) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('subscription-checkout') }}',
                    method: "POST",
                    data: {
                        'planId': planId,
                        'driverId': "{{ $driver->id }}"
                    },
                    success: function(response) {
                        window.location.reload();
                    }

                });

            }
        }

        $("#add-wallet-btn").click(function() {
            var amount = $('#amount').val();
            if (amount == '') {
                $('#wallet_error').text('{{ trans('lang.add_wallet_amount_error') }}');
                return false;
            }
        });
        $('input[name="set_booking_limit"]').on('change', function() {
            if ($('#limited_booking').is(':checked')) {
                $('.booking-limit-div').removeClass('d-none');
            } else {
                $('.booking-limit-div').addClass('d-none');
            }
        });
        $(document).on('submit', '#update-limit-form', function(event) {
            let bookingLimit = $('#booking_limit').val().trim();
            var set_booking_limit = $('input[name="set_booking_limit"]:checked').val();
            if (set_booking_limit == 'limited' && bookingLimit === '') {
                event.preventDefault();
                $('.booking_limit_err').html('{{ trans('lang.enter_booking_limit') }}');
            }
        });
    </script>
@endsection
