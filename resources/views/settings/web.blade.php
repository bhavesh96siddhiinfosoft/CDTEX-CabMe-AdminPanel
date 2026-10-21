@extends('layouts.app')

@section('content')
    {{-- Web panel settings, mirroring the Web Settings screen in quicklist-admin:
         look and feel, logos, social links and the cookie consent bar. These are
         read by cabme-web through the v1/settings/ API. --}}
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">

            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.web_settings') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.settings_web_desc') }}</p>
                </div>
                <a href="{{ route('settings') }}" class="btn ec-btn-ghost">
                    <i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}
                </a>
            </div>

            <div class="error_top"></div>

            <form action="{{ route('general-settings.update', ['id' => $settings->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- tells the controller which columns this page owns --}}
                <input type="hidden" name="_section" value="web">

                @php
                    $social = $settings->web_social ?? [];
                    $cookie = $settings->web_cookie_consent ?? [];
                @endphp

                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-body">

                        <div class="ec-section">
                            <h6 class="ec-section-title">{{ trans('lang.look_and_feel') }}</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label" for="web_panel_color">{{ trans('lang.web_panel_color_settings') }}</label>
                                    <div class="ec-color-input">
                                        <input type="color" class="brand-color-input" name="web_panel_color" id="web_panel_color"
                                               value="{{ $settings->web_panel_color ?: ($settings->website_color ?: '#d92662') }}">
                                        <code class="ec-color-hex" data-for="web_panel_color">#000000</code>
                                    </div>
                                    <div class="ec-form-help">{{ trans('lang.web_panel_color_help') }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label" for="web_panel_url">{{ trans('lang.web_panel_url') }}</label>
                                    <input type="url" class="form-control" name="web_panel_url" id="web_panel_url"
                                           value="{{ $settings->web_panel_url }}" placeholder="https://www.yoursite.com">
                                    <div class="ec-form-help">{{ trans('lang.web_panel_url_help') }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="ec-form-label" for="web_footer_description">{{ trans('lang.footer_description') }}</label>
                                    <textarea class="form-control" name="web_footer_description" id="web_footer_description" rows="3"
                                              placeholder="{{ trans('lang.footer_description_placeholder') }}">{{ $settings->web_footer_description }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="ec-form-label" for="web_map_iframe">{{ trans('lang.google_map_iframe_link') }}</label>
                                    <textarea class="form-control" name="web_map_iframe" id="web_map_iframe" rows="3"
                                              placeholder="{{ trans('lang.google_map_iframe_placeholder') }}">{{ $settings->web_map_iframe }}</textarea>
                                    <div class="ec-form-help">{{ trans('lang.google_map_iframe_help') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="ec-section">
                            <h6 class="ec-section-title">{{ trans('lang.logos_and_icons') }}</h6>
                            @php
                                // [field name, label key, help key, preview img id]
                                $ec_web_logos = [
                                    ['web_header_logo', 'header_logo', 'header_logo_help',  'web_header_logo_img'],
                                    ['web_footer_logo', 'footer_logo', 'footer_logo_help',  'web_footer_logo_img'],
                                    ['web_favicon',     'favicon',     'web_favicon_help',  'web_favicon_img'],
                                ];
                            @endphp
                            <div class="row">
                                @foreach ($ec_web_logos as [$ec_name, $ec_label, $ec_help, $ec_img])
                                    @php $ec_file = $settings->{$ec_name}; @endphp
                                    <div class="col-md-4 mb-3">
                                        <label class="ec-form-label">{{ trans('lang.' . $ec_label) }}</label>
                                        <label class="ec-upload" data-placeholder="{{ trans('lang.click_to_upload') }}">
                                            <input type="file" accept="image/*" name="{{ $ec_name }}" id="{{ $ec_name }}"
                                                   onchange="ecWebPreview(this, '#{{ $ec_img }}');">
                                            <i class="mdi mdi-cloud-upload-outline"></i>
                                            <span class="ec-upload-text">{{ trans('lang.click_to_upload') }}</span>
                                            <span class="ec-upload-hint">{{ trans('lang.png_svg') }}</span>
                                        </label>
                                        <div class="ec-form-help">{{ trans('lang.' . $ec_help) }}</div>
                                        <div class="mt-2">
                                            @if (! empty($ec_file) && file_exists(public_path('assets/images/' . $ec_file)))
                                                <img class="rounded" id="{{ $ec_img }}" src="{{ asset('assets/images/' . $ec_file) }}" alt="image">
                                            @else
                                                <img class="rounded" id="{{ $ec_img }}" src="{{ asset('assets/images/logo-placeholder-image.png') }}" alt="image">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="ec-section">
                            <h6 class="ec-section-title">{{ trans('lang.social_media_links') }}</h6>
                            @php
                                $ec_socials = [
                                    ['facebook',  'facebook',  'https://facebook.com/yourpage'],
                                    ['instagram', 'instagram', 'https://instagram.com/yourpage'],
                                    ['x',         'x_twitter', 'https://x.com/yourpage'],
                                    ['linkedin',  'linkedin',  'https://linkedin.com/company/yourpage'],
                                    ['pinterest', 'pinterest', 'https://pinterest.com/yourpage'],
                                    ['youtube',   'youtube',   'https://youtube.com/@yourchannel'],
                                ];
                            @endphp
                            <div class="row">
                                @foreach ($ec_socials as [$ec_key, $ec_label, $ec_placeholder])
                                    <div class="col-md-6 mb-3">
                                        <label class="ec-form-label" for="social_{{ $ec_key }}">{{ trans('lang.' . $ec_label) }}</label>
                                        <input type="url" class="form-control" name="social_{{ $ec_key }}" id="social_{{ $ec_key }}"
                                               value="{{ $social[$ec_key] ?? '' }}" placeholder="{{ $ec_placeholder }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="ec-section">
                            <h6 class="ec-section-title">{{ trans('lang.cookie_consent') }}</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="switch-box switch-box-tight mb-3">
                                        <label class="control-label">{{ trans('lang.enable_cookie_consent') }}</label>
                                        <label class="switch">
                                            <input type="checkbox" name="cookie_consent_enabled" id="cookie_consent_enabled"
                                                   {{ ! empty($cookie['enabled']) ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="ec-form-help">{{ trans('lang.enable_cookie_consent_help') }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label" for="cookie_button_text">{{ trans('lang.cookie_accept_button') }}</label>
                                    <input type="text" class="form-control" name="cookie_button_text" id="cookie_button_text"
                                           value="{{ $cookie['buttonText'] ?? '' }}" placeholder="{{ trans('lang.cookie_accept_button_placeholder') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="ec-form-label" for="cookie_message">{{ trans('lang.cookie_message') }}</label>
                                    <textarea class="form-control" name="cookie_message" id="cookie_message" rows="3"
                                              placeholder="{{ trans('lang.cookie_message_placeholder') }}">{{ $cookie['message'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="ec-form-label" for="cookie_policy_url">{{ trans('lang.cookie_policy_link') }}</label>
                                    <input type="url" class="form-control" name="cookie_policy_url" id="cookie_policy_url"
                                           value="{{ $cookie['policyUrl'] ?? '' }}" placeholder="https://...">
                                    <div class="ec-form-help">{{ trans('lang.cookie_policy_link_help') }}</div>
                                </div>
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
        // Mirror the swatch into its hex readout, matching the branding screen.
        function syncColorHexReadouts() {
            $('.ec-color-hex').each(function () {
                var val = $('#' + $(this).data('for')).val();
                $(this).text((val || '#000000').toUpperCase());
            });
        }

        $(document).on('input change', '.brand-color-input', syncColorHexReadouts);
        $(document).ready(syncColorHexReadouts);

        function ecWebPreview(input, target) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) { $(target).attr('src', e.target.result); };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
