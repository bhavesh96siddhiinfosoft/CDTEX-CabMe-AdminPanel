@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.surge_zone_settings') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_surge_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

            <div class="error_top"></div>

            <form action="{{ route('general-settings.update', ['id' => $settings->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- tells the controller which columns this page owns --}}
                <input type="hidden" name="_section" value="surge">
                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-body">
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                                    <div class="ec-section">
                                        <h6 class="ec-section-title">{{ trans('lang.surge_zone_settings') }}</h6>
                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{ trans('lang.surge_zone_radius') }}</label>
                                            <div class="col-7">
                                                <input name="surge_zone_radius" id="surge_zone_radius" class="form-control" type="number" step="0.1" min="0.1" value="{{ $settings->surge_zone_radius }}">
                                                <div class="form-text text-muted">{{ trans('lang.surge_zone_radius_help') }}</div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{ trans('lang.surge_zone_ride_count') }}</label>
                                            <div class="col-7">
                                                <input name="surge_zone_ride_count" id="surge_zone_ride_count" class="form-control" type="number" min="1" value="{{ $settings->surge_zone_ride_count }}">
                                                <div class="form-text text-muted">{{ trans('lang.surge_zone_ride_count_help') }}</div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{ trans('lang.surge_zone_selection_minutes') }}</label>
                                            <div class="col-7">
                                                <input name="surge_zone_selection_minutes" id="surge_zone_selection_minutes" class="form-control" type="number" min="1" value="{{ $settings->surge_zone_selection_minutes }}">
                                                <div class="form-text text-muted">{{ trans('lang.surge_zone_selection_minutes_help') }}</div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{ trans('lang.surge_zone_duration_minutes') }}</label>
                                            <div class="col-7">
                                                <input name="surge_zone_duration_minutes" id="surge_zone_duration_minutes" class="form-control" type="number" min="1" value="{{ $settings->surge_zone_duration_minutes }}">
                                                <div class="form-text text-muted">{{ trans('lang.surge_zone_duration_minutes_help') }}</div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{ trans('lang.surge_zone_surge_percentage') }}</label>
                                            <div class="col-7">
                                                <input name="surge_zone_surge_percentage" id="surge_zone_surge_percentage" class="form-control" type="number" step="0.1" min="0" value="{{ $settings->surge_zone_surge_percentage }}">
                                                <div class="form-text text-muted">{{ trans('lang.surge_zone_surge_percentage_help') }}</div>
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
