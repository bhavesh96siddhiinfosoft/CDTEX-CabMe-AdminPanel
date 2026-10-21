@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.sos') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.sos_table_text') }}</p>
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
                        <input type="hidden" name="selected_search" value="status">
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
                                <h5 class="mb-0">{{ trans('lang.sos') }} {{ trans('lang.list') }}</h5>
                            </div>
                            <div class="card-body">
                                @can('sos.delete')
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
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                        <span>{{ trans('lang.entries') }}</span>
                                    </div>
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $sos->firstItem() }} {{trans('lang.to_small')}} {{ $sos->lastItem() }} {{trans('lang.of')}} {{ $sos->total() }} {{trans('lang.entries')}}</div>
                                </div>
                                
                                <div class="table-responsive m-t-10">
                                    <table id="example24" class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                @can('sos.delete')
                                                <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}"></th>
                                                @endcan
                                                <th>{{ trans('lang.ride_id') }}</th>
                                                <th>{{ trans('lang.userName') }}</th>
                                                <th>{{ trans('lang.driver_name') }}</th>
                                                <th>{{ trans('lang.sos_status') }}</th>
                                                <th>{{ trans('lang.created') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1">
                                            @if (count($sos) > 0)
                                                @foreach ($sos as $so)
                                                    <tr>
                                                        @can('sos.delete')
                                                        <td class="ec-check-col"><input type="checkbox" id="is_open_{{ $so->id }}" class="is_open ec-check" dataid="{{ $so->id }}"></td>
                                                        @endcan
                                                        <td>
                                                            {{-- <a href="{{route('ride.show', ['id' => $so->ride_id])}}">{{ $so->ride_id}}</a> --}}
                                                            <a href="{{ route('sos.show', ['id' => $so->id]) }}">{{ $so->ride_id }}</a>
                                                        </td>
                                                        <td>@if($so->id_user_app)<a href="{{ route('users.show', $so->id_user_app) }}">{{ $so->userPreNom }} {{ $so->userNom }}</a>@else {{ trans('lang.not_available') }} @endif</td>
                                                        <td>@if($so->id_conducteur)<a href="{{ route('driver.show', $so->id_conducteur) }}">{{ $so->driverPreNom }} {{ $so->driverNom }}</a>@else {{ trans('lang.not_available') }} @endif</td>
                                                        <td>
                                                            @if ($so->status == 'initiated')
                                                                <span class="badge badge-warning">{{ $so->status }}</span>
                                                            @elseif($so->status == 'processing')
                                                                <span class="badge badge-primary">{{ $so->status }}</span>
                                                            @elseif($so->status == 'user feel not safe')
                                                                <span class="badge badge-danger">{{ trans('lang.sos_user_unsafe') }}</span>
                                                            @elseif($so->status == 'driver feel not safe')
                                                                <span class="badge badge-danger">{{ trans('lang.sos_driver_unsafe') }}</span>
                                                            @else
                                                                <span class="badge badge-success">{{ $so->status }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="date">{{ date('d F Y', strtotime($so->creer)) }}</span>
                                                            <span class="time">{{ date('h:i A', strtotime($so->creer)) }}</span>
                                                        </td>
                                                        <td class="action-btn ec-actions">
                                                            <a class="ec-act" href="{{ route('sos.show', ['id' => $so->id]) }}" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                            @can('sos.delete')
                                                            <a id="'+val.id+'" class="delete-btn ec-act ec-act-danger" name="user-delete" href="{{ route('sos.delete', ['id' => $so->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                            @endcan
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
                                    <div class="table-page d-flex align-items-center justify-content-between mt-3"> 
                                        <nav aria-label="Page navigation example" class="custom-pagination">
                                            {{ $sos->appends(request()->query())->links() }}
                                        </nav>
                                        {{ $sos->links('pagination.pagination') }}
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div>
                                            {{ $sos->links('pagination.pagination') }}
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
                    var url = "{{ url('sos/delete', 'id') }}";
                    url = url.replace('id', arrayUsers);
                    window.location.href = url;
                });
            } else {
                alert('{{ trans('lang.select_delete_alert') }}');
            }
        });
    </script>
@endsection
