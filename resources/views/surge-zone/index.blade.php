@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    

    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.surge_zone_plural') }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card ec-card"><div class="card-body p-2"><div id="map" style="height: 600px; border-radius: 12px;"></div></div></div>
            </div>
        </div>
    </div>
</div>

@php
    $useGoogleMap = ($settings->map_for_application ?? 'OSM') === 'Google';
@endphp

@push('styles')
@unless ($useGoogleMap)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endunless
@endpush
@endsection

@section('scripts')
<script>
const zones = @json($zones);
const defaultLat = {{ $settings?->latitude ?? 22.3039 }};
const defaultLng = {{ $settings?->longitude ?? 70.8022 }};
const canEditZone = @json(auth()->user()?->can('surgezone.edit') ?? false);

const L_ZONE            = @json(trans('lang.zone'));
const L_SURGE           = @json(trans('lang.surge'));
const L_ACTIVE_REQUESTS = @json(trans('lang.active_requests'));
const L_ONLINE_DRIVERS  = @json(trans('lang.online_drivers'));
const L_RIDE_ID         = @json(trans('lang.ride_id'));
const L_USER            = @json(trans('lang.user'));
const L_PICKUP_ADDRESS  = @json(trans('lang.pickup_address'));
const L_UNKNOWN         = @json(trans('lang.unknown'));
const L_NA              = @json(trans('lang.not_available'));

function surgeColor(m) {
    m = parseFloat(m);
    if (m <= 1.0) return "#00FF00";
    if (m <= 1.5) return "#ADFF2F";
    if (m <= 2.0) return "#FFFF00";
    if (m <= 2.5) return "#FFA500";
    return "#FF0000";
}
</script>

@if ($useGoogleMap)

    <script>
    function initSurgeMap() {
        const map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: defaultLat, lng: defaultLng },
            zoom: 10,
        });

        const bounds = new google.maps.LatLngBounds();
        let circleCount = 0;

        zones.forEach(zone => {
            const name = zone.name || 'Zone';
            const color = surgeColor(zone.surge_multiplier);
            const center = { lat: parseFloat(zone.latitude), lng: parseFloat(zone.longitude) };
            const radiusM = parseFloat(zone.radius_km) * 1000;

            const circle = new google.maps.Circle({
                map, center, radius: radiusM,
                strokeColor: color, strokeOpacity: 1, strokeWeight: 2,
                fillColor: color, fillOpacity: 0.35,
                clickable: canEditZone,
            });

            if (canEditZone) {
                circle.addListener('click', () => window.location.href = `/surge-zone/edit/${zone.id}`);
            }

            const labelHtml = `<div style="background:rgba(255,255,255,0.9);padding:4px 8px;border-radius:4px;font-weight:bold;border:1px solid ${color}">${L_ZONE}: ${name}<br>${L_SURGE}: ${zone.surge_multiplier}x<br>${L_ACTIVE_REQUESTS}: ${zone.active_requests}<br>${L_ONLINE_DRIVERS}: ${zone.online_drivers}</div>`;
            const label = new google.maps.InfoWindow({ content: labelHtml, disableAutoPan: true });
            label.setPosition(center);
            label.open({ map, shouldFocus: false });

            bounds.union(circle.getBounds());
            circleCount++;

            if (zone.order_ids && zone.order_ids.length > 0) {
                fetch(`/surge-zone/${zone.id}/orders`)
                    .then(r => r.json())
                    .then(orders => {
                        orders.forEach(o => {
                            const marker = new google.maps.Marker({
                                position: { lat: parseFloat(o.lat), lng: parseFloat(o.lng) },
                                map,
                            });
                            const info = new google.maps.InfoWindow({
                                content: `<b>${L_RIDE_ID}:</b> ${o.id}<br><b>${L_USER}:</b> ${o.user_name || L_UNKNOWN}<br><b>${L_PICKUP_ADDRESS}:</b> ${o.pickup || L_NA}`,
                            });
                            marker.addListener('click', () => info.open({ map, anchor: marker }));
                        });
                    });
            }
        });

        if (circleCount > 0) {
            map.fitBounds(bounds);
        }
    }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $settings->google_map_api_key }}&callback=initSurgeMap"></script>

@else

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    $(document).ready(function () {
        const map = L.map('map').setView([defaultLat, defaultLng], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        const circles = [];
        zones.forEach(zone => {
            const name = zone.name || 'Zone';
            const color = surgeColor(zone.surge_multiplier);
            const circle = L.circle([zone.latitude, zone.longitude], {
                color, fillColor: color, fillOpacity: 0.35, weight: 2,
                radius: parseFloat(zone.radius_km) * 1000,
            }).addTo(map);

            L.marker([zone.latitude, zone.longitude], {
                icon: L.divIcon({
                    className: 'surge-label',
                    html: `<div style="background:rgba(255,255,255,0.9);padding:4px 8px;border-radius:4px;font-weight:bold;border:1px solid ${color}">${L_ZONE}: ${name}<br>${L_SURGE}: ${zone.surge_multiplier}x<br>${L_ACTIVE_REQUESTS}: ${zone.active_requests}<br>${L_ONLINE_DRIVERS}: ${zone.online_drivers}</div>`,
                    iconSize: [200, 30], iconAnchor: [50, 15],
                }),
                interactive: false,
            }).addTo(map);

            if (canEditZone) {
                circle.on('click', () => window.location.href = `/surge-zone/edit/${zone.id}`);
            }
            circles.push(circle);

            if (zone.order_ids && zone.order_ids.length > 0) {
                fetch(`/surge-zone/${zone.id}/orders`)
                    .then(r => r.json())
                    .then(orders => {
                        orders.forEach(o => {
                            L.marker([parseFloat(o.lat), parseFloat(o.lng)])
                                .addTo(map)
                                .bindPopup(`<b>${L_RIDE_ID}:</b> ${o.id}<br><b>${L_USER}:</b> ${o.user_name || L_UNKNOWN}<br><b>${L_PICKUP_ADDRESS}:</b> ${o.pickup || L_NA}`);
                        });
                    });
            }
        });

        if (circles.length > 0) {
            const group = L.featureGroup(circles);
            map.fitBounds(group.getBounds());
        }
    });
    </script>

@endif
@endsection
