@extends('layouts.app')
@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.upload_doc') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.upload_document_desc') }}</p>
            </div>
            <a href="{{ route('driver.documentView', ['id' => $id]) }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.cancel') }}</a>
        </div>

        <form action="{{ route('driver.updatedocument', ['id' => $id]) }}" method="post" enctype="multipart/form-data" id="create_driver">
            @csrf
            @method('PUT')
            <div class="card ec-card ec-form-card">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-file-document-outline"></i></span>
                    <h5 class="mb-0 ml-2 doc-title">{{ trans('lang.document_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label">{{ trans('lang.category_name') }}</label>
                            <select class="form-control" disabled>
                                @foreach ($document as $doc)
                                    <option {{ $doc->id == $document_id ? 'selected' : '' }} value="{{ $doc->id }}">{{ $doc->title }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="document_id" value="{{ $document_id }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label">{{ trans('lang.upload_doc') }} <span class="text-danger">*</span></label>
                            <label class="ec-upload">
                                <input type="file" name="document_path" class="image">
                                <i class="mdi mdi-cloud-upload-outline"></i>
                                <span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span>
                                <span class="ec-upload-hint">PNG / JPG</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand save-form-btn">{{ trans('lang.save') }}</button>
                    <a href="{{ route('driver.documentView', ['id' => $id]) }}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
@endsection
