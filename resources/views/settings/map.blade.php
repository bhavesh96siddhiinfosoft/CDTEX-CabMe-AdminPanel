@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.map_settings') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_map_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

            <div class="error_top"></div>

            <form action="{{ route('general-settings.update', ['id' => $settings->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- tells the controller which columns this page owns --}}
                <input type="hidden" name="_section" value="map">
                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-body">
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                                    <div class="ec-section">
                                        <h6 class="ec-section-title">{{trans('lang.google_map_api_key')}}</h6>

                                        <div class="form-group row width-100">
                                            <label
                                                class="col-3 control-label">{{trans('lang.google_map_api_key')}}</label>
                                            <div class="col-7">
                                                <input type="password" class="form-control address_line1" name="map_key"
                                                    id="map_key" value="{{$settings->google_map_api_key}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-section">
                                        <h6 class="ec-section-title">{{trans('lang.map_redirection')}}</h6>
                                        <div class="form-group row width-100">
                                            <label
                                                class="col-4 control-label">{{trans('lang.select_map_type_for_application')}}</label>
                                            <div class="col-7">
                                                <select name="map_for_app" id="map_for_app"
                                                    class="form-control map_for_app">
                                                    <option value="Google" {{($settings->map_for_application == "Google") ?
        "selected" : ""}} >{{trans("lang.google_maps")}}</option>
                                                    <option value="OSM" {{ ($settings->map_for_application == "OSM" ?
        "selected" : "") }}>{{trans("lang.open_street_map")}}</option>
                                                </select>

                                            </div>
                                            <div class="form-text pl-3 text-muted">
                                                <span><strong>{{trans("lang.note")}} :</strong>
                                                    {{trans("lang.google_map_note")}}<br>
                                                    {{trans("lang.open_street_map_note")}}<br>
                                                    <strong>{{trans("lang.recommended_note")}}</strong></span>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{trans('lang.select_map_type')}}</label>
                                            <div class="col-7">
                                                <select name="map_type" id="map_type" class="form-control map_type">
                                                    <option value="">{{trans("lang.select_type")}}</option>
                                                    <option value="google" {{($settings->mapType == "google") ? "selected"
        : ""}} >{{trans("lang.google_map")}}</option>
                                                    <option value="googleGo" {{ ($settings->mapType == "googleGo" ?
        "selected" : "") }}>{{trans("lang.google_go_map")}}</option>
                                                    <option value="waze" {{ ($settings->mapType == "waze" ? "selected" :
        "") }}>{{trans("lang.waze_map")}}</option>
                                                    <option value="mapswithme" {{ ($settings->mapType == "mapswithme" ?
        "selected" : "") }}>{{trans("lang.mapswithme_map")}}</option>
                                                    <option value="yandexNavi" {{ ($settings->mapType == "yandexNavi" ?
        "selected" : "") }}>{{trans("lang.vandexnavi_map")}}</option>
                                                    <option value="yandexMaps" {{ ($settings->mapType == "yandexMaps" ?
        "selected" : "") }}>{{trans("lang.vandex_map")}}</option>
                                                    <option value="inappmap" {{ ($settings->mapType == "inappmap" ?
        "selected" : "") }}>{{trans("lang.inapp_map")}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row width-100">
                                            <label
                                                class="col-4 control-label">{{trans('lang.driver_location_update')}}</label>
                                            <div class="col-7">
                                                <input name="driver_location_update" id="driver_location_update"
                                                    class="form-control" value="{{$settings->driverLocationUpdate}}">
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
