@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
	

<div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.car_model_create')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.car_model_edit_desc') }}</p>
            </div>
            <a href="{!! route('car-model.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.car_model')}}
            </a>
        </div>

  <div class="row">
    <div class="col-12">
      <div>

	<div>

		<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
			{{trans('lang.processing')}}
		</div>
		
		<form action="{{ route('car-model.update',$carModel->id) }}" method="post"  enctype="multipart/form-data">
		@csrf
        @method("PUT")
			
          	
          		<div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.edit_brand')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
              		

                      <div class="col-md-6 mb-3">
							<label class="ec-form-label">{{trans('lang.name')}} <span class="text-danger">*</span></label>
							
								<input type="text" class="form-control car_model_name" name="name" value="{{$carModel->name}}" placeholder="{{ trans('lang.car_model_name_help') }}">
								
							
						</div>

						<div class="col-md-6 mb-3">
							<label class="ec-form-label">{{trans('lang.brand')}} <span class="text-danger">*</span></label>
							
								<select  class="form-control brand_id" name="brand_name">
                                    
									@foreach($brand as $value)
                                  		<option value="{{ $value->id }}" {{$carModel->brand_id== $value->id ? 'selected' : '' }}>{{$value->name}}</option>
									@endforeach
                                   
								</select>
								
							
						</div>
						<div class="col-md-6 mb-3">
							<label class="ec-form-label">{{trans('lang.vehicle_type')}} <span class="text-danger">*</span></label>
							
							<select  class="form-control brand_id" name="vehicle_id">
									@foreach($vehicleType as $value)
									<option value="{{ $value->id }}" {{$carModel->vehicle_type_id== $value->id ? 'selected' : '' }}>{{$value->libelle}}</option>
									@endforeach
								</select>
								
							
						</div>
					 <div class="col-12 mb-3">
						<div class="switch-box switch-box-tight">
                                    <label class="control-label">{{trans('lang.active')}}</label>
                                    <label class="switch">@if ($carModel->status === "yes")    
							<input type="checkbox" class="car_model_active" id="car_model_active" name="status" checked="checked">
                            @else
                            <input type="checkbox" class="car_model_active" id="car_model_active" name="status">
                        @endif<span class="slider round"></span></label>
                                </div>
					</div>
						
		 		</div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-form-btn" >{{ trans('lang.save')}}</button>
				<a href="{!! route('car-model.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
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
 
@endsection

