@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.contact_us') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_contact_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

            <div class="error_top"></div>

            <form action="{{ route('general-settings.update', ['id' => $settings->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- tells the controller which columns this page owns --}}
                <input type="hidden" name="_section" value="contact">
                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-body">
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                                    <div class="ec-section">
                                        <h6 class="ec-section-title">{{trans('lang.contact_us')}}</h6>
                                        <div class="form-group row width-50">
                                            <label
                                                class="col-3 control-label">{{trans('lang.contact_us_email')}}</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control contact_us_email"
                                                    name="contact_us_email" id="contact_us_email"
                                                    value="{{$settings->contact_us_email}}">
                                            </div>
                                        </div>
                                        <div class="form-group row width-50">
                                            <label
                                                class="col-3 control-label">{{trans('lang.contact_us_phone')}}</label>
                                            <div class="col-7">
                                                <input type="number" class="form-control contact_us_phone"
                                                    name="contact_us_phone" id="contact_us_phone"
                                                    value="{{$settings->contact_us_phone}}">
                                            </div>
                                        </div>
                                        <div class="form-group row width-50 ec-span-half">
                                            <label
                                                class="col-3 control-label">{{trans('lang.contact_us_address')}}</label>
                                            <div class="col-7">
                                                <textarea class="form-control contact_us_address" rows="3"
                                                    name="contact_us_address"
                                                    id="contact_us_address">{{$settings->contact_us_address}}</textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row width-50">
                                            <label class="col-4 control-label">{{trans('lang.default_country_code')}}</label>
                                            <div class="col-7">                                                
                                                <div class="country-box">
                                                    <select name="country" id="country" class="form-control defaultCountryCode">
                                                        {{-- With nothing stored the browser selects the first country, so
                                                             opening the page and saving silently set one. --}}
                                                        <option value="">{{ trans('lang.select') }}</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->code }}" data-code="{{  $country->code }}"
                                                                data-phonecode="+{{ $country->phone }}" {{ old('country_code', $settings->default_country_code) == $country->code ? 'selected' : '' }}>
                                                                {{ $country->libelle }} +({{ $country->phone }})
                                                            </option>
                                                        @endforeach
                                                    </select>                                                    
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
