@extends('layouts.app')
@section('content')
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.edit_email_template') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.email_template_edit_desc') }}</p>
                </div>
                <a href="{!! route('email-template.index') !!}" class="btn ec-btn-ghost">
                    <i class="mdi mdi-arrow-left"></i> {{ trans('lang.email_template_plural') }}
                </a>
            </div>

            @php
                $typeLabels = [
                    'payment_receipt' => trans('lang.payment_receipt'),
                    'wallet_topup' => trans('lang.wallet_topup'),
                    'payout_approve_disapprove' => trans('lang.payout_approve_disapprove'),
                    'payout_request' => trans('lang.payout_request'),
                    'new_registration' => trans('lang.new_registration'),
                    'reset_password' => trans('lang.reset_password'),
                    'parcel_payment_receipt' => trans('lang.parcel_payment_receipt'),
                    'rental_payment_receipt' => trans('lang.rental_payment_receipt'),
                ];
                $tags = ['{username}' => trans('lang.tag_user_name')];
                $byType = [
                    'payment_receipt' => ['{Distance}' => 'tag_distance', '{Duree}' => 'tag_duration', '{Subtotal}' => 'tag_subtotal', '{Discount}' => 'tag_discount', '{Tax}' => 'tag_tax', '{Tip}' => 'tag_tip', '{Total}' => 'tag_total', '{Date}' => 'tag_date', '{AppName}' => 'tag_app_name'],
                    'wallet_topup' => ['{Amount}' => 'tag_amount', '{Date}' => 'tag_date', '{PaymentMethod}' => 'tag_payment_method', '{TransactionId}' => 'tag_transaction_id', '{Balance}' => 'tag_balance', '{AppName}' => 'tag_app_name'],
                    'payout_approve_disapprove' => ['{RequestId}' => 'tag_request_id', '{Status}' => 'tag_status', '{Amount}' => 'tag_amount', '{Date}' => 'tag_date', '{AppName}' => 'tag_app_name'],
                    'payout_request' => ['{PayoutRequestId}' => 'tag_request_id', '{AppName}' => 'tag_app_name', '{UserId}' => 'tag_userid', '{Amount}' => 'tag_amount', '{Date}' => 'tag_date', '{UserContactInfo}' => 'tag_contact_info'],
                    'new_registration' => ['{AppName}' => 'tag_app_name', '{UserId}' => 'tag_userid', '{UserEmail}' => 'tag_user_email', '{UserPhone}' => 'tag_user_phone', '{Date}' => 'tag_date'],
                    'reset_password' => ['{OTP}' => 'tag_otp'],
                    'parcel_payment_receipt' => ['{AppName}' => 'tag_app_name', '{Subtotal}' => 'tag_subtotal', '{Discount}' => 'tag_discount', '{Tax}' => 'tag_tax', '{Total}' => 'tag_total', '{Date}' => 'tag_date'],
                    'rental_payment_receipt' => ['{AppName}' => 'tag_app_name', '{Subtotal}' => 'tag_subtotal', '{Discount}' => 'tag_discount', '{Tax}' => 'tag_tax', '{Total}' => 'tag_total', '{Date}' => 'tag_date'],
                ];
                foreach ($byType[$template->type] ?? [] as $tag => $key) {
                    $tags[$tag] = trans('lang.' . $key);
                }
            @endphp

            <form action="{{ route('email-template.update', $template->id) }}" method="post" enctype="multipart/form-data" id="edit_template">
                @csrf
                @method('PUT')
                <div class="card ec-card ec-form-card">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-email-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.email_template') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="error_top" style="display:none"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.template_type') }}</label>
                                <input type="text" class="form-control" name="type" id="type" value="{{ $typeLabels[$template->type] ?? $template->type }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.subject') }}</label>
                                <input type="text" class="form-control" name="subject" id="subject" value="{{ $template->subject }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="ec-form-label">{{ trans('lang.message') }}</label>
                                <textarea class="form-control" name="message" id="message">{{ $template->message }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="ec-form-label">{{ trans('lang.available_tags') }}</label>
                                <div class="ec-tag-list">
                                    @foreach ($tags as $tag => $label)
                                        <span class="ec-tag-item"><code>{{ $tag }}</code> {{ $label }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="switch-box switch-box-tight">
                                    <label class="ec-form-label" for="send_admin">{{ trans('lang.is_send_to_admin') }}</label>
                                    <label class="switch">
                                        <input type="checkbox" class="send_admin" id="send_admin" name="send_admin" {{ $template->send_to_admin ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ec-form-actions">
                        <button type="submit" class="btn ec-btn-brand edit-setting-btn">{{ trans('lang.save') }}</button>
                        <a href="{{ route('email-template.index') }}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    $('#message').summernote({
        height: 400,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['forecolor', ['forecolor']],
            ['backcolor', ['backcolor']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['view', ['codeview', 'help']],
        ]
    });
    $('#edit_template').on('submit', function() {
        var editor = $('#message');
        if (editor.summernote('codeview.isActivated')) {
            editor.summernote('code', editor.summernote('code'));
        }
    });
</script>
@endsection
