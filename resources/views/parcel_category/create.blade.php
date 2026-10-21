@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
	


	<div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.create_parcel_category')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.parcel_category_create_desc') }}</p>
            </div>
            <a href="{!! route('parcel-category.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.parcel_category')}}
            </a>
        </div>

		<div class="row">
			<div class="col-12">
				<div>
					<div>

						<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
							{{trans('lang.processing')}}
						</div>
						
						<form action="{{route('parcel-category.store')}}" method="post" enctype="multipart/form-data" id="create_driver">
							@csrf
							
								
									<div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.create_parcel_category')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
										

										<div class="col-md-6 mb-3">
											<label class="ec-form-label">{{trans('lang.title')}} <span class="text-danger">*</span></label>
											
												<input type="text" class="form-control title" name="title">

											
										</div>
										<div class="col-md-6 mb-3">
											<label class="ec-form-label">{{trans('lang.photo')}} <span class="text-danger">*</span></label>
											
												<label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}"><input type="file" name="image" value="" onchange="readURL(this);"><i class="mdi mdi-cloud-upload-outline"></i><span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span><span class="ec-upload-hint">PNG / JPG</span></label>
												<div id="image_preview" style="display: none; padding-left: 15px;">
													<img class="rounded" style="width:50px" id="uploding_image" src="#" alt="image">
												</div>
											
										</div>

										<div class="col-12 mb-3">
											<label class="ec-form-label">{{trans('lang.description')}} <span class="text-danger">*</span></label>
											
												<textarea rows="2" class="form-control" name="description" value=""></textarea>
											
										</div>

										<div class="col-12 mb-3">
											<div class="switch-box switch-box-tight"><label class="control-label">{{trans('lang.status')}}</label><label class="switch"><input type="checkbox" class="user_active" id="status" name="status"><span class="slider round"></span></label></div>
										</div>

								



								</div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand save-form-btn">{{ trans('lang.save')}}</button>
						<a href="{!! route('parcel-category.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
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
<script type="text/javascript">
        $(document).ready(function () {
            $(".shadow-sm").hide();
        })

        function readURL(input) {
            console.log(input.files);
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#image_preview').show();
                    $('#uploding_image').attr('src', e.target.result);


                }

                reader.readAsDataURL(input.files[0]);
            }
        }

    </script>
@endsection