@extends('layouts.app')
@section('content')
<div class="page-wrapper form-page">
    {{-- Page head, matching every other converted screen. --}}
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
                                style="display: none;">{{trans('lang.processing')}}</div>
                            <div class="row restaurant_payout_create">
                                <div class="restaurant_payout_create-inner">
                                    <div class="ec-section">
                                        <h6 class="ec-section-title">{{trans('lang.app_setting_payfast')}}
                                        </h6>
                                        @foreach($payfast as $data)
                                            <div class="form-check width-100">
                                                <input type="hidden" class="id" id="id" value="{{$data->id}}">
                                                <input type="checkbox" class="enable_payfast" id="enable_payfast"
                                                    value="{{$data->isEnabled}}" @if($data->isEnabled == 'true')
                                                    checked=checked @endif>
                                                <label class="col-3 control-label"
                                                    for="enable_payfast">{{trans('lang.active')}}</label>
                                            </div>
                                            <div class="form-check width-100">
                                                <input type="checkbox" class="sandbox_enable" id="sandbox_enable"
                                                    value="{{$data->isSandboxEnabled}}" @if($data->isSandboxEnabled == 'true')
                                                    checked=checked @endif>
                                                <label class="col-3 control-label"
                                                    for="sandbox_enable">{{trans('lang.sandbox_enable_payfast')}}</label>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label
                                                    class="col-3 control-label">{{trans('lang.app_setting_payfast_key')}}</label>
                                                <div class="col-7">
                                                    <input type="password" class="form-control stripe_key"
                                                        value="{{ $data->merchant_key }}">
                                                    <div class="form-text text-muted">
                                                        {!! trans('lang.app_setting_payfast_key_help') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label
                                                    class="col-3 control-label">{{trans('lang.app_setting_payfast_merchant_id')}}</label>
                                                <div class="col-7">
                                                    <input type="password" class=" col-7 form-control payfast_secret"
                                                        value="{{ $data->merchant_Id }}">
                                                    <div class="form-text text-muted">
                                                        {!! trans('lang.app_setting_payfast_merchant_id_help') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label
                                                    class="col-3 control-label">{{trans('lang.app_setting_payfast_cancel_url')}}</label>
                                                <div class="col-7">
                                                    <input type="text" class=" col-7 form-control payfast_cancel_url"
                                                        value="{{ $data->cancel_url }}">
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label
                                                    class="col-3 control-label">{{trans('lang.app_setting_payfast_notify_url')}}</label>
                                                <div class="col-7">
                                                    <input type="text" class=" col-7 form-control payfast_notify_url"
                                                        value="{{ $data->notify_url }}">
                                                </div>
                                            </div>
                                            <div class="form-group row width-100">
                                                <label
                                                    class="col-3 control-label">{{trans('lang.app_setting_payfast_return_url')}}</label>
                                                <div class="col-7">
                                                    <input type="text" class=" col-7 form-control payfast_return_url"
                                                        value="{{ $data->return_url }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @foreach($stripe as $stripe)
                                        <input style="display:none" type="checkbox" class="enable_stripe"
                                            value="{{$stripe->isEnabled}}" id="enable_stripe"
                                            @if($stripe->isEnabled == "true") checked @endif>
                                    @endforeach 
                                    @foreach($cods as $cods)
                                        <input style="display:none" type="checkbox" class="enable_cod"
                                            value="{{$cods->isEnabled}}" id="enable_cod" @if($cods->isEnabled == "true")
                                            checked @endif>
                                    @endforeach 
                                    @foreach($paypal as $paypal)
                                        <input style="display:none" type="checkbox" class="enable_paypal"
                                            value="{{$paypal->isEnabled}}" id="enable_paypal"
                                            @if($paypal->isEnabled == "true") checked @endif>
                                    @endforeach 
                                    @foreach($razorpay as $razorpay)
                                        <input style="display:none" type="checkbox" class="enable_razor"
                                            value="{{$razorpay->isEnabled}}" id="enable_razor"
                                            @if($razorpay->isEnabled == "true") checked @endif>
                                    @endforeach 
                                    @foreach($wallet as $wallet)
                                        <input style="display:none" type="checkbox" class="enable_wallet"
                                            value="{{$wallet->isEnabled}}" id="enable_wallet"
                                            @if($wallet->isEnabled == "true") checked @endif>
                                    @endforeach 
                                    @foreach($paystack as $paystack)
                                        <input style="display:none" type="checkbox" class="enable_paystack"
                                            value="{{$paystack->isEnabled}}" id="enable_paystack"
                                            @if($paystack->isEnabled == "true") checked @endif>
                                    @endforeach 
                                    @foreach($flutterwave as $flutterwave)
                                        <input style="display:none" type="checkbox" class="enable_flutterwave"
                                            value="{{$flutterwave->isEnabled}}" id="enable_flutterwave"
                                            @if($flutterwave->isEnabled == "true") checked @endif>
                                    @endforeach 
                                    @foreach($mercadopago as $mercadopago)
                                        <input style="display:none" type="checkbox" class="enable_mercadopago"
                                            value="{{$mercadopago->isEnabled}}" id="enable_mercadopago"
                                            @if($mercadopago->isEnabled == "true") checked @endif>
                                    @endforeach 
                                    @foreach($applePay as $applePay)
                                        <input style="display:none" type="checkbox" class="enable_applePay"
                                            value="{{$applePay->isEnabled}}" id="enable_applePay"
                                            @if($applePay->isEnabled == "true") checked @endif>
                                    @endforeach
                                    @foreach($orangepay as $orangepay)
                                        <input style="display:none" type="checkbox" class="enable_orangepay"
                                            value="{{$orangepay->isEnabled}}" id="enable_orangepay"
                                            @if($orangepay->isEnabled == "true") checked @endif>
                                    @endforeach 
                                    @foreach($xendit as $xendit)
                                        <input style="display:none" type="checkbox" class="enable_xendit"
                                            value="{{$xendit->isEnabled}}" id="enable_xendit"
                                            @if($xendit->isEnabled == "true") checked @endif>
                                    @endforeach  
                            @foreach($midtrans as $midtrans)
                                <input style="display:none" type="checkbox" class="enable_midtrans"
                                    value="{{$midtrans->isEnabled}}" id="enable_midtrans"
                                    @if($midtrans->isEnabled == "true") checked @endif>
                            @endforeach 
                                </div>
                            </div>
                        </div>
            <div class="ec-info-note">
                <i class="mdi mdi-information-outline"></i>
                <span>{!! trans('lang.payment_currency_note', ['gateway' => 'PayFast', 'url' => 'https://developers.payfast.co.za/docs']) !!}</span>
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
    var isStripeEnabled = $(".enable_stripe").val();
    var isRazorpayenabled = $(".enable_razor").val();
    var isCodenabled = $(".enable_cod").val();
    var isPaypalenabled = $(".enable_paypal").val();
    var isPayfastenabled = $(".enable_payfast").val();
    var isPaystackenabled = $(".enable_paystack").val();
    var isflutterwaveenabled = $(".enable_flutterwave").is(":checked");
    var isWalletenabled = $(".enable_wallet").val();
    var ismercadoenabled = $(".enable_mercadopago").val();
    var isapplepayenabled = $(".enable_applePay").val();
    var isxenditenabled = $('.enable_xendit').val();
    var isorangePayenabled = $('.enable_orangepay').val();
    var ismidTransenabled = $('.enable_midtrans').val();
    $(document).ready(function () {
        try {
            if (isStripeEnabled == 'true') {
                jQuery(".stripe_active_label span").addClass('badge-success');
                jQuery(".stripe_active_label span").text('Active');
            }
            if (isRazorpayenabled == 'true') {
                jQuery(".razorpay_active_label span").addClass('badge-success');
                jQuery(".razorpay_active_label span").text('Active');
            }
            if (isCodenabled == 'true') {
                jQuery(".cod_active_label span").addClass('badge-success');
                jQuery(".cod_active_label span").text('Active');
            }
            if (isPaypalenabled == 'true') {
                jQuery(".paypal_active_label span").addClass('badge-success');
                jQuery(".paypal_active_label span").text('Active');
            }
            if (isPayfastenabled == 'true') {
                jQuery(".PayFast_active_label span").addClass('badge-success');
                jQuery(".PayFast_active_label span").text('Active');
            }
            if (isPaystackenabled == 'true') {
                jQuery(".PayStack_active_label span").addClass('badge-success');
                jQuery(".PayStack_active_label span").text('Active');
            }
            if (isflutterwaveenabled == true) {
                jQuery(".FlutterWave_active_label span").addClass('badge-success');
                jQuery(".FlutterWave_active_label span").text('Active');
            }
            if (isWalletenabled == 'true') {
                jQuery(".wallet_active_label span").addClass('badge-success');
                jQuery(".wallet_active_label span").text('Active');
            }
            if (ismercadoenabled == 'true') {
                jQuery(".mercadopago_active_label span").addClass('badge-success');
                jQuery(".mercadopago_active_label span").text('Active');
            }
            if (isapplepayenabled == 'true') {
                jQuery(".apple_pay_active_label span").addClass('badge-success');
                jQuery(".apple_pay_active_label span").text('Active');
            }
            if (isorangePayenabled == 'true') {
                jQuery(".orangepay_active_label span").addClass('badge-success');
                jQuery(".orangepay_active_label span").text('Active');
            }
            if (ismidTransenabled == 'true') {
                jQuery(".midtrans_active_label span").addClass('badge-success');
                jQuery(".midtrans_active_label span").text('Active');
            }
            if (isxenditenabled == 'true') {
                jQuery(".xendit_active_label span").addClass('badge-success');
                jQuery(".xendit_active_label span").text('Active');
            }
        } catch (error) {
        }
    });
    $(".edit-setting-btn").click(function () {
        var sandbox_enable = $(".sandbox_enable").is(":checked");
        var enable_payfast = $(".enable_payfast").is(":checked");
        var stripe_key = $(".stripe_key").val();
        var payfast_secret = $(".payfast_secret").val();
        var payfast_notify_url = $(".payfast_notify_url").val();
        var payfast_cancel_url = $(".payfast_cancel_url").val();
        var payfast_return_url = $(".payfast_return_url").val();
        var id = $('.id').val();
        var url = "{{ route('payment.payfastUpdate', ':id') }}";
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            type: 'PUT',
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}',
            },
            //dataType: "json",
            data: {
                merchant_Id: payfast_secret,
                merchant_key: stripe_key,
                cancel_url: payfast_cancel_url,
                notify_url: payfast_notify_url,
                return_url: payfast_return_url,
                isEnabled: enable_payfast,
                isSandboxEnabled: sandbox_enable
            },
            success: function (response) {
                window.location.reload();
            },
            error: function (response) {
                console.log(response);
            },
        });
    })
</script>
@endsection