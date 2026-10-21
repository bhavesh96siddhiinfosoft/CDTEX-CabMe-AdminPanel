@extends('layouts.app')
@section('content')
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">
            <div class="ec-page-head mb-4 non-printable">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.rental_detail') }}</h3>
                    <p class="mb-0 text-muted">#{{ $ride->id }} · {{ date('d F Y h:i A', strtotime($ride->created_at)) }}</p>
                </div>
                <div class="d-flex" style="gap:10px">
                    <button type="button" class="btn ec-btn-ghost" onclick="printDiv('printableArea')"><i class="mdi mdi-printer-outline"></i> {{ trans('lang.print') }}</button>
                    <a href="{{ route('rental-orders.index') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.all_rental') }}</a>
                </div>
            </div>

            <div class="card-body pb-5 p-0">
                <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{ trans('lang.processing') }}</div>

                <div class="order_detail printableArea" id="order_detail">
                    <div class="order_detail-top">
                        <div class="row">
                            <div class="order_edit-genrl col-lg-7 col-md-12">
                                {{-- General details --}}
                                <div class="card ec-card ec-form-card">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-clipboard-text-outline"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.general_details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="order_detail-top-box">
                                            <div class="mb-2 gendetail-col">
                                                <label class="mb-0"><strong>{{ trans('lang.rental_id') }}: </strong><span id="ride_id">{{ $ride->id }}</span></label>
                                            </div>
                                            <div class="mb-2 gendetail-col">
                                                <label class="mb-0"><strong>{{ trans('lang.date_created') }}: </strong><span id="createdAt">{{ date('d F Y h:i A', strtotime($ride->created_at)) }}</span></label>
                                            </div>
                                            <div class="mb-2 gendetail-col">
                                                <label class="mb-0"><strong>{{ trans('lang.payment_status') }}: </strong>
                                                    <span id="payment_status">
                                                        @if ($ride->payment_status == 'yes')
                                                            <span class="badge badge-success">{{ trans('lang.paid') }}</span>
                                                        @else
                                                            <span class="badge badge-warning">{{ trans('lang.not_paid') }}</span>
                                                        @endif
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="mb-2 gendetail-col payment_method">
                                                <label class="mb-0"><strong>{{ trans('lang.payment_methods') }}: </strong>
                                                    <span id="payment_method">
                                                        @if ($ride->image)
                                                            <img class="rounded" style="width:70px" src="{{ asset('/assets/images/payment_method/' . $ride->image) }}" alt="image">
                                                        @endif
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="mb-2 gendetail-col">
                                                <label class="mb-0"><strong>{{ trans('lang.booked_by') }}: </strong>
                                                    <span>{{ $ride->ride_type == 'dispatcher' ? trans('lang.dispatcher') : trans('lang.customer') }}</span>
                                                </label>
                                            </div>
                                            <div class="mb-2 gendetail-col">
                                                <label class="mb-0"><strong>{{ trans('lang.rental_status') }}: </strong>
                                                    <span>
                                                        @if ($ride->status == 'completed')
                                                            <span class="badge badge-success">{{ ucfirst($ride->status) }}</span>
                                                        @elseif ($ride->status == 'confirmed')
                                                            <span class="badge badge-secondary">{{ ucfirst($ride->status) }}</span>
                                                        @elseif ($ride->status == 'new')
                                                            <span class="badge badge-primary">{{ ucfirst($ride->status) }}</span>
                                                        @elseif ($ride->status == 'rejected' || $ride->status == 'driver_rejected')
                                                            <span class="badge badge-danger">{{ ucfirst($ride->status) }}</span>
                                                        @else
                                                            <span class="badge badge-warning">{{ ucfirst($ride->status) }}</span>
                                                        @endif
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Package details --}}
                                <div class="card ec-card ec-form-card mt-4">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-car-clock"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.package_details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="address order_detail-top-box package-details">
                                            <p><strong>{{ trans('lang.name') }}: </strong>
                                                <span>{{ $ride->package_title }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.description') }}:</strong>
                                                <span>{{ $ride->package_description }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.package_base_fare') }}:</strong>
                                                <span>
                                                    @if ($currency->symbol_at_right == 'true')
                                                        {{ number_format($ride->package_baseFare, $currency->decimal_digit) . $currency->symbole }}
                                                    @else
                                                        {{ $currency->symbole . number_format($ride->package_baseFare, $currency->decimal_digit) }}
                                                    @endif
                                                </span>
                                            </p>
                                            <p><strong>{{ trans('lang.package_included_hours') }}:</strong>
                                                <span>{{ $ride->package_includedHours }} H</span>
                                            </p>
                                            <p><strong>{{ trans('lang.package_included_distance') }}:</strong>
                                                <span>{{ $ride->package_includedDistance }} Km</span>
                                            </p>
                                            <p><strong>{{ trans('lang.package_extra_km_fare') }}:</strong>
                                                <span>
                                                    @if ($currency->symbol_at_right == 'true')
                                                        {{ number_format($ride->package_extraKmFare, $currency->decimal_digit) . $currency->symbole }}
                                                    @else
                                                        {{ $currency->symbole . number_format($ride->package_extraKmFare, $currency->decimal_digit) }}
                                                    @endif
                                                </span>
                                            </p>
                                            <p><strong>{{ trans('lang.package_extra_min_fare') }}:</strong>
                                                <span>
                                                    @if ($currency->symbol_at_right == 'true')
                                                        {{ number_format($ride->package_extraMinuteFare, $currency->decimal_digit) . $currency->symbole }}
                                                    @else
                                                        {{ $currency->symbole . number_format($ride->package_extraMinuteFare, $currency->decimal_digit) }}
                                                    @endif
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Price details --}}
                                <div class="card ec-card ec-form-card mt-4">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-currency-usd"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.price_details') }}</h5>
                                    </div>
                                    <div class="card-body price_detail">
                                        <div class="table-responsive bk-summary-table">
                                            <table class="order-totals">
                                                <tbody id="order_products_total">
                                                    @if (!empty($ride->transaction_id))
                                                        <tr>
                                                            <td class="label"><strong>{{ trans('lang.transaction_id') }}</strong></td>
                                                            <td><strong>{{ $ride->transaction_id }}</strong></td>
                                                        </tr>
                                                    @endif
                                                    @if ($ride->base_fare > 0)
                                                        <tr>
                                                            <td>{{ trans('lang.base_fare') }}</td>
                                                            <td>
                                                                @if ($currency->symbol_at_right == 'true')
                                                                    {{ number_format($ride->base_fare, $currency->decimal_digit) . $currency->symbole }}
                                                                @else
                                                                    {{ $currency->symbole . number_format($ride->base_fare, $currency->decimal_digit) }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if ($ride->extra_minutes_cost > 0)
                                                        <tr>
                                                            <td>
                                                                {{ trans('lang.extra_time_cost') }}
                                                                ({{ trans('lang.included') }}: {{ $ride->included_hours }} H,
                                                                {{ trans('lang.extra') }}: {{ $ride->extra_hours }} H {{ $ride->extra_minutes }} M)
                                                            </td>
                                                            <td class="text-danger">
                                                                @if ($currency->symbol_at_right == 'true')
                                                                    {{ number_format($ride->extra_minutes_cost, $currency->decimal_digit) . $currency->symbole }}
                                                                @else
                                                                    {{ $currency->symbole . number_format($ride->extra_minutes_cost, $currency->decimal_digit) }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if ($ride->extra_km_cost > 0)
                                                        <tr>
                                                            <td>
                                                                {{ trans('lang.extra_distance_cost') }}
                                                                ({{ trans('lang.included') }}: {{ $ride->included_distance }} Km,
                                                                {{ trans('lang.traveled') }}: {{ number_format($ride->total_distance, 2) }} Km,
                                                                {{ trans('lang.extra') }}: {{ number_format($ride->extra_km, 2) }} Km)
                                                            </td>
                                                            <td class="text-danger">
                                                                @if ($currency->symbol_at_right == 'true')
                                                                    {{ number_format($ride->extra_km_cost, $currency->decimal_digit) . $currency->symbole }}
                                                                @else
                                                                    {{ $currency->symbole . number_format($ride->extra_km_cost, $currency->decimal_digit) }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td class="seprater" colspan="2">
                                                            <hr>
                                                            <span>{{ trans('lang.sub_total') }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr class="final-rate">
                                                        <td class="label">{{ trans('lang.sub_total') }}</td>
                                                        @php
                                                            $subtotal = $ride->base_fare + $ride->extra_minutes_cost + $ride->extra_km_cost;
                                                        @endphp
                                                        <td>{{ $currency->symbol_at_right == 'true'
                                                                ? number_format($subtotal, $currency->decimal_digit) . $currency->symbole
                                                                : $currency->symbole . number_format($subtotal, $currency->decimal_digit) }}
                                                        </td>
                                                    </tr>
                                                    @if ($ride->discount > 0)
                                                        <tr>
                                                            <td class="seprater" colspan="2">
                                                                <hr>
                                                                <span>{{ trans('lang.discount') }}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="label">{{ trans('lang.discount') }}</td>
                                                            <td>
                                                                <span class="ec-amt-debit">
                                                                    @if ($currency->symbol_at_right == 'true')
                                                                        (- {{ number_format(floatval($ride->discount), $currency->decimal_digit) . '' . $currency->symbole }})
                                                                    @else
                                                                        (-{{ $currency->symbole . '' . number_format(floatval($ride->discount), $currency->decimal_digit) }})
                                                                    @endif
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if (!empty($taxHtml))
                                                        <tr>
                                                            <td class="seprater" colspan="2">
                                                                <hr>
                                                                <span>{{ trans('lang.tax_calculation') }}</span>
                                                            </td>
                                                        </tr>
                                                        {!! $taxHtml !!}
                                                    @endif
                                                    @if ($ride->tip > 0)
                                                        <tr>
                                                            <td class="seprater" colspan="2">
                                                                <hr>
                                                                <span>{{ trans('lang.tip') }}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="label">{{ trans('lang.tip_amount') }}</td>
                                                            <td>
                                                                @if ($currency->symbol_at_right == 'true')
                                                                    {{ number_format(floatval($ride->tip), $currency->decimal_digit) . '' . $currency->symbole }}
                                                                @else
                                                                    {{ $currency->symbole . '' . number_format(floatval($ride->tip), $currency->decimal_digit) }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td class="seprater" colspan="2">
                                                            <hr>
                                                        </td>
                                                    </tr>
                                                    <tr class="grand-total">
                                                        <td class="label">{{ trans('lang.total_amount') }}</td>
                                                        <td class="total_price_val">
                                                            @if ($currency->symbol_at_right == 'true')
                                                                {{ number_format(floatval($totalAmount), $currency->decimal_digit) . '' . $currency->symbole }}
                                                            @else
                                                                {{ $currency->symbole . '' . number_format(floatval($totalAmount), $currency->decimal_digit) }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @if ($ride->admin_commission != '')
                                                        <tr>
                                                            <td class="label">
                                                                <small> {{ trans('lang.admin_commission') }} </small>
                                                            </td>
                                                            <td class="adminCommission_val">
                                                                <small>
                                                                    <span class="ec-amt-debit">
                                                                        @if ($currency->symbol_at_right == 'true')
                                                                            ({{ number_format(floatval($ride->admin_commission), $currency->decimal_digit) . '' . $currency->symbole }})
                                                                        @else
                                                                            ( {{ $currency->symbole . '' . number_format(floatval($ride->admin_commission), $currency->decimal_digit) }})
                                                                        @endif
                                                                    </span>
                                                                </small>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- Driver review --}}
                                <div class="card ec-card ec-form-card mt-4">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-star-half-full"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.driver_review') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        @if ($driver_review->isEmpty())
                                            <div class="text-center text-muted py-3">{{ trans('lang.no_review_found') }}</div>
                                        @else
                                            @foreach ($driver_review as $review)
                                                <div class="d-flex">
                                                    {{ $review->comment }}
                                                    <div class="ml-auto">
                                                        <ul class="rating" data-rating="{{ number_format($review->rating, 1) }}">
                                                            <li class="rating__item"></li>
                                                            <li class="rating__item"></li>
                                                            <li class="rating__item"></li>
                                                            <li class="rating__item"></li>
                                                            <li class="rating__item"></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <div class="order_addre-edit col-lg-5 col-md-12">
                                {{-- User details --}}
                                <div class="card ec-card ec-form-card">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-account-edit-outline"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.user_details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="address order_detail-top-box user-details">
                                            <p><strong>{{ trans('lang.name') }}: </strong>
                                                <span>{{ $ride->userPrenom && $ride->userNom ? $ride->userPrenom . ' ' . $ride->userNom : trans('lang.not_available') }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.phone') }}:</strong>
                                                <span>{{ $ride->user_phone ?: trans('lang.not_available') }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.address') }}:</strong>
                                                <span>{{ $ride->depart_name }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @if (isset($ride->id_conducteur) && $ride->id_conducteur !== null)
                                    {{-- Driver details --}}
                                    <div class="resturant-detail mt-4">
                                        <div class="card ec-card ec-form-card">
                                            <div class="card-header d-flex align-items-center">
                                                <span class="ec-filter-badge"><i class="mdi mdi-steering"></i></span>
                                                <h5 class="mb-0 ml-2">{{ trans('lang.driver_detail') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <a href="{{ route('driver.show', ['id' => $ride->id_conducteur]) }}" class="row redirecttopage align-items-center" id="driver-view">
                                                    <div class="col-2">
                                                        @if (!empty($ride->driver_photo) && file_exists(public_path('assets/images/driver/' . $ride->driver_photo)))
                                                            <img class="rounded" style="width:50px" src="{{ asset('assets/images/driver/' . $ride->driver_photo) }}" alt="Image">
                                                        @else
                                                            <img class="rounded" style="width:50px" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="Image">
                                                        @endif
                                                    </div>
                                                    <div class="col-8">
                                                        <h4 class="vendor-title">{{ $ride->driverPrenom || $ride->driverNom ? trim($ride->driverPrenom . ' ' . $ride->driverNom) : trans('lang.not_available') }}</h4>
                                                        <span class="badge badge-warning text-white"><i class="fa fa-star"></i> {{ $driverRating }}</span>
                                                    </div>
                                                </a>
                                                <h5 class="contact-info">{{ trans('lang.contact_info') }}:</h5>
                                                <p><strong>{{ trans('lang.email') }}:</strong>
                                                    <span id="vendor_email">{{ $ride->driver_email ?: trans('lang.not_available') }}</span>
                                                </p>
                                                <p><strong>{{ trans('lang.phone') }}:</strong>
                                                    <span id="vendor_phone">{{ $ride->driver_phone ?: trans('lang.not_available') }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($ride->ownerId))
                                    {{-- Owner details --}}
                                    <div class="resturant-detail mt-4">
                                        <div class="card ec-card ec-form-card">
                                            <div class="card-header d-flex align-items-center">
                                                <span class="ec-filter-badge"><i class="mdi mdi-account-tie-outline"></i></span>
                                                <h5 class="mb-0 ml-2">{{ trans('lang.owner_details') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <a href="{{ route('owners.show', ['id' => $ride->ownerId]) }}" class="row redirecttopage align-items-center" id="resturant-view">
                                                    <div class="col-2">
                                                        @if (!empty($ride->owner_photo) && file_exists(public_path('assets/images/drivers/' . $ride->owner_photo)))
                                                            <img class="rounded" style="width:50px" src="{{ asset('assets/images/drivers/' . $ride->owner_photo) }}" alt="Image">
                                                        @else
                                                            <img class="rounded" style="width:50px" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="Image">
                                                        @endif
                                                    </div>
                                                    <div class="col-8">
                                                        <h4 class="vendor-title">{{ $ride->ownerPrenom || $ride->ownerNom ? trim($ride->ownerPrenom . ' ' . $ride->ownerNom) : trans('lang.not_available') }}</h4>
                                                    </div>
                                                </a>
                                                <h5 class="contact-info">{{ trans('lang.contact_info') }}:</h5>
                                                <p><strong>{{ trans('lang.email') }}:</strong>
                                                    <span id="vendor_email">{{ $ride->owner_email ?: trans('lang.not_available') }}</span>
                                                </p>
                                                <p><strong>{{ trans('lang.phone') }}:</strong>
                                                    <span>{{ $ride->owner_phone ?: trans('lang.not_available') }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Customer review --}}
                                <div class="card ec-card ec-form-card mt-4">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-star-outline"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.ride_customer_review') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        @if ($customer_review->isEmpty())
                                            <div class="text-center text-muted py-3">{{ trans('lang.no_review_found') }}</div>
                                        @else
                                            @foreach ($customer_review as $review)
                                                <div class="d-flex">
                                                    {{ $review->comment }}
                                                    <div class="ml-auto">
                                                        <ul class="rating" data-rating="{{ number_format($review->rating, 1) }}">
                                                            <li class="rating__item"></li>
                                                            <li class="rating__item"></li>
                                                            <li class="rating__item"></li>
                                                            <li class="rating__item"></li>
                                                            <li class="rating__item"></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                {{-- Complaints --}}
                                <div class="card ec-card ec-form-card mt-4">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-alert-circle-outline"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.complaints') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        @if (count($complaints))
                                            <div class="table-responsive bk-summary-table">
                                                <table class="order-totals">
                                                    <tbody>
                                                        @foreach ($complaints as $complaint)
                                                            <tr>
                                                                <td class="label">{{ trans('lang.title') }}</td>
                                                                <td>{{ $complaint->title }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="label">{{ trans('lang.message') }}</td>
                                                                <td>{{ $complaint->description }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center text-muted py-3">{{ trans('lang.no_complaint_found') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="{{ asset('css/leaflet/leaflet.css') }}" />
@endsection
@section('scripts')
    <script src="{{ asset('js/leaflet/leaflet.js') }}"></script>
    <script type="text/javascript">
        function printDiv(divName) {
            var css = '@page { size: portrait; }',
                head = document.head || document.getElementsByTagName('head')[0],
                style = document.createElement('style');
            style.type = 'text/css';
            style.media = 'print';
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            head.appendChild(style);
            var printContents = document.getElementsByClassName(divName).html;
            window.print();
        }
    </script>
@endsection
