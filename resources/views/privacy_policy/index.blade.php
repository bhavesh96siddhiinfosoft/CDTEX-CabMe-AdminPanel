@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.privacy_policy') }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card ec-card pb-4">
                    <div class="card-body">
                        <div class="error_top"></div>
                        <div class="row restaurant_payout_create term-cond">
                            <div class="restaurant_payout_create-inner">
                                <fieldset>
                                    <legend>{{trans('lang.privacy_policy')}}</legend>
                                    <div class="form-group row ">
                                        <div class="col-12 p-0">
                                            <input type="hidden" name="id" id="id" value="{{$privacyPolicy->id}}">
                                            <textarea class="form-control col-7" name="privacy_policy" id="privacy_policy">{{$privacyPolicy->privacy_policy}}</textarea>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="ec-form-actions">
                            <button type="button" class="btn ec-btn-brand edit-setting-btn">{{ trans('lang.save') }}</button>
                            <a href="{{ route('settings') }}" class="btn ec-btn-ghost">{{ trans('lang.back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $('#privacy_policy').summernote({
        height: 400,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['forecolor', ['forecolor']],
            ['backcolor', ['backcolor']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });
    $(".edit-setting-btn").click(function() {
        var privacy_policy = $('#privacy_policy').summernote('code');
        var id = $('#id').val();
        var url = "{{ route('privacy-policy.update',':id') }}";
        url = url.replace(':id', id);
        if (privacy_policy == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.user_firstname_error')}}</p>");
            window.scrollTo(0, 0);
        } else {
            $.ajax({
                url: url,
                type: 'PUT',
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
                //dataType: "json",
                data: {
                    privacy_policy: privacy_policy,
                },
                // Saved over ajax, so a server-side flash would be consumed by this
                // request rather than the page — report it here instead. No reload:
                // the editor already holds what was saved, and it would kill the toast.
                success: function(response) {
                    toastr.success('{{ trans('lang.settings_have_been_saved_successfully') }}');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    toastr.error('{{ trans('lang.something_went_wrong') }}');
                },
            });
        }
    })
</script>
@endsection