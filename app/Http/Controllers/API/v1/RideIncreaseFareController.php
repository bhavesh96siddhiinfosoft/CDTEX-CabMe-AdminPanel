<?php

namespace App\Http\Controllers\API\v1;

use App\Events\DriverRidesUpdatedEvent;
use App\Http\Controllers\API\v1\GcmController;
use App\Http\Controllers\Controller;
use App\Jobs\AssignDriverJob;
use App\Models\Driver;
use App\Models\Requests;
use App\Models\Settings;
use App\Observers\RideObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class RideIncreaseFareController extends Controller
{
    public function bumpFare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_ride'     => 'required',
            'id_user'     => 'required',
            'new_amount'  => 'required|numeric|min:0',
            'bump_amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'Failed',
                'code'    => 404,
                'message' => $validator->errors()->first(),
                'data'    => null,
            ]);
        }

        $ride = Requests::where('id', $request->get('id_ride'))
            ->where('id_user_app', $request->get('id_user'))
            ->first();

        if (!$ride) {
            return response()->json([
                'success' => 'Failed',
                'code'    => 404,
                'message' => 'Ride not found',
                'data'    => null,
            ]);
        }

        if (!in_array($ride->statut, ['new', 'canceled'])) {
            return response()->json([
                'success' => 'Failed',
                'code'    => 400,
                'message' => 'Fare can only be bumped while ride is still searching for a driver',
                'data'    => null,
            ]);
        }

        $oldDriverId = $ride->assigned_driver_id ?: $ride->id_conducteur;

        $ride->montant             = $request->get('new_amount');
        $ride->bump_amount         = (float) ($ride->bump_amount ?? 0) + (float) $request->get('bump_amount');
        $ride->statut              = 'new';
        $ride->assigned_driver_id  = null;
        $ride->rejected_driver_id  = null;
        $ride->modifier            = date('Y-m-d H:i:s');
        $ride->save();

        // Notify the previously-assigned driver(s) so their UI clears the ride
        // immediately instead of waiting for a manual pull-to-refresh. In
        // broadcast mode assigned_driver_id held a comma-separated wave.
        foreach (array_filter(explode(',', (string) $oldDriverId)) as $previousDriverId) {
            $oldDriver = Driver::find($previousDriverId);
            if ($oldDriver && !empty($oldDriver->fcm_id)) {
                $message = [
                    "body"  => "Ride re-dispatched. Customer increased fare.",
                    "title" => "Ride update",
                    "sound" => "mySound",
                    "tag"   => "ridebumped",
                ];
                GcmController::sendNotification($oldDriver->fcm_id, $message);
            }

            $driverRides = (new RideObserver)->getDriverRides($previousDriverId);
            broadcast(new DriverRidesUpdatedEvent($previousDriverId, $driverRides));
            Log::info('DriverRidesUpdatedEvent re-fired for previously assigned driver', [
                'driver_id' => $previousDriverId,
                'driverRides' => $driverRides,
            ]);
        }

        // Hand the ride to a driver NOW, exactly as booking does. Leaving
        // this to the queue alone meant that with no worker running the ride
        // sat at statut=new with no assigned_driver_id — visible to nobody,
        // which is how a bumped fare "disappeared" from every driver screen.
        // The job still follows, as the accept/reject watchdog.
        $settings = Settings::first();
        $accept_reject_time = (int) ($settings->trip_accept_reject_driver_time_sec ?: 0);
        $broadcast = ($settings->driver_order_assignment ?? 'sequential') == 'broadcast';

        $finder = new RideRegisterController();
        $search = [
            'latitude_depart'  => $ride->latitude_depart,
            'longitude_depart' => $ride->longitude_depart,
            'vehicle_type_id'  => $ride->vehicle_type_id,
        ];

        $notify = function ($driver) {
            if (empty($driver->fcm_id)) {
                return;
            }

            GcmController::sendNotification($driver->fcm_id, [
                "body"  => "A ride near you now pays more",
                "title" => "Fare increased",
                "sound" => "mySound",
                "tag"   => "ridenewrider",
            ]);
        };

        if ($broadcast) {
            $wave = $finder->findoutDriver($search, $settings, true);

            if ($wave->isNotEmpty()) {
                $ride->assigned_driver_id = $wave->pluck('id')->implode(',');
                $ride->save();
                $wave->each($notify);
            }
        } else {
            $newDriver = $finder->findoutDriver($search, $settings);

            if ($newDriver) {
                $ride->assigned_driver_id = (string) $newDriver->id;
                $ride->save();
                $notify($newDriver);
            }
        }

        AssignDriverJob::dispatch($ride)->delay(now()->addSeconds($accept_reject_time > 0 ? $accept_reject_time : 30));

        Log::info('Ride fare bumped and re-dispatched', [
            'ride_id'     => $ride->id,
            'new_amount'  => $ride->montant,
            'total_bump'  => $ride->bump_amount,
            'old_driver'  => $oldDriverId,
            'assigned_to' => $ride->assigned_driver_id,
        ]);

        return response()->json([
            'success' => 'Success',
            'code'    => 200,
            'message' => 'Fare updated and ride re-dispatched to drivers',
            'data'    => [
                'id'          => $ride->id,
                'montant'     => (string) $ride->montant,
                'bump_amount' => (string) $ride->bump_amount,
                'statut'      => $ride->statut,
            ],
        ]);
    }
}
