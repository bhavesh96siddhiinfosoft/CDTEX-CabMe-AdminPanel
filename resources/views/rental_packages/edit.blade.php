@extends('layouts.app')
@section('content')
<div class="page-wrapper listings-page form-page">
    
    <div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.edit_rental_package') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.rental_package_edit_desc') }}</p>
            </div>
            <a href="{{ url('/dashboard') }}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{ trans('lang.dashboard') }}
            </a>
        </div>

        <div>
            <div class="error_top" style="display:none"></div>
            <div class="success_top" style="display:none"></div>
            <form action="{{ route('rental-packages.update',$package->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method("PUT")
                
                    
                        <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.package_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
                            
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.package_name') }} <span class="text-danger">*</span></label>
                                
                                    <input type="text" class="form-control" id="title" name="title" value="{{ $package->title }}" placeholder="{{ trans('lang.enter_package_name') }}">
                                    
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.vehicle_type') }} <span class="text-danger">*</span></label>
                                
                                    <select class="form-control model" name="vehicleTypeId" id="vehicleTypeId">
                                        <option value="">{{ trans('lang.select_type') }}</option>
                                        @foreach ($vehicleType as $value)
                                            <option value="{{ $value->id }}" {{ $package->vehicleTypeId == $value->id ? 'selected="selected"' : '' }}>{{ $value->libelle }}</option>
                                        @endforeach
                                    </select>
                                    
                                
                            </div>
                            <div class="col-12 mb-3">
                                <label class="ec-form-label">{{ trans('lang.package_description') }} <span class="text-danger">*</span></label>
                                
                                    <textarea class="form-control" id="description" name="description" rows="5" placeholder="{{ trans('lang.enter_package_description') }}">{{ $package->description }}</textarea>
                                    
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.package_basefare_price') }} <span class="text-danger">*</span></label>
                                
                                    <input type="number" class="form-control" id="baseFare" name="baseFare" value="{{ $package->baseFare }}" placeholder="{{ trans('lang.enter_package_basefare_price') }}">
                                    
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.package_included_hours') }} <span class="text-danger">*</span></label>
                                
                                    <input type="number" class="form-control" id="includedHours" name="includedHours" value="{{ $package->includedHours }}" placeholder="{{ trans('lang.enter_package_included_hours') }}">
                                    
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.package_included_distance') }} <span class="text-danger">*</span></label>
                                
                                    <input type="number" class="form-control" id="includedDistance" name="includedDistance" value="{{ $package->includedDistance }}" placeholder="{{ trans('lang.enter_package_included_distance') }}">
                                    
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.package_extra_km_fare') }} <span class="text-danger">*</span></label>
                                
                                    <input type="number" class="form-control" id="extraKmFare" name="extraKmFare" value="{{ $package->extraKmFare }}" placeholder="{{ trans('lang.enter_package_extra_km_fare') }}">
                                    
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.package_extra_minute_fare') }} <span class="text-danger">*</span></label>
                                
                                    <input type="number" class="form-control" id="extraMinuteFare" name="extraMinuteFare" value="{{ $package->extraMinuteFare }}" placeholder="{{ trans('lang.enter_package_extra_minute_fare') }}">
                                    
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.package_ordering') }} <span class="text-danger">*</span></label>
                                
                                    <input type="number" class="form-control" id="ordering" name="ordering" value="{{ $package->ordering }}" placeholder="{{ trans('lang.enter_package_ordering') }}">
                                    
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="switch-box switch-box-tight">
                                    <label class="control-label">{{ trans('lang.published') }}</label>
                                    <label class="switch"><input type="checkbox" id="published" name="published" {{ $package->published == "true" ? 'checked' : "" }}><span class="slider round"></span></label>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-form-btn">{{ trans('lang.save') }}
                    </button>
                    <a href="{{ url('rental-packages') }}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                </div>
            </div>
                    
                
                
            </form>

        </div>

    </div>
</div>
@endsection

@section('scripts')

@endsection