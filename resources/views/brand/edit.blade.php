@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
	


<div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.edit_brand')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.brand_edit_desc') }}</p>
            </div>
            <a href="{!! route('brand.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.brand')}}
            </a>
        </div>

  <div class="row">
    <div class="col-12">
      <div>
	<div>

		<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
			{{trans('lang.processing')}}
		</div>
		
		<form action="{{ route('brand.update',$brand->id) }}" method="post"  enctype="multipart/form-data">
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
							<label class="ec-form-label">{{trans('lang.brand_plural')}} <span class="text-danger">*</span></label>
							
								<input type="text" class="form-control libelle" name="name" value="{{$brand->name}}">
								
							
						</div>
						
                                    
						<div class="col-12 mb-3">
						<div class="switch-box switch-box-tight">
                                    <label class="control-label">{{trans('lang.status')}}</label>
                                    <label class="switch">@if ($brand->status === "yes")                 
							<input type="checkbox" class="user_active" id="status" name="status" checked="checked">
                        @else
                        <input type="checkbox" class="user_active" id="status" name="status">
                        @endif<span class="slider round"></span></label>
                                </div>
					</div> 
						
					

						
						
		 		</div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-form-btn" >{{ trans('lang.save')}}</button>
				<a href="{!! route('brand.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
                </div>
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

