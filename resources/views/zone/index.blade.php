@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        

        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.zone_table') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.zone_table_text') }}</p>
                </div>
                @can('zone.create')
                    <a class="btn ec-head-btn" href="{!! route('zone.create') !!}">
                        <i class="mdi mdi-plus"></i> {{ trans('lang.zone_create') }}
                    </a>
                @endcan
            </div>

            {{-- Filters. Both controls submit on their own; the per-page select keeps
                 its inline onchange, which is why ec-form.js leaves it as a native
                 control instead of turning it into a select2. --}}
            <div class="card ec-card ec-filter-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-filter-variant"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.filters_search') }}</h5>
                    </div>
                    <a class="btn ec-btn-ghost" href="{{ url('zone') }}">
                        <i class="mdi mdi-refresh"></i> {{ trans('lang.reset') }}
                    </a>
                </div>
                <div class="card-body">
                    {{-- One form for the whole card: per-page, field and term submit
                         together, so changing the page size keeps the active search. --}}
                    <form method="GET" action="{{ route('zone.index') }}" id="perPageForm">
                        {{-- The controllers switch their search SQL on selected_search; the first
                             (previously default) field is pinned here now that the dropdown is gone. --}}
                        <input type="hidden" name="selected_search" value="name">
                        {{-- Search goes first on purpose: .ec-filter-row ignores the
                             col-* widths and gives its first column double the flex,
                             which is the space the search field wants. --}}
                        <div class="row ec-filter-row align-items-end">
                            <div class="col-lg-6 col-md-12 mb-2">
                                <label class="ec-filter-label" for="search">{{ trans('lang.search') }}</label>
                                <div class="ec-search-field">
                                    <i class="mdi mdi-magnify"></i>
                                    <input type="search" class="form-control" name="search" id="search"
                                           value="{{ request('search') }}"
                                           placeholder="{{ trans('lang.search_here') }}"
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
                                <h5 class="mb-0">{{ trans('lang.zone_table') }} {{ trans('lang.list') }}</h5>
                            </div>
                            <div class="card-body">
                                @can('zone.delete')
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
                                        <select form="perPageForm" name="per_page" id="per_page" class="form-control">
                                    @foreach ([10, 20, 30, 50, 100] as $size)
                                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                    @endforeach
                                </select>
                                        <span>{{ trans('lang.entries') }}</span>
                                    </div>
                                    <div class="ec-list-info">
                                        {{ trans('lang.showing') }} {{ $zones->firstItem() }}
                                        {{ trans('lang.to_small') }} {{ $zones->lastItem() }}
                                        {{ trans('lang.of') }} {{ $zones->total() }} {{ trans('lang.entries') }}
                                    </div>
                                </div>
                                <div class="table-responsive m-t-10">
                                    <table id="example24" class="display nowrap table table-hover ec-list-table"
                                           cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                @can('zone.delete')
                                                    <th class="ec-check-col">
                                                        <input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}">
                                                        <label class="control-label" for="is_active">
                                                            
                                                        </label>
                                                    </th>
                                                @endcan
                                                <th>{{ trans('lang.currency_name') }}</th>
                                                <th>{{ trans('lang.currency_status') }}</th>
                                                <th>{{ trans('lang.currency_created') }}</th>
                                                <th>{{ trans('lang.currency_modified') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1">
                                            @forelse ($zones as $zone)
                                                <tr>
                                                    @can('zone.delete')
                                                        <td class="ec-check-col">
                                                            <input type="checkbox" id="is_open_{{ $zone->id }}" class="is_open ec-check" dataid="{{ $zone->id }}">
                                                        </td>
                                                    @endcan
                                                    <td><a href="{{ route('zone.edit', ['id' => $zone->id]) }}">{{ $zone->name }}</a></td>
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" id="{{ $zone->id }}" name="isSwitch" {{ $zone->status == 'yes' ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <span class="date">{{ date('d F Y', strtotime($zone->created_at)) }}</span>
                                                        <span class="time">{{ date('h:i A', strtotime($zone->created_at)) }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="date">{{ date('d F Y', strtotime($zone->updated_at)) }}</span>
                                                        <span class="time">{{ date('h:i A', strtotime($zone->updated_at)) }}</span>
                                                    </td>
                                                    <td class="action-btn ec-actions">
                                                        <a href="{{ route('zone.edit', ['id' => $zone->id]) }}" class="do_not_edit ec-act"
                                                           data-toggle="tooltip" title="{{ trans('lang.edit') }}"><i class="mdi mdi-lead-pencil"></i></a>
                                                        @can('zone.delete')
                                                            <a href="{{ route('zone.delete', ['id' => $zone->id]) }}" class="delete-btn ec-act ec-act-danger"
                                                               data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" align="center">{{ trans('lang.no_result') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="d-flex justify-content-end align-items-center flex-wrap mt-3">
                                        <div>{{ $zones->links('pagination.pagination') }}</div>
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
                    var url = "{{ url('zone/delete', 'id') }}";
                    url = url.replace('id', arrayUsers);
                    window.location.href = url;
                });
            } else {
                alert('{{ trans('lang.select_delete_alert') }}');
            }
        });
        $(document).on("click", "input[name='isSwitch']", function(e) {
            var ischeck = $(this).is(':checked');
            var id = this.id;
            console.log(id);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('zone.switch') }}',
                method: "POST",
                data: {
                    'ischeck': ischeck,
                    'id': id
                },
                success: function(data) {
                },
            });
        });
    </script>
@endsection
