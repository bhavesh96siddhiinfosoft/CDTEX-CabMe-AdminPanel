@extends('layouts.app')
@section('content')
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.edit_on_boarding') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.onboarding_edit_desc') }}</p>
                </div>
                <a href="{!! route('on-boarding.index') !!}" class="btn ec-btn-ghost">
                    <i class="mdi mdi-arrow-left"></i> {{ trans('lang.on_boarding') }}
                </a>
            </div>

            <form action="{{ route('on-boarding.update', $onboarding->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card ec-card ec-form-card pb-4">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-clipboard-text-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.on_board_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="error_top"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.title') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control title" name="title" value="{{ $onboarding->title }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.onboarding_type') }}</label>
                                <select class="form-control type" name="type">
                                    <option value="Customer" {{ $onboarding->type == 'Customer' ? 'selected' : '' }}>{{ trans('lang.customer') }}</option>
                                    <option value="Driver" {{ $onboarding->type == 'Driver' ? 'selected' : '' }}>{{ trans('lang.driver') }}</option>
                                </select>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.description') }} <span class="text-danger">*</span></label>
                                <textarea rows="6" id="description" class="description form-control" name="description">{{ $onboarding->description }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.photo') }}</label>
                                <label class="ec-upload">
                                    <input type="file" name="image" onchange="readURL(this);">
                                    <i class="mdi mdi-cloud-upload-outline"></i>
                                    <span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span>
                                    <span class="ec-upload-hint">PNG / JPG</span>
                                </label>
                                <div class="mt-2">
                                    @if (!empty($onboarding->image) && file_exists(public_path('assets/images/onboarding/' . $onboarding->image)))
                                        <div class="placeholder_img_thumb mt-2"><img class="rounded ec-doc-thumb" id="uploding_image" src="{{ asset('assets/images/onboarding') . '/' . $onboarding->image }}" alt="image"></div>
                                    @else
                                        <div class="placeholder_img_thumb mt-2"><img class="rounded ec-doc-thumb" id="uploding_image" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ec-form-actions">
                        <button type="submit" class="btn ec-btn-brand edit-form-btn">{{ trans('lang.save') }}</button>
                        <a href="{!! route('on-boarding.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $(".shadow-sm").hide();
    })
    function readURL(input) {
        if(input.files&&input.files[0]) {
            var reader=new FileReader();
            reader.onload=function(e) {
                $('#image_preview').show();
                $('#uploding_image').attr('src',e.target.result);
            }
           reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
