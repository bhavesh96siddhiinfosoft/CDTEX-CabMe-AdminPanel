@extends('layouts.app')
@section('content')
<div class="page-wrapper form-page">
	<div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.admin_commission') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_commission_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

        
		<div>
			<div>
				<div>
					<div class="card-body">
						<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
							{{trans('lang.processing')}}
						</div>
						<div class="error_top"></div>
						<div class="row restaurant_payout_create">
							<div class="restaurant_payout_create-inner">
								<div class="ec-section">
									<h6 class="ec-section-title">{{ trans('lang.subscription_based_model_settings') }}</h6>
									<div class="form-group row width-100 switch-box">
											<div class="col-12">
												<div class="switch-box-inner">
													<label class=" control-label">{{ trans('lang.subscription_based_model') }}</label>
													<label class="switch"> <input type="checkbox" name="subscription_model" data-id="{{$subscription->id}}" id="subscription_model" {{$subscription->subscription_model=="true" ? 'checked' : ''}}><span
															class=" slider round"></span></label>
													<i class="text-light fs-12 fa-solid fa fa-info" data-toggle="tooltip"
														title="{{ trans('lang.subscription_tooltip') }}" aria-describedby="tippy-3"></i>
												</div>
											</div>
									</div>
								</div>
								<form action="{{ route('commission.update',$commission->id) }}" method="post" enctype="multipart/form-data" id="create_driver">
									@csrf
									@method("PUT")
									<div class="ec-section">
										<h6 class="ec-section-title">{{trans('lang.commission_based_model_settings')}}</h6>
										<div class="form-group row width-100 switch-box">
										  <div class="col-12">	
											<div class="switch-box-inner">
												<label class=" control-label">{{ trans('lang.commission_based_model') }}</label>
												<label class="switch"> <input type="checkbox" name="status" onclick="ShowHideDiv()"
														id="enable_commission" {{$commission->statut=='yes' ? 'checked' : ''}}><span class="slider round"></span></label>
												<i class="text-light fs-12 fa-solid fa fa-info" data-toggle="tooltip"
													title="{{ trans('lang.commission_tooltip') }}" aria-describedby="tippy-3"></i>
											</div>
										  </div>
										</div>
										<div class="form-group row width-50 admin_commision_detail d-none">
											<label class="control-label">{{trans('lang.commission_type')}}</label>
											<div class="col-12"><select name="type" class="form-control">
												<option value="Percentage" {{$commission->type == 'Percentage' ? 'selected' : ''}}>{{trans('lang.percentage')}}</option>
												<option value="Fixed" {{$commission->type == 'Fixed' ? 'selected' : ''}}>{{trans('lang.fix')}}</option>
											</select>
										  </div>
										</div>
										<div class="form-group row width-50 admin_commision_detail d-none">
											<label class="control-label">{{trans('lang.dashboard_total_admin_commission')}}</label>
											<div class="col-12"><input type="text" class="form-control code" name="value" value="{{ $commission->value }}"></div>
										</div>
										<div class="ec-form-actions">
											<button type="submit" class="btn ec-btn-brand edit-setting-btn">{{ trans('lang.save') }}</button>
										</div>
									</div>
								</form>
								<form action="{{ route('bulk.commission.update') }}" method="post" enctype="multipart/form-data">
									@csrf
									@method("PUT")
									<div class="ec-section">
										<h6 class="ec-section-title">{{ trans('lang.bulk_update')}}</h6>
										<div class="form-group row width-100">
											<label class="control-label">{{ trans('lang.driver') }} <i
													class="text-light fs-12 fa-solid fa fa-info" data-toggle="tooltip"
													title="{{ trans('lang.bulk_update_commission_tooltip') }}" aria-describedby="tippy-3"></i>
											</label>
											<div class="col-12">
												<select id="driver_type" name="driver_type" class="form-control mb-2" required>
													<option value="all">{{ trans('lang.all_driver')}}</option>
													<option value="custom">{{ trans('lang.custom_driver')}}</option>
												</select>
												{{-- select2 renders its own box with the same placeholder, so no
													 help text here — it read as a second, empty field. --}}
												<select id="driver" name="driver[]" style="display:none" multiple class="form-control mt-3">
													@foreach ($drivers as $driver )
													<option value="{{$driver->id}}">{{$driver->prenom." ".$driver->nom }}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="form-group row width-50">
											<label class="col-4 control-label">{{ trans('lang.commission_type')}}</label>
											<div class="col-7">
												<select class="form-control bulk_commission_type" id="bulk_commission_type" name="bulk_commission_type">
													<option value="Percentage">{{trans('lang.coupon_percent')}}</option>
													<option value="Fixed">{{trans('lang.coupon_fixed')}}</option>
												</select>
											</div>
										</div>
										<div class="form-group row width-50">
											<label class="col-4 control-label">{{ trans('lang.admin_commission')}}</label>
											<div class="col-7">
												<input type="number" value="0" class="form-control bulk_admin_commission_value" name="bulk_admin_commission_value">
											</div>
										</div>
											<div class="ec-form-actions">
												<button type="submit" id="bulk_update_btn" class="btn ec-btn-brand edit-setting-btn">{{ trans('lang.bulk_update') }}</button>
												<a href="{{ route('settings') }}" class="btn ec-btn-ghost">{{ trans('lang.back') }}</a>
											</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- select2 is positioned absolutely by default and sized from the hidden
	 select, so the driver box broke out of its column. --}}
<style>
	.select2.select2-container {
		width: 100% !important;
		position: static;
		margin-top: 1rem;
	}
</style>
@endsection
@section('scripts')

{{-- session('success') / session('success_bulk') are toasted by the layout. --}}

<script>
	function ShowHideDiv() {
		var checkboxValue = $("#enable_commission").is(":checked");
		if (checkboxValue) {
			$(".admin_commision_detail").removeClass('d-none');
		} else {
			$(".admin_commision_detail").addClass('d-none');
		}
	}
	var enableCommision = "{{$commission->statut}}";
	if (enableCommision == 'yes') {
		$(".admin_commision_detail").removeClass('d-none');
	}
	/* toggal publish action code start*/
	$(document).on("click", "input[name='subscription_model']", function(e) {
		var subscription_model = $("#subscription_model").is(":checked");
		var id = $(this).attr('data-id');
		// The answer arrives after the click, so keep a handle on the toggle to
		// put it back when the change is declined.
		var $toggle = $(this);
		ecConfirm(subscription_model ? "{{ trans('lang.enable_subscription_plan_confirm_alert')}}" : "{{ trans('lang.disable_subscription_plan_confirm_alert')}}").then(function(ok) {
			if (!ok) {
				$toggle.prop("checked", !subscription_model);
				return;
			}
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: 'subscription-model-switch',
				method: "POST",
				data: {
					'ischeck': subscription_model,
					'id': id
				},
				success: function(response) {
					toastr.success('{{trans("lang.update_complete")}}');
				},
				error: function() {
					$toggle.prop("checked", !subscription_model);
					toastr.error('{{trans("lang.something_went_wrong")}}');
				},
			});
		});
	});
	$(document).ready(function() {
		$('#driver_type').on('change', function() {
			if ($('#driver_type').val() === 'custom') {
				$('#driver').show();
				$('#driver').select2({
					placeholder: "{{trans('lang.select_driver')}}",
					allowClear: true,
					width: '100%',
					dropdownAutoWidth: true
				});
			} else {
				$('#driver').hide();
				$('#driver').select2('destroy');
			}
		});
	});

</script>

@endsection