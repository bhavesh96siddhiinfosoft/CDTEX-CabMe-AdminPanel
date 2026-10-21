@extends('layouts.app')

@section('content')
<div class="page-wrapper form-page">
    {{-- Page head, matching every other converted screen. --}}
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.parcel_charges') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.parcel_charges_desc') }}</p>
            </div>
            <a href="{{ route('settings') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.settings') }}</a>
        </div>
    </div>

    <div class="card-body">

        <form action="{{ route('parcel-charges.update') }}" method="post">
            @csrf
            <div class="row restaurant_payout_create">
                <div class="restaurant_payout_create-inner">
                    <div class="ec-section">
                        <h6 class="ec-section-title">{{ trans('lang.delivery_charge') }}</h6>

                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#charges-default" role="tab">{{ trans('lang.default') }}</a>
                            </li>
                            @foreach($zones as $zone)
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#charges-zone-{{ $zone->id }}" role="tab">{{ $zone->name }}</a>
                                </li>
                            @endforeach
                        </ul>

                        @php
                            $tabs = [['key' => 'default', 'id' => 'charges-default', 'active' => true, 'row' => $charges['default'] ?? null]];
                            foreach ($zones as $zone) {
                                $tabs[] = ['key' => $zone->id, 'id' => 'charges-zone-'.$zone->id, 'active' => false, 'row' => $charges[$zone->id] ?? null];
                            }
                        @endphp

                        <div class="tab-content">
                            @foreach($tabs as $tab)
                                <div class="tab-pane {{ $tab['active'] ? 'active' : '' }}" id="{{ $tab['id'] }}" role="tabpanel">
                                    <div class="form-group row width-100">
                                        <label class="col-4 control-label">{{ trans('lang.delivery_charge_per') }} {{ $delivery_distance }}</label>
                                        <div class="col-7">
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                name="charges[{{ $tab['key'] }}][per_km]"
                                                value="{{ $tab['row']->delivery_charge_parcel ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row width-100">
                                        <label class="col-4 control-label">{{ trans('lang.parcel_per_weight_charge') }}</label>
                                        <div class="col-7">
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                name="charges[{{ $tab['key'] }}][per_kg]"
                                                value="{{ $tab['row']->parcel_per_weight_charge ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="ec-form-actions">
                <button type="submit" class="btn ec-btn-brand edit-setting-btn">{{ trans('lang.save') }}</button>
                <a href="{{ route('settings') }}" class="btn ec-btn-ghost">{{ trans('lang.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
