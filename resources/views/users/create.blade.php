@extends('layouts.app')
@section('content')
<div class="page-wrapper listings-page form-page">
	
<div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.create_user') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.user_create_desc') }}</p>
            </div>
            <a href="{!! route('users.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.user_plural')}}
            </a>
        </div>

  	<div class="row">
		<div class="col-12">
			<div>
				<div>
					<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
						{{trans('lang.processing')}}
					</div>
					
					<form action="{{route('users.storeuser')}}" method="post"  enctype="multipart/form-data" id="create_driver">
					@csrf
						
							
								<div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-plus-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.user_details')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
									
									<div class="col-md-6 mb-3">
										<label class="ec-form-label">{{trans('lang.first_name')}} <span class="text-danger">*</span></label>
										
											<input type="text" class="form-control user_first_name" name="prenom" value="{{ Request::old('prenom')}}" placeholder="{{ trans('lang.eg_first_name') }}">
											
										
									</div>
									<div class="col-md-6 mb-3">
										<label class="ec-form-label">{{trans('lang.last_name')}} <span class="text-danger">*</span></label>
										
											<input type="text" class="form-control user_last_name" name="nom" value="{{ Request::old('nom')}}" placeholder="{{ trans('lang.eg_last_name') }}">
											
										
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
												<input type="text" class="form-control user_phone" name="phone" value="{{ Request::old('phone')}}" placeholder="{{ trans('lang.eg_phone') }}">
											</div>
										
										
										<input type="hidden" name="country_iso_code" id="country_iso_code"
      										value="{{ old('country_iso_code', $settings->default_country_code) }}">

									</div>
									<div class="col-md-6 mb-3">
										<label class="ec-form-label">{{trans('lang.email')}} <span class="text-danger">*</span></label>
										
											<input type="text" class="form-control user_email" name="email" value="{{ Request::old('email')}}" placeholder="{{ trans('lang.eg_user_email') }}" autocomplete="off">
											
										
									</div>
									<div class="col-md-6 mb-3">
										<label class="ec-form-label">{{trans('lang.password')}} <span class="text-danger">*</span></label>
										
											<input type="password" class="form-control user_password" name="password" value="{{ Request::old('password')}}" placeholder="{{ trans('lang.password') }}" autocomplete="new-password">
											
										
									</div>
									<div class="col-md-6 mb-3">
										<label class="ec-form-label">{{trans('lang.confirm_password')}} <span class="text-danger">*</span></label>
										
											<input type="password" class="form-control confirm_password" name="confirm_password" value="{{ Request::old('confirm_password')}}" placeholder="{{ trans('lang.password') }}" autocomplete="new-password">
											
										
									</div>
									<div class="col-md-6 mb-3">
										<label class="ec-form-label">{{trans('lang.profile_image')}}</label>
										<label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}"><input type="file" name="photo" onchange="readURL(this);"><i class="mdi mdi-cloud-upload-outline"></i><span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span><span class="ec-upload-hint">PNG / JPG</span></label>
										<div class="placeholder_img_thumb user_image"></div>
										<div id="image_preview" style="display: none; padding-left: 15px;">
										<img class="rounded" style="width:50px" id="uploding_image" src="#" alt="image">
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="switch-box switch-box-tight"><label class="control-label">{{trans('lang.active')}}</label><label class="switch"><input type="checkbox" class="user_active" id="user_active" name="statut"><span class="slider round"></span></label></div>
									</div>	
								</div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand save-form-btn" >{{ trans('lang.create_user_btn') }}</button>
							<a href="{!! route('users.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
                </div>
            </div>		
							
						
						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
 <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
	<script type="text/javascript">
		
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

		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
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
				reader.onload = function (e) {
					$('#placeholder_img_thumb').show();
					$('#user_nic_image').attr('src', e.target.result);
				}
				reader.readAsDataURL(input.files[0]);
			}
		}
	</script>
@endsection
