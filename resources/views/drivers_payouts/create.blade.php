@extends('layouts.app')

@section('content')
    <div class="page-wrapper listings-page form-page">
    <div class="container-fluid">

            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{trans('lang.drivers_payout_plural')}}</h3>
                        <p class="mb-0 text-muted">{{ trans('lang.payout_create_desc') }}</p>
                </div>
                <a href="{{url('/driversPayouts')}}" class="btn ec-btn-ghost">
                    <i class="mdi mdi-arrow-left"></i> {{trans('lang.drivers_payout_plural')}}
                </a>
            </div>
    
            <div>
                <div id="data-table_processing" class="dataTables_processing panel panel-default"
                     style="display: none;">{{trans('lang.processing')}}</div>
                
                @if (Session::has('message'))
                    <div class="alert alert-success">{{Session::get('message')}}</div>
                @endif
                <form action="{{route('driversPayouts.store')}}" method="post"  enctype="multipart/form-data" id="create_driver">
                    @csrf
                
                    
                        <div class="card ec-card ec-form-card mb-3">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                        <h5 class="mb-0 ml-2">{{trans('lang.drivers_payout_create')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="error_top"></div>
                        <div class="row">
                            
    
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.drivers_payout_driver_id')}} <span class="text-danger">*</span></label>
                                
                                    <select id="select_restaurant" class="form-control" name="driverId">
                                        @foreach($driver as $value)
                                        <option value="{{$value->id}}">{{ $value->prenom }} {{$value->nom}}</option>
                                        @endforeach
                                    </select>
                                    
                                
                            </div>
    
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{trans('lang.drivers_payout_amount')}} <span class="text-danger">*</span></label>
                                
                                    <input type="number" class="form-control payout_amount" name="payout" placeholder="{{ trans('lang.drivers_payout_amount_placeholder') }}">
                                    
                                
                            </div>
    
                            <div class="col-12 mb-3">
                                <label class="ec-form-label">{{ trans('lang.restaurants_payout_note')}} <span class="text-danger">*</span></label>
                                <div class="col-12">
                                    <textarea type="text" rows="7" class="form-control form-control payout_note" name="note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ec-form-actions">
                        <button type="submit" class="btn ec-btn-brand save-form-btn">{{trans('lang.save')}}</button>
                            <a href="{!! route('driversPayouts.index') !!}" class="btn ec-btn-ghost">{{trans('lang.cancel')}}</a>
                    </div>
                </div>
                        
                    
                
                </form>
    
            </div>
    
    
    
        
    </div>
</div>
    </div>


@endsection

@section('scripts')


@endsection
