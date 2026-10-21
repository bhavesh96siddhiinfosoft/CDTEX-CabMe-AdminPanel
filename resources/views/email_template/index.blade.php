@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.email_template_plural') }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card ec-card ec-list-card">
                    <div class="card-header"><h5 class="mb-0">{{ trans('lang.email_template_plural') }} {{ trans('lang.list') }}</h5></div>
                    <div class="card-body">
                        <div class="table-responsive m-t-10">
                            <table id="example24"
                                class="display nowrap table table-hover ec-list-table"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{trans("lang.template_type")}}</th>
                                        <th>{{trans('lang.subject')}}</th>
                                        <th>{{trans('lang.created')}}</th>
                                        <th>{{trans('lang.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody id="append_list1">
                                    @foreach($templates as $template)
                                    <tr>
                                        <td>
                                            @if($template->type=="payment_receipt") {{trans("lang.payment_receipt")}}
                                            @elseif($template->type=="wallet_topup"){{trans("lang.wallet_topup")}}
                                            @elseif($template->type=="payout_approve_disapprove"){{trans("lang.payout_approve_disapprove")}}
                                            @elseif($template->type=="payout_request"){{trans("lang.payout_request")}}
                                            @elseif($template->type=="new_registration"){{trans("lang.new_registration")}}
                                            @elseif($template->type=="reset_password"){{trans("lang.reset_password")}}
                                            @elseif($template->type=="parcel_payment_receipt"){{trans("lang.parcel_payment_receipt")}}
                                            @elseif($template->type=="rental_payment_receipt"){{trans("lang.rental_payment_receipt")}}
                                            @endif
                                        </td>
                                        <td class="address-td">{{ $template->subject}}</td>
                                        <td class="dt-time"><span class="date">{{ date('d F
                                                Y',strtotime($template->created_at))}}</span>
                                            <span class="time">{{ date('h:i A',strtotime($template->created_at))}}</span>
                                        </td>
                                        <td class="action-btn">
                                            <a class="ec-act" href="{{route('email-template.edit', ['id' => $template->id])}}" data-toggle="tooltip" title="{{ trans('lang.edit') }}"><i class="mdi mdi-lead-pencil"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

@endsection