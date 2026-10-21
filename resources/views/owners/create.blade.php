@extends('layouts.app')

@section('content')
    <div class="page-wrapper listings-page form-page">
        

        <div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.owners_create') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.owner_create_desc') }}</p>
            </div>
            <a href="{!! route('owners.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{ trans('lang.owner_plural') }}
            </a>
        </div>

            <div class="row">
                <div class="col-12">
                    <div>
                        <div>
                            <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{ trans('lang.processing') }}</div>
                            
                            <form action="{{ route('owners.store') }}" method="post" enctype="multipart/form-data" id="create_driver">
                                @csrf

                                
                                    
                                        <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-plus-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.driver_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.first_name') }} <span class="text-danger">*</span></label>
                                                
                                                    <input type="hidden" class="form-control user_first_name" name="id">
                                                    <input type="text" class="form-control user_first_name" name="nom" value="{{ Request::old('nom') }}" placeholder="{{ trans('lang.first_name_help') }}">
                                                    
                                                
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.last_name') }} <span class="text-danger">*</span></label>
                                                
                                                    <input type="text" class="form-control user_last_name" name="prenom" value="{{ Request::old('prenom') }}" placeholder="{{ trans('lang.last_name_help') }}">
                                                    
                                                
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.email') }} <span class="text-danger">*</span></label>
                                                
                                                    <input type="email" class="form-control user_email" name="email" value="{{ Request::old('email') }}" placeholder="{{ trans('lang.user_email_help') }}">
                                                    
                                                
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.password') }} <span class="text-danger">*</span></label>
                                                
                                                    <input type="password" class="form-control user_password" name="password" value="{{ Request::old('password') }}" placeholder="{{ trans('lang.user_password_help') }}">
                                                    
                                                
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{trans('lang.user_phone')}} <span class="text-danger">*</span></label>
                                                
                                                    <div class="phone-box position-relative">
                                                        <select name="country_code" class="form-control" id="country_code">
                                                            @foreach($countries as $country)
                                                                <option value="{{ $country->phone }}" data-code="{{ strtolower($country->code) }}" data-isocode="{{ $country->code }}"
                                                                    {{ old('country_code', $settings->default_country_code ) == $country->code ? 'selected' : '' }}>
                                                                    {{ $country->libelle }} (+{{ $country->phone }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="text" class="form-control user_phone" name="phone" value="{{ Request::old('phone')}}" placeholder="{{ trans('lang.user_phone_help') }}">
                                                    </div>
                                                
                                                
                                                <input type="hidden" name="country_iso_code" id="country_iso_code"
                                                    value="{{ old('country_iso_code', $settings->default_country_code) }}">

                                            </div>
                                          

                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.profile_image') }}</label>
                                                
                                                    <label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}"><input type="file" name="photo" value="{{ Request::old('photo') }}" onchange="readURL(this);"><i class="mdi mdi-cloud-upload-outline"></i><span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span><span class="ec-upload-hint">PNG / JPG</span></label>
                                                    
                                                
                                                <div id="image_preview" style="display: none; padding-left: 15px;">
                                                    <img class="rounded" style="width:50px" id="uploding_image" src="#" alt="image">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="multi-select">	
                                                    <label class="ec-form-label">{{ trans('lang.select_service_type') }}</label>
                                                    
                                                        <select class="form-control" name="service_type[]" id="service_type" multiple="multiple">
                                                            <option value="ride" {{ old('service_type') == 'ride' ? 'selected' : '' }}>{{trans('lang.ride')}}</option>
                                                            <option value="parcel" {{ old('service_type') == 'parcel' ? 'selected' : '' }}>{{trans('lang.parcel')}}</option>
                                                            <option value="rental" {{ old('service_type') == 'rental' ? 'selected' : '' }}>{{trans('lang.rental')}}</option>
                                                        </select>
                                                        
                                                    
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="switch-box switch-box-tight">
                                    <label class="control-label">{{ trans('lang.active') }}</label>
                                    <label class="switch"><input type="checkbox" class="form-check-inline user_active" id="user_active" name="statut" value="yes"><span class="slider round"></span></label>
                                </div>
                                            </div>
                                            
                                        </div>
                </div>
            </div>

                                        <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-plus-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.bank_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.bank_name') }}</label>
                                                
                                                    <input type="text" name="bank_name" class="form-control" id="bankName">

                                                
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.branch_name') }}</label>
                                                
                                                    <input type="text" name="branch_name" class="form-control" id="branchName">

                                                

                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.holder_name') }}</label>
                                                
                                                    <input type="text" name="holder_name" class="form-control" id="holderName">

                                                
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.account_number') }}</label>
                                                
                                                    <input type="text" name="account_number" class="form-control" id="accountNumber">

                                                
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.ifsc_code') }}</label>
                                                
                                                    <input type="text" class="form-control user_phone" name="ifsc_code">

                                                

                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{ trans('lang.other_information') }}</label>
                                                
                                                    <input type="text" name="other_information" class="form-control" id="otherDetails">

                                                
                                            </div>

                                        </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand save-form-btn">{{ trans('lang.save') }}</button>
                                            <a href="{!! route('owners.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                </div>
            </div>

                                        
                            </form>
                        
                    
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
    
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

        function readURLNic(input) {
            console.log(input.files);
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#placeholder_img_thumb').show();
                    $('#user_nic_image').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function updateFlag() {
			let code = $("#country_code option:selected").data('code');
			let isocode = $("#country_code option:selected").data('isocode');
			if (code) {
				$("#flag_icon").attr("src", `https://flagcdn.com/w40/${code}.png`);
				$('#country_iso_code').val(isocode);
			}
		}
		$(document).ready(function () {

			$('#country_code').select2(ecCountrySelect2());

			updateFlag(); 
		});

		$(document).on("change", "#country_code", updateFlag);


        $('#service_type').select2({
            placeholder: "{{trans('lang.select_service_type')}}",
            allowClear: true
        });
    </script>
@endsection
