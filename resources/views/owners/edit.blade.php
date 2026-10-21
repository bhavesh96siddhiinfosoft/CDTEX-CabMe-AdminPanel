@extends('layouts.app')

@section('content')

    <div class="page-wrapper listings-page form-page">

        

        <div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.owner_edit') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.owner_edit_desc') }}</p>
            </div>
            <a href="{!! route('owners.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{ trans('lang.owner_plural') }}
            </a>
        </div>


            <div>

                <div>

                    <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">

                        {{ trans('lang.processing') }}
                    </div>

                    

                    <form method="post" action="{{ route('owners.update', $owner->id) }}" enctype="multipart/form-data">

                        @csrf

                        @method('PUT')

                        

                            

                                <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-edit-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.owner_edit') }}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">

                                    

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.first_name') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="text" class="form-control user_first_name" name="nom" value="{{ $owner->nom }}" placeholder="{{ trans('lang.user_first_name_help') }}">

                                            

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.last_name') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="text" class="form-control user_last_name" name="prenom" value="{{ $owner->prenom }}" placeholder="{{ trans('lang.user_last_name_help') }}">

                                            

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.email') }} <span class="text-danger">*</span></label>

                                        

                                            <input type="text" class="form-control user_email" name="email" value="{{ $owner->email }}" readonly placeholder="{{ trans('lang.user_email_help') }}">

                                            

                                        

                                    </div>
                                    @php
                                        $selectedCountry = null;

                                        if (!empty($owner->country_iso_code)) {										
                                            $selectedCountry = $countries->firstWhere('code', $owner->country_iso_code);
                                        }

                                        if (!$selectedCountry) {										
                                            $selectedCountry = $countries->firstWhere(
                                                'phone',
                                                old('country_code', ltrim($owner->country_code, '+'))
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
                                                <input type="text" class="form-control user_phone" name="phone" value="{{$owner->phone}}" readonly placeholder="{{ trans('lang.user_phone_help') }}">
                                            </div>
                                            
                                        

                                    </div>


                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.restaurant_image') }}</label>

                                        <label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}"><input type="file" name="photo" onchange="readURL(this);"><i class="mdi mdi-cloud-upload-outline"></i><span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span><span class="ec-upload-hint">PNG / JPG</span></label>

                                        @if (file_exists(public_path('assets/images/driver' . '/' . $owner->photo_path)) && !empty($owner->photo_path))
                                            <td><div class="placeholder_img_thumb mt-2"><img class="rounded" id="uploding_image" src="{{ asset('assets/images/driver') . '/' . $owner->photo_path }}" alt="image"></div></td>
                                        @else
                                            <td><div class="placeholder_img_thumb mt-2"><img class="rounded" id="uploding_image" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image"></div></td>
                                        @endif

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <div class="switch-box switch-box-tight">
                                    <label class="control-label">{{ trans('lang.status') }}</label>
                                    <label class="switch">@if ($owner->statut === 'yes')
                                                <input type="checkbox" class="user_active" name="statut" id="user_active" checked="checked">
                                            @else
                                                <input type="checkbox" class="user_active" name="statut" id="user_active">
                                            @endif<span class="slider round"></span></label>
                                </div>

                                    </div>

                                </div>
                </div>
            </div>
                                @if (!empty($owner->subscriptionPlanId))
                                    <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-edit-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.subscription_model') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                                        

                                        <div class="col-12 mb-3">
                                            <label class="ec-form-label">{{ trans('lang.change_expiry_date') }}</label>
                                            
                                                <input type="date" name="change_expiry_date" class="form-control" id="change_expiry_date" value="{{ !empty($owner->subscriptionExpiryDate) ? \Carbon\Carbon::parse($owner->subscriptionExpiryDate)->format('Y-m-d') : '' }}">
                                            
                                        </div>

                                    </div>
                </div>
            </div>
                                @endif

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
                                            <option value="Percentage" {{ !empty($owner->adminCommission) ? ($owner->adminCommission['type'] == 'Percentage' ? 'selected' : '') : '' }}>{{trans('lang.percentage')}}</option>
                                            <option value="Fixed" {{ !empty($owner->adminCommission) ? ($owner->adminCommission['type'] == 'Fixed' ? 'selected' : '') : '' }}>{{trans('lang.fix')}}</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3 admin_commision_detail ">
                                        <label class="ec-form-label">{{ trans('lang.dashboard_total_admin_commission') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control code" name="commission_value" value="{{ !empty($owner->adminCommission) ? $owner->adminCommission['value'] : '' }}" placeholder="{{ trans('lang.insert_commission_value') }}">
                                        
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

                                        

                                            <input type="text" class="form-control address_line1" name="bank_name" value="{{ $owner->bank_name }}">

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.branch_name') }}</label>

                                        

                                            <input type="text" class="form-control user_first_name" name="branch_name" value="{{ $owner->branch_name }}" placeholder="{{ trans('lang.user_first_name_help') }}">

                                            {{--  --}}

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.holder_name') }}</label>

                                        

                                            <input type="text" class="form-control user_last_name" name="holder_name" value="{{ $owner->holder_name }}" placeholder="{{ trans('lang.user_last_name_help') }}">

                                            {{--  --}}

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.account_no') }}</label>

                                        

                                            <input type="text" class="form-control user_email" name="account_no" value="{{ $owner->account_no }}" placeholder="{{ trans('lang.user_email_help') }}">

                                            {{--  --}}

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.Other_info') }}</label>

                                        

                                            <input type="text" class="form-control user_phone" name="other_info" value="{{ $owner->other_info }}" placeholder="{{ trans('lang.user_phone_help') }}">

                                            {{--  --}}

                                        

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="ec-form-label">{{ trans('lang.ifsc_code') }}</label>

                                        

                                            <input type="text" class="form-control user_phone" name="ifsc_code" value="{{ $owner->ifsc_code }}">

                                        

                                    </div>

                                </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-form-btn">{{ trans('lang.save') }}</button>

                    <a href="{!! route('owners.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                </div>
            </div>

                            

                        

                </div>

                

            </div>

            </form>

        </div>

    </div>

@endsection

@section('scripts')
    <script>
    

        function readURL(input) {

            console.log(input.files);

            if (input.files && input.files[0]) {

                var reader = new FileReader();
                reader.onload = function(e) {

                    //	$('#image_preview').show();

                    $('#uploding_image').attr('src', e.target.result);
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
