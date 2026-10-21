@extends('layouts.app')

@section('content')
@php
    $walletStr = $currency->symbol_at_right == 'true'
        ? number_format($user->amount, $currency->decimal_digit) . $currency->symbole
        : $currency->symbole . number_format($user->amount, $currency->decimal_digit);
    $activeTab = request('tab', 'rides');
@endphp
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">

        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.user_details') }}</h3>
                <p class="mb-0 text-muted">{{ $user->prenom }} {{ $user->nom }}</p>
            </div>
            <div class="d-flex align-items-center">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#addWalletModal" class="btn ec-head-btn add-wallate"><i class="mdi mdi-plus"></i> {{ trans('lang.add_wallet_amount') }}</a>
                <a href="{{ route('users.index') }}" class="btn ec-btn-ghost ml-2"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.user_plural') }}</a>
            </div>
        </div>

        {{-- KPI strip --}}
        <div class="row ec-kpi-row">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="ec-kpi kpi-brand ec-kpi-stat">
                    <span class="ec-kpi-icon"><i class="mdi mdi-car"></i></span>
                    <div class="ec-kpi-body">
                        <div class="ec-kpi-value">{{ $totalRides }}</div>
                        <div class="ec-kpi-label">{{ trans('lang.total_rides') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="ec-kpi kpi-blue ec-kpi-stat">
                    <span class="ec-kpi-icon"><i class="mdi mdi-package-variant-closed"></i></span>
                    <div class="ec-kpi-body">
                        <div class="ec-kpi-value">{{ $totalParcelOrders }}</div>
                        <div class="ec-kpi-label">{{ trans('lang.total_parcel_orders') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="ec-kpi kpi-green ec-kpi-stat">
                    <span class="ec-kpi-icon"><i class="mdi mdi-car-clock"></i></span>
                    <div class="ec-kpi-body">
                        <div class="ec-kpi-value">{{ $totalrentalOrders }}</div>
                        <div class="ec-kpi-label">{{ trans('lang.total_rental_orders') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="ec-kpi kpi-amber ec-kpi-stat">
                    <span class="ec-kpi-icon"><i class="mdi mdi-wallet-outline"></i></span>
                    <div class="ec-kpi-body">
                        <div class="ec-kpi-value wallet_balance" id="wallet_balance">{{ $walletStr }}</div>
                        <div class="ec-kpi-label">{{ trans('lang.wallet_balance') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row rv-eq rv-page">
            {{-- Hero --}}
            <div class="col-lg-7 mb-4">
                <div class="card ec-card ec-form-card rv-hero h-100">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-account-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.user_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="rv-profile">
                            <div class="rv-avatar">
                                @if (!empty($user->photo_path) && file_exists(public_path('assets/images/users/' . $user->photo_path)))
                                    <img src="{{ asset('assets/images/users') . '/' . $user->photo_path }}" alt="image">
                                @else
                                    <img src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">
                                @endif
                            </div>
                            <div class="rv-profile-body">
                                <div class="rv-title-row">
                                    <h4 class="rv-name mb-0">{{ $user->prenom }} {{ $user->nom }}</h4>
                                    @if ($user->statut == 'yes')
                                        <span class="badge badge-success">{{ trans('lang.enabled') }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ trans('lang.disabled') }}</span>
                                    @endif
                                </div>
                                <ul class="rv-meta">
                                    <li><i class="mdi mdi-email-outline"></i><span>{{ $user->email ?: trans('lang.not_available') }}</span></li>
                                    <li><i class="mdi mdi-phone-outline"></i><span>{{ $user->country_code }}{{ $user->phone }}</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="rv-facts rv-facts-2">
                            <div class="rv-fact">
                                <span class="rv-fact-label"><i class="mdi mdi-wallet-outline"></i>{{ trans('lang.wallet_balance') }}</span>
                                <span class="rv-fact-value rv-fact-money">{{ $walletStr }}</span>
                            </div>
                            <div class="rv-fact">
                                <span class="rv-fact-label"><i class="mdi mdi-star-outline"></i>{{ trans('lang.rating') }}</span>
                                <span class="rv-fact-value">{{ $user->average_rating ?: '0.0' }} ({{ $user->review_count ?: 0 }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Account details --}}
            <div class="col-lg-5 mb-4">
                <div class="card ec-card ec-form-card h-100">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-card-account-details-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.user_phone') }}</span>
                            <span class="ec-detail-value">{{ $user->country_code }}{{ $user->phone }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.created_at') }}</span>
                            <span class="ec-detail-value">{{ date('d F Y h:i A', strtotime($user->creer)) }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.edited') }}</span>
                            <span class="ec-detail-value">
                                @if ($user->modifier != '0000-00-00 00:00:00' && $user->modifier)
                                    {{ date('d F Y h:i A', strtotime($user->modifier)) }}
                                @endif
                            </span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.status') }}</span>
                            <span class="ec-detail-value">
                                @if ($user->statut == 'yes')
                                    <span class="badge badge-success">{{ trans('lang.enabled') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ trans('lang.disabled') }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="mt-3">
                            @if ($user->statut == 'no')
                                <a href="{{ route('users.changeStatus', ['id' => $user->id]) }}" class="btn ec-btn-brand btn-sm direct-click-btn">{{ trans('lang.enable_account') }}</a>
                            @else
                                <a href="{{ route('users.changeStatus', ['id' => $user->id]) }}" class="btn ec-btn-ghost btn-sm direct-click-btn">{{ trans('lang.disable_account') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity tabs --}}
        <div class="row">
            <div class="col-12">
                <div class="card ec-card ec-list-card mb-4">
                    <div class="card-body">
                        <div class="menu-tab">
                            <ul>
                                <li class="{{ $activeTab == 'rides' ? 'active' : '' }}"><a href="#rides" role="tab">{{ trans('lang.rides') }}</a></li>
                                <li class="{{ $activeTab == 'parcels' ? 'active' : '' }}"><a href="#parcels" role="tab">{{ trans('lang.parcel') }}</a></li>
                                <li class="{{ $activeTab == 'rentals' ? 'active' : '' }}"><a href="#rentals" role="tab">{{ trans('lang.rental') }}</a></li>
                                <li class="{{ $activeTab == 'transactions' ? 'active' : '' }}"><a href="#transactions" role="tab">{{ trans('lang.wallet_transaction_plural') }}</a></li>
                            </ul>
                        </div>

                        <div class="tab-content pt-3">
                            <div role="tabpanel" class="tab-pane {{ $activeTab == 'rides' ? 'active' : '' }}" id="rides">
                                @if (count($rides) > 0)
                                    <div class="table-responsive">
                                        <table class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('lang.order_id') }}</th>
                                                    <th>{{ trans('lang.driver_name') }}</th>
                                                    <th>{{ trans('lang.status') }}</th>
                                                    <th>{{ trans('lang.created') }}</th>
                                                    <th>{{ trans('lang.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rides as $ride)
                                                    <tr>
                                                        <td><a href="{{ route('ride.show', ['id' => $ride->id]) }}">{{ $ride->id }}</a></td>
                                                        <td>
                                                            @if ($ride->driver_id)
                                                                <a href="{{ route('driver.show', ['id' => $ride->driver_id]) }}">{{ $ride->driverPrenom }} {{ $ride->driverNom }}</a>
                                                            @else
                                                                {{ trans('lang.not_available') }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($ride->statut == 'completed')
                                                                <span class="badge badge-success">{{ ucfirst($ride->statut) }}</span>
                                                            @elseif ($ride->statut == 'rejected')
                                                                <span class="badge badge-danger">{{ ucfirst($ride->statut) }}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ ucfirst($ride->statut) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d F Y h:i A', strtotime($ride->creer)) }}</td>
                                                        <td class="action-btn ec-actions">
                                                            <a href="{{ route('ride.show', ['id' => $ride->id]) }}" class="ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                            <a class="delete-btn ec-act ec-act-danger" name="user-delete" href="{{ route('ride.delete', ['rideid' => $ride->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {{ $rides->appends(['tab' => 'rides'])->links('pagination.pagination') }}
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                @endif
                            </div>

                            <div role="tabpanel" class="tab-pane {{ $activeTab == 'parcels' ? 'active' : '' }}" id="parcels">
                                @if (count($parcelOrders) > 0)
                                    <div class="table-responsive">
                                        <table class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('lang.order_id') }}</th>
                                                    <th>{{ trans('lang.driver_name') }}</th>
                                                    <th>{{ trans('lang.status') }}</th>
                                                    <th>{{ trans('lang.created') }}</th>
                                                    <th>{{ trans('lang.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($parcelOrders as $parcel)
                                                    <tr>
                                                        <td><a href="{{ route('parcel.show', ['id' => $parcel->id]) }}">{{ $parcel->id }}</a></td>
                                                        <td>
                                                            @if ($parcel->driver_id)
                                                                <a href="{{ route('driver.show', ['id' => $parcel->driver_id]) }}">{{ $parcel->driverPrenom }} {{ $parcel->driverNom }}</a>
                                                            @else
                                                                {{ trans('lang.not_available') }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (in_array($parcel->status, ['completed', 'confirmed']))
                                                                <span class="badge badge-success">{{ ucfirst($parcel->status) }}</span>
                                                            @elseif ($parcel->status == 'new')
                                                                <span class="badge badge-primary">{{ ucfirst($parcel->status) }}</span>
                                                            @elseif ($parcel->status == 'rejected')
                                                                <span class="badge badge-danger">{{ ucfirst($parcel->status) }}</span>
                                                            @elseif ($parcel->status == 'driver_rejected')
                                                                <span class="badge badge-danger">{{ trans('lang.driver_rejected') }}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ ucfirst($parcel->status) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d F Y h:i A', strtotime($parcel->created_at)) }}</td>
                                                        <td class="action-btn ec-actions">
                                                            <a href="{{ route('parcel.show', ['id' => $parcel->id]) }}" class="ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                            <a class="delete-btn ec-act ec-act-danger" name="user-delete" href="{{ route('parcel.delete', ['rideid' => $parcel->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {{ $parcelOrders->appends(['tab' => 'parcels'])->links('pagination.pagination') }}
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                @endif
                            </div>

                            <div role="tabpanel" class="tab-pane {{ $activeTab == 'rentals' ? 'active' : '' }}" id="rentals">
                                @if (count($rentalOrders) > 0)
                                    <div class="table-responsive">
                                        <table class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('lang.order_id') }}</th>
                                                    <th>{{ trans('lang.driver_name') }}</th>
                                                    <th>{{ trans('lang.status') }}</th>
                                                    <th>{{ trans('lang.created') }}</th>
                                                    <th>{{ trans('lang.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rentalOrders as $rental)
                                                    <tr>
                                                        <td><a href="{{ route('rental-orders.show', ['id' => $rental->id]) }}">{{ $rental->id }}</a></td>
                                                        <td>
                                                            @if ($rental->driver_id)
                                                                <a href="{{ route('driver.show', ['id' => $rental->driver_id]) }}">{{ $rental->driverPrenom }} {{ $rental->driverNom }}</a>
                                                            @else
                                                                {{ trans('lang.not_available') }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (in_array($rental->status, ['completed', 'confirmed']))
                                                                <span class="badge badge-success">{{ ucfirst($rental->status) }}</span>
                                                            @elseif ($rental->status == 'new')
                                                                <span class="badge badge-primary">{{ ucfirst($rental->status) }}</span>
                                                            @elseif ($rental->status == 'rejected')
                                                                <span class="badge badge-danger">{{ ucfirst($rental->status) }}</span>
                                                            @elseif ($rental->status == 'driver_rejected')
                                                                <span class="badge badge-danger">{{ trans('lang.driver_rejected') }}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ ucfirst($rental->status) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d F Y h:i A', strtotime($rental->created_at)) }}</td>
                                                        <td class="action-btn ec-actions">
                                                            <a href="{{ route('rental-orders.show', ['id' => $rental->id]) }}" class="ec-act" data-toggle="tooltip" title="{{ trans('lang.view_details') }}"><i class="mdi mdi-eye"></i></a>
                                                            <a class="delete-btn ec-act ec-act-danger" name="user-delete" href="{{ route('rental-orders.delete', ['id' => $rental->id]) }}" data-toggle="tooltip" title="{{ trans('lang.delete') }}"><i class="mdi mdi-delete-outline"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {{ $rentalOrders->appends(['tab' => 'rentals'])->links('pagination.pagination') }}
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                @endif
                            </div>

                            <div role="tabpanel" class="tab-pane {{ $activeTab == 'transactions' ? 'active' : '' }}" id="transactions">
                                @if (count($transactions) > 0)
                                    <div class="table-responsive">
                                        <table class="display nowrap table table-hover ec-list-table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('lang.transaction_id') }}</th>
                                                    <th>{{ trans('lang.amount') }}</th>
                                                    <th>{{ trans('lang.created_at') }}</th>
                                                    <th>{{ trans('lang.payment_method') }}</th>
                                                    <th>{{ trans('lang.note') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactions as $transaction)
                                                    @php
                                                        $amountStr = $currency->symbol_at_right == 'true'
                                                            ? number_format(floatval($transaction->amount), $currency->decimal_digit) . $currency->symbole
                                                            : $currency->symbole . number_format(floatval($transaction->amount), $currency->decimal_digit);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $transaction->id }}</td>
                                                        <td>
                                                            @if ($transaction->is_credited == '0')
                                                                <span class="ec-amt-debit">(-{{ $amountStr }})</span>
                                                            @else
                                                                <span class="ec-amt-credit">{{ $amountStr }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d F Y h:i A', strtotime($transaction->created_at)) }}</td>
                                                        <td>
                                                            @if ($transaction->image)
                                                                <img class="rounded" style="width:50px" src="{{ asset('/assets/images/payment_method/' . $transaction->image) }}" alt="image">
                                                            @else
                                                                {{ $transaction->payment_method }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $transaction->note }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {{ $transactions->appends(['tab' => 'transactions'])->links('pagination.pagination') }}
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">{{ trans('lang.no_result') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addWalletModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered location_modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title locationModalTitle">{{ trans('lang.add_wallet_amount') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('users.wallet', $user->id) }}" method="post">
                    @csrf
                    <div class="row">
                        <label class="col-md-12 control-label">{{ trans('lang.amount') }}</label>
                        <div class="col-md-12">
                            <input type="number" name="amount" class="form-control" id="amount" placeholder="{{ trans('lang.enter_amount') }}">
                            <div id="wallet_error" style="color:red"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn ec-btn-brand save-form-btn" id="add-wallet-btn">{{ trans('lang.save') }}</button>
                        <button type="button" class="btn ec-btn-ghost" data-dismiss="modal" aria-label="Close">{{ trans('lang.close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $("#add-wallet-btn").click(function() {
            var amount = $('#amount').val();
            if (amount == '') {
                $('#wallet_error').text('{{ trans('lang.add_wallet_amount_error') }}');
                return false;
            }
        });
    </script>
@endsection
