@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.version') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_version_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

            <div class="error_top"></div>

            <form action="{{ route('general-settings.update', ['id' => $settings->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- tells the controller which columns this page owns --}}
                <input type="hidden" name="_section" value="version">
                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-body">
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                                    <div class="ec-section">
                                        <h6 class="ec-section-title">{{trans('lang.version')}}</h6>
                                        <div class="form-group row width-50">
                                            <label class="col-3 control-label">{{trans('lang.app_version')}}</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control app_version" name="app_version"
                                                    id="app_version" value="{{$settings->app_version}}">
                                            </div>
                                        </div>
                                        <div class="form-group row width-50">
                                            <label class="col-3 control-label">{{trans('lang.web_version')}}</label>
                                            <div class="col-7">
                                                {{-- no .web_version class: that one styles the version badge in the
                                                     sidebar (centred, white, bold) and was centring this input. --}}
                                                <input type="text" class="form-control" name="web_version"
                                                    id="web_version" value="{{$settings->web_version}}">
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
