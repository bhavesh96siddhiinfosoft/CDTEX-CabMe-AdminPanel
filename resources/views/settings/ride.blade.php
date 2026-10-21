@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.ride_settings') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_ride_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

            <div class="error_top"></div>

            <form action="{{ route('general-settings.update', ['id' => $settings->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- tells the controller which columns this page owns --}}
                <input type="hidden" name="_section" value="ride">
                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-body">
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                                    <div class="ec-section">

                                        <h6 class="ec-section-title">{{trans('lang.ride_settings')}}</h6>
                                        <div class="form-group row width-50">
                                            <label class="col-3 control-label">{{trans('lang.driver_radios')}}
                                                ({{$settings->delivery_distance}})</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control user_phone" name="driver_radios"
                                                    id="driver_radios" value="{{$settings->driver_radios}}">
                                            </div>
                                        </div>

                                        <div class="form-group row width-100">
                                            <label
                                                class="col-3 control-label">{{trans('lang.trip_accept_reject_by_driver')}}</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control trip_accept_reject_by_driver"
                                                    name="trip_accept_reject_by_driver"
                                                    id="trip_accept_reject_by_driver"
                                                    value="{{$settings->trip_accept_reject_driver_time_sec}}">
                                            </div>
                                        </div>

                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">{{trans('lang.order_assignment')}}</label>
                                            <div class="col-7">
                                                <select name="driver_order_assignment" id="driver_order_assignment" class="form-control">
                                                    <option value="sequential" @if($settings->driver_order_assignment != 'broadcast') selected @endif>{{trans('lang.sequential_assignment')}}</option>
                                                    <option value="broadcast" @if($settings->driver_order_assignment == 'broadcast') selected @endif>{{trans('lang.broadcast_assignment')}}</option>
                                                </select>
                                                <small class="form-text text-muted d-block mt-2">
                                                    <strong>{{trans('lang.sequential_assignment')}}</strong> &mdash; {{trans('lang.order_assignment_sequential_desc')}}<br>
                                                    <strong>{{trans('lang.broadcast_assignment')}}</strong> &mdash; {{trans('lang.order_assignment_broadcast_desc')}}
                                                </small>
                                            </div>
                                        </div>

                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">{{trans('lang.show_ride_otp')}}</label>
                                            <div class="col-7">
                                                <select name="show_ride_otp" id="show_ride_otp" class="form-control">
                                                    @if($settings->show_ride_otp == 'yes')
                                                    <option value="yes" selected>{{trans('lang.yes')}}</option>
                                                    <option value="no">{{trans('lang.no')}}</option>
                                                    @else
                                                    <option value="no" selected>{{trans('lang.no')}}</option>
                                                    <option value="yes">{{trans('lang.yes')}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-section">
                                        <h6 class="ec-section-title">{{trans('lang.delivery_charge_distance')}}</h6>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">{{trans('lang.distance')}}</label>
                                            <div class="col-7">
                                                <select name="delivery_distance" id="delivery_distance"
                                                    class="form-control">
                                                    @if($settings->delivery_distance == 'Miles')
                                                    <option value="KM">{{trans('lang.km')}}</option>
                                                    <option value="Miles" selected>{{trans('lang.miles')}}</option>
                                                    @else
                                                    <option value="KM" selected>{{trans('lang.km')}}</option>
                                                    <option value="Miles">{{trans('lang.miles')}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                    </div>
                </div>
                </div>

                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-setting-btn">{{ trans('lang.save') }}</button>
                    <a href="{{ route('settings') }}" class="btn ec-btn-ghost">{{ trans('lang.back') }}</a>
                </div>
                </div>
            </form>
    </div>
</div>
@endsection
