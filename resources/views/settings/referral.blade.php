@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.referral_settings') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_referral_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

            <div class="error_top"></div>

            <form action="{{ route('general-settings.update', ['id' => $settings->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- tells the controller which columns this page owns --}}
                <input type="hidden" name="_section" value="referral">
                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-body">
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                                    <div class="ec-section">
                                        <h6 class="ec-section-title">{{trans('lang.referral_settings')}}</h6>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">{{trans('lang.referral_amount')}}</label>

                                            <div class="col-7">
                                                <div class="control-inner">
                                                    <input type="number" class="form-control referral_amount"
                                                        name="referral_amount" value="{{$settings->referral_amount}}">
                                                    <span class="currentCurrency">{{$currency->symbole}}</span>
                                                    <div class="form-text text-muted">
                                                        {{ trans("lang.referral_amount_help") }}
                                                    </div>
                                                </div>
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
