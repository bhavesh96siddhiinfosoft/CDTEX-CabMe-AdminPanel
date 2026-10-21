<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\SurgeZone;
use App\Services\SurgeService;
use Illuminate\Http\Request;

class SurgeZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(SurgeService $surge)
    {
        $surge->sweepAll();
        $zones = SurgeZone::all()->map(function ($z) {
            return [
                'id' => $z->id,
                'name' => $z->name,
                'latitude' => (float) $z->latitude,
                'longitude' => (float) $z->longitude,
                'radius_km' => (float) $z->radius_km,
                'surge_multiplier' => (float) $z->surge_multiplier,
                'active_requests' => (int) $z->active_requests,
                'online_drivers' => (int) $z->online_drivers,
                'order_ids' => $z->order_ids ?: [],
            ];
        });
        $settings = Settings::first();
        return view('surge-zone.index', compact('zones', 'settings'));
    }

    public function edit($id)
    {
        $zone = SurgeZone::findOrFail($id);
        return view('surge-zone.edit', compact('zone'));
    }

    public function update(Request $request, $id)
    {
        $zone = SurgeZone::findOrFail($id);
        $zone->name = $request->input('name');
        $zone->surge_multiplier = (float) $request->input('surge_multiplier', 1);
        $zone->manual_override = $request->boolean('manual_override');
        $zone->save();

        return redirect()->route('surgezone.index')->with('message', 'Surge zone updated');
    }

    public function ordersForZone($id)
    {
        $zone = SurgeZone::findOrFail($id);
        $ids = $zone->order_ids ?: [];
        if (empty($ids)) return response()->json([]);

        $orders = \DB::table('requete')
            ->leftJoin('user_app', 'user_app.id', '=', 'requete.id_user_app')
            ->whereIn('requete.id', $ids)
            ->select(
                'requete.id',
                'requete.latitude_depart as lat',
                'requete.longitude_depart as lng',
                'requete.depart_name as pickup',
                \DB::raw("CONCAT(COALESCE(user_app.prenom,''),' ',COALESCE(user_app.nom,'')) as user_name")
            )
            ->get();

        return response()->json($orders);
    }
}
