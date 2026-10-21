@extends('layouts.app')
@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.surge_zone_edit') }}</h3>
                <p class="mb-0 text-muted">{{ $zone->name }}</p>
            </div>
            <a href="{{ route('surgezone.index') }}" class="btn ec-btn-ghost">
                <i class="mdi mdi-arrow-left"></i> {{ trans('lang.surge_zone_plural') }}
            </a>
        </div>

        <form action="{{ route('surgezone.update', ['id' => $zone->id]) }}" method="post">
            @csrf
            <div class="card ec-card ec-form-card">
                <div class="card-header d-flex align-items-center">
                    <span class="ec-filter-badge"><i class="mdi mdi-trending-up"></i></span>
                    <h5 class="mb-0 ml-2">{{ trans('lang.surge_zone_edit') }}</h5>
                </div>
                <div class="card-body">
                    <div class="error_top"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label">{{ trans('lang.zone_name') }}</label>
                            <input type="text" class="form-control" name="name" value="{{ $zone->name }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="ec-form-label">{{ trans('lang.surge_multiplier') }}</label>
                            <input type="number" step="0.1" min="1" name="surge_multiplier" class="form-control" value="{{ $zone->surge_multiplier }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="switch-box switch-box-tight">
                                <label class="ec-form-label" for="manual_override">{{ trans('lang.lock_multiplier') }}</label>
                                <label class="switch">
                                    <input type="checkbox" id="manual_override" name="manual_override" value="1" {{ $zone->manual_override ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="ec-form-help">{{ trans('lang.lock_multiplier_help') }}</div>
                        </div>
                    </div>
                </div>
                <div class="ec-form-actions">
                    <button type="submit" class="btn ec-btn-brand edit-form-btn">{{ trans('lang.save') }}</button>
                    <a href="{{ route('surgezone.index') }}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
