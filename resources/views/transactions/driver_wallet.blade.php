@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">@if($transaction->count() > 0)
                                            @if($transaction[0]->role == 'owner')
                                                {{ trans('lang.owner_wallet_transaction_table') }}
                                            @else
                                                {{ trans('lang.driver_wallet_transaction_table') }}
                                            @endif
                                        @else
                                            @if($userRole == 'owner')
                                                {{ trans('lang.owner_wallet_transaction_table') }}
                                            @else
                                                {{ trans('lang.driver_wallet_transaction_table') }}
                                            @endif
                                        @endif {{ trans('lang.list') }}</h3>
                    <p class="mb-0 text-muted">@if($transaction->count() > 0)
                                            @if($transaction[0]->role == 'owner')
                                                {{ trans('lang.owner_wallet_transaction_table_text') }}
                                            @else
                                                {{ trans('lang.driver_wallet_transaction_table_text') }}
                                            @endif
                                        @else
                                            @if($userRole == 'owner')
                                                {{ trans('lang.owner_wallet_transaction_table_text') }}
                                            @else
                                                {{ trans('lang.driver_wallet_transaction_table_text') }}
                                            @endif
                                        @endif</p>
                </div>
            </div>
                        <div class="card ec-card ec-filter-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-filter-variant"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.filters_search') }}</h5>
                    </div>
                    <a class="btn ec-btn-ghost" href="{{ $id != '' ? route('walletstransaction.driver', ['id' => $id]) : route('walletstransaction.driver') }}">
                        <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" id="perPageForm">
                        {{-- the controller switches its search SQL on selected_search; this
                             listing only searches the transaction id, so it is pinned here --}}
                        <input type="hidden" name="selected_search" value="transaction_id">
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
                                <h5 class="mb-0">@if($transaction->count() > 0)
                                            @if($transaction[0]->role == 'owner')
                                                {{ trans('lang.owner_wallet_transaction_table') }}
                                            @else
                                                {{ trans('lang.driver_wallet_transaction_table') }}
                                            @endif
                                        @else
                                            @if($userRole == 'owner')
                                                {{ trans('lang.owner_wallet_transaction_table') }}
                                            @else
                                                {{ trans('lang.driver_wallet_transaction_table') }}
                                            @endif
                                        @endif {{ trans('lang.list') }}</h5>
                            </div>
                            <div class="card-body">
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
                                    <div class="ec-list-info">{{ trans('lang.showing') }} {{ $transaction->firstItem() }} {{ trans('lang.to_small') }} {{ $transaction->lastItem() }} {{ trans('lang.of') }} {{ $transaction->total() }} {{ trans('lang.entries') }}</div>
                                </div>
                            </div>
                            <div class="table-responsive m-t-10">
                                <table id="example24" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('lang.transaction_id') }}</th>
                                            @if ($id == '')
                                                <th>{{ trans('lang.driver_name') }}</th>
                                            @endif
                                            <th>{{ trans('lang.amount') }}</th>
                                            <th>{{ trans('lang.date_label') }}</th>
                                            <th>{{ trans('lang.payment_method') }}</th>
                                            <th>{{ trans('lang.note') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list1">
                                        @if (count($transaction) > 0)
                                            @foreach ($transaction as $data)
                                                <tr>
                                                    <td>{{ $data->id }}</td>
                                                    @if ($id == '')
                                                        <td>
                                                            <a href="{{ route('driver.show', ['id' => $data->userId]) }}">{{ $data->firstname }} {{ $data->lastname }}</a>
                                                        </td>
                                                    @endif
                                                    <td>
                                                        @if ($currency->symbol_at_right == 'true')
                                                            @if ($data->is_credited == '0')
                                                                <span style="color:red">(-{{ number_format($data->amount, $currency->decimal_digit) . '' . $currency->symbole }})</span>
                                                            @else
                                                                <span style="color:green">{{ number_format($data->amount, $currency->decimal_digit) . '' . $currency->symbole }}</span>
                                                            @endif
                                                        @else
                                                            @if ($data->is_credited == '0')
                                                                <span style="color:red">(-{{ $currency->symbole . '' . number_format($data->amount, $currency->decimal_digit) }})</span>
                                                            @else
                                                                <span style="color:green">{{ $currency->symbole . '' . number_format($data->amount, $currency->decimal_digit) }}</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>                                                      
                                                        <span class="date">{{ date('d F Y', strtotime($data->created_at)) }}</span>
                                                        <span class="time">{{ date('h:i A', strtotime($data->created_at)) }}</span>
                                                    </td>
                                                    @if ($data->image)
                                                        <td><img class="rounded" style="width:50px" src="{{ asset('/assets/images/payment_method/' . $data->image) }}" alt="image"></td>
                                                    @else
                                                        <td>{{ $data->payment_method }}</td>
                                                    @endif
                                                    <td>{{ $data->note }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="11" align="center">{{ trans('lang.no_result') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                {{-- the count already sits in the toolbar above the table --}}
                                <div class="d-flex justify-content-end align-items-center mb-3">
                                    <div>
                                        {{ $transaction->links('pagination.pagination') }}
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
@endsection
@section('scripts')
@endsection
