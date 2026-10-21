@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.notification_setting') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_notifications_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

            <div class="error_top"></div>

            <form action="{{ route('general-settings.update', ['id' => $settings->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- tells the controller which columns this page owns --}}
                <input type="hidden" name="_section" value="notifications">
                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-body">
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                                    <div class="ec-section">
                                        <h6 class="ec-section-title">{{trans('lang.notification_setting')}}</h6>
                                        <div class="form-group row width-100">
                                            <label class="col-5 control-label">{{trans('lang.sender_id')}}</label>
                                            <div class="col-7">
                                                <input type="password" class="form-control" name="senderId"
                                                    value="{{$settings->senderId}}">
                                            </div>
                                            <div class="form-text pl-3 text-muted">
                                                {{ trans("lang.notification_sender_id_help") }}
                                            </div>
                                        </div>

                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">{{trans('lang.upload_json_file')}}</label>
                                            <label class="ec-upload">
                                                <input type="file" name="serviceJson">
                                                <i class="mdi mdi-cloud-upload-outline"></i>
                                                <span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span>
                                                <span class="ec-upload-hint">JSON</span>
                                            </label>
                                            @if(! empty($settings->serviceJson) && Storage::disk('local')->has('firebase/credentials.json'))
                                            <div id="uploded_json_file" class="btn-link pl-3">
                                                <span class="text-green">{{ trans('lang.file_uploaded') }}</span>
                                            </div>
                                            @endif
                                            <div class="form-text pl-3 text-muted">
                                                {{ trans("lang.notification_json_file_help") }}
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
