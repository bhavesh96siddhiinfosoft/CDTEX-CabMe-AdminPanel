<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\GcmController;
use App\Http\Controllers\Controller;
use App\Models\Requests;
use App\Models\Settings;
use App\Models\Driver;
use App\Models\Tax;
use App\Models\Coupon;
use App\Models\Zone;
use App\Jobs\AssignDriverJob;
use App\Helpers\Helper;
use App\Services\SurgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DB;
use Validator;

class RideRegisterController extends Controller
{
    public function rideBook(Request $request){

        $response = array();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:user_app,id',
            'latitude_depart' => 'required',
            'longitude_depart' => 'required',
            'latitude_arrivee' => 'required',
            'longitude_arrivee' => 'required',
            'depart_name' => 'required',
            'destination_name' => 'required',
            'total_people' => 'required',
            'total_children' => 'required',
            'sub_total' => 'required',
            'distance' => 'required',
            'duration' => 'required',
        ]);

        if($validator->fails()){
            $response['success'] = 'Failed';
            $response['code'] = 404;
            $response['message'] = $validator->errors()->first();
            $response['data'] = null;
            return response()->json($response);
        }

        Log::info('Ride book app request', ['data' => $request->all()]);

        $hasActiveRide = Requests::where('id_user_app', $request->get('user_id'))->whereIn('statut', ['new', 'confirmed', 'on ride'])->exists();
        if ($hasActiveRide) {
            $response['success'] = 'Failed';
            $response['code'] = 200;
            $response['message'] = 'You already have an active ride in progress';
            $response['data'] = null;
            return response()->json($response);
        }

        $settings = Settings::first();
        $accept_reject_time = (int) ($settings->trip_accept_reject_driver_time_sec ?: 0);
        $mapType = $settings->map_for_application;
        $google_map_api_key = $settings->google_map_api_key;

        $lat_source = $request->get('latitude_depart');
        $lng_source = $request->get('longitude_depart');

        // Service-area check: bail only if location is outside every zone
        $userZoneId = Helper::getUserZoneId($lat_source, $lng_source);
        if (empty($userZoneId)) {
            $response['success'] = 'Failed';
            $response['code'] = 200;
            $response['message'] = 'No service available in your area';
            $response['data'] = null;
            return response()->json($response);
        }

        $country = '';
        if($mapType == "Google" && !empty($google_map_api_key)){
            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat_source},{$lng_source}&key={$google_map_api_key}";
            $addressResponse = file_get_contents($url);
            $data = json_decode($addressResponse, true);
            if (!empty($data['results'][0]['address_components'])) {
                foreach ($data['results'][0]['address_components'] as $component) {
                    if (in_array('country', $component['types'])) {
                        $country = $component['long_name'];
                        break;
                    }
                }
            }
        }else{
            $addressResponse = Http::withHeaders([
                'User-Agent' => env('APP_NAME','Cabme')
                ])->get("https://nominatim.openstreetmap.org/reverse",[
                'lat' => $lat_source,
                'lon' => $lng_source,
                'format' => 'json'
            ]);
            $country = optional($addressResponse->json('address'))['country'] ?? '';
        }

        if($country){
            $taxes = Tax::where('statut','yes')->where('country',$country)->get()->toArray();
        }else{
            $taxes = Tax::where('statut','yes')->get()->toArray();
        }

        $discount_type = '';
        if($request->has('discount_id') && !empty($request->get('discount_id'))){
            $discount = Coupon::find($request->get('discount_id'));
            $discount_type = $discount ? ['type' => $discount->type, 'value' => $discount->discount] : '';
        }

        // Always create the booking. AssignDriverJob will keep searching
        // until a driver becomes free or the max wait window elapses.
        $booking = Requests::create([
            'id_user_app' => $request->get('user_id'),
            'latitude_depart' => $request->get('latitude_depart'),
            'longitude_depart' => $request->get('longitude_depart'),
            'latitude_arrivee' => $request->get('latitude_arrivee'),
            'longitude_arrivee' => $request->get('longitude_arrivee'),
            'depart_name' => $request->get('depart_name'),
            'destination_name' => $request->get('destination_name'),
            'number_poeple' => $request->get('total_people'),
            'total_children' => $request->get('total_children'),
            'vehicle_type_id' => $request->get('vehicle_type_id'),
            'distance' => $request->get('distance'),
            'distance_unit' => $settings->delivery_distance,
            'montant' => $request->get('sub_total'),
            'surge_multiplier' => $request->get('surge_multiplier'),
            'duree' => $request->get('duration'),
            'id_payment_method' => $request->get('id_payment'),
            'statut' => 'new',
            'creer' => date('Y-m-d H:i:s'),
            'modifier' => date('Y-m-d H:i:s'),
            'otp' => random_int(100000, 999999),
            'booking_number' => "#".random_int(100000, 999999),
            'otp_created' => now(),
            'statut_paiement' => 'no',
            'admin_commission_type' => null,
            'tax' => json_encode($taxes),
            'discount_type' => $discount_type ? json_encode($discount_type) : null,
            'stops' => Helper::normaliseStops($request->get('stops')),
        ]);

        // Order assignment mode (Foodie port): sequential offers the ride to
        // the nearest driver only; broadcast offers it to every eligible
        // nearby driver at once and the first to accept wins.
        if (($settings->driver_order_assignment ?? 'sequential') == 'broadcast') {
            $broadcastDrivers = $this->findoutDriver($request->all(), $settings, true);

            if ($broadcastDrivers->isNotEmpty()) {
                $booking->assigned_driver_id = $broadcastDrivers->pluck('id')->implode(',');
                $booking->save();

                foreach ($broadcastDrivers as $broadcastDriver) {
                    if (!empty($broadcastDriver->fcm_id)) {
                        $message = array("body" => 'A new ride booking has been assigned to you', "title" => 'New ride booking available', "sound" => "mySound", "tag" => "ridenewrider");
                        GcmController::sendNotification($broadcastDriver->fcm_id, $message);
                    }
                }

                AssignDriverJob::dispatch($booking)->delay(now()->addSeconds($accept_reject_time));
                Log::info('Broadcast dispatch from booking API', ['booking_id' => $booking->id, 'driver_ids' => $booking->assigned_driver_id]);
            } else {
                $retryDelay = $accept_reject_time > 0 ? $accept_reject_time : 30;
                AssignDriverJob::dispatch($booking)->delay(now()->addSeconds($retryDelay));
                Log::info('No driver currently free at booking time (broadcast) — retry scheduled.', ['booking_id' => $booking->id, 'retry_in_sec' => $retryDelay]);
            }
        } else {

        $newDriver = $this->findoutDriver($request->all(), $settings);

        if($newDriver){
            $booking->assigned_driver_id = (string) $newDriver->id;
            $booking->save();

            $fcm_token = $newDriver->fcm_id;
            if (!empty($fcm_token)) {
                $message = array("body" => 'A new ride booking has been assigned to you', "title" => 'New ride booking available', "sound" => "mySound", "tag" => "ridenewrider");
                GcmController::sendNotification($fcm_token, $message);
            }

            AssignDriverJob::dispatch($booking)->delay(now()->addSeconds($accept_reject_time));
            Log::info('AssignDriverJob first time triggered from booking API', ['booking_id' => $booking->id, 'driver_id' => $newDriver->id]);
        }else{
            $retryDelay = $accept_reject_time > 0 ? $accept_reject_time : 30;
            AssignDriverJob::dispatch($booking)->delay(now()->addSeconds($retryDelay));
            Log::info('No driver currently free at booking time — retry scheduled.', ['booking_id' => $booking->id, 'retry_in_sec' => $retryDelay]);
        }

        }

        // Trigger surge AFTER booking is persisted so demand counts include it.
        app(SurgeService::class)->handleRideCreated($booking);

        $bookingData = Requests::find($booking->id);
        $bookingData->discount_type = $bookingData->discount_type ? json_decode($bookingData->discount_type,true) : null;
        $bookingData->tax = $bookingData->tax ? json_decode($bookingData->tax,true) : null;
        $bookingData->stops = $bookingData->stops ? json_decode($bookingData->stops,true) : null;

        $response['success'] = 'success';
        $response['code'] = 200;
        $response['message'] = 'New booking successfully created';
        $response['data'] = $bookingData;

        return response()->json($response);
    }

    /**
     * $broadcast = false: nearest eligible driver (or '') — the original flow.
     * $broadcast = true: collection of ALL eligible drivers in the radius
     * (nearest first, capped), each already checked against its plan limit.
     */
    public function findoutDriver($data, $settings, $broadcast = false){

        //Get source lat long
        $lat = $data['latitude_depart'];
        $long = $data['longitude_depart'];
        $vehicle_type_id = $data['vehicle_type_id'];

        $userZoneId = Helper::getUserZoneId($lat, $long);
        if (empty($userZoneId)) {
            return $broadcast ? collect() : '';
        }

        //Get radius & distance map
        $delivery_distance = $settings->delivery_distance;
        $earthRadius = $delivery_distance == "KM" ? "6371" : "3959";
        $radius = $settings->driver_radios;
        $minimum_wallet_balance = $settings->minimum_deposit_amount ? $settings->minimum_deposit_amount : 0;

        $driverQuery = Driver::select(
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
        ->addBinding([$lat, $long, $lat], 'select')
        ->where('type_vehicule.id', $vehicle_type_id)
        ->where('conducteur.statut', 'yes')
        ->where('conducteur.online', 'yes')
        ->where('conducteur.driver_on_ride', 'no')
        ->where(function ($q) use ($minimum_wallet_balance, $settings) {
            // Owner
            $q->where('conducteur.isOwner', 'true')
            ->when($settings->owner_doc_verification == 'yes', function($query) {
                $query->where('conducteur.is_verified', 1);
            })
            // Drivers under owner (no verification needed)
            ->orWhere(function($q1) {
                $q1->whereNotNull('conducteur.ownerId');
                    // No is_verified filter here
            })
            // Individual drivers
            ->orWhere(function ($q2) use ($minimum_wallet_balance, $settings) {
                $q2->where('conducteur.isOwner', 'false')
                    ->whereNull('conducteur.ownerId')
                    ->where('conducteur.amount', '>=', $minimum_wallet_balance)
                    ->when($settings->driver_doc_verification == 'yes', function($sub2) {
                        $sub2->where('conducteur.is_verified', 1);
                    });
            });
        })
        // In broadcast mode a driver may hold several pending offers at once
        // (first accept wins), so only sequential filters out drivers who
        // already have an offer.
        ->when(!$broadcast, function ($q) {
            $q->whereNotIn('conducteur.id', function($query) {
                $query->select('assigned_driver_id')
                    ->from('requete')
                    ->whereNotIn('statut',['canceled', 'completed','rejected'])
                    ->whereNotNull('assigned_driver_id')
                    ->where('assigned_driver_id', '!=', '');
            });
        })
        ->having('distance', '<=', $radius)
        ->orderBy('distance', 'asc');

        if ($broadcast) {
            // Cap the wave so one booking cannot ping an unbounded fleet.
            $eligible = $driverQuery->limit(30)->get()
                ->filter(function ($d) { return Helper::isDriverBookingAllowed($d->id, 'subscriptionTotalOrders'); })
                ->values();

            Log::info('Ride broadcast drivers found', ['count' => $eligible->count()]);

            return $eligible;
        }

        $newDriver = $driverQuery->first();

        Log::info('Ride newDriver Found', ['newDriver' => $newDriver]);

        if ($newDriver && Helper::isDriverBookingAllowed($newDriver->id, 'subscriptionTotalOrders')) {
            return $newDriver;
        }

        return '';
     }
}