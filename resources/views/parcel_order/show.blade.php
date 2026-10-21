@extends('layouts.app')

@section('content')
    <div class="page-wrapper listings-page form-page">
        <div class="container-fluid">
            <div class="ec-page-head mb-4 non-printable">
                <div class="ec-page-head-text">
                    <h3 class="mb-1">{{ trans('lang.parcel_detail') }}</h3>
                    <p class="mb-0 text-muted">#{{ $ride->id }} · {{ date('d F Y h:i A', strtotime($ride->created_at)) }}</p>
                </div>
                <div class="d-flex" style="gap:10px">
                    <button type="button" class="btn ec-btn-ghost" onclick="printDiv('printableArea')"><i class="mdi mdi-printer-outline"></i> {{ trans('lang.print') }}</button>
                    <a href="{{ route('parcel.index') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.all_parcel') }}</a>
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
                                                <label class="mb-0"><strong>{{ trans('lang.parcel_id') }}: </strong><span id="ride_id">{{ $ride->id }}</span></label>
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
                                                <label class="mb-0"><strong>{{ trans('lang.parcel_distance') }}: </strong><span id="trip_objective">{{ number_format($ride->distance, 2) . ' ' . $ride->distance_unit }}</span></label>
                                            </div>
                                            <div class="mb-2 gendetail-col">
                                                <label class="mb-0"><strong>{{ trans('lang.booked_by') }}: </strong>
                                                    <span>{{ $ride->ride_type == 'dispatcher' ? trans('lang.dispatcher') : trans('lang.customer') }}</span>
                                                </label>
                                            </div>
                                            <div class="mb-2 gendetail-col">
                                                <label class="mb-0"><strong>{{ trans('lang.parcel_status') }}: </strong>
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

                                {{-- Location details --}}
                                <div class="card ec-card ec-form-card mt-4">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-map-marker-path"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.location_details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="live-tracking-list">
                                            <div class="live-tracking-box track-from">
                                                <div class="live-tracking-inner">
                                                    <div class="location-ride">
                                                        <div class="from-ride">{{ $ride->source }}</div>
                                                        <div class="to-ride">{{ $ride->destination }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Parcel details --}}
                                <div class="card ec-card ec-form-card mt-4">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-package-variant-closed"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.parcel_details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="address order_detail-top-box user-details">
                                            <p><strong>{{ trans('lang.parcel_type') }}:</strong>
                                                <span>{{ !empty($ride->title) ? $ride->title : '' }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.parcel_weight') }}:</strong>
                                                <span>{{ $ride->parcel_weight }} Kg</span>
                                            </p>
                                            <p><strong>{{ trans('lang.parcel_dimension') }}:</strong>
                                                <span>{{ !empty($ride->parcel_dimension) ? $ride->parcel_dimension . ' ft' : '-' }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.parcel_note') }}:</strong>
                                                <span>{{ !empty($ride->note) ? $ride->note : '-' }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.parcel_date') }}:</strong>
                                                <span>{{ date('d F Y h:i A', strtotime($ride->parcel_date . $ride->parcel_time)) }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.receive_date') }}:</strong>
                                                <span>{{ date('d F Y h:i A', strtotime($ride->receive_date . $ride->receive_time)) }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.parcel_image') }}:</strong>
                                                @if (!empty($parcel_image))
                                                    <span class="d-flex">
                                                        @foreach ($parcel_image as $image)
                                                            <span class="user-img mr-2"><img class="rounded" style="width:50px" src="{{ $image }}" alt="Image"></span>
                                                        @endforeach
                                                    </span>
                                                @else
                                                    <span class="user-img"><img class="rounded" style="width:50px" src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="Image"></span>
                                                @endif
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
                                                    <tr>
                                                        <td class="seprater" colspan="2">
                                                            <hr>
                                                            <span>{{ trans('lang.sub_total') }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr class="final-rate">
                                                        <td class="label">{{ trans('lang.sub_total') }}</td>
                                                        <td>
                                                            @if ($currency->symbol_at_right == 'true')
                                                                {{ number_format(floatval($ride->amount), $currency->decimal_digit) . '' . $currency->symbole }}
                                                            @else
                                                                {{ $currency->symbole . '' . number_format(floatval($ride->amount), $currency->decimal_digit) }}
                                                            @endif
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
                            </div>


                            <div class="order_addre-edit col-lg-5 col-md-12">
                                {{-- Sender --}}
                                <div class="card ec-card ec-form-card">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-account-arrow-right-outline"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.sender_details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="address order_detail-top-box user-details">
                                            <p><strong>{{ trans('lang.name') }}: </strong>
                                                <span>{{ $ride->sender_name }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.phone') }}:</strong>
                                                <span>{{ $ride->sender_phone }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.address') }}:</strong>
                                                <span>{{ $ride->source }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Receiver --}}
                                <div class="card ec-card ec-form-card mt-4">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-account-arrow-left-outline"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.receiver_details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="address order_detail-top-box user-details">
                                            <p><strong>{{ trans('lang.name') }}: </strong>
                                                <span>{{ $ride->receiver_name }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.phone') }}:</strong>
                                                <span>{{ $ride->receiver_phone }}</span>
                                            </p>
                                            <p><strong>{{ trans('lang.address') }}:</strong>
                                                <span>{{ $ride->destination }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Map --}}
                                <div class="card ec-card ec-form-card mt-4 non-printable">
                                    <div class="card-header d-flex align-items-center">
                                        <span class="ec-filter-badge"><i class="mdi mdi-map-outline"></i></span>
                                        <h5 class="mb-0 ml-2">{{ trans('lang.map_view') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="map" style="height:300px"></div>
                                    </div>
                                </div>

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
                                                        <h4 class="vendor-title">{{ $ride->ownerPrenom }} {{ $ride->ownerNom }}</h4>
                                                    </div>
                                                </a>
                                                <h5 class="contact-info">{{ trans('lang.contact_info') }}:</h5>
                                                <p><strong>{{ trans('lang.email') }}:</strong>
                                                    <span id="vendor_email">{{ $ride->owner_email }}</span>
                                                </p>
                                                <p><strong>{{ trans('lang.phone') }}:</strong>
                                                    <span>{{ $ride->owner_phone }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

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
                                                        <h4 class="vendor-title">{{ $ride->driverPrenom }} {{ $ride->driverNom }}</h4>
                                                        <span class="badge badge-warning text-white"><i class="fa fa-star"></i> {{ $driverRating }}</span>
                                                    </div>
                                                </a>
                                                <h5 class="contact-info">{{ trans('lang.contact_info') }}:</h5>
                                                <p><strong>{{ trans('lang.email') }}:</strong>
                                                    <span id="vendor_email">{{ $ride->driver_email }}</span>
                                                </p>
                                                <p><strong>{{ trans('lang.phone') }}:</strong>
                                                    <span id="vendor_phone">{{ $ride->driver_phone }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

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

<style>
    #map {
        position: relative;
        z-index: 0;
    }
</style>

@section('scripts')
    <script src="{{ asset('js/leaflet/leaflet.js') }}"></script>

    <script type="text/javascript">

        var map;
        let mapType = "{{ $mapType }}";

        const origin = {
            lat: {{ $ride->lat_source ?? 0 }},
            lng: {{ $ride->lng_source ?? 0 }}
        };

        const destination = {
            lat: {{ $ride->lat_destination ?? 0 }},
            lng: {{ $ride->lng_destination ?? 0 }}
        };

        // Start on document ready, like the other map screens. Listening for the
        // layout's `mapsLoaded` event was a race: the event can fire while this
        // block is still being parsed, so the listener never ran and the map box
        // stayed blank. Leaflet is loaded by the layout before page scripts.
        $(document).ready(function () {
            initMap();
        });
        function initMap() {
            if (window.__parcelMapReady) return;
            window.__parcelMapReady = true;
            if (mapType === "OSM") {

                map = L.map('map').setView([origin.lat, origin.lng], 10);

                // Add OpenStreetMap tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Add marker for destination
                var marker = L.marker([destination.lat, destination.lng]).addTo(map);
                marker.bindPopup('{!! $ride->destination !!}').openPopup();

                if (typeof L.Routing !== 'undefined') {
                    // Add route for OSM using leaflet routing machine
                    L.Routing.control({
                        waypoints: [
                            L.latLng(origin.lat, origin.lng),
                            L.latLng(destination.lat, destination.lng)
                        ],
                        routeWhileDragging: false,
                        draggableWaypoints: false,
                        addWaypoints: false
                    }).addTo(map);
                } else {
                    console.error("Leaflet Routing Machine is not loaded properly.");
                }
            } else {

                var marker;
                var myLatlng = new google.maps.LatLng('{!! $ride->lat_destination !!}', '{!! $ride->lng_destination !!}');
                var geocoder = new google.maps.Geocoder();
                var infowindow = new google.maps.InfoWindow();

                var mapOptions = {
                    zoom: 10,
                    center: myLatlng,
                    streetViewControl: false,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                map = new google.maps.Map(document.getElementById("map"), mapOptions);
                marker = new google.maps.Marker({
                    map: map,
                    position: myLatlng,
                    draggable: true
                });

                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.setContent('{!! $ride->destination_name !!}');
                    infowindow.open(map, marker);
                });

                //Set direction route

                let directionsService = new google.maps.DirectionsService();
                let directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setMap(map);

                const route = {
                    origin: origin,
                    destination: destination,
                    travelMode: 'DRIVING'
                }

                directionsService.route(route, function(response, status) {
                    if (status !== 'OK') {
                        window.alert("{{trans('lang.directions_request_failed_due_to')}} "  + status);
                        return;
                    } else {
                        directionsRenderer.setDirections(response);
                        var directionsData = response.routes[0].legs[0];
                    }
                });
            }
        }

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
