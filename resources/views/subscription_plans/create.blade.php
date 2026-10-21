@extends('layouts.app')
@section('content')
    <div class="page-wrapper listings-page form-page">
        
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.create_subscription_plan') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.subscription_create_desc') }}</p>
            </div>
            <a href="{{ url('/dashboard') }}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{ trans('lang.dashboard') }}
            </a>
        </div>

            <div>
                <div class="error_top" style="display:none"></div>
                <div class="success_top" style="display:none"></div>
                <form action="{{ route('subscription-plans.store') }}" method="post" enctype="multipart/form-data"
                    id="create_driver">
                    @csrf
                    
                        
                            <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.plan_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
                                
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label">{{ trans('lang.plan_name') }} <span class="text-danger">*</span></label>
                                    
                                        <input type="text" class="form-control" id="plan_name" name="planName"
                                            placeholder="{{ trans('lang.enter_plan_name') }}"
                                            value="{{ old('planName') }}">
                                    
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label" for="">{{ trans('lang.plan_type') }}</label>
                                    <div class="form-check width-50">
                                        <input type="radio" id="free_type" name="planType" value="free"
                                            {{ old('planType', 'free') == 'free' ? 'checked' : '' }}>
                                        <label class="ec-form-label" for="free_type">{{ trans('lang.free') }}</label>
                                    </div>
                                    <div class="form-check width-50">
                                        <input type="radio" id="paid_type" name="planType" value="paid"
                                            {{ old('planType') == 'paid' ? 'checked' : '' }}>
                                        <label class="ec-form-label" for="paid_type">{{ trans('lang.paid') }}</label>
                                    </div>
                                </div>
                                <div
                                    class="col-12 mb-3 {{ old('planType', 'free') == 'paid' ? '' : 'd-none' }} plan_price_div">
                                    <label class="ec-form-label">{{ trans('lang.plan_price') }}</label>
                                    
                                        <input type="number" class="form-control" id="plan_price" name="planPrice"
                                            placeholder="{{ trans('lang.enter_plan_price') }}"
                                            value="{{ old('planPrice') }}">
                                    
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="ec-form-label">{{ trans('lang.plan_validity_days') }}</label>
                                    <div class="form-check width-100">
                                        <input type="radio" id="unlimited_days" name="plan_validity_days"
                                            value="unlimited"
                                            {{ old('plan_validity_days', 'unlimited') == 'unlimited' ? 'checked' : '' }}>
                                        <label class="ec-form-label"
                                            for="unlimited_days">{{ trans('lang.unlimited') }}</label>
                                    </div>
                                    <div class="d-flex">
                                        <div class="form-check width-50 limited_days_div">
                                            <input type="radio" id="limited_days" name="plan_validity_days"
                                                value="limited"
                                                {{ old('plan_validity_days') == 'limited' ? 'checked' : '' }}>
                                            <label class="ec-form-label"
                                                for="limited_days">{{ trans('lang.limited') }}</label>
                                        </div>
                                        <div
                                            class="form-check width-50 {{ old('plan_validity_days', 'unlimited') == 'limited' ? '' : 'd-none' }} expiry-limit-div">
                                            <input type="number" id="plan_validity" class="form-control"
                                                name="plan_validity" placeholder="{{ trans('lang.ex_365') }}"
                                                value="{{ old('plan_validity') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="ec-form-label">{{ trans('lang.description') }} <span class="text-danger">*</span></label>
                                    
                                        <textarea class="form-control" id="description" name="description" rows="5">{{ old('description') }}</textarea>
                                    
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="ec-form-label">{{ trans('lang.order') }} <span class="text-danger">*</span></label>
                                    
                                        <input type="number" class="form-control" id="order" name="order"
                                            placeholder="{{ trans('lang.enter_display_order') }}"
                                            value="{{ old('order') }}">
                                    
                                </div>

                                <div class="col-12 mb-3 status-div">
                                    <div class="switch-box switch-box-tight">
                                    <label class="control-label">{{ trans('lang.status') }}</label>
                                    <label class="switch"><input type="checkbox" id="status" name="status"
                                            {{ old('status') !== null ? 'checked' : '' }}><span class="slider round"></span></label>
                                </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label">{{ trans('lang.image') }} <span class="text-danger">*</span></label>
                                    
                                        <label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}"><input type="file" name="image" onchange="readURL(this);"><i class="mdi mdi-cloud-upload-outline"></i><span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span><span class="ec-upload-hint">PNG / JPG</span></label>
                                        <div id="image_preview" style="display: none; padding-left: 15px;">
                                            <img class="rounded" style="width:50px" id="uploding_image" src="#" alt="image">
                                        </div>
                                    
                                    <div class="placeholder_img_thumb plan_image"></div>
                                    <div id="uploding_image"></div>
                                </div>
                            </div>
                </div>
            </div>
                            <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.plan_points') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                                

                                <div class="col-md-6 mb-3 options-div ">

                                    <div id="options-container">
                                        @if (old('plan_points'))
                                            @php $i = 0;
                                              $optionLength = count(old('plan_points')) - 1;
                                            @endphp 
                                            @foreach (old('plan_points') as $key => $value)
                                                <div class="row d-flex ml-1 option-row mb-2">
                                                    <div class="col-10">
                                                        <input type="text" class="form-control plan_points"
                                                            name="plan_points[]" value="{{ $value }}">
                                                    </div>
                                                    <div class="col-2 d-flex">
                                                        @if ($optionLength == $key)
                                                            <button class="btn btn-success" type="button"
                                                                onclick=addRow()><span class="fa fa-plus"></span></button>
                                                        @endif
                                                        <button class="btn btn-danger ml-2" type="button"
                                                            onclick=deleteRow(this)><span
                                                                class="fa fa-trash"></span></button>

                                                    </div>
                                                    
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="row d-flex ml-1">
                                                <div class="col-10">
                                                    <input type="text" class="form-control plan_points"
                                                        name="plan_points[]" id="plan_points">
                                                </div>

                                                <div class="col-2 d-flex">
                                                    <button class="btn btn-success" type="button" onclick=addRow()><span
                                                            class="fa fa-plus"></span></button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                </div>
            </div>
                            <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.maximum_booking_limit') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                                
                                <div class="col-12 mb-3">
                                    <div class="form-check width-100">
                                        <input type="radio" id="unlimited_booking" name="set_booking_limit"
                                            value="unlimited"
                                            {{ old('set_booking_limit', 'unlimited') == 'unlimited' ? 'checked' : '' }}>
                                        <label class="ec-form-label"
                                            for="unlimited_booking">{{ trans('lang.unlimited') }}</label>
                                    </div>
                                    <div class="d-flex ">
                                        <div class="form-check width-50 limited_booking_div  ">
                                            <input type="radio" id="limited_booking" name="set_booking_limit"
                                                value="limited"
                                                {{ old('set_booking_limit') == 'limited' ? 'checked' : '' }}>

                                            <label class="ec-form-label"
                                                for="limited_booking">{{ trans('lang.limited') }}</label>
                                        </div>
                                        <div
                                            class="form-check width-50 {{ old('set_booking_limit', 'unlimited') == 'limited' ? '' : 'd-none' }} booking-limit-div">
                                            <input type="number" id="booking_limit" class="form-control"
                                                name="booking_limit" placeholder="{{ trans('lang.ex_1000') }}"
                                                value="{{ old('booking_limit') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div>
            </div>
                             <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.plan_for') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                                
                                <div class="col-12 mb-3">
                                    <div class="form-check width-100">
                                        <input type="radio" id="driver" name="plan_for" value="driver" {{ old('plan_for','driver') == 'driver' ? 'checked' : '' }}>
                                        <label class="ec-form-label" for="driver">{{ trans('lang.individual_driver') }}</label>
                                    </div>
                                    <div class="form-check width-100">
                                        <input type="radio" id="owner" name="plan_for" value="owner" {{ old('plan_for','driver') == 'owner' ? 'checked' : '' }}>
                                        <label class="ec-form-label" for="owner">{{ trans('lang.owner') }}</label>
                                    </div>
                                    <div class="row owner-field {{ old('plan_for') == 'owner' ? '' : 'd-none' }}">
                                    <div class="col-md-6 mb-3">
                                        <label class="ec-form-label" for="driver">{{ trans('lang.max_vehicle_create_limit') }}</label>
                                        <input type="number" id="vehicle_limit" class="form-control" name="vehicle_limit" placeholder="{{ trans('lang.ex_1000_and_minus_one') }}" value="{{ old('vehicle_limit') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="ec-form-label" for="driver">{{ trans('lang.max_driver_create_limit') }}</label>
                                        <input type="number" id="driver_limit" class="form-control" name="driver_limit" placeholder="{{ trans('lang.ex_1000_and_minus_one') }}" value="{{ old('driver_limit') }}">
                                    </div>
                                    <div class="col-md-6 mb-3 mt-2">
                                        <label class="ec-form-label" for="">{{ trans('lang.dispatcher_panel_access') }}</label>
                                        <div class="form-check width-50">
                                            <input type="radio" id="dispatcher_access_yes" name="dispatcher_access" value="yes" {{ old('dispatcher_access') == 'yes' ? 'checked' : '' }}>
                                            <label class="ec-form-label" for="dispatcher_access_yes">{{ trans('lang.yes') }}</label>
                                        </div>
                                        <div class="form-check width-50">
                                            <input type="radio" id="dispatcher_access_no" name="dispatcher_access" value="no" {{ old('dispatcher_access', 'no') == 'no' ? 'checked' : '' }}>
                                            <label class="ec-form-label" for="dispatcher_access_no">{{ trans('lang.no') }}</label>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-form-btn">{{ trans('lang.save') }}
                        </button>
                        <a href="{{ url('subscription-plans') }}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                </div>
            </div>

                        
                    
                    
                </form>

            </div>

        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(async function() {

            $('input[name="plan_validity_days"]').on('change', function() {
                if ($('#limited_days').is(':checked')) {
                    $('.expiry-limit-div').removeClass('d-none');
                } else {
                    $('.expiry-limit-div').addClass('d-none');
                }
            });
            $('input[name="set_booking_limit"]').on('change', function() {
                if ($('#limited_booking').is(':checked')) {
                    $('.booking-limit-div').removeClass('d-none');
                } else {
                    $('.booking-limit-div').addClass('d-none');
                }
            });
            $('input[name="planType"]').on('change', function() {
                if ($('input[name="planType"]:checked').val() == 'free') {
                    $('.plan_price_div').addClass('d-none');
                    $("#plan_price").val(0);
                } else {
                    $('.plan_price_div').removeClass('d-none');
                }
            });
            $('input[name="plan_for"]').on('change', function() {
                if ($('input[name="plan_for"]:checked').val() == 'owner') {
                    $('.owner-field').removeClass('d-none');
                } else {
                    $('.owner-field').addClass('d-none');
                }
            });
        });
        let optionIndex = 1;

        function addRow() {
            const container = document.getElementById('options-container');
            const newRow = document.createElement('div');
            newRow.className = 'row d-flex ml-1 option-row mt-3';
            newRow.innerHTML = `
        <div class="col-10">
            <input type="text" class="form-control plan_points" name="plan_points[]">
        </div>
        <div class="col-2 d-flex">
            <button class="btn btn-success" type="button" onclick="addRow()"><span class="fa fa-plus"></span></button>
            <button class="btn btn-danger ml-2" type="button" onclick="deleteRow(this)"><span class="fa fa-trash"></span></button>
        </div>`;
            container.appendChild(newRow);
            optionIndex++;
        }

        function deleteRow(button) {
            const row = button.closest('.option-row');
            row.remove();
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#image_preview').show();
                    $('#uploding_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
