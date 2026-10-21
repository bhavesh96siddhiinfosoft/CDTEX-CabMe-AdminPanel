@extends('layouts.app')

@section('content')


<div class="page-wrapper listings-page form-page">
	 

		<div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.cms_plural')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.cms_edit_desc') }}</p>
            </div>
            <a href="{!! route('cms.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.cms_plural')}}
            </a>
        </div>

      <div class="row">
        <div class="col-12">
          <div>

			<div>
				
				<div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{trans('lang.processing')}}</div>
        
        <form method="post" action="{{ route('cms.updateCms',$cmss->cms_id) }}" enctype="multipart/form-data">
          @csrf
          @method("PUT")
      
        

          <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.page_edit')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
            
            <div class="col-12 mb-3">
              <label class="ec-form-label">{{trans('lang.page_name')}} <span class="text-danger">*</span></label>
              
                <input type="text" class="form-control" name="cms_name" value="{{$cmss->cms_name}}" id="name"  onkeyup="return seo_url(this.value)" placeholder="{{ trans('lang.page_name') }}">
                
              
            </div>
            
            <div class="col-12 mb-3">
              <label class="ec-form-label">{{trans('lang.page_slug')}} <span class="text-danger">*</span></label>
              
                <input type="text" class="form-control" name="cms_slug" value="{{$cmss->cms_slug}}" id="cms_slug" onChange="this.value=this.value.toLowerCase();">
                <div class="form-text text-muted slug-info">{{ $pageBaseUrl }}{{$cmss->cms_slug}}</div>
                <input type="hidden" id="total_slug" value="0" />
              
            </div>

            <div class="col-12 mb-3">
              <label class="ec-form-label">{{trans('lang.page_description')}} <span class="text-danger">*</span></label>
              
                <textarea class="form-control" name="cms_desc" id="description" placeholder="{{trans('lang.page_description')}}">{{$cmss->cms_desc}}</textarea>
                
              
            </div>

            <div class="col-12 mb-3">
              <div class="switch-box switch-box-tight">
                                    <label class="control-label">{{trans('lang.status')}}</label>
                                    <label class="switch">@if ($cmss->cms_status === "on")
                   <input type="checkbox" class="cms_enabled" id="cms_enabled" name="cms_status" checked="checked">
                @elseif ($cmss->cms_status === "Publish")
                   <input type="checkbox" class="cms_enabled" id="cms_enabled" name="cms_status" checked="checked">
                
                @else
                   <input type="checkbox" class="cms_enabled" id="cms_enabled" name="cms_status" >
                @endif<span class="slider round"></span></label>
                                </div>
            </div>
          </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-form-btn" >{{ trans('lang.save')}}</button>
      <a href="{!! route('cms.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
                </div>
            </div>

        
      
    </div>
    

</div>
</form>
</div>
</div>
</div>
</div>

@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>

          $('#description').summernote({
	        height: 400,
	        width: 1000,
	        toolbar: [
	    	    ['style', ['bold', 'italic', 'underline', 'clear']],
			    ['font', ['strikethrough', 'superscript', 'subscript']],
			    ['fontsize', ['fontsize']],
			    ['color', ['color']],
			    ['forecolor', ['forecolor']],
			    ['backcolor', ['backcolor']],
			    ['para', ['ul', 'ol', 'paragraph']],
			    ['height', ['height']],
			    ['view', ['fullscreen', 'codeview', 'help']],
	  		]
	    });

         function seo_url(str){
            str = str.replace(/[^a-zA-Z0-9]+/g,'-').toLowerCase();
		$("#cms_slug").val(str);
            $(".slug-info").text('{{ $pageBaseUrl }}'+str);
            checkSlug();         
          }
          
         async function checkSlug(){
        	var cms_slug = $("#cms_slug").val();
        	var pages = await ref.where('cms_slug','==',cms_slug).get();
        	$("#total_slug").val(pages.docs.length);
        }

</script>
@endsection