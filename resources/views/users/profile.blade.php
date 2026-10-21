@extends('layouts.app')
@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.user_profile') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.user_profile_desc') }}</p>
            </div>
            <a href="{!! route('dashboard') !!}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.dashboard') }}</a>
        </div>
        @if (Session::has('message'))
            <div class="alert alert-error error_top"><p>{{ Session::get('message') }}</p></div>
        @endif
        <form method="post" action="{{ route('users.profile.update', $user->id) }}">
            @csrf
            <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-account-circle-outline"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.profile_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label" for="profile_name">{{ trans('lang.user_name') }}</label>
                            <div class="ec-fcol">
                                <input type="text" class="form-control" id="profile_name" name="name" value="{{ $user->name }}">
                                <div class="ec-form-help">{{ trans('lang.user_name_help') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label" for="profile_email">{{ trans('lang.user_email') }}</label>
                            <div class="ec-fcol">
                                <input type="text" class="form-control" id="profile_email" value="{{ $user->email }}" name="email">
                                <div class="ec-form-help">{{ trans('lang.user_email_help') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card ec-card ec-form-card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-key-variant"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.change_password') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="ec-form-label" for="old_password">{{ trans('lang.old_password') }}</label>
                            <div class="ec-fcol">
                                <input type="password" class="form-control" id="old_password" name="old_password" autocomplete="current-password">
                                <div class="ec-form-help">{{ trans('lang.old_password_help') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="ec-form-label" for="new_password">{{ trans('lang.new_password') }}</label>
                            <div class="ec-fcol">
                                <input type="password" class="form-control" id="new_password" name="password" autocomplete="new-password">
                                <div class="ec-form-help">{{ trans('lang.user_password_help') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="ec-form-label" for="confirm_password">{{ trans('lang.confirm_password') }}</label>
                            <div class="ec-fcol">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" autocomplete="new-password">
                                <div class="ec-form-help">{{ trans('lang.confirm_password_help') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-setting-btn" id="edit-setting-btn">{{ trans('lang.save') }}</button>
                    <a href="{!! route('dashboard') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
@endsection
