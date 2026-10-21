@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.admin_table') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.admin_user_table_text') }}</p>
                </div>
                <a class="btn ec-head-btn" href="{!! route('admin-users.create') !!}"><i class="mdi mdi-plus"></i>{{ trans('lang.create_admin') }}</a>
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
                           
                            <h5 class="mb-0">{{ trans('lang.admin_table') }} {{ trans('lang.list') }}</h5>
                            
                        </div>
                        <div class="card-body">
                                @can('admin-users.delete')
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
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $users->firstItem() }} {{trans('lang.to_small')}} {{ $users->lastItem() }} {{trans('lang.of')}} {{ $users->total() }} {{trans('lang.entries')}}</div>
                                </div>
                            
                            <div class="table-responsive m-t-10">
                                <table id="adminTable"
                                    class="display nowrap table table-hover ec-list-table"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            @can('admin-users.delete')
                                            <th class="ec-check-col"><input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}"><label
                                                    class="control-label" for="is_active">
                                                    </label></th>
                                            @endcan
                                            <th>{{trans('lang.name')}}</th>
                                            <th>{{trans('lang.email')}}</th>
                                            <th>{{trans('lang.role')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list1">
                                        @foreach($users as $user)
                                        <tr>
                                            @can('admin-users.delete')
                                            <td class="ec-check-col"><input type="checkbox" id="is_open_{{$user->id}}"
                                                    class="is_open ec-check" dataid="{{$user->id}}"><label
                                                    class="control-label" for="is_open_{{$user->id}}"></label>
                                            </td>
                                            @endcan
                                            <td>
                                                <a href="{{route('admin-users.edit', ['id' => $user->id])}}">{{ $user->name}}</a>
                                            </td>
                                            <td>
                                                {{ $user->email}}
                                            </td>
                                            <td>
                                                {{ $user->getRoleNames()->first() }}
                                            </td>
                                            <td class="action-btn ec-actions">
                                                <a class="ec-act" href="{{route('admin-users.edit', ['id' => $user->id])}}"><i
                                                        class="mdi mdi-lead-pencil" title="{{ trans('lang.edit') }}"></i></a>
                                                @can('admin-users.delete')
                                                <a href="{{route('admin-users.delete', ['id' => $user->id])}}" class="delete-btn ec-act ec-act-danger"><i
                                                        class="mdi mdi-delete-outline"></i></a>
                                                        @endcan
                                            </td>
                                        </tr>
                                        @endforeach
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
</div>
</div>
@endsection
@section('scripts')

<script type="text/javascript">

    $("#is_active").click(function () {
        $("#adminTable .is_open").prop('checked', $(this).prop('checked'));
    });
    
    $("#deleteAll").click(function (e) {
        e.preventDefault();
        if ($('#adminTable .is_open:checked').length) {
            ecConfirm('{{ trans('lang.selected_delete_alert') }}').then(function (ok) {
                if (!ok) return;
                var arrayUsers = [];
                $('#adminTable .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');
                    arrayUsers.push(dataId);
                });
                arrayUsers = JSON.stringify(arrayUsers);
                var url = "{{url('admin-users/delete', 'id')}}";
                url = url.replace('id', arrayUsers);
                window.location.href = url;
            });
        } else {
            alert('Please Select Any One Record .');
        }
    });

</script>
@endsection