<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?> dir="rtl" <?php } ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title id="app_name">{{ $app_setting->admin_title ?: config('app.name', 'CabMe') }}</title>
    <meta name="title" content="{{ $app_setting->admin_title ?: config('app.name', 'CabMe') }}">

    @if (!empty($app_setting->app_logo_favicon) && file_exists(public_path('assets/images/' . $app_setting->app_logo_favicon)))
        <link rel="icon" id="favicon" type="image/x-icon" href="{{ asset('assets/images/') . '/' . $app_setting->app_logo_favicon }}">
    @else
        <link rel="icon" id="favicon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap">

    @php
        // Cache buster for the stylesheets we actually edit. They are served without
        // far-future headers, so without a changing URL the browser keeps its old copy
        // and CSS changes appear not to apply until a manual hard refresh.
        $ec_asset_version = function ($path) {
            $full = public_path($path);
            return file_exists($full) ? filemtime($full) : null;
        };
    @endphp

    <!-- Vendor styles: everything style.css needs to override loads first -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
        <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap-rtl.min.css') }}" rel="stylesheet">
    <?php } ?>
    <link href="{{ asset('css/icons/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/icons/font-awesome/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chosen.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tagsinput.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/summernote/summernote.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1/daterangepicker.min.css">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/7.2.96/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('css/leaflet/leaflet.css') }}">
    <link rel="stylesheet" href="{{ asset('css/leaflet/leaflet.draw.css') }}">

    @php
        // Panel accent. settings.adminpanel_color is the source of truth (shared by
        // AppServiceProvider); everything downstream reads the tokens below rather
        // than hardcoding a hex, so one setting recolours the whole panel.
        $ec_hex = ltrim($admin_panel_color ?? '#facc15', '#');
        if (strlen($ec_hex) === 3) {
            $ec_hex = $ec_hex[0].$ec_hex[0].$ec_hex[1].$ec_hex[1].$ec_hex[2].$ec_hex[2];
        }
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $ec_hex)) {
            $ec_hex = 'facc15';
        }
        $ec_r = hexdec(substr($ec_hex, 0, 2));
        $ec_g = hexdec(substr($ec_hex, 2, 2));
        $ec_b = hexdec(substr($ec_hex, 4, 2));

        // White ink on the accent, always — matching the reference panels. The YIQ
        // pick used dark text on light accents (this panel's yellow), which was
        // rejected in review: brand buttons read white like foodie's.
        $ec_contrast = '#ffffff';

        // Pressed/hover shade and the two tinted surfaces the components use.
        $ec_mix = function ($c, $ratio, $towards) {
            return (int) round($c + ($towards - $c) * $ratio);
        };
        $ec_dark = sprintf('#%02x%02x%02x', $ec_mix($ec_r, .18, 0), $ec_mix($ec_g, .18, 0), $ec_mix($ec_b, .18, 0));
        $ec_tint = sprintf('#%02x%02x%02x', $ec_mix($ec_r, .92, 255), $ec_mix($ec_g, .92, 255), $ec_mix($ec_b, .92, 255));
        $ec_tint_strong = sprintf('#%02x%02x%02x', $ec_mix($ec_r, .70, 255), $ec_mix($ec_g, .70, 255), $ec_mix($ec_b, .70, 255));

        // Link ink. A light accent (this panel's yellow) is unreadable as text on
        // white, so links get a heavily darkened shade of it; an accent that is
        // already dark enough (foodie's orange) is used as-is.
        $ec_yiq = ($ec_r * 299 + $ec_g * 587 + $ec_b * 114) / 1000;
        $ec_link = $ec_yiq >= 160
            ? sprintf('#%02x%02x%02x', $ec_mix($ec_r, .48, 0), $ec_mix($ec_g, .48, 0), $ec_mix($ec_b, .48, 0))
            : '#' . $ec_hex;
        $ec_link_dark = sprintf('#%02x%02x%02x',
            $ec_mix(hexdec(substr(ltrim($ec_link, '#'), 0, 2)), .18, 0),
            $ec_mix(hexdec(substr(ltrim($ec_link, '#'), 2, 2)), .18, 0),
            $ec_mix(hexdec(substr(ltrim($ec_link, '#'), 4, 2)), .18, 0));
    @endphp
    <style>
        :root {
            --admin-panel-color: {{ '#'.$ec_hex }};
            --admin-panel-rgb: {{ $ec_r }}, {{ $ec_g }}, {{ $ec_b }};
            --admin-panel-contrast: {{ $ec_contrast }};
            --admin-panel-color-dark: {{ $ec_dark }};
            --admin-panel-tint: {{ $ec_tint }};
            --admin-panel-tint-strong: {{ $ec_tint_strong }};
            --admin-panel-link: {{ $ec_link }};
            --admin-panel-link-dark: {{ $ec_link_dark }};
            --admin-panel-sec-color: {{ $admin_panel_sec_color ?? '#6365f1' }};
        }
    </style>

    {{-- style.css is the single design-system sheet. It loads after every vendor sheet
         (bootstrap, select2, leaflet, icons) so its rules win on specificity ties.
         admin.css sits last: it is the accent-colour override layer. --}}
    <link href="{{ asset('css/style.css') }}?v={{ $ec_asset_version('css/style.css') }}" rel="stylesheet">
    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
        <link href="{{ asset('css/style_rtl.css') }}?v={{ $ec_asset_version('css/style_rtl.css') }}" rel="stylesheet">
    <?php } ?>
    <link href="{{ asset('css/admin.css') }}?v={{ $ec_asset_version('css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}?v={{ $ec_asset_version('css/custom.css') }}" rel="stylesheet">

    @yield('style')
