@extends('layouts.app')
@section('content')
    {{-- Laid out on foodie's admin_users/edit.blade.php: one container-fluid,
         card-header badge, then the profile fields two per row. The form wraps
         both the body and the actions so the submit stays inside it. --}}
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.admin_user_edit') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.admin_manage_desc') }}</p>
                </div>
                <a href="{!! route('admin-users.index') !!}" class="btn ec-btn-ghost">
                    <i class="mdi mdi-arrow-left"></i> {{ trans('lang.admin_table') }}
                </a>
            </div>

            <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-shield-account-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.profile_details') }}</h5>
                </div>
                <form method="post" action="{{ route('admin-users.update', $user->id) }}">
                    @csrf
                    <div class="card-body">
                        <div class="error_top">@if (Session::has('message'))<p>{{ Session::get('message') }}</p>@endif</div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.user_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $user->name }}" placeholder="{{ trans('lang.user_name_help') }}">
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.user_email') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="email" value="{{ $user->email }}" placeholder="{{ trans('lang.user_email_help') }}">
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.old_password') }}</label>
                                <input type="password" class="form-control" name="old_password" placeholder="{{ trans('lang.old_password_help') }}">
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.new_password') }}</label>
                                <input type="password" class="form-control" name="password" placeholder="{{ trans('lang.user_password_help') }}">
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.confirm_password') }}</label>
                                <input type="password" class="form-control" name="confirm_password" placeholder="{{ trans('lang.confirm_password_help') }}">
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="ec-form-label">{{ trans('lang.role') }} <span class="text-danger">*</span></label>
                                <select class="form-control" name="role_id">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ $role->id == $roleId ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ec-form-actions">
                        @if ($user->id != 1)
                            <button type="submit" class="btn ec-btn-brand edit-form-btn" id="save_user_btn">{{ trans('lang.save') }}</button>
                        @endif
                        <a href="{!! route('admin-users.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
