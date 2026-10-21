@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.service_type_settings') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_services_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

            <div class="error_top"></div>

            <form action="{{ route('general-settings.update', ['id' => $settings->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- tells the controller which columns this page owns --}}
                <input type="hidden" name="_section" value="services">
                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-body">
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                                    <div class="ec-section">
                                        
                                        <h6 class="ec-section-title">{{trans('lang.service_type_settings')}}</h6>
                                        <div class="form-group row width-100">
                                            <div class="form-check">
                                                <input type="checkbox" id="ride_service" value="ride"
                                                    name="active_services[]" {{ in_array('ride', $active_services) ? 'checked' : '' }}>
                                                <label class="col-3 control-label"
                                                    for="ride_service">{{trans('lang.ride_service')}}</label>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <div class="form-check">
                                                <input type="checkbox" id="parcel_service" value="parcel"
                                                    name="active_services[]" {{ in_array('parcel', $active_services) ? 'checked' : '' }}>
                                                <label class="col-3 control-label"
                                                    for="parcel_service">{{trans('lang.parcel_service')}}</label>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <div class="form-check">
                                                <input type="checkbox" id="rental_service" value="rental"
                                                    name="active_services[]" {{ in_array('rental', $active_services) ? 'checked' : '' }}>
                                                <label class="col-3 control-label"
                                                    for="rental_service">{{trans('lang.rental_service')}}</label>
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
