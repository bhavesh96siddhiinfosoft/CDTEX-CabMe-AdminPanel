@extends('layouts.app')

@section('content')


    <div class="page-wrapper listings-page form-page">
        

        <div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.vehicle_type')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.vehicle_create_desc') }}</p>
            </div>
            <a href="{{ url('vehicle-type.index') }}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.vehicle_type')}}
            </a>
        </div>

            <div class="row">
                <div class="col-12">
                    <div>

                        <div>

                            <div id="data-table_processing" class="dataTables_processing panel panel-default"
                                 style="display: none;">{{trans('lang.processing')}}</div>
                            
                            <form action="{{route('vehicle-type.store')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                
                                    


                                        <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.create_vehicle_type')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{trans('lang.vehicle_type')}} <span class="text-danger">*</span></label>
                                                
                                                    <input type="text" class="form-control"
                                                           value="{{ Request::old('libelle') }}" name="libelle">
                                                
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="ec-form-label">{{trans('lang.Image')}}</label>
                                                
                                                    <label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}"><input type="file" name="image" value="" onchange="readURL(this);"><i class="mdi mdi-cloud-upload-outline"></i><span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span><span class="ec-upload-hint">PNG / JPG</span></label>
                                                    <div id="image_preview" style="display: none; padding-left: 15px;">
                                                        <img class="rounded" style="width:50px" id="uploding_image"
                                                             src="#" alt="image">
                                                    </div>
                                                
                                            </div>
                                            <div class="col-md-6 mb-3">
                                  						<div class="switch-box switch-box-tight"><label class="control-label">{{trans('lang.active')}}</label><label class="switch"><input type="checkbox" class="user_active" id="user_active" name="status" value="Yes"><span class="slider round"></span></label></div>
                                  					</div>

                                        </div>
                </div>
            </div>
                                        <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.delivery_charge')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                                            

                                            <div class="col-12 mb-3">
<ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" href="#charges-default" role="tab">{{ trans('lang.default') }}</a>
                                                </li>
                                                @foreach($zones as $zone)
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-toggle="tab" href="#charges-zone-{{ $zone->id }}" role="tab">{{ $zone->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <div class="tab-content pt-3">
                                                @php
                                                    $tabs = [['key' => 'default', 'id' => 'charges-default', 'active' => true]];
                                                    foreach ($zones as $zone) {
                                                        $tabs[] = ['key' => $zone->id, 'id' => 'charges-zone-'.$zone->id, 'active' => false];
                                                    }
                                                @endphp

                                                @foreach($tabs as $tab)
                                                    <div class="tab-pane {{ $tab['active'] ? 'active' : '' }}" id="{{ $tab['id'] }}" role="tabpanel">
                                                        <div class="col-12 mb-3">
                                                            <label class="ec-form-label">{{trans('lang.delivery_charge_per')}} {{$delivery_distance}}</label>
                                                            
                                                                <input type="number" step="0.01" min="0" class="form-control"
                                                                    name="charges[{{ $tab['key'] }}][per_km]"
                                                                    value="{{ old('charges.'.$tab['key'].'.per_km') }}">
                                                            
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <label class="ec-form-label">{{trans('lang.minimum_delivery_charge')}}</label>
                                                            
                                                                <input type="number" step="0.01" min="0" class="form-control"
                                                                    name="charges[{{ $tab['key'] }}][minimum]"
                                                                    value="{{ old('charges.'.$tab['key'].'.minimum') }}">
                                                            
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <label class="ec-form-label">{{trans('lang.minimum_delivery_charge_within')}} {{$delivery_distance}}</label>
                                                            
                                                                <input type="number" step="0.01" min="0" class="form-control"
                                                                    name="charges[{{ $tab['key'] }}][within_km]"
                                                                    value="{{ old('charges.'.$tab['key'].'.within_km') }}">
                                                            
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            </div>
                                        </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand save-form-btn">{{ trans('lang.save')}}</button>
                            <a href="{{ route('vehicle-type.index') }}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
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
