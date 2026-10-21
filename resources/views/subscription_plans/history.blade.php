@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.subscription_history') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.subscription_history_table') }}</p>
                </div>
            </div>

            <div class="card ec-card ec-filter-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-filter-variant"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.filters_search') }}</h5>
                    </div>
                    <a class="btn ec-btn-ghost" href="{{ route('subscription-history.index') }}">
                        <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('subscription-history.index') }}" id="perPageForm">
                        {{-- The controller switches its search SQL on selected_search; the
                             previously-default field is pinned now that the dropdown is gone. --}}
                        <input type="hidden" name="selected_search" value="name">
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
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">{{ trans('lang.subscription_history') }} {{ trans('lang.list') }}</h5>
                            </div>
                            <div class="card-body">
                                @can('subscription-history.delete')
                                <div id="ec-bulk-bar" class="ec-bulk-bar mb-3" style="display:none">
                                    <span class="ec-bulk-count"><strong id="ec-sel-count">0</strong> {{ trans('lang.bulk_selected') }}</span>
                                    <div class="ec-bulk-actions">
                                        <a class="btn ec-btn-ghost btn-sm ec-act-danger do_not_delete" id="deleteAll" href="javascript:void(0)"><i class="mdi mdi-delete-outline"></i> {{ trans('lang.delete') }}</a>
                                        <button type="button" class="btn ec-btn-ghost btn-sm" id="ec-bulk-clear">{{ trans('lang.bulk_clear') }}</button>
                                    </div>
                                </div>
                                @endcan
                                <div class="ec-list-toolbar">
                                    <div class="ec-length">
                                        <span>{{ trans('lang.show') }}</span>
                                        <select form="perPageForm" name="per_page" id="per_page" class="form-control">
                                            @foreach ([10, 20, 30, 50, 100] as $size)
                                                <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                            @endforeach
                                        </select>
                                        <span>{{ trans('lang.entries') }}</span>
                                    </div>
                                    <div class="ec-list-info">
                                        {{ trans('lang.showing') }} {{ $history->firstItem() }}
                                        {{ trans('lang.to_small') }} {{ $history->lastItem() }}
                                        {{ trans('lang.of') }} {{ $history->total() }} {{ trans('lang.entries') }}
                                    </div>
                                </div>
                                <div class="table-responsive m-t-10">
                                    <table id="subscriptionHistoryTable" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}"></th>
                                                <th>{{ trans('lang.driver/owner') }}</th>
                                                <th>{{ trans('lang.plan_name') }}</th>
                                                <th>{{ trans('lang.plan_type') }}</th>
                                                <th>{{ trans('lang.payment_method') }}</th>
                                                <th>{{ trans('lang.plan_expires_at') }}</th>
                                                <th>{{ trans('lang.purchase_date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1">
                                            @forelse ($history as $value)
                                                <tr>
                                                    <td class="ec-check-col">
                                                        <input type="checkbox" id="is_open_{{ $value->id }}" class="is_open ec-check" dataid="{{ $value->id }}">
                                                    </td>
                                                    <td>
                                                        <a href="{{ $value->isOwner == 'true' ? route('owners.show', ['id' => $value->user_id]) : route('driver.show', ['id' => $value->user_id]) }}">{{ $value->prenom . ' ' . $value->nom }}</a>
                                                    </td>
                                                    <td>{{ $value->subscription_plan['name'] }}</td>
                                                    <td>
                                                        @if ($value->subscription_plan['type'] == 'free')
                                                            <span class="badge badge-success">{{ $value->subscription_plan['type'] }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $value->subscription_plan['type'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($value->plan_type == 'free')
                                                            {{ trans('lang.free') }}
                                                        @else
                                                            {{ $value->payment_name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($value->expiry_date == null)
                                                            {{ trans('lang.unlimited') }}
                                                        @else
                                                            <span class="date">{{ date('d F Y', strtotime($value->expiry_date)) }}</span>
                                                            <span class="time">{{ date('h:i A', strtotime($value->expiry_date)) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="date">{{ date('d F Y', strtotime($value->created_at)) }}</span>
                                                        <span class="time">{{ date('h:i A', strtotime($value->created_at)) }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" align="center">{{ trans('lang.no_result') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div>{{ $history->links('pagination.pagination') }}</div>
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
    <script>
        $("#is_active").click(function() {
            $("#subscriptionHistoryTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function (e) {
            e.preventDefault();
            if ($('#subscriptionHistoryTable .is_open:checked').length) {
                ecConfirm('{{ trans('lang.selected_delete_alert') }}').then(function (ok) {
                    if (!ok) return;
                    var arrayUsers = [];
                    $('#subscriptionHistoryTable .is_open:checked').each(function() {
                        var dataId = $(this).attr('dataId');
                        arrayUsers.push(dataId);
                    });
                    arrayUsers = JSON.stringify(arrayUsers);
                    var url = "{{ url('subscription-history/delete', 'id') }}";
                    url = url.replace('id', arrayUsers);
                    window.location.href = url;
                });
            } else {
                alert("{{trans('lang.select_delete_alert')}}");
            }
        });
    </script>
@endsection
