@extends('layouts.app')


@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">

        <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{trans('lang.language_edit')}}</h3>
                        <p class="mb-0 text-muted">{{ trans('lang.language_edit_desc') }}</p>
                </div>
                <a href="{!! route('language.index') !!}" class="btn ec-btn-ghost">
                    <i class="mdi mdi-arrow-left"></i> {{trans('lang.user_plural')}}
                </a>
            </div>
    
        <div>
            <div>
    
                <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                    {{trans('lang.processing')}}
                </div>
                
                <form method="post" action="{{ route('language.update',$language->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method("PUT")
                    
                        
    
                            <div class="card ec-card ec-form-card mb-3">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                        <h5 class="mb-0 ml-2">{{trans('lang.language_edit')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="error_top"></div>
                        <div class="row">
                                
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label">{{trans('lang.language')}} <span class="text-danger">*</span></label>
                                    
                                        <input type="text" class="form-control language" name="language"
                                               value="{{$language->language}}" placeholder="{{ trans('lang.language_help') }}">
                                        
                                    
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label">{{trans('lang.code')}} <span class="text-danger">*</span></label>
                                    
                                        <input type="text" class="form-control code" name="code"
                                               value="{{$language->code}}" placeholder="{{ trans('lang.code_help') }}">
                                        
                                    
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label">{{trans('lang.flag')}}</label>
                                    <label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}"><input type="file" name="flag"><i class="mdi mdi-cloud-upload-outline"></i><span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span><span class="ec-upload-hint">PNG / JPG</span></label>
    
                                    @if (file_exists(public_path('assets/images/flags'.'/'.$language->flag)) && !empty($language->flag))
                                    <div class="placeholder_img_thumb mt-2"><img class="rounded"
                                         src="{{asset('assets/images/flags').'/'.$language->flag}}" alt="image"></div>
                                    @else
                                    <div class="placeholder_img_thumb mt-2"><img class="rounded"
                                          src="{{asset('assets/images/placeholder_image.jpg')}}" alt="image"></div>
    
                                    @endif
    
                                </div>
    
                                <div class="col-md-6 mb-3">
    
                                    <div class="switch-box switch-box-tight">
                                        <label class="control-label">{{trans('lang.active')}}</label>
                                        <label class="switch">@if ($language->status === "true")
                                        <input type="checkbox" class="user_active" name="status" id="status"
                                               checked="checked">
                                        @else
                                        <input type="checkbox" class="user_active" name="status" id="status" value="false"/>
                                        @endif<span class="slider round"></span></label>
                                    </div>
    
                                </div>
    
                                <div class="col-md-6 mb-3">
    
                                    <div class="switch-box switch-box-tight">
                                        <label class="control-label">{{trans('lang.is_rtl')}}</label>
                                        <label class="switch">@if ($language->is_rtl === "true")
                                        <input type="checkbox" class="user_active" name="is_rtl" id="is_rtl"
                                               checked="checked">
                                        @else
                                        <input type="checkbox" class="user_active" name="is_rtl" id="is_rtl" value="false">
                                        @endif<span class="slider round"></span></label>
                                    </div>
    
                                </div>
                            </div>
                    </div>
                    <div class="ec-form-actions">
                        <button type="submit" class="btn ec-btn-brand edit-setting-btn">{{
                    trans('lang.save')}}
                </button>
                <a href="{!! route('language.index') !!}" class="btn ec-btn-ghost">{{
                    trans('lang.cancel')}}</a>
                    </div>
                </div>
    
    
                        
                    
            </div>
            
    
        </div>
        </form>
    
    
    </div>
</div>
@endsection

@section('scripts')

@endsection