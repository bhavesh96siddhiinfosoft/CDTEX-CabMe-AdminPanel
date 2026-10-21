@extends('layouts.app')

@section('content')
@php $activeTab = request('tab', 'rides'); @endphp
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">

        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.dispatcher_user_details') }}</h3>
                <p class="mb-0 text-muted">{{ $user->first_name }} {{ $user->last_name }}</p>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('dispatcher-users.index') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.dispatcher_user') }}</a>
            </div>
        </div>

        {{-- KPI strip --}}
        <div class="row ec-kpi-row">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="ec-kpi kpi-brand ec-kpi-stat">
                    <span class="ec-kpi-icon"><i class="mdi mdi-car"></i></span>
                    <div class="ec-kpi-body">
                        <div class="ec-kpi-value">{{ $rides->total() }}</div>
                        <div class="ec-kpi-label">{{ trans('lang.total_rides') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="ec-kpi kpi-green ec-kpi-stat">
                    <span class="ec-kpi-icon"><i class="mdi mdi-car-clock"></i></span>
                    <div class="ec-kpi-body">
                        <div class="ec-kpi-value">{{ $rental->total() }}</div>
                        <div class="ec-kpi-label">{{ trans('lang.total_rental_orders') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row rv-eq rv-page">
            {{-- Hero --}}
            <div class="col-lg-7 mb-4">
                <div class="card ec-card ec-form-card rv-hero h-100">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-account-tie-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.dispatcher_user_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="rv-profile">
                            <div class="rv-avatar">
                                @if (!empty($user->profile_picture_path) && file_exists(public_path('assets/images/dispatcher_users/' . $user->profile_picture_path)))
                                    <img src="{{ asset('assets/images/dispatcher_users') . '/' . $user->profile_picture_path }}" alt="image">
                                @else
                                    <img src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">
                                @endif
                            </div>
                            <div class="rv-profile-body">
                                <div class="rv-title-row">
                                    <h4 class="rv-name mb-0">{{ $user->first_name }} {{ $user->last_name }}</h4>
                                    @if ($user->status == 'yes')
                                        <span class="badge badge-success">{{ trans('lang.enabled') }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ trans('lang.disabled') }}</span>
                                    @endif
                                </div>
                                <ul class="rv-meta">
                                    <li><i class="mdi mdi-email-outline"></i><span>{{ $user->email ?: trans('lang.not_available') }}</span></li>
                                    <li><i class="mdi mdi-phone-outline"></i><span>{{ $user->country_code }}{{ $user->phone }}</span></li>
                                </ul>
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
                            <span class="ec-detail-value">{{ $user->country_code }}{{ $user->phone }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.created_at') }}</span>
                            <span class="ec-detail-value">{{ date('d F Y h:i A', strtotime($user->created_at)) }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.edited') }}</span>
                            <span class="ec-detail-value">
                                @if ($user->updated_at != '0000-00-00 00:00:00' && $user->updated_at)
                                    {{ date('d F Y h:i A', strtotime($user->updated_at)) }}
                                @endif
                            </span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.status') }}</span>
                            <span class="ec-detail-value">
                                @if ($user->status == 'yes')
                                    <span class="badge badge-success">{{ trans('lang.enabled') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ trans('lang.disabled') }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="mt-3">
                            @if ($user->status == 'no')
                                <a href="{{ route('dispatcher-users.changestatus', ['id' => $user->id]) }}" class="btn ec-btn-brand btn-sm direct-click-btn">{{ trans('lang.enable_account') }}</a>
                            @else
                                <a href="{{ route('dispatcher-users.changestatus', ['id' => $user->id]) }}" class="btn ec-btn-ghost btn-sm direct-click-btn">{{ trans('lang.disable_account') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity tabs --}}
        <div class="row">
            <div class="col-12">
                <div class="card ec-card ec-list-card mb-4">
                    <div class="card-body">
                        <div class="menu-tab">
                            <ul>
                                <li class="{{ $activeTab == 'rides' ? 'active' : '' }}"><a href="#rides" role="tab">{{ trans('lang.rides') }}</a></li>
                                <li class="{{ $activeTab == 'rental' ? 'active' : '' }}"><a href="#rental" role="tab">{{ trans('lang.rental') }}</a></li>
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
                                                    <th>{{ trans('lang.pickup_location') }}</th>
                                                    <th>{{ trans('lang.dropup_location') }}</th>
                                                    <th>{{ trans('lang.status') }}</th>
                                                    <th>{{ trans('lang.created') }}</th>
                                                    <th>{{ trans('lang.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rides as $ride)
                                                    <tr>
                                                        <td><a href="{{ route('ride.show', ['id' => $ride->id]) }}">{{ $ride->id }}</a></td>
                                                        <td class="space-wrap">{{ $ride->depart_name }}</td>
                                                        <td class="space-wrap">{{ $ride->destination_name }}</td>
                                                        <td>
                                                            @if ($ride->statut == 'new')
                                                                <span class="badge badge-primary">{{ trans('lang.new') }}</span>
                                                            @elseif ($ride->statut == 'on ride')
                                                                <span class="badge badge-warning">{{ trans('lang.on_ride') }}</span>
                                                            @elseif ($ride->statut == 'confirmed')
                                                                <span class="badge badge-success">{{ trans('lang.confirmed') }}</span>
                                                            @elseif ($ride->statut == 'canceled')
                                                                <span class="badge badge-danger">{{ trans('lang.canceled') }}</span>
                                                            @elseif ($ride->statut == 'completed')
                                                                <span class="badge badge-success">{{ trans('lang.completed') }}</span>
                                                            @elseif ($ride->statut == 'rejected')
                                                                <span class="badge badge-danger">{{ trans('lang.rejected') }}</span>
                                                            @elseif ($ride->statut == 'driver_rejected')
                                                                <span class="badge badge-danger">{{ trans('lang.driver_rejected') }}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ ucfirst($ride->statut) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d F Y h:i A', strtotime($ride->creer)) }}</td>
                                                        <td class="action-btn ec-actions">
                                                            <a href="{{ route('ride.show', ['id' => $ride->id]) }}" class="ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
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

                            <div role="tabpanel" class="tab-pane {{ $activeTab == 'rental' ? 'active' : '' }}" id="rental">
                                @if (count($rental) > 0)
                                    <div class="table-responsive">
                                        <table class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('lang.ride_id') }}</th>
                                                    <th>{{ trans('lang.pickup_location') }}</th>
                                                    <th>{{ trans('lang.status') }}</th>
                                                    <th>{{ trans('lang.created') }}</th>
                                                    <th>{{ trans('lang.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rental as $ride)
                                                    <tr>
                                                        <td><a href="{{ route('rental-orders.show', ['id' => $ride->id]) }}">{{ $ride->id }}</a></td>
                                                        <td class="space-wrap">{{ $ride->depart_name }}</td>
                                                        <td>
                                                            @if ($ride->status == 'new')
                                                                <span class="badge badge-primary">{{ trans('lang.new') }}</span>
                                                            @elseif ($ride->status == 'on ride')
                                                                <span class="badge badge-warning">{{ trans('lang.on_ride') }}</span>
                                                            @elseif ($ride->status == 'confirmed')
                                                                <span class="badge badge-success">{{ trans('lang.confirmed') }}</span>
                                                            @elseif ($ride->status == 'canceled')
                                                                <span class="badge badge-danger">{{ trans('lang.canceled') }}</span>
                                                            @elseif ($ride->status == 'completed')
                                                                <span class="badge badge-success">{{ trans('lang.completed') }}</span>
                                                            @elseif ($ride->status == 'rejected')
                                                                <span class="badge badge-danger">{{ trans('lang.rejected') }}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ $ride->status }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d F Y h:i A', strtotime($ride->created_at)) }}</td>
                                                        <td class="action-btn ec-actions">
                                                            <a href="{{ route('rental-orders.show', ['id' => $ride->id]) }}" class="ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {{ $rental->appends(['tab' => 'rental'])->links('pagination.pagination') }}
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