</head>

<body>

<div id="app" class="fix-header fix-sidebar card-no-border">
    <div id="main-wrapper">
        {{-- Kept as #data-table_processing: list views show and hide this overlay by
             that exact id while a request is in flight. --}}
        <div id="data-table_processing" class="page-overlay" style="display:none;">
            <div class="ec-loader-box">
                <span class="ec-spinner" role="status" aria-label="Loading"></span>
                <span class="ec-spinner-label">{{ $app_setting->admin_title ?: config('app.name', 'CabMe') }}</span>
            </div>
        </div>

        @if (!View::hasSection('header'))
            <header class="topbar non-printable">
                <nav class="navbar top-navbar navbar-expand-md navbar-light">
                    @include('layouts.header')
                </nav>
            </header>
        @endif

        @if (!View::hasSection('sidebar'))
            <aside class="left-sidebar non-printable">
                <div class="scroll-sidebar">
                    @include('layouts.menu')
                </div>
            </aside>
        @endif
    </div>

    <main class="py-4">
        @yield('content')
    </main>

    {{-- The footer sits in normal flow after the content: the theme's .footer is
         position:absolute against #main-wrapper, which does not grow with the page,
         so content scrolled over it. style.css makes it static. --}}
    @if (!View::hasSection('footer'))
        @include('layouts.footer')
    @endif
</div>

<script src="{{ asset('js/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('js/leaflet/leaflet.draw.js') }}"></script>
<script src="{{ asset('js/leaflet/leaflet.editable.min.js') }}"></script>
<script src="{{ asset('js/leaflet/leaflet.draw-src.js') }}"></script>
<script src="{{ asset('js/leaflet/leaflet-routing-machine.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('js/waves.js') }}"></script>
<script src="{{ asset('js/sidebarmenu.js') }}"></script>
<script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('js/toastr.min.js') }}"></script>

{{-- Laravel flash messages and validation errors as toasts.
     Controllers redirect back with ->with('message'|'success'|'error', …); rather
     than every page rendering its own banner (and several of them forgetting to),
     they are picked up here once so a save reports the same way everywhere. --}}
@php
    $ec_flash_ok = array_values(array_filter([session('message'), session('success'), session('success_bulk')], fn ($m) => is_string($m) && $m !== ''));
    $ec_flash_err = array_values(array_filter(array_merge(
        is_array(session('error')) ? session('error') : [session('error')],
        $errors->all()
    ), fn ($m) => is_string($m) && $m !== ''));
@endphp
@if ($ec_flash_ok || $ec_flash_err)
    <script>
        $(function () {
            @foreach ($ec_flash_ok as $ec_msg)
                toastr.success(@json($ec_msg));
            @endforeach
            @foreach ($ec_flash_err as $ec_msg)
                toastr.error(@json($ec_msg));
            @endforeach
        });
    </script>
