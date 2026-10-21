@extends('layouts.app')

@section('content')
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.owner_reports') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.owner_reports_desc') }}</p>
                </div>
            </div>

            <div class="card ec-card ec-form-card pb-4">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-file-chart-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.owner_reports') }}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <form action="{{ route('ownerreport.downloadExcelOwner') }}" method="get" enctype="multipart/form-data" id="create_driver">
                        <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label">{{trans('lang.select_status')}}</label>
                            <div class="ec-fcol">
                                <select class="form-control" name="driver_status">
                                <option value="0">{{trans('lang.select_status')}}</option>
                                <option value="yes">{{trans('lang.active')}}</option>
                                <option value="no">{{trans('lang.inactive')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label">{{trans('lang.date')}}</label>
                            <div class="ec-fcol">
                                <select class="form-control" name="date">
                                <option value="0">{{trans('lang.select_date')}}</option>
                                <option value="today">{{trans('lang.today')}}</option>
                                <option value="week">{{trans('lang.week')}}</option>
                                <option value="month">{{trans('lang.month')}}</option>
                                <option value="year">{{trans('lang.year')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label">{{trans('lang.from')}}</label>
                            <div class="ec-fcol">
                                <input type="date" class="form-control" name="from">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label">{{trans('lang.to')}}</label>
                            <div class="ec-fcol">
                                <input type="date" class="form-control" name="to">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label">{{trans('lang.file_format')}}</label>
                            <div class="ec-fcol">
                                <select class="form-control type" name="type">
                                <option value="">{{trans('lang.file_format')}}</option>
                                <option value="xls">{{trans('lang.xls')}}</option>
                                <option value="csv">{{trans('lang.csv')}}</option>
                                <option value="pdf">{{trans('lang.pdf')}}</option>
                                </select>
                            </div>
                        </div>
                        </div>
                        <div class="ec-form-actions">
                            <button type="submit" class="btn ec-btn-brand download"><i class="mdi mdi-download"></i> {{ trans('lang.download') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
