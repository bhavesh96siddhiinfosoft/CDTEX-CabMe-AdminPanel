@extends('layouts.app')
@section('content')
{{-- MTN MoMo. Added alongside the other gateways cabme was missing next to
     foodie; the row is payment_method mtnmomo and its credentials live in the
     columns named in SettingsController::EXTRA_GATEWAYS. --}}
<div class="page-wrapper form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.administration_payment_methods') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_payment_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

        @include('settings.payment.tabs')

        <div class="card ec-card ec-form-card">
            <div class="card-body">
                <div id="data-table_processing" class="dataTables_processing panel panel-default"
                    style="display: none;">{{ trans('lang.processing') }}</div>
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <div class="ec-section">
                            <h6 class="ec-section-title">{{ trans('lang.app_setting_mtnmomo') }}</h6>
                            @foreach ($gateway as $data)
                                <div class="form-check width-100">
                                    <input type="hidden" class="id" id="id" value="{{ $data->id }}">
                                    <input type="checkbox" class="enable_gateway" id="enable_gateway"
                                        value="{{ $data->isEnabled }}" @if ($data->isEnabled == 'true') checked=checked @endif>
                                    <label class="col-3 control-label" for="enable_gateway">{{ trans('lang.active') }}</label>
                                </div>
                                <div class="form-check width-100">
                                    <input type="checkbox" class="sandbox_enable" id="sandbox_enable"
                                        value="{{ $data->isSandboxEnabled }}" @if ($data->isSandboxEnabled == 'true') checked=checked @endif>
                                    <label class="col-3 control-label" for="sandbox_enable">{{ trans('lang.gateway_sandbox_enable') }}</label>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{ trans('lang.gateway_primary_key') }}</label>
                                    <div class="col-7">
                                        <input type="password" class="form-control primary_key" value="{{ $data->key }}">
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{ trans('lang.gateway_secondary_key') }}</label>
                                    <div class="col-7">
                                        <input type="password" class="form-control secondary_key" value="{{ $data->secret_key }}">
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{ trans('lang.gateway_callback_url') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control callback_url" value="{{ $data->callback_url }}">
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{ trans('lang.gateway_target_environment') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control target_environment" value="{{ $data->target_environment }}">
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-3 control-label">{{ trans('lang.gateway_expiry_time_seconds') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control expiry_time_seconds" value="{{ $data->expiry_time_seconds }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="ec-info-note">
                <i class="mdi mdi-information-outline"></i>
                <span>{!! trans('lang.payment_currency_note', ['gateway' => 'MTN MoMo', 'url' => 'https://momodeveloper.mtn.com/']) !!}</span>
            </div>
            <div class="ec-form-actions">
                <button type="button" class="btn ec-btn-brand edit-setting-btn">{{ trans('lang.save') }}</button>
                <a href="{{ route('settings') }}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(".edit-setting-btn").click(function () {
        var url = "{{ route('payment.gatewayUpdate', ['gateway' => 'mtnmomo', 'id' => ':id']) }}";
        url = url.replace(':id', $('.id').val());
        $.ajax({
            url: url,
            type: 'PUT',
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}',
            },
            data: {
                key: $(".primary_key").val(),
                secret_key: $(".secondary_key").val(),
                callback_url: $(".callback_url").val(),
                target_environment: $(".target_environment").val(),
                expiry_time_seconds: $(".expiry_time_seconds").val(),
                isEnabled: $(".enable_gateway").is(":checked"),
                isSandboxEnabled: $(".sandbox_enable").is(":checked")
            },
            success: function (response) {
                toastr.success('{{ trans('lang.settings_have_been_saved_successfully') }}');
            },
            error: function (response) {
                toastr.error('{{ trans('lang.something_went_wrong') }}');
            },
        });
    })
</script>
@endsection