@endif

<script src="{{ asset('assets/plugins/summernote/summernote.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="{{ asset('js/chosen.jquery.js') }}"></script>
<script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.1/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>

{{-- Shared ec-* form behaviour (styled file inputs, etc.). It runs outside Blade,
     so the strings it needs are handed over here. --}}
<script>
    window.ecLang = { error: @json(trans('lang.error')) };
    window.ecStrings = window.ecStrings || {};
    window.ecStrings.somethingWrong = @json(trans('lang.something_went_wrong'));
    {{-- placeholder in the country picker's search box --}}
    window.ecStrings.searchCountry = @json(trans('lang.search_country'));
    {{-- Buttons on the ecConfirm popup, so the dialog follows the panel language. --}}
    {{-- Own keys rather than lang.cancel, which reads "Back" for form footers. --}}
    window.ecStrings.yes = @json(trans('lang.confirm_yes'));
    window.ecStrings.cancel = @json(trans('lang.confirm_cancel'));
    window.ecStrings.ok = @json(trans('lang.confirm_ok'));
</script>
<script src="{{ asset('js/ec-form.js') }}?v={{ $ec_asset_version('js/ec-form.js') }}"></script>

<script type="text/javascript">

    jQuery(window).scroll(function () {
        var scroll = jQuery(window).scrollTop();
        if (scroll <= 60) {
            jQuery("body").removeClass("sticky");
        } else {
            jQuery("body").addClass("sticky");
        }
    });

    function loadGoogleMapsScript() {

        const script = document.createElement('script');
        var mapType = "{!! $app_setting->map_for_application !!}";
        if (mapType == "OSM") {
            script.src = "{{ asset('js/leaflet/leaflet.js') }}";
            script.src = "{{ asset('js/leaflet/leaflet.draw.js') }}";
            script.src = "{{ asset('js/leaflet/leaflet.editable.min.js') }}";
            script.src = "{{ asset('js/leaflet/leaflet.draw-src.js') }}";
            script.src = "{{ asset('js/leaflet/leaflet.ajax.min.js') }}";
            script.src = "{{ asset('js/leaflet/leaflet-routing-machine.js') }}";
        } else {
            script.src = "https://maps.googleapis.com/maps/api/js?key={{ $app_setting->google_map_api_key }}&libraries=drawing,geometry,places";
        }
        script.onload = function () {
            navigator.geolocation.getCurrentPosition(GeolocationSuccessCallback, GeolocationErrorCallback);
            // Only announce that the map library is ready. Calling initMap() here
            // as well ran it twice on pages that listen for this event: Leaflet
            // then throws "Map container is already initialized" and can leave the
            // container blank. Every map screen starts its own map, either on
            // document ready or from this event.
            document.dispatchEvent(new Event("mapsLoaded"));
        };
        document.head.appendChild(script);
    }

    const GeolocationSuccessCallback = (position) => {
        if (position.coords != undefined) {
            default_latitude = position.coords.latitude
            default_longitude = position.coords.longitude
            setCookie('default_latitude', default_latitude, 365);
            setCookie('default_longitude', default_longitude, 365);
        }
    };

    const GeolocationErrorCallback = (error) => {
        console.log('Error: You denied for your default Geolocation', error.message);
        setCookie('default_latitude', '23.022505', 365);
        setCookie('default_longitude', '72.571365', 365);
    };

    // Only inside the panel. On the login screen this would fire the browser's
    // geolocation permission prompt before anyone has signed in.
    @auth
        loadGoogleMapsScript();
    @endauth

    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        let name = cname + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    $(document).ready(function () {
        var url = "{{ route('language.header') }}";
        $.ajax({
            url: url,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
            },
            dataType: 'json',
            success: function (data) {
                $.each(data, function (key, value) {
                    $('#language_dropdown').append($("<option></option>").attr("value", value.code).text(value.language));
                });
                <?php if (session()->get('locale')) { ?>
                    $("#language_dropdown").val("<?php echo session()->get('locale'); ?>");
                <?php } ?>
                // Only show the picker once there is more than one language to pick.
                if (data.length > 1) {
                    $('#language_dropdown_box').css('visibility', 'visible');
                }
            }
        });
    });

    $(document).on('change', '.changeLang', function () {
        var slug = $(this).val();
        var url = "{{ route('lang.code', ':slugid') }}";
        url = url.replace(':slugid', slug);
        if (slug) {
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                },
                dataType: 'json',
                success: function (data) {
                    $.each(data, function (key, value) {
                        if (value.code == slug) {
                            if (value.is_rtl == false) {
                                setCookie('is_rtl', 'false', 365);
                            } else {
                                setCookie('is_rtl', value.is_rtl.toString(), 365);
                            }
                            window.location.href = "{{ route('changeLang') }}" + "?lang=" + value.code;
                        }
                    });
                }
            });
        }
    });

    // ── Language dropdown ────────────────────────────────────────────────────
    // Presentational shell over the hidden native <select> the ajax above fills.
    // Picking an item writes back to the select and fires its change event, so the
    // .changeLang handler stays the single place that switches language.
    (function () {
        var btn  = document.getElementById('ec-lang-btn');
        var menu = document.getElementById('ec-lang-menu');
        var cur  = document.getElementById('ec-lang-current');
        var sel  = document.getElementById('language_dropdown');
        var box  = document.getElementById('language_dropdown_box');
        if (!btn || !menu || !cur || !sel || !box) return;

        function rebuild() {
            var opts = Array.prototype.slice.call(sel.options);
            if (!opts.length) return; // keep the server-rendered code until options land

            var currentCode = String(sel.value || opts[0].value).toLowerCase();
            var active = opts.filter(function (o) {
                return String(o.value).toLowerCase() === currentCode;
            })[0] || opts[0];

            cur.textContent = active.textContent;

            // Active option first, the rest in their original order.
            var ordered = [active].concat(opts.filter(function (o) { return o !== active; }));
            menu.innerHTML = ordered.map(function (o) {
                var isActive = o === active;
                return '<li role="menuitem" data-code="' + o.value + '" class="' + (isActive ? 'active' : '') + '">' +
                           '<span>' + o.textContent + '</span>' +
                           (isActive ? '<i class="mdi mdi-check"></i>' : '') +
                       '</li>';
            }).join('');
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = box.classList.toggle('open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        menu.addEventListener('click', function (e) {
            var li = e.target.closest('li[data-code]');
            if (!li) return;
            box.classList.remove('open');
            sel.value = li.dataset.code;
            $(sel).trigger('change');
        });

        document.addEventListener('click', function (e) {
            if (!box.contains(e.target)) box.classList.remove('open');
        });

        // Options arrive asynchronously from language.header — rebuild when they do.
        new MutationObserver(rebuild).observe(sel, { childList: true });
        setTimeout(rebuild, 800);
    })();

