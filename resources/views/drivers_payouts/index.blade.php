@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.drivers_disbursements') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.drivers_payout_table_text') }}</p>
                </div>
                <div class="ec-page-head-actions">
                    {{-- data-toggle, not data-bs-toggle: the layout ships the theme's
                         Bootstrap 4, whose dropdown plugin only reads the BS4 attribute.
                         (The old page leaned on a second, BS5 bundle the layout no
                         longer loads.) --}}
                    <div class="dropdown d-inline-block mr-2">
                        <button class="btn ec-btn-ghost dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-download"></i> {{ trans('lang.export_as') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportDropdown">
                            <a class="dropdown-item" href="{{ route('export.data', ['type' => 'excel', 'model' => 'Withdrawal']) }}">{{ trans('lang.export_excel') }}</a>
                            <a class="dropdown-item" href="{{ route('export.data', ['type' => 'pdf', 'model' => 'Withdrawal']) }}">{{ trans('lang.export_pdf') }}</a>
                            <a class="dropdown-item" href="{{ route('export.data', ['type' => 'csv', 'model' => 'Withdrawal']) }}">{{ trans('lang.export_csv') }}</a>
                        </div>
                    </div>
                    <a class="btn ec-head-btn" href="{!! route('driversPayouts.create') !!}"><i class="mdi mdi-plus"></i>{{ trans('lang.drivers_payout_create') }}</a>
                </div>
            </div>

            <div class="card ec-card ec-filter-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-filter-variant"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.filters_search') }}</h5>
                    </div>
                    <a class="btn ec-btn-ghost" href="{{ url()->current() }}">
                        <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" id="perPageForm">
                        {{-- The controllers switch their search SQL on selected_search; the first
                             (previously default) field is pinned here now that the dropdown is gone. --}}
                        <input type="hidden" name="selected_search" value="note">
                        <div class="row ec-filter-row align-items-end">
                            <div class="col-lg-6 col-md-12 mb-2">
                                <label class="ec-filter-label" for="search">{{ trans('lang.search') }}</label>
                                <div class="ec-search-field">
                                    <i class="mdi mdi-magnify"></i>
                                    <input type="search" class="form-control" name="search" id="search"
                                           value="{{ request('search') }}" placeholder="{{ trans('lang.search_here') }}"
                                           autocomplete="off" spellcheck="false">
                                </div>
                            </div>

                            

                            

                            
                        </div>
                    </form>
                </div>
            </div>
        <div class="table-list">
            <div class="row">
                <div class="col-12">
                    <div class="card ec-card ec-list-card">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <h5 class="mb-0">{{ trans('lang.drivers_disbursements') }} {{ trans('lang.list') }}</h5>
                            
                        </div>
                        <div class="card-body">
                                <div id="ec-bulk-bar" class="ec-bulk-bar mb-3" style="display:none">
                                    <span class="ec-bulk-count"><strong id="ec-sel-count">0</strong> {{ trans('lang.bulk_selected') }}</span>
                                    <div class="ec-bulk-actions">
                                        <a class="btn ec-btn-ghost btn-sm ec-act-danger do_not_delete" id="deleteAll"  href="javascript:void(0)"><i class="mdi mdi-delete-outline"></i> {{ trans('lang.delete') }}</a>
                                        <button type="button" class="btn ec-btn-ghost btn-sm" id="ec-bulk-clear">{{ trans('lang.bulk_clear') }}</button>
                                    </div>
                                </div>
                                <div class="ec-list-toolbar">
                                    <div class="ec-length">
                                        <span>{{ trans('lang.show') }}</span>
                                        <select form="perPageForm" name="per_page" id="per_page" class="form-control">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                        <span>{{ trans('lang.entries') }}</span>
                                    </div>
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $withdrawal->firstItem() }} {{trans('lang.to_small')}} {{ $withdrawal->lastItem() }} {{trans('lang.of')}} {{ $withdrawal->total() }} {{trans('lang.entries')}}</div>
                                </div>
                            
                            <div class="table-responsive m-t-10">
                                <table id="example24" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}"></th>
                                            <th>{{ trans('lang.payout_request_id') }}</th>
                                            <th>{{ trans('lang.driver') }}</th>
                                            <th>{{ trans('lang.paid_amount') }}</th>
                                            <th>{{ trans('lang.drivers_payout_note') }}</th>
                                            <th>{{ trans('lang.status') }}</th>
                                            <th>{{ trans('lang.drivers_payout_paid_date') }}</th>
                                            <th>{{ trans('lang.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list1">
                                        @if (count($withdrawal) > 0)
                                            @foreach ($withdrawal as $value)
                                                <tr>
                                                    <td class="ec-check-col"><input type="checkbox" id="is_open_{{ $value->id }}" class="is_open ec-check" dataid="{{ $value->id }}"></td>
                                                    <td>{{ $value->request_id }}</td>
                                                    <td>{{ $value->prenom }} {{ $value->nom }}</td>
                                                    <td>
                                                        @if ($currency->symbol_at_right == 'true')
                                                            <span style="color:red">({{ number_format($value->amount, $currency->decimal_digit) . '' . $currency->symbole }})</span>
                                                        @else
                                                            <span style="color:red">({{ $currency->symbole . '' . number_format($value->amount, $currency->decimal_digit) }})</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $value->note }} </td>
                                                    <td>
                                                        @if ($value->statut == 'success')
                                                            <span class="badge badge-success">{{ $value->statut }}<span>
                                                        @elseif ($value->statut == 'pending')
                                                            <span class="badge badge-warning">{{ $value->statut }}<span>
                                                        @elseif ($value->statut == 'reject')
                                                            <span class="badge badge-danger">{{ $value->statut }}<span>
                                                        @endif
                                                    </td>
                                                    <td>{{ date('d F Y h:i A', strtotime($value->creer)) }} </td>
                                                    <td class="action-btn ec-actions">
                                                        
                                                        @if ($value->statut == 'pending')
                                                           
                                                            <a class="ec-act" href="javascript:void(0)" 
                                                                data-toggle="tooltip" 
                                                                title="{{ trans('lang.accept') }}" 
                                                                onclick="openBankDetailsModal({{ $value->id_conducteur }}, {{ $value->id }}, {{$value->amount}})">
                                                                <i class="mdi mdi-check-circle"></i>
                                                            </a>


                                                            <a class="ec-act" href="javascript:void(0)" 
                                                                data-toggle="tooltip" 
                                                                title="{{ trans('lang.reject') }}" 
                                                                onclick="openCancelRequestModal({{ $value->id }})">
                                                                <i class="mdi mdi-close-circle-outline"></i>
                                                            </a>

                                                        @endif

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="11" align="center">{{ trans('lang.no_result') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div>
                                            {{ $withdrawal->links('pagination.pagination') }}
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
    </div>
</div>

<div class="modal fade" id="bankdetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered location_modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title locationModalTitle">{{ trans('lang.bankdetails') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeBankDetailsModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('withdraw.accept') }}">
                    @csrf
                    <div class="form-row">
                       
                        <div class="form-group row">
                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{ trans('lang.bank_name') }}</label>
                                <div class="col-12">
                                    <input type="text" name="bank_name" class="form-control" id="bankName" disabled>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{ trans('lang.branch_name') }}</label>
                                <div class="col-12">
                                    <input type="text" name="branch_name" class="form-control" id="branchName" disabled>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-4 control-label">{{ trans('lang.holer_name') }}</label>
                                <div class="col-12">
                                    <input type="text" name="holer_name" class="form-control" id="holderName" disabled>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{ trans('lang.account_number') }}</label>
                                <div class="col-12">
                                    <input type="text" name="account_number" class="form-control" id="accountNumber" disabled>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{ trans('lang.other_information') }}</label>
                                <div class="col-12">
                                    <input type="text" name="other_information" class="form-control" id="otherDetails" disabled>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{ trans('lang.ifsc_code') }}</label>
                                <div class="col-12">
                                    <input type="text" name="ifsc_code" class="form-control" id="ifsc_code" disabled>
                                </div>
                            </div>
                            <input type="hidden" name="driverId" id="driverId">
                            <input type="hidden" name="withdrawalId" id="withdrawalId">
                            <input type="hidden" name="walletBalance" id="walletBalance">
                            <input type="hidden" name="requestedAmount" id="requestedAmount"> 


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary save-form-btn" id="submit_accept">
                            {{ trans('lang.accept') }}</a>
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close" onclick="closeBankDetailsModal()">
                            {{ trans('lang.close') }}</a>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="cancelRequestModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title locationModalTitle">{{ trans('lang.cancel_payout_request') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeCancelRequestModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="data-table_processing_modal" class="dataTables_processing panel panel-default" style="display: none;">{{ trans('lang.processing') }}
                </div>
                <form id="cancelRequestForm" method="POST">
                @csrf
                    <div class="form-row">
                        <div class="form-group row">
                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{ trans('lang.notes') }}</label>
                                <div class="col-12">
                                    <textarea name="admin_note" class="form-control @error('admin_note') is-invalid @enderror" id="admin_note" cols="5" rows="5"></textarea>
                                    @error('admin_note')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary save-form-btn" id="submit_cancel">
                            {{ trans('lang.submit') }}</a>
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close" onclick="closeCancelRequestModal()">
                            {{ trans('lang.close') }}</a>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    let currentWithdrawalId = null;

    $("#deleteAll").click(function (e) {
        e.preventDefault();
        if ($('#example24 .is_open:checked').length) {
            ecConfirm('{{ trans('lang.selected_delete_alert') }}').then(function (ok) {
                if (!ok) return;
                var arrayUsers = [];
                $('#example24 .is_open:checked').each(function() {
                    arrayUsers.push($(this).attr('dataId'));
                });
                var url = "{{ route('driversPayouts.delete', ':id') }}";
                url = url.replace(':id', 0);                
                url += "?ids=" + arrayUsers.join(",");
                window.location.href = url;
            });
        } else {
            alert('{{trans("lang.select_delete_alert")}}');
        }
    });
    function openBankDetailsModal(driverId, withdrawalId, requestedAmount) {
        $('#bankdetailsModal').modal('show');

        $('#driverId').val(driverId);
        $('#withdrawalId').val(withdrawalId);
        $('#requestedAmount').val(requestedAmount);

        // reset fields
        $('#bankName').val('');
        $('#branchName').val('');
        $('#holderName').val('');
        $('#accountNumber').val('');
        $('#otherDetails').val('');
        $('#ifsc_code').val('');
        $('#walletBalance').val(''); 

        $.ajax({
            url: "/drivers/" + driverId + "/bank-details",
            type: "GET",
            success: function (data) {
                $('#bankName').val(data.bank_name);
                $('#branchName').val(data.branch_name);
                $('#holderName').val(data.holder_name);
                $('#accountNumber').val(data.account_number);
                $('#otherDetails').val(data.other_information);
                $('#ifsc_code').val(data.ifsc_code);
                $('#walletBalance').val(data.amount); 
            },
            error: function () {
                alert("Could not load bank details.");
            }
        });
    }



    function closeBankDetailsModal() {
        $('#bankdetailsModal').modal('hide');
    }
   
    window.openCancelRequestModal = function(id) {
        currentWithdrawalId = id;
        $('#cancelRequestForm').attr('action', '/withdrawals/reject/' + id);
        $('#cancelRequestModal').modal('show');
    };


    window.closeCancelRequestModal = function() {
        $('#cancelRequestModal').modal('hide');
        $('#admin_note').val('');
        currentWithdrawalId = null;
    };
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });



</script>
@endsection
