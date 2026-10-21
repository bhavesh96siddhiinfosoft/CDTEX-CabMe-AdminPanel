<?php

namespace App\Http\Controllers;

use App\Models\ParcelDeliveryCharges;
use App\Models\Settings;
use App\Models\Zone;
use Illuminate\Http\Request;

class ParcelChargeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $zones = Zone::where('status', 'yes')->orderBy('name')->get();

        $rows = ParcelDeliveryCharges::all();
        $charges = ['default' => $rows->firstWhere('zone_id', null)];
        foreach ($zones as $z) {
            $charges[$z->id] = $rows->firstWhere('zone_id', $z->id);
        }

        $delivery_distance = optional(Settings::first())->delivery_distance ?? 'KM';

        return view('parcel-charges.index', compact('zones', 'charges', 'delivery_distance'));
    }

    public function update(Request $request)
    {
        $payload = $request->input('charges', []);
        $zones = Zone::where('status', 'yes')->pluck('id')->push(null); // include default

        foreach ($payload as $key => $values) {
            $zoneId = ($key === 'default') ? null : (int) $key;

            if (!is_null($zoneId) && !$zones->contains($zoneId)) {
                continue;
            }

            $perKm   = $values['per_km']     ?? null;
            $perKg   = $values['per_kg']     ?? null;

            $allBlank = ($perKm === null || $perKm === '') && ($perKg === null || $perKg === '');

            $existing = is_null($zoneId)
                ? ParcelDeliveryCharges::whereNull('zone_id')->first()
                : ParcelDeliveryCharges::where('zone_id', $zoneId)->first();

            if ($allBlank) {
                if ($existing) $existing->delete();
                continue;
            }

            $data = [
                'zone_id' => $zoneId,
                'delivery_charge_parcel' => $perKm !== '' ? $perKm : null,
                'parcel_per_weight_charge' => $perKg !== '' ? $perKg : null,
            ];

            if ($existing) {
                $existing->update($data);
            } else {
                ParcelDeliveryCharges::create($data);
            }
        }

        return redirect()->route('parcel-charges.index')->with('message', trans('lang.parcel_charges_updated'));
    }
}
