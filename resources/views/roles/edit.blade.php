@extends('layouts.app')
@section('content')
    {{-- Laid out on foodie's role/edit.blade.php: page head with a sub-heading,
         a single ec-form-card, the name and the All Permissions switch on one
         baseline-aligned row, then the permission matrix under its own label.
         The data (menus, labelMap, rolePermissions) comes from the controller
         exactly as before — only the presentation changed. --}}
    @php $ec_is_super = $role->id == 1; @endphp
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.edit_role') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.role_edit_desc') }}</p>
                </div>
                <a href="{!! route('roles.index') !!}" class="btn ec-btn-ghost">
                    <i class="mdi mdi-arrow-left"></i> {{ trans('lang.role_plural') }}
                </a>
            </div>
            {{-- The page head and the form card sit inside ONE
                 container-fluid, so their left and right edges line up. --}}
            <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-shield-key-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.role_details') }}</h5>
                </div>
                <div class="card-body">
                <div class="error_top" style="display:none"></div>
                <div class="success_top" style="display:none"></div>
                <form action="{{ route('roles.update', $role->id) }}" method="post" id="roleForm">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label">{{ trans('lang.name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ $role->name }}"
                                   placeholder="{{ trans('lang.role_name_placeholder') }}">
                        </div>
                        <div class="col-md-6 mb-3 d-flex justify-content-end">
                            <div class="switch-box switch-box-tight">
                                <label class="control-label">{{ trans('lang.all_permissions') }}</label>
                                <label class="switch">
                                    <input type="checkbox" name="all_permission" id="all_permission">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <label class="ec-form-label mb-2">{{ trans('lang.assign_permissions') }} <span class="text-danger">*</span></label>
                    @if ($ec_is_super)
                        <div class="ec-superadmin-note d-flex align-items-center mb-3">
                            <i class="mdi mdi-shield-crown-outline mr-2"></i>
                            <span>{{ trans('lang.super_admin_locked_note') }}</span>
                        </div>
                    @endif
                    <div class="role-table">
                        <table class="table table-striped">
                            <thead>
                                <th>{{ trans('lang.menu') }}</th>
                                <th>{{ trans('lang.permission') }}</th>
                            </thead>
                            <tbody>
                                @foreach ($menus as $menu => $actions)
                                    <tr>
                                        <td><strong>{{ Str::title(str_replace(['-', '_', '.'], ' ', $menu)) }}</strong></td>
                                        <td>
                                            @foreach (['list', 'create', 'edit', 'delete', 'view'] as $act)
                                                @if (isset($actions[$act]))
                                                    @php
                                                        $permName = $actions[$act];
                                                        $checked = in_array($permName, $rolePermissions);
                                                    @endphp
                                                    <input type="checkbox" name="permissions[]"
                                                        value="{{ $permName }}"
                                                        id="{{ $menu . '_' . $act }}"
                                                        {{ $checked ? 'checked' : '' }} class="permission">
                                                    <label class="control-label2" for="{{ $menu . '_' . $act }}">{{ $labelMap[$act] }}</label>
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
                </div>
                <div class="ec-form-actions">
                    <button type="button" class="btn ec-btn-brand edit-form-btn">{{ trans('lang.update_role') }}</button>
                    <a href="{{ route('roles.index') }}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        @if ($ec_is_super)
        // Super Admin (role 1) — full access, read-only. Show everything granted
        // and lock the form; the controller rejects updates for id 1 regardless.
        $('.permission').prop('checked', true).prop('disabled', true);
        $('#all_permission').prop('checked', true).prop('disabled', true);
        $('#name').prop('readonly', true);
        $('.edit-form-btn').hide();
        @endif

        $(".edit-form-btn").click(function () {
            $(".success_top").hide();
            $(".error_top").hide();
            var name = $("#name").val();
            if (name == "") {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.user_name_help') }}</p>");
                window.scrollTo(0, 0);
                return false;
            } else {
                $('form#roleForm').submit();
            }
        });

        $('#all_permission').on('click', function () {
            if ($(this).is(':checked')) {
                $.each($('.permission'), function () {
                    $(this).prop('checked', true);
                });
            } else {
                $.each($('.permission'), function () {
                    $(this).prop('checked', false);
                });
            }
        });
    </script>
@endsection
