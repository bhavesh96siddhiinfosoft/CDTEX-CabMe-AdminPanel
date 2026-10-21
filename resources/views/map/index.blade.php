@extends('layouts.app')
@section('content')
        
<div class="page-wrapper listings-page">
    <div class="container-fluid">
        <div class="ec-page-head mb-4">
            <div class="ec-page-head-text">
                <h3 class="mb-1">{{ trans('lang.live_tracking') }}</h3>
                <p class="mb-0 text-muted">{{ trans('lang.live_tracking_desc') }}</p>
            </div>
        </div>
        <div class="row lt-row">
            <div class="col-lg-4 mb-4">
                <div class="card ec-card ec-form-card lt-card h-100">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-bike-fast"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.drivers') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="lt-search">
                            <i class="mdi mdi-magnify"></i>
                            <input type="text" id="searchInput" class="form-control" oninput="searchDriver()" placeholder="{{ trans('lang.search_driver') }}">
                        </div>
                        <div class="ride-list">
                            <div id="overlay" style="display:none">
                                <img src="{{ asset('images/spinner.gif') }}">
                            </div>
                            <div class="live-tracking-list"></div>
                            <div id="load-more-div" style="display:none" class="text-center mt-2">
                                <a href="javascript:void(0)" class="btn ec-btn-ghost btn-sm" id="load-more">{{ trans('lang.load_more') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mb-4">
                <div class="card ec-card ec-form-card lt-card h-100">
                    <div class="card-header d-flex align-items-center">
                        <span class="ec-filter-badge"><i class="mdi mdi-map-marker-radius-outline"></i></span>
                        <h5 class="mb-0 ml-2">{{ trans('lang.map') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="map"></div>
                        <div id="legend">
                            <h3>{{ trans('lang.legend') }}</h3>
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
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>

<script type="text/javascript">
    var map;
    var marker;
    var markers = [];
    var map_data = [];
    
    var default_lat = '23.022505';
    var default_lng ='72.571365';
    let mapType = "{{ $mapType }}"; 
    let defaultLatLong = @json($lat_long);
    let driver_data = @json($driver_data);
    
    var itemsPerPage = 10;
    var currentPage = 1; 
    
    if(defaultLatLong.length !=0 ){
        default_lat = parseFloat(defaultLatLong['lat']);
        default_lng = parseFloat(defaultLatLong['lng']);
    }
    
    var base_url = '{!! asset('/images/') !!}';
    
    $(document).ready(function () {

        setTimeout(function(){
            initMap();
            $(".sidebartoggler").click();
        },1000);
        
        $(document).on("click",".ride-list .track-from",function(){
            var lat = $(this).data('lat');
            var lng = $(this).data('lng');
            var index = $(this).data('index');
            if (mapType == "OSM"){
                if (markers[index]) {
                    map.setView([lat, lng], map.getZoom());
                    markers[index].openPopup();
                } else {
                    console.log("Marker at index " + index + " is undefined.");
                }
            } else{
                if (markers[index]) {
                    map.panTo(new google.maps.LatLng(lat, lng));
                    google.maps.event.trigger(markers[index], 'click');
                } else {
                    console.log("Marker at index " + index + " is undefined.");
                }
            }
        });
    });

    // Filter the rendered driver cards by name, like foodie's list.
    function searchDriver() {
        var term = document.getElementById("searchInput").value.trim().toLowerCase();
        document.querySelectorAll(".live-tracking-box").forEach(function (box) {
            if (term === "") { box.style.display = ""; return; }
            var name = box.querySelector(".drier-name");
            box.style.display = name && name.textContent.toLowerCase().indexOf(term) !== -1 ? "" : "none";
        });
    }

    function initMap() {

        var default_lat = getCookie('default_latitude');
        var default_lng = getCookie('default_longitude');
        var legend = document.getElementById('legend');

        if (mapType == "OSM" ){

            map = L.map('map').setView([default_lat, default_lng], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

        } else{
            var myLatlng = new google.maps.LatLng(default_lat, default_lng);
            var infowindow = new google.maps.InfoWindow();
            var mapOptions = {
                zoom: 10,
                center: myLatlng,
                streetViewControl: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("map"), mapOptions);
        }
        var fliter_icons = {
            available: {
                name: 'Available',
                icon: base_url + '/available.png'
            },
            ontrip: {
                name: 'In Transit',
                icon: base_url + '/ontrip.png'
            }
        };

        for (var key in fliter_icons) {
            var type = fliter_icons[key];
            var name = type.name;
            var icon = type.icon;
            var div = document.createElement('div');
            div.innerHTML = '<img src="' + icon + '"> ' + name;
            legend.appendChild(div);
        }
        
        if (mapType == "OSM" ){
            var lmaplegend  = L.control({ position: 'bottomleft' });
            lmaplegend.onAdd = function (map) {
                var div = L.DomUtil.create('div', 'legend');
                div.innerHTML = "<h4>Map Legend</h4>"; 
                div.appendChild(legend);
                return div;
            };
            lmaplegend.addTo(map);
        } else{
            map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(legend);
        }

        loadData(driver_data,currentPage);
    }

    function loadData(data,page) {
        var startIndex = (page - 1) * itemsPerPage;
        var endIndex = startIndex + itemsPerPage;
        var itemsToDisplay = data.slice(startIndex, endIndex);
        itemsToDisplay.forEach(async (item, i) => {
            val = item;
            var html = '';
            html += '<div class="live-tracking-box track-from" data-lat="'+val.driver_latitude+'" data-lng="'+val.driver_longitude+'" data-index='+i+'>';
                html += '<div class="live-tracking-inner">';
                    html += '<span class="listicon"></span>';
                    if(val.flag == "on_ride" && val.hasOwnProperty('ride_details')){
                        var routeUrl  = '';
                        if(val.ride_details.ride_type == "Ride"){
                            routeUrl = '/ride/show/'+val.ride_details.ride_id;
                        }else if(val.ride_details.ride_type == "Parcel"){
                            routeUrl = '/parcel/show/'+val.ride_details.ride_id;
                        }else if(val.ride_details.ride_type == "Rental"){
                            routeUrl = '/rental-orders/show/'+val.ride_details.ride_id;
                        }
                        html += '<a href="'+routeUrl+'" target="_blank"><i class="text-dark fs-12 fa-solid fa-circle-info" data-toggle="tooltip"></i></a>';
                        html += '&nbsp;&nbsp;<span>'+val.ride_details.ride_type+'</span>';
                    }
                    html += '<h3 class="drier-name">{{trans("lang.driver_name")}} : '+val.driver_name+'</h3>';
                    if(val.user_name){
                        html += '<h4 class="user-name">{{trans("lang.user_name")}} : '+val.user_name+'</h4>';
                    }
                    if(val.hasOwnProperty('ride_details')){
                        html += '<div class="location-ride">';	
                            html += '<div class="from-ride"><span>'+val.ride_details.depart_name+'</span></div>';
                            html += '<div class="to-ride"><span>'+val.ride_details.destination_name+'</span></div>';
                        html += '</div>';
                    }
                    if(val.flag == "on_ride"){
                        html += '<span class="badge badge-danger">On Ride</span>';
                    }else{
                        html += '<span class="badge badge-success">Available</span>';
                    }
                html += '</div>';
            html += '</div>';
            $(".live-tracking-list").append(html);
            if (typeof val.driver_latitude != 'undefined' && typeof val.driver_longitude != 'undefined') {
                let content = `
                    <div class="p-2">
                        <h6>{{trans('lang.driver_name')}} : ${val.driver_name ?? '-'} </h6>
                        <h6>{{trans('lang.mobile_no')}} : ${val.driver_mobile ?? '-'} </h6>
                        <h6>{{trans('lang.brand')}} : ${val.vehicle_brand ?? '-'} </h6>
                        <h6>{{trans('lang.car_number')}} : ${val.vehicle_number ?? '-'} </h6>
                        <h6>{{trans('lang.car_model')}} : ${val.vehicle_model ?? '-'} </h6>
                        <h6>{{trans('lang.car_make')}} : ${val.vehicle_make ?? '-'} </h6>
                    </div>`;
                let iconImg = '';
                let position = '';
                if(val.flag == "available"){
                    iconImg = base_url + '/car_available.png';
                }else{
                    iconImg = base_url + '/car_on_trip.png';
                }
                if (mapType == "OSM" ){
                    var customIcon = L.icon({
                        iconUrl: iconImg,
                        iconSize: [25, 25],
                    });
                    let marker = L.marker([val.driver_latitude, val.driver_longitude], { icon: customIcon }).addTo(map);
                    marker.bindPopup(content);
                    markers[i] = marker;
                    marker.on('click', function () {
                        marker.openPopup();
                    });
                    setInterval(function () {
                        locationUpdate(marker, val);
                    }, 10000);
                } else {
                    let marker = new google.maps.Marker({
                        position: new google.maps.LatLng(val.driver_latitude, val.driver_longitude),
                        icon: {
                            url: iconImg,
                            scaledSize: new google.maps.Size(25, 25)
                        },
                        map: map
                    });
                    let infowindow = new google.maps.InfoWindow({
                        content: content
                    });
                    marker.addListener('click', function () {
                        infowindow.open(map, marker);
                    });
                    markers.push(marker);
                    marker.setMap(map);
                    setInterval(function() {
                        locationUpdate(marker,val);
                    },10000);
                }
            }
        });

        async function locationUpdate(marker, val) {
            try {
                let url = `/get-driver-location/${val.driver_id}`;;
                let response = await fetch(url);
                let data = await response.json();
                if (data && data.driver_latitude && data.driver_longitude) {
                    if (mapType === "OSM") {
                        marker.setLatLng([data.driver_latitude, data.driver_longitude]);
                    } else {
                        marker.setPosition(new google.maps.LatLng(data.driver_latitude, data.driver_longitude));
                    }
                }
            } catch (error) {
                console.error("Location update failed:", error);
            }
        }

        jQuery("#overlay").hide();
         if (endIndex >= data.length) {
            $('#load-more-div').css('display','none');
        }else{
            $('#load-more-div').css('display','block');
        }
    }
    
    $('#load-more').on('click',function(){
        currentPage++;
        loadData(driver_data,currentPage);
    });

</script>
@endsection
