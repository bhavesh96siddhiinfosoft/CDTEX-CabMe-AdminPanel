@extends('layouts.app')
@section('content')
    {{-- Laid out on foodie's notification/send.blade.php. cabme sends to
         customers and drivers with checkboxes rather than a single role select,
         so those stay — the field names and the form action are unchanged. --}}
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">
            <div class="ec-page-head mb-4">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.create_notification') }}</h3>
                    <p class="mb-0 text-muted">{{ trans('lang.notification_send_help') }}</p>
                </div>
                <a href="{!! route('notifications.index') !!}" class="btn ec-btn-ghost">
                    <i class="mdi mdi-arrow-left"></i> {{ trans('lang.notification_plural') }}
                </a>
            </div>

            <form action="{{ route('notifications.send') }}" method="post" enctype="multipart/form-data" id="send_notification">
                @csrf
                <div class="card ec-card ec-form-card mb-3">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-bell-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.create_notification') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="error_top"></div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="ec-form-label" for="title">{{ trans('lang.title') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control title" id="title" name="title"
                                       placeholder="{{ trans('lang.notification_title_help') }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="ec-form-label" for="message">{{ trans('lang.message') }} <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control message" id="message" rows="6"
                                          placeholder="{{ trans('lang.notification_message_help') }}">{{ Request::old('message') }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="ec-form-label">{{ trans('lang.notification_send_help') }}</label>
                                <div class="d-flex flex-wrap">
                                    <div class="form-check width-50">
                                        <input type="checkbox" name="send_to[]" id="customer" value="customer">
                                        <label class="ec-form-label" for="customer">{{ trans('lang.sendto_customer') }}</label>
                                    </div>
                                    <div class="form-check width-50">
                                        <input type="checkbox" name="send_to[]" id="driver" value="driver">
                                        <label class="ec-form-label" for="driver">{{ trans('lang.sendto_driver') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ec-form-actions">
                        <button type="submit" class="btn ec-btn-brand save-form-btn">{{ trans('lang.send_notification') }}</button>
                        <a href="{!! route('notifications.index') !!}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
