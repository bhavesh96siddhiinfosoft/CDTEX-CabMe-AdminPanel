@extends('layouts.app')

@section('content')
<div class="page-wrapper form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.maintenance_mode') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.settings_maintenance_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>

        
        <div>
            <div>
                <div>
                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">
                            {{trans('lang.processing')}}
                        </div>
                        <div class="error_top"></div>
                        <form action="{{route('maintenance-settings.update', ['id' => 1])}}" method="post"
                            enctype="multipart/form-data" id="setting_form">
                            @csrf
                            <div class="row restaurant_payout_create">
                                <div class="restaurant_payout_create-inner">                               

                                    <div class="ec-section">                                        
                                        <h6 class="ec-section-title">{{trans('lang.maintenance_mode_settings')}}</h6>
                                        <div class="form-group row width-100">
                                            <div class="form-check">
                                                <input type="checkbox" id="customer_maintenance_mode" 
                                                    name="customer_maintenance_mode" {{ $settings && $settings->is_customer_maintenance_mode == "true" ? 'checked' : '' }}>
                                                <label class="col-3 control-label"
                                                    for="customer_maintenance_mode">{{trans('lang.customer_maintenance_mode')}}</label>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <div class="form-check">
                                                <input type="checkbox" id="driver_maintenance_mode" 
                                                    name="driver_maintenance_mode" {{ $settings && $settings->is_driver_maintenance_mode == "true" ? 'checked' : '' }}>
                                                <label class="col-3 control-label"
                                                    for="driver_maintenance_mode">{{trans('lang.driver_maintenance_mode')}}</label>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <div class="form-check">
                                                <input type="checkbox" id="dispatcher_maintenance_mode" 
                                                    name="dispatcher_maintenance_mode" {{ $settings && $settings->is_dispatcher_maintenance_mode == "true" ? 'checked' : '' }}>
                                                <label class="col-3 control-label"
                                                    for="dispatcher_maintenance_mode">{{trans('lang.dispatcher_maintenance_mode')}}</label>
                                            </div>
                                        </div>                                       
                                    </div>
                                </div>
                            </div>
                            <div class="ec-form-actions">
                <input type="hidden" class="form-control" name="id" id="id" value="{{$settings->id}}">
                <button type="submit" class="btn ec-btn-brand edit-setting-btn">{{ trans('lang.save') }}</button>
                <a href="{{ route('settings') }}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

  
    @endsection

    @section('scripts')

    <script>
        
      
    </script>

    @endsection