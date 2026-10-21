@extends('layouts.app')

@section('content')

<div class="page-wrapper listings-page form-page">
    


    <div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.tax_edit')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.tax_edit_desc') }}</p>
            </div>
            <a href="{{url('/dashboard')}}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.dashboard')}}
            </a>
        </div>

        <div class="row">
            <div class="col-12">
                <div>
                    <div>

                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">
                            {{trans('lang.processing')}}
                        </div>
                        
                        <form action="{{route('tax.update',$Tax->id)}}" method="post" enctype="multipart/form-data"
                            id="create_driver">
                            @csrf
                            @method("PUT")

                            
                                
                                    <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.tax_edit')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
                                        

                                        <div class="col-md-6 mb-3">
                                            <label class="ec-form-label">{{trans('lang.label')}} <span class="text-danger">*</span></label>
                                            
                                                <input type="text" value="{{$Tax->libelle}}" class="form-control libelle" name="libelle">

                                            
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="ec-form-label">{{trans('lang.tax_table')}} <span class="text-danger">*</span></label>
                                            
                                                <input type="text" value="{{$Tax->value}}" class="form-control tax" name="tax">

                                            
                                        </div>


                                        <div class="col-md-6 mb-3">
                                            <label class="ec-form-label">{{trans('lang.type_tax')}} <span class="text-danger">*</span></label>
                                            
                                                <select class="form-control commission_type" name="type">
                                                    <option value="Percentage" {{($Tax->type=="Percentage")?"selected":""}}>{{trans("lang.percentage")}}</option>
                                                    <option value="Fixed" {{($Tax->type=="Fixed")?"selected":""}}>{{trans("lang.fix")}}</option>
                                                </select>
                                            
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="ec-form-label">{{trans('lang.country')}} <span class="text-danger">*</span></label>
                                            
                                                <select name="country" id="tax_country" class="form-control country">

                                                    @foreach($countries as $country)
                                                    
                                                        <option value="{{$country->libelle}}" data-code="{{ strtolower($country->code) }}" {{($country->libelle == $Tax->country) ? "selected" : "" }}>{{$country->libelle}}</option>
                                            
                                                    @endforeach


                                                </select>

                                            
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="switch-box switch-box-tight"><label class="control-label">{{trans('lang.status')}}</label><label class="switch"><input type="checkbox" class="user_active" id="status" name="statut"
                                                    value="yes" {{$Tax->statut=="yes" ? "checked" : ""}}><span class="slider round"></span></label></div>
                                        </div>

                                



                                </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-setting-btn">{{
                            trans('lang.save')}}</button>
                        <a href="{!! route('tax.index') !!}" class="btn ec-btn-ghost">{{
                            trans('lang.cancel')}}</a>
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
    <script>
        // same flag + search picker the phone fields use
        $(function () { $('#tax_country').select2(ecCountrySelect2()); });
    </script>

<script type="text/javascript">
</script>

@endsection