@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.current_subscriber_list_of') }} {{ $subscriptionPlan->name }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.current_subscriber_list') }}</p>
            </div>
            <a class="btn ec-btn-ghost" href="{{ url('/subscription-plans') }}">
                <i class="mdi mdi-arrow-left"></i> {{ trans('lang.subscription_plans') }}
            </a>
        </div>

        <div class="card ec-card ec-filter-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-filter-variant"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.filters_search') }}</h5>
                </div>
                <a class="btn ec-btn-ghost" href="{{ route('current-subscriber.list', $subscriptionPlan->id) }}">
                    <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                </a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('current-subscriber.list', $subscriptionPlan->id) }}" id="perPageForm">
                    {{-- The controller switches its search SQL on selected_search; the
                         previously-default field is pinned now that the dropdown is gone. --}}
                    <input type="hidden" name="selected_search" value="driver">
                    <div class="row ec-filter-row align-items-end">
                        <div class="col-lg-6 col-md-12 mb-2">
                            <label class="ec-filter-label" for="search">{{ trans('lang.search') }}</label>
                            <div class="ec-search-field">
                                <i class="mdi mdi-magnify"></i>
                                <input type="search" class="form-control" name="search" id="search"
                                       value="{{ request('search') }}" placeholder="{{ trans('lang.search_here') }}"
                                       autocomplete="off" spellcheck="false">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-list">
            <div class="row">
                <div class="col-12">
                    <div class="card ec-card ec-list-card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ trans('lang.current_subscriber_list') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="ec-list-toolbar">
                                <div class="ec-length">
                                    <span>{{ trans('lang.show') }}</span>
                                    <select form="perPageForm" name="per_page" id="per_page" class="form-control">
                                        @foreach ([10, 20, 30, 50, 100] as $size)
                                            <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                        @endforeach
                                    </select>
                                    <span>{{ trans('lang.entries') }}</span>
                                </div>
                                <div class="ec-list-info">
                                    {{ trans('lang.showing') }} {{ $currentSubscribers->firstItem() }}
                                    {{ trans('lang.to_small') }} {{ $currentSubscribers->lastItem() }}
                                    {{ trans('lang.of') }} {{ $currentSubscribers->total() }} {{ trans('lang.entries') }}
                                </div>
                            </div>
                            <div class="table-responsive m-t-10">
                                <table id="subscriptionHistoryTable" class="display nowrap table table-hover ec-list-table"
                                       cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('lang.driver_or_owner') }}</th>
                                            <th>{{ trans('lang.plan_name') }}</th>
                                            <th>{{ trans('lang.plan_type') }}</th>
                                            <th>{{ trans('lang.booking_limit') }}</th>
                                            @if(isset($subscriptionPlan) && $subscriptionPlan->plan_for == 'owner')
                                            <th>{{ trans('lang.driver_limit') }}</th>
                                            <th>{{ trans('lang.vehicle_limit') }}</th>
                                            @endif
                                            <th>{{ trans('lang.plan_expires_at') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($currentSubscribers as $value)
                                        <tr>
                                            <td>
                                                @if($value->role == 'owner' && !empty($value->id))
                                                    <a href="{{ route('owners.show', ['id' => $value->id]) }}">
                                                        {{ $value->prenom }} {{ $value->nom }}
                                                    </a>
                                                @elseif(!empty($value->id))
                                                    <a href="{{ route('driver.show', ['id' => $value->id]) }}">
                                                        {{ $value->prenom }} {{ $value->nom }}
                                                    </a>
                                                @else
                                                    {{ $value->prenom }} {{ $value->nom }}
                                                @endif
                                            </td>
                                            <td>{{ $value->subscription_plan['name'] }}</td>
                                            <td>{{ $value->subscription_plan['type'] }}</td>
                                            <td>
                                                <span>{{ trans('lang.total') }} :
                                                    {{ $value->subscription_plan['bookingLimit'] == '-1' ? trans('lang.unlimited') : $value->subscription_plan['bookingLimit'] }}</span><br>
                                                <span>{{ trans('lang.available') }} :
                                                    {{ $value->subscriptionTotalOrders == '-1' ? trans('lang.unlimited') : $value->subscriptionTotalOrders }}</span>
                                            </td>
                                            @if(isset($subscriptionPlan) && $subscriptionPlan->plan_for == 'owner')
                                            <td>
                                                <span>{{ trans('lang.total') }} :
                                                    {{ $value->subscription_plan['driver_limit'] == '-1' ? trans('lang.unlimited') : $value->subscription_plan['driver_limit'] }}</span><br>
                                                <span>{{ trans('lang.available') }} : {{ $value->subscriptionTotalDriver }}</span>
                                            </td>
                                            <td>
                                                <span>{{ trans('lang.total') }} :
                                                    {{ $value->subscription_plan['vehicle_limit'] == '-1' ? trans('lang.unlimited') : $value->subscription_plan['vehicle_limit'] }}</span><br>
                                                <span>{{ trans('lang.available') }} : {{ $value->subscriptionTotalVehicle }}</span>
                                            </td>
                                            @endif
                                            <td>
                                                @if ($value->subscriptionExpiryDate == null)
                                                    {{ trans('lang.unlimited') }}
                                                @else
                                                    <span class="date">{{ date('d F Y', strtotime($value->subscriptionExpiryDate)) }}</span>
                                                    <span class="time">{{ date('h:i A', strtotime($value->subscriptionExpiryDate)) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="{{ isset($subscriptionPlan) && $subscriptionPlan->plan_for == 'owner' ? 7 : 5 }}" align="center">{{ trans('lang.no_result') }}</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end align-items-center mb-3">
                                    <div>{{ $currentSubscribers->links('pagination.pagination') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
