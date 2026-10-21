<div class="navbar-header">
    <a class="navbar-brand" href="{{ url('/') }}">
        <b>
            @if (!empty($app_setting->app_logo) && file_exists(public_path('assets/images/' . $app_setting->app_logo)))
                <img src="{{ asset('assets/images/') . '/' . $app_setting->app_logo }}" alt="homepage" class="dark-logo" id="logo_web">
            @else
                <img src="{{ asset('/images/logo.png') }}" alt="homepage" class="dark-logo" id="logo_web">
            @endif

            @if (!empty($app_setting->app_logo_small) && file_exists(public_path('assets/images/' . $app_setting->app_logo_small)))
                <img src="{{ asset('assets/images/') . '/' . $app_setting->app_logo_small }}" alt="homepage" class="light-logo" id="small-logo">
            @else
                <img src="{{ asset('images/logo-small.png') }}" alt="homepage" class="light-logo" id="small-logo">
            @endif
        </b>
        <span></span>
    </a>
</div>

<div class="navbar-collapse">
    <ul class="navbar-nav mr-auto mt-md-0">
        <li class="nav-item">
            <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)">
                <i class="mdi mdi-menu"></i>
            </a>
        </li>
        <li class="nav-item m-l-0">
            <a class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark" href="javascript:void(0)">
                <i class="mdi mdi-arrow-left"></i>
            </a>
        </li>
    </ul>

    {{-- Language picker. The hidden native <select> stays in the DOM because the
         ajax in layouts/app.blade.php populates it and the .changeLang handler
         listens on it; the visible list mirrors its options. The whole box stays
         hidden until more than one language is active. --}}
    <div class="ec-lang" id="language_dropdown_box" style="visibility: hidden;">
        <button type="button" class="ec-lang-btn" id="ec-lang-btn" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-globe"></i>
            <span id="ec-lang-current">{{ strtoupper(session()->get('locale', app()->getLocale())) }}</span>
            <i class="mdi mdi-chevron-down ec-lang-chev"></i>
        </button>
        <ul class="ec-lang-menu" id="ec-lang-menu" role="menu"></ul>
        <select class="changeLang" id="language_dropdown" style="display:none" aria-hidden="true"></select>
    </div>

    <ul class="navbar-nav my-lg-0 border-left pl-2 ml-2">
        <li class="nav-item dropdown">
            <div class="d-flex align-items-center dropdown-toggle text-muted waves-effect waves-dark"
                 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <a class="nav-link" href="">
                    <img src="{{ asset('/images/user.png') }}" alt="user" class="profile-pic">
                </a>
                {{-- Name only in the top bar — the role lives in the dropdown's user box. --}}
                <div class="nav-item-user">
                    <h5 class="text-dark mb-0">{{ Auth::check() ? Auth::user()->name : '' }}</h5>
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-right scale-up">
                <ul class="dropdown-user">
                    <li>
                        <div class="dw-user-box">
                            <div class="u-img">
                                <img src="{{ asset('/images/user.png') }}" alt="user" style="max-width: 45px;">
                            </div>
                            <div class="u-text">
                                <h4>{{ Auth::check() ? Auth::user()->name : '' }}</h4>
                                <p class="text-muted">{{ Auth::check() ? Auth::user()->getRoleNames()->first() : '' }}</p>
                            </div>
                        </div>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="{{ route('users.profile') }}">
                            <i class="ti-user"></i> {!! trans('lang.user_profile') !!}
                        </a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-power-off"></i> {{ trans('lang.logout') }}
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </ul>
            </div>
        </li>
    </ul>
</div>
