@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.role_table') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.role_table_text') }}</p>
                </div>
                <a class="btn ec-head-btn" href="{!! route('roles.create') !!}"><i class="mdi mdi-plus"></i>{{ trans('lang.create_role') }}</a>
            </div>


            {{-- ── KPI strip — three summary cards ── --}}
            <div class="row ec-kpi-row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="ec-kpi kpi-brand ec-kpi-stat">
                        <span class="ec-kpi-icon"><i class="mdi mdi-account-key-outline"></i></span>
                        <div class="ec-kpi-body">
                            <div class="ec-kpi-value">{{ number_format($totalRoles ?? 0) }}</div>
                            <div class="ec-kpi-label">{{ trans('lang.total_roles') }}</div>
                            <div class="ec-kpi-sub">{{ trans('lang.in_this_panel') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="ec-kpi kpi-green ec-kpi-stat">
                        <span class="ec-kpi-icon"><i class="mdi mdi-shield-account-outline"></i></span>
                        <div class="ec-kpi-body">
                            <div class="ec-kpi-value">{{ number_format($totalAdmins ?? 0) }}</div>
                            <div class="ec-kpi-label">{{ trans('lang.assigned_admins') }}</div>
                            <div class="ec-kpi-sub">{{ trans('lang.across_all_roles') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="ec-kpi kpi-blue ec-kpi-stat">
                        <span class="ec-kpi-icon"><i class="mdi mdi-account-off-outline"></i></span>
                        <div class="ec-kpi-body">
                            <div class="ec-kpi-value">{{ number_format($unusedRoles ?? 0) }}</div>
                            <div class="ec-kpi-label">{{ trans('lang.unused_roles') }}</div>
                            <div class="ec-kpi-sub">{{ trans('lang.no_admin_assigned') }}</div>
                        </div>
                    </div>
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
                            <h5 class="mb-0">{{ trans('lang.role_table') }} {{ trans('lang.list') }}</h5>
                            
                        </div>
                        <div class="card-body">
                                @can('roles.delete')
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
                                    <div class="ec-list-info">{{trans('lang.showing')}} {{ $roles->firstItem() }} {{trans('lang.to_small')}} {{ $roles->lastItem() }} {{trans('lang.of')}} {{ $roles->total() }} {{trans('lang.entries')}}</div>
                                </div>
                            
                            <div class="table-responsive m-t-10">
                                <table id="roleTable"
                                    class="display nowrap table table-hover ec-list-table"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            @can('roles.delete')
                                            <th class="ec-check-col">
                                                <input type="checkbox" class="ec-check" id="is_active" title="{{ trans('lang.bulk_select_all') }}">
                                                <label class="control-label" for="is_active">
                                                    
                                                </label>
                                            </th>
                                            @endcan
                                            <th>{{trans('lang.name')}}</th>
                                            <th>{{trans('lang.admins')}}</th>
                                            <th>{{trans('lang.permissions')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list1">
                                        @if (count($roles) > 0)
                                        @foreach($roles as $role)
                                                <tr>
                                                    @can('roles.delete')
                                                    <td class="ec-check-col">
                                                        @if($role->id != 1)
                                                        <input type="checkbox" id="is_open_{{$role->id}}" class="is_open ec-check" dataId="{{$role->id}}">
                                                        @endif
                                                    </td>
                                                    @endcan
                                                    <td>
                                                        <span class="ec-seller">
                                                            <span class="ec-avatar-fallback">{{ strtoupper(mb_substr($role->name, 0, 1)) }}</span>
                                                            <a class="ec-seller-name" href="{{ route('roles.edit', ['id' => $role->id]) }}">{{ $role->name }}
                                                                @if ($role->id == 1)
                                                                    <i class="mdi mdi-shield-crown-outline ec-verified-badge" title="{{ trans('lang.super_admin') }}"></i>
                                                                @endif
                                                            </a>
                                                        </span>
                                                    </td>
                                                    @php $ec_admins = ($adminsByRole[$role->id] ?? collect())->all(); @endphp
                                                    <td>
                                                        @if (count($ec_admins))
                                                            <button type="button" class="ec-cat-chip ec-count-chip ec-admin-chip"
                                                                    data-role-name="{{ $role->name }}"
                                                                    data-admins="{{ json_encode($ec_admins) }}"
                                                                    title="{{ trans('lang.view_admins') }}">
                                                                {{ count($ec_admins) }} <i class="mdi mdi-eye-outline"></i>
                                                            </button>
                                                        @else
                                                            <span class="ec-cat-chip">0</span>
                                                        @endif
                                                    </td>
                                                    @php $ec_perms = ($permissionsByRole[$role->id] ?? collect())->all(); @endphp
                                                    <td>
                                                        @if (count($ec_perms))
                                                            <button type="button" class="ec-cat-chip ec-count-chip ec-perm-chip"
                                                                    data-role-name="{{ $role->name }}"
                                                                    data-perms="{{ json_encode($ec_perms) }}"
                                                                    title="{{ trans('lang.view_permissions') }}">
                                                                {{ count($ec_perms) }} <i class="mdi mdi-eye-outline"></i>
                                                            </button>
                                                        @else
                                                            <span class="ec-cat-chip">0</span>
                                                        @endif
                                                    </td>
                                                    <td class="action-btn ec-actions"> 
                                                        <a class="ec-act" href="{{route('roles.edit', ['id' => $role->id])}}">
                                                            <i class="mdi mdi-lead-pencil" title="{{ trans('lang.edit') }}"></i>
                                                        </a>
                                                        @can('roles.delete')
                                                        @if($role->id != 1)
                                                        <a class="ec-act" href="{{route('roles.delete', ['id' => $role->id])}}">
                                                            <i class="mdi mdi-delete-outline"></i>
                                                        </a>
                                                        @endif
                                                        @endcan
                                                    </td>
                                                </tr>
                                        @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" align="center">{{ trans('lang.no_result') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div>
                                            {{ $roles->links('pagination.pagination') }}
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

    {{-- Admins popup: which accounts hold this role --}}
    <div class="modal fade" id="adminsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('lang.assigned_admins') }} <span class="admins-role-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('lang.close') }}"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped perm-table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ trans('lang.name') }}</th>
                                    <th>{{ trans('lang.email') }}</th>
                                </tr>
                            </thead>
                            <tbody class="admins-list"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn ec-btn-ghost" data-dismiss="modal">{{ trans('lang.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Permissions popup: which permissions a role actually holds --}}
    <div class="modal fade" id="permissionsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('lang.role_permissions') }} <span class="perm-role-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('lang.close') }}"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="perm_search" class="form-control mb-3" placeholder="{{ trans('lang.search_here') }}">
                    <div class="role-table perm-scroll">
                        <table class="table table-striped perm-table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ trans('lang.menu') }}</th>
                                    <th>{{ trans('lang.permission') }}</th>
                                </tr>
                            </thead>
                            <tbody class="perm-list"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn ec-btn-ghost" data-dismiss="modal">{{ trans('lang.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    /* Permissions are stored as flat slugs ("zone.list"); group them by module so
       the popup reads like the role form rather than a wall of raw slugs. */
    var EC_ACTION_LABELS = {
        'index': '{{ trans('lang.list') }}', 'list': '{{ trans('lang.list') }}',
        'create': '{{ trans('lang.create') }}', 'store': '{{ trans('lang.create') }}',
        'edit': '{{ trans('lang.edit') }}', 'update': '{{ trans('lang.update') }}',
        'delete': '{{ trans('lang.delete') }}', 'view': '{{ trans('lang.view') }}'
    };
    function ecTitleCase(str) {
        return String(str).replace(/[-_.]+/g, ' ').replace(/([a-z])([A-Z])/g, '$1 $2')
            .replace(/\b\w/g, function (c) { return c.toUpperCase(); });
    }
    function ecBuildPermRows(perms) {
        if (!perms || !perms.length) return '';
        var groups = {}, order = [];
        perms.forEach(function (raw) {
            var slug = String(raw);
            var i = slug.indexOf('.');
            var key = (i === -1) ? slug : slug.slice(0, i);
            // a bare slug is the module's list page; "x.view" is the detail page
            var action = (i === -1) ? 'index' : slug.slice(i + 1);
            var label = ecTitleCase(key);
            if (!groups[label]) { groups[label] = []; order.push(label); }
            groups[label].push(action);
        });
        order.sort(function (a, b) { return a.localeCompare(b); });
        return order.map(function (label) {
            var seen = {};
            var acts = groups[label].map(function (a) {
                return EC_ACTION_LABELS[a.toLowerCase()] || ecTitleCase(a);
            }).filter(function (t) {
                if (seen[t]) return false;      // two routes that read the same show once
                seen[t] = true; return true;
            }).map(function (t) {
                return '<span class="perm-allowed"><i class="mdi mdi-check-circle"></i> ' + t + '</span>';
            }).join('');
            return '<tr class="perm-item"><td><strong>' + label + '</strong></td><td>' + acts + '</td></tr>';
        }).join('') + '<tr class="perm-empty-row" style="display:none"><td colspan="2" class="text-center text-muted py-4">{{ trans('lang.no_result') }}</td></tr>';
    }

    // Each row carries its own lists, so the popups need no extra request.
    $(document).on('click', '.ec-admin-chip', function () {
        var admins = $(this).data('admins') || [];
        $('.admins-role-name').text('— ' + $(this).data('role-name'));
        $('.admins-list').html(admins.map(function (a) {
            var tag = a.primary ? ' <span class="ec-cat-chip">{{ trans('lang.primary_account') }}</span>' : '';
            return '<tr><td>' + $('<div>').text(a.name || '').html() + tag + '</td><td>' + $('<div>').text(a.email || '').html() + '</td></tr>';
        }).join(''));
        $('#adminsModal').modal('show');
    });

    $(document).on('click', '.ec-perm-chip', function () {
        var perms = $(this).data('perms') || [];
        $('.perm-role-name').text('— ' + $(this).data('role-name'));
        $('#perm_search').val('');
        $('.perm-list').html(ecBuildPermRows(perms));
        $('.perm-empty-row').hide();
        $('#permissionsModal').modal('show');
    });

    $(document).on('keyup search input', '#perm_search', function () {
        var term = (this.value || '').toLowerCase();
        var shown = 0;
        $('.perm-list tr.perm-item').each(function () {
            var hit = $(this).text().toLowerCase().indexOf(term) !== -1;
            $(this).toggle(hit); if (hit) shown++;
        });
        $('.perm-empty-row').toggle(shown === 0);
    });
</script>

<script type="text/javascript">
        $("#is_active").click(function () {
            $("#roleTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(function (e) {
            e.preventDefault();
            let checked = $('#roleTable .is_open:checked');
            if (!checked.length) {
                alert("{{trans('lang.select_delete_alert')}}");
                return;
            }
            ecConfirm("{{trans('lang.selected_delete_alert')}}").then(function (ok) {
                if (!ok) return;
                let arrayUsers = [];
                checked.each(function () {
                    arrayUsers.push($(this).attr('dataId'));
                });
                // join with comma
                let ids = arrayUsers.join(',');
                let url = "{{ url('roles/delete', 'ids') }}";
                url = url.replace('ids', ids);
                window.location.href = url;
            });
        });
</script>
@endsection