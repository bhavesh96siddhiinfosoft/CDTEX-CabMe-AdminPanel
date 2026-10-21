@extends('layouts.app')

@section('content')

    <div class="page-wrapper listings-page form-page">

        

        <div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.driver_edit') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.driver_edit_desc') }}</p>
            </div>
            <a href="{!! route('drivers.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{ trans('lang.driver_plural') }}
            </a>
        </div>


            <div>

                <div>

                    <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">

                        {{ trans('lang.processing') }}
                    </div>

                    

                    <form method="post" action="{{ route('driver.update', $driver->id) }}" enctype="multipart/form-data">

                        @csrf

                        @method('PUT')

                        

                            

                                <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-edit-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.driver_edit') }}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">

                                    

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.first_name') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="text" class="form-control user_first_name" name="nom" value="{{ $driver->nom }}" placeholder="{{ trans('lang.user_first_name_help') }}">

                                            

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.last_name') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="text" class="form-control user_last_name" name="prenom" value="{{ $driver->prenom }}" placeholder="{{ trans('lang.user_last_name_help') }}">

                                            

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.email') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="text" class="form-control user_email" name="email" value="{{ $driver->email }}" readonly placeholder="{{ trans('lang.user_email_help') }}">

                                            

                                        

                                    </div>
                                    @php
                                        $selectedCountry = null;

                                        if (!empty($driver->country_iso_code)) {										
                                            $selectedCountry = $countries->firstWhere('code', $driver->country_iso_code);
                                        }

                                        if (!$selectedCountry) {										
                                            $selectedCountry = $countries->firstWhere(
                                                'phone',
                                                old('country_code', ltrim($driver->country_code, '+'))
                                            );
                                        }
                                    @endphp

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.user_phone') }} <span class="text-danger">*</span></label>

                                        
                                            <div class="phone-box position-relative">
                                                <select name="country_code" class="form-control" id="country_code" readonly>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->phone }}" data-code="{{ strtolower($country->code) }}"
                                                        {{ $selectedCountry && $selectedCountry->id === $country->id ? 'selected' : '' }}>
                                                        {{ $country->libelle }} (+{{ $country->phone }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="text" class="form-control user_phone" name="phone" value="{{$driver->phone}}" readonly placeholder="{{ trans('lang.user_phone_help') }}">
                                            </div>
                                            

                                        

                                    </div>                                   

                                    <div class="col-md-6 mb-3">
                                        <div class="multi-select">	
                                            <label class="ec-form-label">{{ trans('lang.zone') }}</label>
                                            
                                                <select id="zone" name="zone[]"  multiple="multiple" class="form-control">
                                                    @foreach ($zones as $zone)
                                                        <option value="{{ $zone->id }}" {{ in_array($zone->id, explode(',', $driver->zone_id ?? '')) ? 'selected' : '' }}>{{ $zone->name }}</option>
                                                    @endforeach
                                                </select>
                                            
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.restaurant_image') }}</label>

                                        <label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}"><input type="file" name="photo" onchange="readURL(this);"><i class="mdi mdi-cloud-upload-outline"></i><span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span><span class="ec-upload-hint">PNG / JPG</span></label>

                                        @if (file_exists(public_path('assets/images/driver' . '/' . $driver->photo_path)) && !empty($driver->photo_path))
                                            <td><div class="placeholder_img_thumb mt-2"><img class="rounded" id="uploding_image" src="{{ asset('assets/images/driver') . '/' . $driver->photo_path }}" alt="image"></div></td>
                                        @else
                                            <td><div class="placeholder_img_thumb mt-2"><img class="rounded" id="uploding_image" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image"></div></td>
                                        @endif

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <div class="switch-box switch-box-tight">
                                    <label class="control-label">{{ trans('lang.status') }}</label>
                                    <label class="switch">@if ($driver->statut === 'yes')
                                                <input type="checkbox" class="user_active" name="statut" id="user_active" checked="checked">
                                            @else
                                                <input type="checkbox" class="user_active" name="statut" id="user_active">
                                            @endif<span class="slider round"></span></label>
                                </div>

                                    </div>

                                </div>
                </div>
            </div>
                                @if (!empty($driver->subscriptionPlanId) && empty($driver->ownerId))
                                    <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-edit-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.subscription_model') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                                        

                                        <div class="col-12 mb-3">
                                            <label class="ec-form-label">{{ trans('lang.change_expiry_date') }}</label>
                                            
                                                <input type="date" name="change_expiry_date" class="form-control" id="change_expiry_date" value="{{ !empty($driver->subscriptionExpiryDate) ? \Carbon\Carbon::parse($driver->subscriptionExpiryDate)->format('Y-m-d') : '' }}">
                                            
                                        </div>

                                    </div>
                </div>
            </div>
                                @endif
                                @if (empty($driver->ownerId))
                                <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-edit-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.admin_commission_settings') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">

                                    
                                    <div class="col-md-6 mb-3 admin_commision_detail ">

                                        <label class="ec-form-label">{{ trans('lang.commission_type') }} <span class="text-danger">*</span></label>
                                        <select name="commission_type" class="form-control">
                                            <option value="Percentage" {{ !empty($driver->adminCommission) ? ($driver->adminCommission['type'] == 'Percentage' ? 'selected' : '') : '' }}>{{ trans('lang.percentage') }}</option>
                                            <option value="Fixed" {{ !empty($driver->adminCommission) ? ($driver->adminCommission['type'] == 'Fixed' ? 'selected' : '') : '' }}>{{ trans('lang.fixed') }}</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3 admin_commision_detail ">
                                        <label class="ec-form-label">{{ trans('lang.dashboard_total_admin_commission') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control code" name="commission_value" value="{{ !empty($driver->adminCommission) ? $driver->adminCommission['value'] : '' }}" placeholder="{{ trans('lang.insert_commission_value') }}">
                                        
                                    </div>

                                </div>
                </div>
            </div>
                                @endif

                                @if(!$isFleet)
                                <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-edit-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.vehicle_info') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">

                                    

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.vehicle_type') }} <span class="text-danger">*</span></label>

                                        
                                            <select class="form-control model" name="id_type_vehicule" id="id_type_vehicule">
                                                <option value="">{{ trans('lang.select_type') }}</option>
                                                @foreach ($vehicleType as $value)
                                                   <option value="{{ $value->id }}" {{ !empty($vehicle) && $vehicle->id_type_vehicule == $value->id ? 'selected' : '' }}>
                                                        {{ $value->libelle }}
                                                    </option>

                                                @endforeach
                                            </select>
                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.vehicle_brand') }} <span class="text-danger">*</span></label>

                                        

                                            <select class="form-control brand_id" name="brand">

                                                <option value="">{{ trans('lang.select_brand') }}</option>
                                            

                                               @foreach ($brand as $value)
                                                    <option value="{{ $value->id }}" {{ !empty($vehicle) && $vehicle->brand == $value->id ? 'selected' : '' }}>
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach


                                            </select>

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.vehicle_model') }} <span class="text-danger">*</span></label>

                                        

                                            <select class="form-control model" name="model" id="model">

                                                <option value="">{{ trans('lang.select_model') }}</option>

                                                @foreach ($model as $value)
                                                    <option value="{{ $value->id }}" {{ !empty($vehicle) && $vehicle->model == $value->id ? 'selected' : '' }}>
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach


                                            </select>

                                            

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.vehicle_color') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="text" class="form-control user_last_name" name="color" value="{{ !empty($vehicle) ? $vehicle->color : '' }}" placeholder="{{ trans('lang.car_color_help') }}">

                                            

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.vehicle_numberplate') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="text" class="form-control user_email" name="numberplate" value="{{ !empty($vehicle) ? $vehicle->numberplate : '' }}" placeholder="{{ trans('lang.car_number_help') }}">

                                            

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.number_of_pessanger') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="number" class="form-control user_phone" name="passenger" value="{{ !empty($vehicle) ? $vehicle->passenger : '' }}" placeholder="{{ trans('lang.number_of_passenger_help') }}">

                                            

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.vehicle_milage') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="number" class="form-control user_phone" name="milage" value="{{ !empty($vehicle) ? $vehicle->milage : '' }}">

                                            

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.vehicle_km') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="number" class="form-control user_phone" name="km" value="{{ !empty($vehicle) ? $vehicle->km : '' }}" placeholder="{{ trans('lang.vehicle_km_help') }}">

                                            

                                        

                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="ec-form-label">{{ trans('lang.registration_year') }} <span class="text-danger">*</span></label>
                                        
                                            <input type="number" class="form-control" name="registration_year" value="{{ !empty($vehicle) ? $vehicle->car_make : '' }}"  min="1980" max="{{ date('Y') }}" step="1" placeholder="{{ trans('lang.registration_year_help') }}">
                                            
                                        
                                    </div>

                                </div>
                </div>
            </div>

                                <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-edit-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.driver_bank_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">

                                    

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.bank_name') }}</label>

                                        

                                            <input type="text" class="form-control address_line1" name="bank_name" value="{{ $driver->bank_name }}">

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.branch_name') }}</label>

                                        

                                            <input type="text" class="form-control user_first_name" name="branch_name" value="{{ $driver->branch_name }}">
                                          

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.holder_name') }}</label>

                                        

                                            <input type="text" class="form-control user_last_name" name="holder_name" value="{{ $driver->holder_name }}">                                        

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.account_no') }}</label>

                                        

                                            <input type="text" class="form-control user_email" name="account_no" value="{{ $driver->account_no }}">
                                           

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.Other_info') }}</label>

                                        

                                            <input type="text" class="form-control user_phone" name="other_info" value="{{ $driver->other_info }}">

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.ifsc_code') }}</label>

                                        

                                            <input type="text" class="form-control user_phone" name="ifsc_code" value="{{ $driver->ifsc_code }}">

                                        

                                    </div>

                                </div>
                </div>
                
            </div>
                                @endif
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-form-btn">{{ trans('lang.save') }}</button>

                    @if($isFleet)
                        <a href="{!! route('fleetdrivers.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                    @else
                        <a href="{!! route('drivers.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                    @endif
                </div>

                            

                        

                </div>

                

            </div>

            </form>

        </div>

    </div>

@endsection

@section('scripts')

    <script>

        $(document).ready(function() {

            $('select[name="brand"]').on('change', function() {
                var brand_id = $(this).val();
                var id_type_vehicule = $('select[name="id_type_vehicule"]').val();
                var url = "{{ route('driver.model', ':brandId') }}";
                url = url.replace(':brandId', brand_id);
                if (brand_id) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            id_type_vehicule: id_type_vehicule,
                            _token: '{{ csrf_token() }}',
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('select[name="model"]').empty();
                            $('select[name="model"]').append('<option value="">{{ trans('lang.select_model') }}</option>');
                            $.each(data.model, function(key, value) {
                                $('select[name="model"]').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('select[name="model"]').empty();
                }
            });
            $('#zone').select2({
                placeholder: "{{trans('lang.select_zone')}}",
                allowClear: true
            });
        });


        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#uploding_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function readCarURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#car_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURLNic(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#user_nic_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $('#country_code').select2(ecCountrySelect2());
        $(document).on("change", "#country_code", function() {
            let code = $("#country_code option:selected").data('code');
            $("#flag_icon").attr("src", `https://flagcdn.com/w40/${code}.png`)
        });
         
    </script>

@endsection
