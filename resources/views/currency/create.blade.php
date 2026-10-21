@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
	


<div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.currency_create')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.currency_create_desc') }}</p>
            </div>
            <a href="{!! route('currency.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.currency_plural')}}
            </a>
        </div>

  <div class="row">
    <div class="col-12">
      <div>
	<div>

		<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
			{{trans('lang.processing')}}
		</div>
		
		<form action="{{route('currency.store')}}" method="post"  enctype="multipart/form-data" id="create_driver">
		@csrf
			
          	
          		<div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.currency_create')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
              		

						<div class="col-md-6 mb-3">
							<label class="ec-form-label">{{trans('lang.currency_plural')}} <span class="text-danger">*</span></label>
							
								<input type="text" class="form-control libelle" name="libelle">

							
						</div>

						<div class="col-md-6 mb-3">
							<label class="ec-form-label">{{trans('lang.currency_symbol')}} <span class="text-danger">*</span></label>
							
								<input type="text" class="form-control code" name="symbol">

							
						</div>
						<div class="col-md-6 mb-3">
						<label class="ec-form-label">{{trans('lang.digit_after_decimal_point')}}</label>
						
							<input type="number" name="decimal_digit" class="form-control decimal_degits" value="0">

						
					</div>

                    <div class="col-md-6 mb-3">
						<div class="switch-box switch-box-tight"><label class="control-label">{{trans('lang.status')}}</label><label class="switch"><input type="checkbox" class="user_active" id="status" name="statut" value="yes"><span class="slider round"></span></label></div>

					</div>

					<div class="col-md-6 mb-3">
						<div class="switch-box switch-box-tight"><label class="control-label">{{trans('lang.symbol_at_right')}}</label><label class="switch"><input type="checkbox" class="symbol_at_right" id="symbol_at_right" name="symbol_at_right" ><span class="slider round"></span></label></div>

					</div>


					



		 		</div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand save-setting-btn" >{{ trans('lang.save')}}</button>
				<a href="{!! route('currency.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
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
