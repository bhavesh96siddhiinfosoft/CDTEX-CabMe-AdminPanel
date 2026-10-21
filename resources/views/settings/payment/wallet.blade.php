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
                                        <h6 class="ec-section-title">{{trans('lang.app_setting_wallet')}}</h6>
                                        @foreach($wallet as $data)
                                            <div class="form-check width-100">
                                                <input type="hidden" class="id" id="id" value="{{$data->id}}">
                                                <input type="checkbox" class=" enable_wallet" id="enable_wallet"
                                                    value="{{$data->isEnabled}}" @if($data->isEnabled == 'true')
                                                    checked=checked @endif>
                                                <label class="col-3 control-label"
                                                    for="enable_wallet">{{trans('lang.app_setting_enable_wallet')}}</label>
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
                                    @foreach($payfast as $payfast)
                                        <input style="display:none" type="checkbox" class="enable_payfast"
                                            value="{{$payfast->isEnabled}}" id="enable_payfast"
                                            @if($payfast->isEnabled == "true") checked @endif>
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
    var isStripeEnabled = $(".enable_stripe").is(":checked");
    var isRazorpayenabled = $(".enable_razor").is(":checked");
    var isCodenabled = $(".enable_cod").is(":checked");
    var isPaypalenabled = $(".enable_paypal").is(":checked");
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
            if (isStripeEnabled) {
                jQuery(".stripe_active_label span").addClass('badge-success');
                jQuery(".stripe_active_label span").text('Active');
            }
            if (isRazorpayenabled) {
                jQuery(".razorpay_active_label span").addClass('badge-success');
                jQuery(".razorpay_active_label span").text('Active');
            }
            if (isCodenabled) {
                jQuery(".cod_active_label span").addClass('badge-success');
                jQuery(".cod_active_label span").text('Active');
            }
            if (isPaypalenabled) {
                jQuery(".paypal_active_label span").addClass('badge-success');
                jQuery(".paypal_active_label span").text('Active');
            }
            if (isPayfastenabled) {
                jQuery(".PayFast_active_label span").addClass('badge-success');
                jQuery(".PayFast_active_label span").text('Active');
            }
            if (isPaystackenabled) {
                jQuery(".PayStack_active_label span").addClass('badge-success');
                jQuery(".PayStack_active_label span").text('Active');
            }
            if (isflutterwaveenabled) {
                jQuery(".FlutterWave_active_label span").addClass('badge-success');
                jQuery(".FlutterWave_active_label span").text('Active');
            }
            if (isWalletenabled) {
                jQuery(".wallet_active_label span").addClass('badge-success');
                jQuery(".wallet_active_label span").text('Active');
            }
            if (ismercadoenabled) {
                jQuery(".mercadopago_active_label span").addClass('badge-success');
                jQuery(".mercadopago_active_label span").text('Active');
            }
            if (isapplepayenabled) {
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
        var isenabled = $(".enable_wallet").is(":checked");
        var id = $('.id').val();
        var url = "{{ route('payment.walletUpdate', ':id') }}";
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            type: 'PUT',
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}',
            },
            //dataType: "json",
            data: {
                isEnabled: isenabled,
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