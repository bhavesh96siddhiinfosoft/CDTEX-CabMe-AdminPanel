@extends('layouts.app')

@section('content')
    {{-- Branding, laid out on foodie's settings/branding.blade.php: one
         ec-form-card holding an ec-section per group (application, logos,
         colours) rather than a single flat list. Field names, ids and the
         _section marker are unchanged, so the controller still writes only the
         branding columns. --}}
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">

            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.branding') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.settings_branding_desc') }}</p>
                </div>
                <a href="{{ route('settings') }}" class="btn ec-btn-ghost">
                    <i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}
                </a>
            </div>

            <div class="error_top"></div>

            <form action="{{ route('general-settings.update', ['id' => $settings->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- tells the controller which columns this page owns --}}
                <input type="hidden" name="_section" value="branding">

                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-body">

                        <div class="ec-section">
                            <h6 class="ec-section-title">{{ trans('lang.application_settings') }}</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label" for="title">{{ trans('lang.settings_panel_title') }}</label>
                                    <input type="text" class="form-control title" name="title" id="title" value="{{ $settings->title }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label" for="footer">{{ trans('lang.settings_panel_footer') }}</label>
                                    <input type="text" class="form-control" name="footer" id="footer" value="{{ $settings->footer }}">
                                </div>
                            </div>
                        </div>

                        <div class="ec-section">
                            <h6 class="ec-section-title">{{ trans('lang.logos_and_icons') }}</h6>
                            @php
                                // [field name, label key, help key, preview wrapper id, preview img id, onchange handler]
                                $ec_logos = [
                                    ['app_logo',         'app_logo',         'logo_admin_help', 'image_preview',  'uploding_image',  'readURL'],
                                    ['app_logo_small',   'app_logo_small',   'logo_small_help', 'image_preview2', 'uploding_image2', 'readURL2'],
                                    ['app_logo_favicon', 'app_logo_favicon', 'favicon_help',    'image_preview3', 'uploding_image3', 'readURL3'],
                                ];
                            @endphp
                            <div class="row">
                                @foreach ($ec_logos as [$ec_name, $ec_label, $ec_help, $ec_wrap, $ec_img, $ec_fn])
                                    @php $ec_file = $settings->{$ec_name}; @endphp
                                    <div class="col-md-4 mb-3">
                                        <label class="ec-form-label">{{ trans('lang.' . $ec_label) }}</label>
                                        <label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}">
                                            <input type="file" accept="image/*" name="{{ $ec_name }}" id="{{ $ec_name }}" onchange="{{ $ec_fn }}(this);">
                                            <i class="mdi mdi-cloud-upload-outline"></i>
                                            <span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span>
                                            <span class="ec-upload-hint">{{ trans('lang.png_svg') }}</span>
                                        </label>
                                        <div class="ec-form-help">{{ trans('lang.' . $ec_help) }}</div>
                                        <div id="{{ $ec_wrap }}" class="mt-2">
                                            @if (! empty($ec_file) && file_exists(public_path('assets/images/' . $ec_file)))
                                                <img class="rounded" id="{{ $ec_img }}" src="{{ asset('assets/images') . '/' . $ec_file }}" alt="image">
                                            @else
                                                <img class="rounded" id="{{ $ec_img }}" src="{{ asset('assets/images/logo-placeholder-image.png') }}" alt="image">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="ec-section">
                            <h6 class="ec-section-title">{{ trans('lang.panel_colours') }}</h6>
                            @php
                                // Ids/names unchanged so the controller keeps mapping them to the same columns.
                                $ec_colors = [
                                    ['adminpanel_color',     'adminpanel_color_settings'],
                                    ['adminpanel_sec_color', 'adminpanel_sec_color_settings'],
                                    ['website_color',        'website_color_settings'],
                                    ['driverapp_color',      'driverapp_color_settings'],
                                ];
                            @endphp
                            {{-- Two per row, so the panel pair sits on one line and the two
                                 app colours drop together onto the next. --}}
                            <div class="row">
                                @foreach ($ec_colors as [$ec_id, $ec_label])
                                    <div class="col-md-6 mb-3">
                                        <label class="ec-form-label" for="{{ $ec_id }}">{{ trans('lang.' . $ec_label) }}</label>
                                        <div class="ec-color-input">
                                            <input type="color" class="brand-color-input" name="{{ $ec_id }}" id="{{ $ec_id }}" value="{{ $settings->{$ec_id} }}">
                                            <code class="ec-color-hex" data-for="{{ $ec_id }}">#000000</code>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    <div class="ec-form-actions">
                        <button type="submit" class="btn ec-btn-brand edit-setting-btn">{{ trans('lang.save') }}</button>
                        <a href="{{ route('settings') }}" class="btn ec-btn-ghost">{{ trans('lang.back') }}</a>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Mirror each swatch into its hex readout, and repaint the panel live
        // when the admin colour changes so the pick can be judged before saving.
        function syncColorHexReadouts() {
            $('.ec-color-hex').each(function () {
                var val = $('#' + $(this).data('for')).val();
                $(this).text((val || '#000000').toUpperCase());
            });
        }

        function applyAdminColorInstantly(color) {
            if (!color) return;
            var root = document.documentElement;
            root.style.setProperty('--admin-panel-color', color);
            var hex = color.replace('#', '');
            if (hex.length === 3) hex = hex.split('').map(function (c) { return c + c; }).join('');
            var r = parseInt(hex.substr(0, 2), 16),
                g = parseInt(hex.substr(2, 2), 16),
                b = parseInt(hex.substr(4, 2), 16);
            root.style.setProperty('--admin-panel-rgb', r + ',' + g + ',' + b);
            // Perceived brightness decides the contrast colour, so text on brand
            // elements stays readable on light picks.
            root.style.setProperty('--admin-panel-contrast',
                ((r * 299 + g * 587 + b * 114) / 1000) >= 150 ? '#1f2937' : '#ffffff');
        }

        $(document).on('input change', '.brand-color-input', function () {
            syncColorHexReadouts();
            if (this.id === 'adminpanel_color') applyAdminColorInstantly($(this).val());
        });

        $(document).ready(syncColorHexReadouts);

        function ecPreview(input, target) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) { $(target).attr('src', e.target.result); };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function readURL(input)  { ecPreview(input, '#uploding_image'); }
        function readURL2(input) { ecPreview(input, '#uploding_image2'); }
        function readURL3(input) { ecPreview(input, '#uploding_image3'); }
    </script>
@endsection
