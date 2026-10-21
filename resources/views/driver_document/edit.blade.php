@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
	

<div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.driver_document_edit')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.document_edit_desc') }}</p>
            </div>
            <a href="{!! route('driver-document.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.driver_document_plural')}}
            </a>
        </div>

  <div class="row">
    <div class="col-12">
		<div>
				<div>

					<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
						{{trans('lang.processing')}}
					</div>
					
					<form action="{{ route('driver-document.update',$document->id) }}" method="post"  enctype="multipart/form-data" id="create_driver">
					@csrf
					@method("PUT")
						
							
								<div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.driver_document_edit')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
									
										<div class="col-12 mb-3">
											<label class="ec-form-label">{{trans('lang.title')}} <span class="text-danger">*</span></label>
											
												<input type="text" class="form-control libelle" name="title" value="{{$document->title}}">

											
										</div>
										<div class="col-12 mb-3">
											<label class="ec-form-label">{{trans('lang.document_for')}} <span class="text-danger">*</span></label>
											<div class="form-check width-100">
												<input type="radio" id="driver" name="type" value="driver" {{$document->type == 'driver' ? 'checked' : ''}}>
												<label class="ec-form-label" for="driver">{{ trans('lang.individual_driver') }}</label>
											</div>
											<div class="form-check width-100">
												<input type="radio" id="owner" name="type" value="owner" {{$document->type == 'owner' ? 'checked' : ''}}>
												<label class="ec-form-label" for="owner">{{ trans('lang.owner') }}</label>
											</div>
										</div>
										<div class="col-12 mb-3">
											<div class="switch-box switch-box-tight">
                                    <label class="control-label">{{trans('lang.status')}}</label>
                                    <label class="switch">@if ($document->is_enabled === "Yes")
													<input type="checkbox" class="user_active" id="status" name="status" checked="checked">
												@else
													<input type="checkbox" class="user_active" id="status" name="status">
												@endif<span class="slider round"></span></label>
                                </div>
										</div>
								</div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-setting-btn" >{{ trans('lang.save')}}</button>
							<a href="{!! route('driver-document.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
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
