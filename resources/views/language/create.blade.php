@extends('layouts.app')

@section('content')

<div class="page-wrapper listings-page form-page">
<div class="container-fluid">
	<div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.language_create')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.language_create_desc') }}</p>
            </div>
            <a href="{!! route('language.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{ trans('lang.language_plural') }}
            </a>
        </div>


		<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
			{{trans('lang.processing')}}
		</div>
		
		<form action="{{route('language.storeuser')}}" method="post"  enctype="multipart/form-data" id="create_driver">
		@csrf
			
          	
          		<div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.language')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
              		

						<div class="col-md-6 mb-3">
							<label class="ec-form-label">{{trans('lang.language')}} <span class="text-danger">*</span></label>
							
								<input type="text" class="form-control language" name="language" placeholder="{{ trans('lang.language_help') }}">
								
							
						</div>

						<div class="col-md-6 mb-3">
							<label class="ec-form-label">{{trans('lang.code')}} <span class="text-danger">*</span></label>
							
								<input type="text" class="form-control code" name="code" placeholder="{{ trans('lang.code_help') }}">
								
							
						</div>
					<div class="col-md-6 mb-3">
						<label class="ec-form-label">{{trans('lang.flag')}}</label>
						<label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}"><input type="file" name="flag"><i class="mdi mdi-cloud-upload-outline"></i><span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span><span class="ec-upload-hint">PNG / JPG</span></label>
						<div class="placeholder_img_thumb user_image"></div>
						<div id="uploding_image"></div>
					</div>	
					
					

					<div class="col-md-6 mb-3">
						<div class="switch-box switch-box-tight"><label class="control-label">{{trans('lang.active')}}</label><label class="switch"><input type="checkbox" class="user_active" id="status" name="status"><span class="slider round"></span></label></div>
					</div>	
					
					<div class="col-md-6 mb-3">
						<div class="switch-box switch-box-tight"><label class="control-label">{{trans('lang.is_rtl')}}</label><label class="switch"><input type="checkbox" class="user_active" id="is_rtl" name="is_rtl" ><span class="slider round"></span></label></div>
					</div>
		 		</div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand save-setting-btn" >{{ trans('lang.save')}}</button>
				<a href="{!! route('language.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
                </div>
            </div> 
			
			
			

			

	</form>
    </div>
</div>
@endsection

@section('scripts')



@endsection