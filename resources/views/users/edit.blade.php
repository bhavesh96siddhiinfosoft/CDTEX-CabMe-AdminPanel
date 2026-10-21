@extends('layouts.app')


@section('content')
<div class="page-wrapper listings-page form-page">
	
	<div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.user_edit')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.user_edit_desc') }}</p>
            </div>
            <a href="{!! route('users.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.user_plural')}}
            </a>
        </div>


		<div>
			<div>
				<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{trans('lang.processing')}}</div>
				
				<form method="post" action="{{ route('users.update',$user->id) }}" enctype="multipart/form-data">
					@csrf
					@method("PUT")
					
						
							<div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-edit-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.user_edit')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
								
								<div class="col-md-6 mb-3">
									<label class="ec-form-label">{{trans('lang.first_name')}} <span class="text-danger">*</span></label>
									
										<input type="text" class="form-control user_first_name" name="prenom" value="{{$user->prenom}}" placeholder="{{ trans('lang.eg_first_name') }}">
										
									
								</div>

								<div class="col-md-6 mb-3">
									<label class="ec-form-label">{{trans('lang.last_name')}} <span class="text-danger">*</span></label>
									
										<input type="text" class="form-control user_last_name" name="nom" value="{{$user->nom}}" placeholder="{{ trans('lang.eg_last_name') }}">
										
									
								</div>
								<div class="col-md-6 mb-3">
									<label class="ec-form-label">{{trans('lang.email')}} <span class="text-danger">*</span></label>
									
										<input type="text" class="form-control user_email" name="email" value="{{$user->email}}" readonly placeholder="{{ trans('lang.eg_user_email') }}" autocomplete="off">
										
									
								</div>
								@php
									$selectedCountry = null;

									if (!empty($user->country_iso_code)) {										
										$selectedCountry = $countries->firstWhere('code', $user->country_iso_code);
									}

									if (!$selectedCountry) {										
										$selectedCountry = $countries->firstWhere(
											'phone',
											old('country_code', ltrim($user->country_code, '+'))
										);
									}
								@endphp

								<div class="col-md-6 mb-3">
									<label class="ec-form-label">{{trans('lang.user_phone')}} <span class="text-danger">*</span></label>
									
										
										<div class="phone-box position-relative">
											<select name="country_code" class="form-control" id="country_code" readonly>
												@foreach($countries as $country)
													<option value="{{ $country->phone }}" data-code="{{ strtolower($country->code) }}"
													 {{ $selectedCountry && $selectedCountry->id === $country->id ? 'selected' : '' }}>
													{{ $country->libelle }} (+{{ $country->phone }})
													</option>
												@endforeach
											</select>
											<input type="text" class="form-control user_phone" name="phone" value="{{$user->phone}}" readonly placeholder="{{ trans('lang.eg_phone') }}">
										</div>
										
									

								</div>						

								<div class="col-md-6 mb-3">
									<label class="ec-form-label">{{trans('lang.profile_image')}}</label>
									<label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}"><input type="file" name="photo" onchange="readURL(this);"><i class="mdi mdi-cloud-upload-outline"></i><span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span><span class="ec-upload-hint">PNG / JPG</span></label>

									{{--@if (file_exists('assets/images/users'.'/'.$user->photo_path) && !empty($user->photo_path))--}}
									@if (file_exists(public_path('assets/images/users'.'/'.$user->photo_path)) && !empty($user->photo_path))
										<div class="placeholder_img_thumb mt-2"><img class="rounded" id="uploding_image" src="{{asset('assets/images/users').'/'.$user->photo_path}}" alt="image"></div>
									@else
									<div class="placeholder_img_thumb mt-2"><img class="rounded" id="uploding_image" src="{{asset('assets/images/placeholder_image.jpg')}}" alt="image"></div>

									@endif

								</div>						
								<div class="col-md-6 mb-3">
									<div class="switch-box switch-box-tight">
                                    <label class="control-label">{{trans('lang.active')}}</label>
                                    <label class="switch">@if ($user->statut === "yes")
											<input type="checkbox" class="user_active" name="statut" id="user_active" checked="checked"  value="yes"/>
										@else
											<input type="checkbox" class="user_active" name="statut" id="user_active" value="no"/>
										@endif<span class="slider round"></span></label>
                                </div>
								</div>						
								
							</div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-form-btn" >{{ trans('lang.update_user_btn') }}</button>
							<a href="{!! route('users.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
                </div>
            </div>
						
						
					</div>
					
				</form>
			</div>
        </div>
	</div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">

	$('#country_code').select2(ecCountrySelect2());

	$(document).on("change", "#country_code", function() {
		let code = $("#country_code option:selected").data('code');
		$("#flag_icon").attr("src", `https://flagcdn.com/w40/${code}.png`)
	});

    function readURL(input) {
		console.log(input.files);
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#uploding_image').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

	function readURLNic(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
				$('#user_nic_image').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
