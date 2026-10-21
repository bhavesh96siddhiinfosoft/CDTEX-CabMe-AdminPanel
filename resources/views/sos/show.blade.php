@extends('layouts.app')
@section('content')
<div class="page-wrapper listings-page form-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.sos_detail') }}</h3>
                <p class="mb-0 text-muted">#{{ $sos->id }} · {{ date('d F Y h:i A', strtotime($sos->creer)) }}</p>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('sos.index') }}" class="btn ec-btn-ghost"><i class="mdi mdi-arrow-left"></i> {{ trans('lang.sos') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7 col-md-12">
                {{-- Map --}}
                <div class="card ec-card ec-form-card">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-map-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.map_view') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="map" style="height:420px"></div>
                    </div>
                </div>

                {{-- Ride route --}}
                <div class="card ec-card ec-form-card mt-4">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-map-marker-path"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.ride') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="live-tracking-list">
                            <div class="live-tracking-box track-from">
                                <div class="live-tracking-inner">
                                    <div class="location-ride">
                                        <div class="from-ride">{{ $sos->depart_name ?: trans('lang.not_available') }}</div>
                                        <div class="to-ride">{{ $sos->destination_name ?: trans('lang.not_available') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-12">
                {{-- SOS status --}}
                <div class="card ec-card ec-form-card">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-alarm-light-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.sos') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.created_at') }}</span>
                            <span class="ec-detail-value">{{ date('d F Y h:i A', strtotime($sos->creer)) }}</span>
                        </div>
                        <div class="ec-detail-row">
                            <span class="ec-detail-label">{{ trans('lang.sos_location') }}</span>
                            <span class="ec-detail-value" id="sos_location"></span>
                        </div>
                        <form method="post" action="{{ route('sos.update', $sos->id) }}" enctype="multipart/form-data" class="mt-3">
                            @csrf
                            @method('PUT')
                            @php
                                $status = ['initiated' => 'Initiated', 'completed' => 'Completed', 'processing' => 'Processing'];
                            @endphp
                            <label class="ec-form-label">{{ trans('lang.status') }}</label>
                            <select name="order_status" class="form-control mb-3">
                                @foreach ($status as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $sos->status ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            <div class="text-right mt-4">
                                <button type="submit" class="btn ec-btn-brand edit-form-btn">{{ trans('lang.update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- User --}}
                <div class="card ec-card ec-form-card mt-4">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-account-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.user') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="rv-profile">
                            <div class="rv-avatar" style="flex-basis:72px;width:72px;height:72px">
                                @if (!empty($sos->user_photo) && file_exists(public_path('assets/images/users/' . $sos->user_photo)))
                                    <img src="{{ asset('assets/images/users') . '/' . $sos->user_photo }}" alt="image">
                                @else
                                    <img src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">
                                @endif
                            </div>
                            <div class="rv-profile-body">
                                <div class="rv-title-row">
                                    <h5 class="rv-name mb-0">
                                        @if ($sos->userID)
                                            <a href="{{ route('users.show', ['id' => $sos->userID]) }}">{{ $sos->userFirstNom }} {{ $sos->userNom }}</a>
                                        @else
                                            {{ trans('lang.not_available') }}
                                        @endif
                                    </h5>
                                </div>
                                <ul class="rv-meta">
                                    <li><i class="mdi mdi-phone-outline"></i><span>{{ $sos->user_phone ?: trans('lang.not_available') }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Driver --}}
                <div class="card ec-card ec-form-card mt-4">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-steering"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.driver') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="rv-profile">
                            <div class="rv-avatar" style="flex-basis:72px;width:72px;height:72px">
                                @if (!empty($sos->driver_photo) && file_exists(public_path('assets/images/driver/' . $sos->driver_photo)))
                                    <img src="{{ asset('assets/images/driver') . '/' . $sos->driver_photo }}" alt="image">
                                @else
                                    <img src="{{ asset('assets/images/placeholder_image.jpg') }}" alt="image">
                                @endif
                            </div>
                            <div class="rv-profile-body">
                                <div class="rv-title-row">
                                    <h5 class="rv-name mb-0">
                                        @if ($sos->driverID)
                                            <a href="{{ route('driver.show', ['id' => $sos->driverID]) }}">{{ $sos->driverPreNom }} {{ $sos->driverNom }}</a>
                                        @else
                                            {{ trans('lang.not_available') }}
                                        @endif
                                    </h5>
                                </div>
                                <ul class="rv-meta">
                                    <li><i class="mdi mdi-phone-outline"></i><span>{{ $sos->driver_phone ?: trans('lang.not_available') }}</span></li>
                                </ul>
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

<script src="{{ asset('js/leaflet/leaflet.js')}}"></script>
<script src="{{ asset('js/leaflet/leaflet-routing-machine.js')}}"></script>

<script type="text/javascript">

    var latitude_depart = '{{ $sos->latitude_depart }}';
    var longitude_depart = '{{ $sos->longitude_depart}}';
    var latitude = '{{ $sos->latitude }}';
    var longitude = '{{ $sos->longitude }}';
    var latitude_arrivee = '{{ $sos->latitude_arrivee }}';
    var longitude_arrivee = '{{ $sos->longitude_arrivee }}';
    var destination_name =  '{{ $sos->destination_name }}';
    let mapType = "{{ $mapType }}";

    var lat = parseFloat(latitude);
    var lng = parseFloat(longitude);

    var locations = [
        ['origin', latitude_depart, longitude_depart, 1],
        ['sos', lat, lng, 2],
        ['des', latitude_arrivee, longitude_arrivee, 3],
    ];

    $(document).ready(function () {
    // document.addEventListener("mapsLoaded", function () {
        initialize();
    });

    function initialize() {

        if(mapType == "OSM"){
            var map = L.map('map').setView([lat, lng], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            var locationsOSM = [
                ['origin', latitude_depart, longitude_depart],
                ['sos', lat, lng],
                ['des', latitude_arrivee, longitude_arrivee],
            ];
            for (var i = 0; i < locationsOSM.length; i++) {
                var marker = L.marker([locationsOSM[i][1], locationsOSM[i][2]]).addTo(map);
                marker.bindPopup(locationsOSM[i][0]);
                if (i == 0) originMarker = marker;
                if (i == locationsOSM.length - 1) destinationMarker = marker;
            }
            L.Routing.control({
                waypoints: [
                    L.latLng(latitude_depart, longitude_depart),
                    L.latLng(lat, lng),
                    L.latLng(latitude_arrivee, longitude_arrivee)
                ],
                routeWhileDragging: true,
                draggableWaypoints: false,
                addWaypoints: false
            }).addTo(map);

            getLocationName(lat, lng, function(name) {
                if(name){
                    $('#sos_location').html(name);
                }else{
                    $('#sos_location').html(destination_name);
                }
            });

        } else {

            var directionsService = new google.maps.DirectionsService();
            var directionsDisplay = new google.maps.DirectionsRenderer();

            var latlng = new google.maps.LatLng(lat, lng);
            var geocoder = geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        $('#sos_location').html(results[1].formatted_address)
                    }
                }
            });
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 6,
                center: new google.maps.LatLng(21.7679, 78.8718),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            directionsDisplay.setMap(map);
            var infowindow = new google.maps.InfoWindow();
            var marker, i;
            var request = {
                travelMode: google.maps.TravelMode.DRIVING
            };
            for (i = 0; i < locations.length; i++) {
                marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                });
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
                })(marker, i));
                if (i == 0) request.origin = marker.getPosition();
                else if (i == locations.length - 1) request.destination = marker.getPosition();
                else {
                if (!request.waypoints) request.waypoints = [];
                request.waypoints.push({
                    location: marker.getPosition(),
                    stopover: true
                });
                }
            }
            directionsService.route(request, function(result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);
                }
            });

        }
    }

    function getLocationName(lat, lng, callback) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    callback(data.display_name);
                } else {
                    callback("Unknown location");
                }
            })
            .catch(err => {
                console.error(err);
                callback("Error getting location");
            });
    }


</script>

@endsection
