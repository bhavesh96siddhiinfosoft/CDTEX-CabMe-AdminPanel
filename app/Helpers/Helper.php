<?php

namespace App\Helpers;

use App\Models\Note;
use App\Models\UserNote;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Settings;
use App\Models\Zone;
use App\Http\Controllers\GcmController;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class Helper {

    /**
     * Mask an address for display when the panel is running as a public demo.
     * Keyed off DEMO_MODE, the same flag the header badge, js/demo-mode.js and
     * DemoModeMiddleware read. Display only — stored data is untouched.
     */
    public static function shortEmail($email, $mask = "**********") {

        if (filter_var(env('DEMO_MODE', false), FILTER_VALIDATE_BOOLEAN)) {
            $atposition = strrpos($email, "@");
            $name = substr($email, 0, $atposition);
            $domain = substr($email, $atposition);
            $shortname = substr($name, 0, 1);
            return  $shortname.$mask.$domain;
        }else{
            return $email;
        }
    }

    /** Demo-mode counterpart of shortEmail() for phone numbers. */
    public static function shortNumber($number, $mask = "**********") {

        if (filter_var(env('DEMO_MODE', false), FILTER_VALIDATE_BOOLEAN)) {
            if(str_contains($number,'+')){           
                $shortnumber = substr($number, 0, 2);
                return  $shortnumber.$mask;
            }else{
                $shortnumber = substr($number, 0, 1);
                return  $shortnumber.$mask;
            }
        }else{
            return $number;
        }
    }

    public static function compressFile($source, $destination, $quality) { 
        // Get image info 
        $imgInfo = getimagesize($source); 
        $mime = $imgInfo['mime']; 
        // Create a new image from file 
        switch($mime){ 
            case 'image/jpeg': 
                $image = imagecreatefromjpeg($source); 
               imagejpeg($image, $destination, $quality);
                break; 
            case 'image/png': 
                $image = imagecreatefrompng($source); 
                imagepng($image, $destination, $quality);
                break; 
            case 'image/gif': 
                $image = imagecreatefromgif($source); 
                imagegif($image, $destination, $quality);
                break; 
            default: 
                $image = imagecreatefromjpeg($source); 
               imagejpeg($image, $destination, $quality);
        } 
        // Return compressed image 
        return $destination; 
    } 

    public static function getVehicleDetails($driverId){

        $vehicle = 
        Vehicle::where('id_conducteur', $driverId)
        ->leftjoin('brands', 'vehicule.brand', '=', 'brands.id')
        ->leftjoin('car_model', 'vehicule.model', '=', 'car_model.id')
        ->leftjoin('type_vehicule', 'vehicule.id_type_vehicule', '=', 'type_vehicule.id')
        ->addSelect('vehicule.brand', 'vehicule.model', 'vehicule.car_make', 'vehicule.numberplate', 'type_vehicule.libelle as type', 'type_vehicule.image','brands.name as brand', 'car_model.name as model')
        ->where('vehicule.statut', 'yes')->first();
        if (!$vehicle) {
            return [];
        }

        $data = $vehicle->toArray();
        
        if(!empty($data['image']) && file_exists(public_path('assets/images/type_vehicle'. '/'.$data['image']))){
            $data['image'] = asset('assets/images/type_vehicle') . '/' . $data['image'];
        }else{
            $data['image'] = asset('assets/images/placeholder_image.jpg');
        }

        return $data;
    }

    public static function isDriverBookingAllowed($driverId, $fieldname)
    {
        $fields = ['subscriptionTotalOrders', 'subscriptionTotalVehicle', 'subscriptionTotalDriver' ];
        if (!in_array($fieldname, $fields)) {
            return true;
        }

        $settings = Settings::first();
        $subscriptionModel = $settings?->subscription_model;
        
        $driverData = Driver::find($driverId);
        if (!empty($driverData->ownerId)) {
            $driverData = Driver::find($driverData->ownerId);
        }

        if ($subscriptionModel === "true") {
            $hasActivePlan = !is_null($driverData->subscriptionPlanId);
            $hasRemaining = $driverData->{$fieldname} == -1 || $driverData->{$fieldname} > 0;
            $notExpired = is_null($driverData->subscriptionExpiryDate) || $driverData->subscriptionExpiryDate > now();

            if ($hasActivePlan && $hasRemaining && $notExpired) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public static function resetDriverSubscriptionLimit($driverId, $fieldname, $action){

        $settings = Settings::first();
        $subscriptionModel = $settings?->subscription_model;
        if ($subscriptionModel !== "true") {
            return true;
        }
        
        $maindriverData = Driver::find($driverId);
        $driverData = !empty($maindriverData->ownerId) ? Driver::find($maindriverData->ownerId) : $maindriverData;

        $hasActivePlan = !is_null($driverData->subscriptionPlanId);
        $hasNotExpired = is_null($driverData->subscriptionExpiryDate) || $driverData->subscriptionExpiryDate > now(); 

        if (!$hasActivePlan || !$hasNotExpired) {
            return true;
        }

        $fields = ['subscriptionTotalOrders', 'subscriptionTotalVehicle', 'subscriptionTotalDriver' ];
        if (!in_array($fieldname, $fields)) {
            return true;
        }

        $currentValue = $driverData->{$fieldname};
        
        if ($currentValue != -1) {
            $currentValue = (int) $currentValue;
            if ($action == 'inc') {
                $newValue = $currentValue + 1;
            }else{
                $newValue = $currentValue - 1;
            }
            Driver::where('id', $driverData->id)->update([$fieldname => $newValue]);
        }

        return true;
    }

    public static function getUserZoneId($source_lat, $source_lng)
    {
        foreach (Zone::where('status', 'yes')->get() as $zone) {
            if (self::pointInZone($zone, $source_lat, $source_lng)) {
                return $zone->id;
            }
        }
        return null;
    }

    public static function driverInZone($driver_id, $source_lat, $source_lng)
    {
        $driver = Driver::find($driver_id);
        if (empty($driver) || empty($driver->zone_id)) {
            return false;
        }

        $driver_zone_ids = array_filter(explode(',', $driver->zone_id));
        if (empty($driver_zone_ids)) {
            return false;
        }

        $zones = Zone::whereIn('id', $driver_zone_ids)->where('status', 'yes')->get();
        foreach ($zones as $zone) {
            if (self::pointInZone($zone, $source_lat, $source_lng)) {
                return true;
            }
        }
        return false;
    }

    protected static function pointInZone($zone, $lat, $lng)
    {
        $area = json_decode($zone->area->toJson(), true);
        if (!isset($area['coordinates'][0])) {
            return false;
        }

        $vertices_x = $vertices_y = [];
        foreach ($area['coordinates'][0] as $v) {
            $vertices_x[] = $v[1]; // lat
            $vertices_y[] = $v[0]; // lng
        }
        return self::isInPolygon(0, $vertices_x, $vertices_y, $lat, $lng);
    }

    public static function isInPolygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y) {
        $n = count($vertices_x);
        if ($n < 3) {
            return false;
        }
        $c = false;
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            if ((($vertices_y[$i] > $latitude_y) != ($vertices_y[$j] > $latitude_y)) &&
                ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i])) {
                $c = !$c;
            }
        }
        return $c;
    }

    public static function getLatLong(){
        $latlong = [];

        $settings = Settings::first();
        $address = $settings->address;
        $mapType = $settings->map_for_application;
        $google_map_api_key = $settings->google_map_api_key;
        if (!empty($address)) {
            if ($mapType == 'osm') { 
                // OpenStreetMap (OSM) API for Geocoding
                $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($address);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Cabme/1.0'); // Required for OSM API
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL issues
                $response = curl_exec($ch);
                if (curl_errno($ch)) {
                    return ['error' => 'cURL Error: ' . curl_error($ch)];
                }
                curl_close($ch);
                $geo = json_decode($response, true);
                if (!empty($geo)) {
                    $latitude = $geo[0]['lat'] ?? null;
                    $longitude = $geo[0]['lon'] ?? null;
                }

            } else {

                if (empty($apiKey)) {
                    return ['error' => trans('lang.google_maps_key_is_missing')];
                }
                $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false&key=' . $google_map_api_key;
                $geo = file_get_contents($url);
                $geo = json_decode($geo, true);
                if (!$geo || !isset($geo['status'])) {
                    return ['error' => 'Invalid API response', 'response' => $geo];
                }
                if ($geo['status'] == 'OK' && !empty($geo['results'])) {
                    $latitude = $geo['results'][0]['geometry']['location']['lat'] ?? null;
                    $longitude = $geo['results'][0]['geometry']['location']['lng'] ?? null;
                    if ($latitude !== null && $longitude !== null) {
                        $latlong = array('lat'=> $latitude ,'lng'=>$longitude);
                    }
                }
            }

            if (!empty($latitude) && !empty($longitude)) {
                $latlong = ['lat' => $latitude, 'lng' => $longitude];
            }
        }

        return $latlong;
    }

    public static function isDriverEligibleForRide($driver_id, $lat_source, $lng_source, $vehicle_type_id, $userZoneId) {

        $settings = Settings::first();
        $radius = $settings->driver_radios;
        $minimum_wallet_balance = $settings->minimum_deposit_amount ? $settings->minimum_deposit_amount : 0;
        $delivery_distance = $settings->delivery_distance;
        $earthRadius = $delivery_distance == "KM" ? "6371" : "3959";

        $driver = Driver::select(
                'conducteur.id',
                'conducteur.fcm_id',
                'conducteur.subscriptionPlanId',
                'conducteur.subscriptionExpiryDate',
                'conducteur.subscriptionTotalOrders',
                'conducteur.subscription_plan',
                'conducteur.ownerId',
                DB::raw("(
                    $earthRadius * acos(
                        cos(radians(?)) *
                        cos(radians(conducteur.latitude)) *
                        cos(radians(conducteur.longitude) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(conducteur.latitude))
                    )
                ) AS distance")
            )
            ->join('vehicule', 'vehicule.id_conducteur', '=', 'conducteur.id')
            ->join('type_vehicule', 'vehicule.id_type_vehicule', '=', 'type_vehicule.id')
            ->join('zones', function ($join) use ($userZoneId) {
                $join->on(DB::raw('FIND_IN_SET(zones.id, conducteur.zone_id)'), '>', DB::raw('0'))
                    ->where('zones.status', '=', 'yes');
                if (!empty($userZoneId)) {
                    $join->where('zones.id', '=', $userZoneId);
                }
            })
            ->leftJoin('conducteur as owner', 'conducteur.ownerId', '=', 'owner.id')
            ->addBinding([$lat_source, $lng_source, $lat_source], 'select')
            ->where('type_vehicule.id', $vehicle_type_id)
            ->where('conducteur.statut', 'yes')
            ->where('conducteur.online', 'yes')
            ->where('conducteur.driver_on_ride', 'no')
            ->where('conducteur.id', $driver_id)
            ->where(function ($q) use ($minimum_wallet_balance, $settings) {
                $q->where('conducteur.isOwner', 'true')
                    ->when($settings->owner_doc_verification == 'yes', function($query) {
                        $query->where('conducteur.is_verified', 1);
                    })
                    ->orWhere(function($q1) {
                        $q1->whereNotNull('conducteur.ownerId');
                    })
                    ->orWhere(function ($q2) use ($minimum_wallet_balance, $settings) {
                        $q2->where('conducteur.isOwner', 'false')
                            ->whereNull('conducteur.ownerId')
                            ->where('conducteur.amount', '>=', $minimum_wallet_balance)
                            ->when($settings->driver_doc_verification == 'yes', function($sub2) {
                                $sub2->where('conducteur.is_verified', 1);
                            });
                    });
            })
            ->whereNotIn('conducteur.id', function($query) {
                $query->select('assigned_driver_id')
                    ->from('requete')
                    ->whereNotIn('statut',['canceled', 'completed','rejected'])
                    ->whereNotNull('assigned_driver_id')
                    ->where('assigned_driver_id', '!=', '');
            })
            ->having('distance', '<=', $radius)
            ->first();

        $eligible = $driver ? true : false;

        return [ 'eligible' => $eligible ];
    }

    public static function isDriverEligibleForRental($driver) {

        $settings = Settings::first();

        if (!$driver) {
            return [
                'eligible' => false,
                'reason' => 'Driver not found.'
            ];
        }

        $isFleet = false;
        $owner = null;

        // Check if driver is part of a fleet
        if (!empty($driver->ownerId)) {
            $isFleet = true;
            $owner = Driver::find($driver->ownerId);
        }

        // Fleet driver document verification
        if ($isFleet && $settings->owner_doc_verification == "yes" && $owner->is_verified == "0") {
            return [
                'eligible' => false,
                'reason' => 'Owner document is not verified.'
            ];
        }

        // Individual driver document verification
        if (!$isFleet && $settings->driver_doc_verification == "yes" && $driver->is_verified == "0") {
            return [
                'eligible' => false,
                'reason' => 'Driver document is not verified.'
            ];
        }

        // Vehicle assigned
        $vehicle = Vehicle::where('id_conducteur', $driver->id)->first();
        if (!$vehicle) {
            return [
                'eligible' => false,
                'reason' => 'Vehicle not assigned.'
            ];
        }

        // Minimum wallet balance check (individual drivers only)
        $driverWallet = $driver->amount ?? 0;
        $minimum_wallet_balance = $settings->minimum_deposit_amount ?? 0;
        if (!$isFleet && $driverWallet < $minimum_wallet_balance) {
            return [
                'eligible' => false,
                'reason' => 'Minimum wallet balance required: ' . $minimum_wallet_balance
            ];
        }

        // If all checks passed
        return [
            'eligible' => true,
            'vehicle' => $vehicle,
            'isFleet' => $isFleet
        ];
    }

    public static function isDriverEligibleForParcel($driver, $source_lat = null, $source_lng = null)
    {
        $settings = Settings::first();

        if (!$driver) {
            return [
                'eligible' => false,
                'reason' => 'Driver not found.'
            ];
        }

        $isFleet = false;
        $owner = null;

        // Check if driver is part of a fleet
        if (!empty($driver->ownerId)) {
            $isFleet = true;
            $owner = Driver::find($driver->ownerId);
        }

        // Fleet driver document verification
        if ($isFleet && $settings->owner_doc_verification == "yes" && $owner->is_verified == "0") {
            return [
                'eligible' => false,
                'reason' => 'Owner document is not verified.'
            ];
        }

        // Individual driver document verification
        if (!$isFleet && $settings->driver_doc_verification == "yes" && $driver->is_verified == "0") {
            return [
                'eligible' => false,
                'reason' => 'Driver document is not verified.'
            ];
        }

        // Minimum wallet balance (individual drivers only)
        $driverWallet = $driver->amount ?? 0;
        $minimum_wallet_balance = $settings->minimum_deposit_amount ?? 0;
        if (!$isFleet && $driverWallet < $minimum_wallet_balance) {
            return [
                'eligible' => false,
                'reason' => 'Minimum wallet balance required: ' . $minimum_wallet_balance
            ];
        }

        // Check if driver is in valid zone for source location
        if (!empty($source_lat) && !empty($source_lng)) {
            $inZone = self::driverInZone($driver->id, $source_lat, $source_lng);
            if (!$inZone) {
                return [
                    'eligible' => false,
                    'reason' => 'Driver is not available in the selected zone.'
                ];
            }
        }

        return [
            'eligible' => true,
            'isFleet' => $isFleet
        ];
    }

    public static function newBookingNotification($serviceType, $booking = null){

        $drivers = Driver::where('role', 'driver')->where('statut', 'yes')->where('online', 'yes')->where('service_type', 'LIKE', "%$serviceType%")->get();

        $eligibleDrivers = [];
        foreach ($drivers as $driver) {
            if($serviceType == "rental"){
                $check = self::isDriverEligibleForRental($driver);    
            }else if($serviceType == "parcel"){
                $check = self::isDriverEligibleForParcel($driver);    
            }
            if ($check['eligible']) {
                $eligibleDrivers[] = $driver;
            }
        }

        if (!empty($eligibleDrivers)) {

            $title = "New $serviceType booking available";
            $msg = "A new $serviceType booking has been created. Please check and accept if available.";
            
            foreach ($eligibleDrivers as $driver) {
                if (!empty($driver->fcm_id)) {
                    $message = [ "body" => $msg, "title" => $title, "sound" => 'mySound', "tag" => $serviceType.'newbooking' ];
                    GcmController::sendNotification($driver->fcm_id, $message);
                    Notification::create([
                        'titre' => $title,
                        'message' => $msg,
                        'statut' => 'yes',
                        'creer' => now(),
                        'modifier' => now(),
                        'to_id' => $driver->id,
                        'from_id' => $booking->id_user_app ?? 0,
                        'type' => $serviceType.'newbooking',
                    ]);
                }
            }
        }
    }

    /**
     * The one shape ride stops are stored in.
     *
     * Clients post this differently — some send {latitude, longitude,
     * location} with string coordinates, some {..., address} with json
     * numbers — and a consumer that parses the list strictly breaks on the
     * first row it does not recognise. Every writer goes through here, so
     * what lands in requete.stops is identical whoever booked the ride.
     *
     * @param  mixed  $stops  json string or array, as posted
     * @return string|null    json ready for the column
     */
    public static function normaliseStops($stops)
    {
        if (is_string($stops)) {
            $stops = json_decode($stops, true);
        }

        if (! is_array($stops)) {
            return null;
        }

        $clean = [];

        foreach ($stops as $stop) {
            if (! is_array($stop) || ! isset($stop['latitude'], $stop['longitude'])) {
                continue;
            }

            $clean[] = [
                'latitude'  => (string) $stop['latitude'],
                'longitude' => (string) $stop['longitude'],
                // `address` is what older web builds sent.
                'location'  => (string) ($stop['location'] ?? $stop['address'] ?? ''),
            ];
        }

        return $clean ? json_encode($clean) : null;
    }
}   
?>