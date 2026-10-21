@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.coupon_table') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.coupon_table_text') }}</p>
                </div>
                <a class="btn ec-head-btn" href="{!! route('coupons.create') !!}"><i class="mdi mdi-plus"></i>{{ trans('lang.coupon_create') }}</a>
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
                        <input type="hidden" name="selected_search" value="code">
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
                                <h5 class="mb-0">{{ trans('lang.coupon_table') }} {{ trans('lang.list') }}</h5>
                                
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
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $discounts->firstItem() }} {{trans('lang.to_small')}} {{ $discounts->lastItem() }} {{trans('lang.of')}} {{ $discounts->total() }} {{trans('lang.entries')}}</div>
                                </div>
                                <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
                                    {{trans('lang.processing')}}
                                </div>
                                
                                <div class="table-responsive m-t-10">
                                    <table id="example24" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}"></th>
                                                <th>{{ trans('lang.coupon_code') }}</th>
                                                <th>{{ trans('lang.coupon_discount') }}</th>
                                                <th>{{ trans('lang.coupon_type') }}</th>
                                                <th>{{ trans('lang.coupon_description') }}</th>
                                                <th>{{ trans('lang.coupon_expires_at') }}</th>
                                                <th>{{ trans('lang.coupon_enabled') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1">
                                            @if (count($discounts) > 0)
                                                @foreach ($discounts as $discount)
                                                    <tr>
                                                        <td class="ec-check-col"><input type="checkbox" id="is_open_{{ $discount->id }}" class="is_open ec-check" dataid="{{ $discount->id }}"></td>
                                                        <td>{{ $discount->code }}</td>
                                                        <td>
                                                            @if($discount->type == "Fixed")
                                                                @if($currency->symbol_at_right == "true")
                                                                    {{ $discount->discount }}{{ $currency->symbole }}
                                                                @else
                                                                    {{ $currency->symbole }}{{ $discount->discount }}   
                                                                @endif
                                                            @else
                                                                {{ $discount->discount }}%  
                                                            @endif
                                                        </td>
                                                        <td>{{ $discount->coupon_type }}</td>
                                                        <td>{{ $discount->discription }}</td>
                                                        <td>
                                                            <span class="date">{{ date('d F Y', strtotime($discount->expire_at)) }}</span>
                                                            <span class="time">{{ date('h:i A', strtotime($discount->expire_at)) }}</span>
                                                        </td>
                                                        <td>
                                                            @if ($discount->statut == 'yes')
                                                                <label class="switch"><input type="checkbox" checked id="{{ $discount->id }}" name="isActive"><span class="slider round"></span></label>
                                                            @else
                                                                <label class="switch"><input type="checkbox" id="{{ $discount->id }}" name="isActive"><span class="slider round"></span></label>
                                                            @endif
                                                        </td>
                                                        <td class="action-btn ec-actions">                                                          
                                                            <a href="{{ route('coupons.edit', ['id' => $discount->id]) }}" class="do_not_edit ec-act" data-toggle="tooltip" title="{{ trans('lang.edit') }}"><i class="mdi mdi-lead-pencil"></i></a><a id="'+val.id+'" class="delete-btn ec-act ec-act-danger" name="coupon-delete" href="{{ route('coupons.delete', ['id' => $discount->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
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
                                            {{ $discounts->links('pagination.pagination') }}
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
    <script type="text/javascript">
        $("#is_active").click(function() {
            $("#example24 .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function (e) {
            e.preventDefault();
            if ($('#example24 .is_open:checked').length) {
                ecConfirm('{{ trans('lang.selected_delete_alert') }}').then(function (ok) {
                    if (!ok) return;
                    var arrayUsers = [];
                    $('#example24 .is_open:checked').each(function() {
                        var dataId = $(this).attr('dataId');
                        arrayUsers.push(dataId);
                    });
                    arrayUsers = JSON.stringify(arrayUsers);
                    var url = "{{ url('coupons/delete', 'id') }}";
                    url = url.replace('id', arrayUsers);
                    window.location.href = url;
                });
            } else {
                alert('{{ trans('lang.select_delete_alert') }}');
            }
        });
        /* toggal publish action code start*/
        $(document).on("click", "input[name='isActive']", function(e) {
            var ischeck = $(this).is(':checked');
            var id = this.id;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: 'coupon/switch',
                method: "POST",
                data: {
                    'ischeck': ischeck,
                    'id': id
                },
                success: function(data) {
                },
            });
        });
        /*toggal publish action code end*/
    </script>
@endsection
