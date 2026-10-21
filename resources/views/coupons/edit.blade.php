@extends('layouts.app')

@section('content')
<div class="page-wrapper listings-page form-page">
  
  <div class="container-fluid">
            <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{trans('lang.coupon_plural')}}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.coupon_edit_desc') }}</p>
            </div>
            <a href="{!! route('coupons.index') !!}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{trans('lang.coupon_plural')}}
            </a>
        </div>

    <div class="row">
      <div class="col-12">
        <div>
          <div>

            <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{trans('lang.processing')}}</div>

            <div class="error_top" style="display:none"></div>
            <form method="post" action="{{ route('coupons.update',$discount->id) }}" enctype="multipart/form-data">
              @csrf
              @method("PUT")
              

                

                  <!-- <div class="col-md-6"> -->
                  <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-form-select"></i></span>
                    <h5 class="mb-0 ml-2">{{trans('lang.coupon_edit')}}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
                    

                    <div class="col-md-6 mb-3">
                      <label class="ec-form-label">{{trans('lang.coupon_code')}} <span class="text-danger">*</span></label>
                      
                        <input type="text" type="text" class="form-control coupon_code" name="code" value="{{$discount->code}}" placeholder="{{ trans('lang.coupon_code_help') }}">
                        
                      
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="ec-form-label">{{trans('lang.coupon_discount_type')}} <span class="text-danger">*</span></label>
                      
                        <select id="coupon_discount_type" class="form-control" name="type">
                          <option value="Percentage" {{ $discount->type == 'Percentage' ? 'selected' : '' }}>{{trans('lang.coupon_percent')}}</option>
                          <option value="Fixed" {{ $discount->type == 'Fixed' ? 'selected' : '' }}>{{trans('lang.coupon_fixed')}}</option>
                        </select>
                        

                      
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="ec-form-label">{{trans('lang.coupon_discount')}} <span class="text-danger">*</span></label>
                      

                        <input type="number" type="text" class="form-control coupon_discount" name="discount" value="{{$discount->discount}}" placeholder="{{ trans('lang.coupon_discount_help') }}">
                        

                      
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="ec-form-label">{{trans('lang.coupon_expires_at')}} <span class="text-danger">*</span></label>
                      
                        <!-- <div class="form-group"> -->
                        <div class='input-group date' id='datetimepicker1'>

                          <input type='date' class="form-control date_picker input-group-addon" name="expire_at" value="{{ date('Y-m-d', strtotime($discount->expire_at)) }}"  min="{{ now()->toDateString() }}"   placeholder="{{ trans('lang.coupon_expires_at_help') }}"/>
                          <span class="">
                            <!-- <span class="glyphicon glyphicon-calendar fa fa-calendar"></span> -->
                          </span>
                        </div>
                        
                        <!-- </div> -->
                      
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="ec-form-label">{{trans('lang.coupon_type')}} <span class="text-danger">*</span></label>
                      
                        <select id="coupon_type" class="form-control" name="coupon_type">
                          <option value="Ride" {{ $discount->coupon_type == 'Ride' ? 'selected' : '' }}>{{trans('lang.ride')}}</option>
                          <option value="Parcel" {{ $discount->coupon_type == 'Parcel' ? 'selected' : '' }}>{{trans('lang.parcel')}}</option>
                          <option value="Rental" {{ $discount->coupon_type == 'Rental' ? 'selected' : '' }}>{{trans('lang.rental')}}</option>
                        </select>
                        
                      
                    </div>


                    <div class="col-12 mb-3">
                      <label class="ec-form-label">{{trans('lang.coupon_description')}} <span class="text-danger">*</span></label>
                      
                        <textarea rows="12" class="form-control coupon_description" name="discription" id="coupon_description" placeholder="{{ trans('lang.coupon_description_help') }}">{{$discount->discription}}</textarea>
                        
                      
                    </div>

                    <div class="col-12 mb-3">
                      <div class="switch-box switch-box-tight">
                                    <label class="control-label">{{trans('lang.coupon_enabled')}}</label>
                                    <label class="switch">@if ($discount->statut === "yes")
                        <input type="checkbox" class="coupon_enabled" id="coupon_enabled" name="statut" checked="checked">

                        @else
                        <input type="checkbox" class="coupon_enabled" id="coupon_enabled" name="statut">
                        @endif<span class="slider round"></span></label>
                                </div>
                    </div>
                  </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-form-btn">{{ trans('lang.save')}}</button>
            <a href="{!! route('coupons.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel')}}</a>
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

  @endsection