</script>

{{-- Live SOS alerts. Signed-in panel only: there is nobody to alert on the login
     screen, and the credentials are not worth shipping to an anonymous visitor. --}}
@auth
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script>
    var pusherSettings = @json($app_setting->pusher_settings);
    var pusher = new Pusher(pusherSettings.pusher_key, {
        cluster: pusherSettings.pusher_cluster
    });
    var channel = pusher.subscribe('sos');
    channel.bind('updated', function (response) {
        var data = response.data;
        Swal.fire({
            icon: 'warning',
            title: 'SOS Alert!',
            html: `<b>User:</b> ${data.user.prenom} ${data.user.nom}<br><b>Location:</b> ${data.destination_name}`,
            showCancelButton: false,
            confirmButtonText: 'View Details',
            confirmButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/sos/show/' + data.sos_id;
            }
        });
    });
</script>
@endauth

@yield('scripts')

<script>
    // Row action icons carry no label, so each has a Bootstrap tooltip saying what
    // it does. Binding on the body with a `selector` delegates, so rows rendered
    // after load are covered without re-initialising.
    jQuery(function () {
        jQuery('body').tooltip({
            // An element can only carry one data-toggle (some controls use it for a
            // modal), so titled action chips are included by class as well.
            selector: '[data-toggle="tooltip"], .ec-act[title]',
            container: 'body',
            trigger: 'hover',
            placement: 'top'
        });
        // A tooltip left open when its row is redrawn would otherwise stay on screen.
        jQuery(document).on('draw.dt page.dt', function () {
            jQuery('.tooltip').remove();
        });
    });
</script>

</body>

</html>
