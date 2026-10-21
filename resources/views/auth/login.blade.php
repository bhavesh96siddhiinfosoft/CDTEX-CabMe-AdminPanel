@extends('layouts.app')

{{-- Guest page: suppress the panel chrome. layouts/app only renders the topbar,
     sidebar and footer when these sections are undefined. --}}
@section('header', '')
@section('sidebar', '')
@section('footer', '')

@section('style')
<style type="text/css">
    /* --admin-panel-color is set on :root by layouts/app; alias it locally. */
    :root { --brand: var(--admin-panel-color); --brand-rgb: var(--admin-panel-rgb); }

    body { margin: 0; font-family: 'Urbanist', 'Nunito', sans-serif; background: #fff; }

    /* Zero out the layout's content padding and hide the (empty) chrome wrapper. */
    #app:has(.auth-split) main.py-4 { padding: 0 !important; margin: 0; }
    #app:has(.auth-split) #main-wrapper { display: none; }

    .auth-split { display: flex; min-height: 100vh; }

    /* Left branding panel */
    .auth-brand {
        flex: 1 1 52%;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 48px;
        background:
            radial-gradient(circle at 22% 18%, rgba(var(--brand-rgb), .22), transparent 42%),
            radial-gradient(circle at 82% 85%, rgba(var(--brand-rgb), .14), transparent 40%),
            linear-gradient(160deg, #0b0f18 0%, #10151f 60%, #0b0f18 100%);
    }
    .auth-brand::before {
        content: ''; position: absolute; width: 540px; height: 540px; border-radius: 50%;
        background: rgba(var(--brand-rgb), .10); top: -160px; right: -160px;
    }
    .auth-brand::after {
        content: ''; position: absolute; width: 380px; height: 380px; border-radius: 50%;
        border: 1px solid rgba(var(--brand-rgb), .18); bottom: -120px; left: -110px;
    }
    .auth-brand-inner { position: relative; z-index: 2; text-align: center; max-width: 760px; }
    .auth-brand-logo { max-width: 210px; margin: 0 auto 34px; display: inline-block; line-height: 0; }
    .auth-brand-logo img { max-width: 100%; height: auto; }
    .auth-brand-inner h1 { color: #fff; font-size: 30px; line-height: 1.3; font-weight: 800; margin: 0 0 16px; }
    .auth-brand-inner h1 span { color: var(--brand); }
    .auth-brand-inner p { color: rgba(255, 255, 255, .62); font-size: 14px; line-height: 1.7; margin: 0; }

    /* Right form panel */
    .auth-form-wrap {
        flex: 1 1 48%;
        display: flex; align-items: center; justify-content: center;
        padding: 40px 24px; background: #fff;
    }
    .auth-card { width: 100%; max-width: 420px; }
    .auth-logo { max-width: 190px; margin-bottom: 30px; display: inline-block; line-height: 0; }
    .auth-logo img { max-width: 100%; height: auto; }
    .auth-card h2 { font-size: 28px; font-weight: 800; color: #0C111C; margin: 0 0 8px; }
    .auth-sub { color: #8a90a6; font-size: 15px; margin: 0 0 30px; }

    .auth-field { margin-bottom: 18px; }
    .auth-field > label { display: block; font-size: 14px; font-weight: 600; color: #3b4060; margin-bottom: 8px; }
    .auth-field input {
        width: 100%; border: 1px solid #e2e6ef; border-radius: 12px; padding: 14px 16px;
        font-size: 15px; color: #0C111C; outline: none; background: #fff;
        transition: border-color .15s, box-shadow .15s; font-family: inherit;
    }
    .auth-field input::placeholder { color: #b3b9c9; }
    .auth-field input:focus { border-color: var(--brand); box-shadow: 0 0 0 3px rgba(var(--brand-rgb), .18); }
    .auth-pass { position: relative; }
    .auth-pass input { padding-right: 48px; }
    .auth-eye {
        position: absolute; right: 6px; top: 0; bottom: 0; width: 40px;
        display: flex; align-items: center; justify-content: center;
        color: #9aa1b4; cursor: pointer; font-size: 21px; background: none; border: none;
    }
    .auth-eye:hover { color: var(--brand); }

    .auth-row { display: flex; align-items: center; justify-content: space-between; margin: 4px 0 26px; }
    .auth-remember { display: flex; align-items: center; gap: 9px; font-size: 14.5px; color: #5a6179; cursor: pointer; margin: 0; user-select: none; }
    /* Undo the base theme's global checkbox reset (it parks native boxes off-screen). */
    .auth-remember input[type="checkbox"] {
        position: static; left: auto; opacity: 1; width: 18px; height: 18px;
        accent-color: var(--brand); cursor: pointer; margin: 0;
    }

    .auth-btn {
        width: 100%; border: none; border-radius: 12px; padding: 15px; font-size: 16px; font-weight: 700;
        background: var(--brand); color: var(--admin-panel-contrast, #fff); cursor: pointer; font-family: inherit;
        box-shadow: 0 8px 20px rgba(var(--brand-rgb), .34); transition: box-shadow .15s, transform .06s;
    }
    .auth-btn:hover { box-shadow: 0 10px 26px rgba(var(--brand-rgb), .46); }
    .auth-btn:active { transform: translateY(1px); }

    .auth-alert {
        border-radius: 10px; padding: 12px 16px; margin-bottom: 18px; font-size: 14px;
        background: #fdecec; color: #c0392b; border: 1px solid #f6c9c9;
    }

    /* Demo credentials — click to copy + autofill */
    .auth-creds {
        display: flex; align-items: center; gap: 14px; margin-top: 26px;
        background: rgba(var(--brand-rgb), .10); border: 1px solid rgba(var(--brand-rgb), .28);
        border-radius: 12px; padding: 12px 14px; cursor: pointer; transition: background .15s;
    }
    .auth-creds:hover { background: rgba(var(--brand-rgb), .16); }
    .auth-creds-icon {
        width: 42px; height: 42px; flex: 0 0 42px; border-radius: 10px; background: var(--brand);
        display: flex; align-items: center; justify-content: center;
        color: var(--admin-panel-contrast, #fff); font-size: 20px;
    }
    .auth-creds-text { font-size: 14px; color: #3b4060; line-height: 1.65; }
    .auth-creds-text strong { color: #0C111C; }

    @media (max-width: 900px) {
        .auth-brand { display: none; }
        .auth-form-wrap { flex: 1 1 100%; }
    }
</style>
@endsection

@section('content')
@php
    $ec_logo = (!empty($app_setting->app_logo) && file_exists(public_path('assets/images/' . $app_setting->app_logo)))
        ? asset('assets/images/') . '/' . $app_setting->app_logo
        : asset('images/logo.png');
    $ec_app_name = $app_setting->admin_title ?: config('app.name', 'CabMe');
@endphp
<section class="auth-split">
    <div class="auth-brand">
        <div class="auth-brand-inner">
            <div class="auth-brand-logo">
                <img src="{{ $ec_logo }}" alt="logo" class="brand-logo-img">
            </div>
            <h1>
                {{ trans('lang.welcome_to') }}
                <span id="brand-app-name">{{ $ec_app_name }}</span>
                {{ trans('lang.admin_panel_label') }}
            </h1>
            <p>{{ trans('lang.login_brand_tagline') }}</p>
        </div>
    </div>

    <div class="auth-form-wrap">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="{{ $ec_logo }}" alt="logo" class="brand-logo-img">
            </div>
            <h2>{{ trans('lang.welcome_back') }}</h2>
            <p class="auth-sub">{{ trans('lang.login_enter_details') }}</p>

            @if (count($errors) > 0)
                @foreach ($errors->all() as $message)
                    <div class="auth-alert">{{ $message }}</div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="auth-field">
                    <label for="email">{{ trans('lang.email_address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           placeholder="{{ trans('lang.email_address') }}" required autocomplete="email" autofocus>
                </div>

                <div class="auth-field">
                    <label for="password">{{ trans('lang.password') }}</label>
                    <div class="auth-pass">
                        <input id="password" type="password" name="password"
                               placeholder="{{ trans('lang.password') }}" required autocomplete="current-password">
                        <button type="button" class="auth-eye" onclick="toggleAuthPass()" aria-label="Show password">
                            <i id="auth-eye-icon" class="mdi mdi-eye-outline"></i>
                        </button>
                    </div>
                </div>

                <div class="auth-row">
                    <label class="auth-remember">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        {{ trans('lang.remember_me') }}
                    </label>
                </div>

                <button type="submit" class="auth-btn">{{ trans('lang.login') }}</button>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script type="text/javascript">
    function toggleAuthPass() {
        var p = document.getElementById('password');
        var icon = document.getElementById('auth-eye-icon');
        if (p.type === 'password') { p.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
        else { p.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
    }
</script>
@endsection
