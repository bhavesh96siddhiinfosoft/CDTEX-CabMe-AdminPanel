@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.dispatcher_users') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.dispatcher_user_text') }}</p>
                </div>
                <div class="ec-page-head-actions">
                    {{-- data-toggle, not data-bs-toggle: the layout ships Bootstrap 4. --}}
                    <div class="dropdown d-inline-block mr-2">
                                <button class="btn ec-btn-ghost dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-download"></i> {{ trans('lang.export_as') }}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportDropdown">
                                    <a class="dropdown-item" href="{{ route('export.data', ['type' => 'excel', 'model' => 'DispatcherUser']) }}">{{ trans("lang.export_excel") }}</a>
                                    <a class="dropdown-item" href="{{ route('export.data', ['type' => 'pdf', 'model' => 'DispatcherUser']) }}">{{ trans("lang.export_pdf") }}</a>
                                    <a class="dropdown-item" href="{{ route('export.data', ['type' => 'csv', 'model' => 'DispatcherUser']) }}">{{ trans("lang.export_csv") }}</a>
                                </div>
                            </div>
                    <a class="btn ec-head-btn" href="{!! route('dispatcher-users.create') !!}"><i class="mdi mdi-plus"></i> {{ trans('lang.dispatcher_user_create') }}</a>
                </div>
            </div>

            {{-- One form for every filter: search, status and date submit together
                 instead of the old three forms dropping each other's values. The
                 status select and date range keep .filteredRecords — the page script
                 submits #filterForm the moment either changes. --}}
            <div class="card ec-card ec-filter-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-filter-variant"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.filters_search') }}</h5>
                    </div>
                    <a class="btn ec-btn-ghost" href="{{ route('dispatcher-users.index') }}">
                        <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dispatcher-users.index') }}" id="filterForm">
                        {{-- The controllers switch their search SQL on selected_search; the first
                             (previously default) field is pinned here now that the dropdown is gone. --}}
                        <input type="hidden" name="selected_search" value="prenom">
                        <div class="row ec-filter-row align-items-end">
                            <div class="col-lg-4 col-md-12 mb-2">
                                <label class="ec-filter-label" for="search">{{ trans('lang.search') }}</label>
                                <div class="ec-search-field">
                                    <i class="mdi mdi-magnify"></i>
                                    <input type="search" class="form-control" name="search" id="search"
                                           value="{{ request('search') }}" placeholder="{{ trans('lang.search_here') }}"
                                           autocomplete="off" spellcheck="false">
                                </div>
                            </div>

                            

                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <label class="ec-filter-label" for="status_selector">{{ trans('lang.status') }}</label>
                                <select class="form-control status_selector filteredRecords" name="status_selector">
                                        <option value="">{{trans("lang.status")}}</option>
                                        <option value="active" {{isset($_GET['status_selector']) && $_GET['status_selector'] =='active' ? 'selected ':'' }}>{{trans("lang.active")}}</option>
                                        <option value="inactive" {{isset($_GET['status_selector']) && $_GET['status_selector'] =='inactive' ? 'selected' :'' }}>{{trans("lang.in_active")}}</option>
                                    </select>
                            </div>

                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <label class="ec-filter-label" for="daterange">{{ trans('lang.created') }}</label>
                                <input type="text" placeholder="dd-mm-yyyy" class="form-control filteredRecords" id="daterange" name="daterange" value="{{ request('daterange') }}" readonly />
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
                                <h5 class="mb-0">{{ trans('lang.dispatcher_users') }} {{ trans('lang.list') }}</h5>
                                
                            </div>
                    <div class="card-body">
                                @can('dispatcher-users.delete')
                                <div id="ec-bulk-bar" class="ec-bulk-bar mb-3" style="display:none">
                                    <span class="ec-bulk-count"><strong id="ec-sel-count">0</strong> {{ trans('lang.bulk_selected') }}</span>
                                    <div class="ec-bulk-actions">
                                        <a class="btn ec-btn-ghost btn-sm ec-act-danger do_not_delete" id="deleteAll"  href="javascript:void(0)"><i class="mdi mdi-delete-outline"></i> {{ trans('lang.delete') }}</a>
                                        <button type="button" class="btn ec-btn-ghost btn-sm" id="ec-bulk-clear">{{ trans('lang.bulk_clear') }}</button>
                                    </div>
                                </div>
                                @endcan
                                <div class="ec-list-toolbar">
                                    <div class="ec-length">
                                        <span>{{ trans('lang.show') }}</span>
                                        <select form="filterForm" name="per_page" id="per_page" class="form-control">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                        <span>{{ trans('lang.entries') }}</span>
                                    </div>
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $users->firstItem() }} {{trans('lang.to_small')}} {{ $users->lastItem() }} {{trans('lang.of')}} {{ $users->total() }} {{trans('lang.entries')}}</div>
                                </div>
                        
                        <div class="table-responsive m-t-10">
                            <table id="example24" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        @can('dispatcher-users.delete')
                                        <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}"></th>
                                        @endcan
                                        <th>{{trans('lang.user_name')}}</th>
                                        <th>{{trans('lang.email')}}</th>
                                        <th>{{trans('lang.user_phone')}}</th>
                                        <th>{{trans('lang.status')}}</th>
                                        <th>{{trans('lang.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody id="append_list12">
                                    @if(count($users) > 0)
                                    @foreach($users as $customer)
                                    <tr>
                                        @can('dispatcher-users.delete')
                                            <td class="ec-check-col"><input type="checkbox" id="is_open_{{$customer->id}}" class="is_open ec-check" dataid="{{$customer->id}}"></td>
                                        @endcan
                                        @php
                                            $relativePath = str_replace(url('/') . '/', '', $customer->profile_picture_path);
                                            $imagePath = !empty($customer->profile_picture_path) && file_exists(public_path($relativePath)) 
                                                        ? $customer->profile_picture_path 
                                                        : asset('assets/images/placeholder_image.jpg');
                                        @endphp
                                        <td>
                                            <img class="rounded" style="width:50px" src="{{ $imagePath }}" alt="image">
                                            <a href="{{ route('dispatcher-users.show', ['id' => $customer->id]) }}">
                                                {{ $customer->first_name }} {{ $customer->last_name }}
                                            </a>
                                        </td>
                                        <td>{{ $customer->email}}</td>
                                        <td>{{ $customer->country_code}}{{ $customer->phone}}</td>
                                        @if ($customer->status=="yes")
                                            <td><label class="switch"><input type="checkbox" checked id="{{$customer->id}}" name="isActive"><span class="slider round"></span></label></td>
                                        @else
                                            <td><label class="switch"><input type="checkbox" id="{{$customer->id}}" name="isActive"><span class="slider round"></span></label></td>
                                        @endif
                                        <td class="action-btn ec-actions">
                                            <a href="{{route('dispatcher-users.show', ['id' => $customer->id])}}" class="ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                            <a class="ec-act" href="{{route('dispatcher-users.edit', ['id' => $customer->id])}}" data-toggle="tooltip" title="{{ trans('lang.edit') }}"><i class="mdi mdi-lead-pencil"></i></a>
                                            @can('dispatcher-users.delete')
                                            <a id="'+val.id+'" class="delete-btn ec-act ec-act-danger" name="user-delete" href="{{route('dispatcher-users.delete', ['id' => $customer->id])}}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                        @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="11" align="center">{{trans("lang.no_result")}}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>                          
                            <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div>
                                            {{ $users->links('pagination.pagination') }}
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

    $(document).ready(function() {
        $(".shadow-sm").hide();
    })
    $("#is_active").click(function() {
        $("#example24 .is_open").prop('checked', $(this).prop('checked'));
    });
    $('.status_selector').select2({
        placeholder: '{{trans("lang.status")}}',
        minimumResultsForSearch: Infinity,
        allowClear: true
    });
    $('select').on("select2:unselecting", function(e) {
        var self = $(this);
        setTimeout(function() {
            self.select2('close');
        }, 0);
    });
    function setDate() {
        let initialDateRange = $('#daterange').val(); // Get the initial value from input
        $('#daterange').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'DD-MM-YYYY',
                cancelLabel: 'Clear'
            }
        });
        if (initialDateRange) {
            let dates = initialDateRange.split(' - ');
            $('#daterange').data('daterangepicker').setStartDate(dates[0]);
            $('#daterange').data('daterangepicker').setEndDate(dates[1]);
            $('#daterange').val(initialDateRange);
        }
        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
            $('.filteredRecords').trigger('change');
        });
        $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('.filteredRecords').trigger('change');
        });
    }
    setDate();
    $('.filteredRecords').change(async function() {
        $('#filterForm').submit();
    })
    $("#deleteAll").click(function (e) {
        e.preventDefault();
        if ($('#example24 .is_open:checked').length) {
            ecConfirm('{{trans("lang.selected_delete_alert")}}').then(function (ok) {
                if (!ok) return;
                var arrayUsers = [];
                $('#example24 .is_open:checked').each(function() {
                    var dataId = $(this).attr('dataId');
                    arrayUsers.push(dataId);
                });
                arrayUsers = JSON.stringify(arrayUsers);
                var url = "{{url('dispatcher-users/delete', 'id')}}";
                url = url.replace('id', arrayUsers);
                window.location.href = url;
            });
        } else {
            alert('{{trans("lang.select_delete_alert")}}');
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
            url: '{{url("dispatcher-users-switch")}}',
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