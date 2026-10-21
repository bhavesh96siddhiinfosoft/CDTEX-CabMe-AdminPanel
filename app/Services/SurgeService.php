<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Requests;
use App\Models\Settings;
use App\Models\SurgeZone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SurgeService
{
    public function multiplierForLocation(?float $lat, ?float $lng): float
    {
        if ($lat === null || $lng === null) return 1.0;
        $zone = $this->findZoneContaining($lat, $lng);
        return $zone ? (float) $zone->surge_multiplier : 1.0;
    }

    public function handleRideCreated(Requests $order): void
    {
        try {
            $lat = (float) $order->latitude_depart;
            $lng = (float) $order->longitude_depart;
            if (!$lat || !$lng) return;

            $settings = Settings::first();
            if (!$settings) return;

            $nearby = $this->countNearbyActiveOrders($lat, $lng, $settings);
            if ($nearby < (int) $settings->surge_zone_ride_count) {
                Log::info('Surge threshold not met', ['order' => $order->id, 'nearby' => $nearby]);
                return;
            }

            $zone = $this->findOrCreateZone($order, $settings);
            $this->updateZone($zone, $settings);
        } catch (\Throwable $e) {
            Log::error('Surge handleRideCreated failed: ' . $e->getMessage());
        }
    }

    public function sweepAll(): void
    {
        $settings = Settings::first();
        if (!$settings) return;

        foreach (SurgeZone::all() as $zone) {
            $this->updateZone($zone, $settings);
        }

        // Pick up rides that were created by other apps (e.g. dispatcher panel)
        // and never went through handleRideCreated. findOrCreateZone is idempotent
        // for rides already attached to an existing zone.
        $cutoff = now()->subMinutes((int) $settings->surge_zone_selection_minutes);
        $recent = Requests::where('statut', 'new')
            ->where('creer', '>=', $cutoff)
            ->whereNotNull('latitude_depart')
            ->whereNotNull('longitude_depart')
            ->get();

        foreach ($recent as $ride) {
            $this->handleRideCreated($ride);
        }
    }

    protected function findZoneContaining(float $lat, float $lng): ?SurgeZone
    {
        foreach (SurgeZone::where('publish', 'yes')->get() as $z) {
            if ($this->distanceKm($lat, $lng, (float) $z->latitude, (float) $z->longitude) <= (float) $z->radius_km) {
                return $z;
            }
        }
        return null;
    }

    protected function findOrCreateZone(Requests $order, Settings $settings): SurgeZone
    {
        $lat = (float) $order->latitude_depart;
        $lng = (float) $order->longitude_depart;
        $orderId = (string) $order->id;

        foreach (SurgeZone::all() as $z) {
            $ids = $z->order_ids ?: [];
            if (in_array($orderId, $ids, true)) return $z;
        }

        foreach (SurgeZone::all() as $z) {
            if ($this->distanceKm($lat, $lng, (float) $z->latitude, (float) $z->longitude) <= (float) $z->radius_km) {
                $ids = $z->order_ids ?: [];
                if (!in_array($orderId, $ids, true)) $ids[] = $orderId;
                $z->order_ids = array_values(array_unique($ids));
                $z->save();
                return $z;
            }
        }

        return SurgeZone::create([
            'name' => $this->reverseGeocode($lat, $lng),
            'latitude' => $lat,
            'longitude' => $lng,
            'radius_km' => (float) $settings->surge_zone_radius,
            'active_requests' => 0,
            'online_drivers' => 0,
            'surge_multiplier' => 1.0,
            'order_ids' => [$orderId],
            'publish' => 'yes',
        ]);
    }

    protected function updateZone(SurgeZone $zone, Settings $settings): void
    {
        $inactivityCutoff = now()->subMinutes((int) $settings->surge_zone_duration_minutes);
        $hasRecentOrders = Requests::where('creer', '>=', $inactivityCutoff)
            ->get(['latitude_depart', 'longitude_depart'])
            ->contains(fn ($r) => $this->distanceKm(
                (float) $r->latitude_depart, (float) $r->longitude_depart,
                (float) $zone->latitude, (float) $zone->longitude
            ) <= (float) $zone->radius_km);

        if (!$hasRecentOrders && !$zone->manual_override) {
            $zone->delete();
            return;
        }

        $demand = $this->countActiveOrdersInZone($zone, $settings);
        $supply = $this->countOnlineDriversInZone($zone);

        $selectionCutoff = now()->subMinutes((int) $settings->surge_zone_selection_minutes);
        $activeIds = Requests::where('statut', 'new')
            ->where('creer', '>=', $selectionCutoff)
            ->get(['id', 'latitude_depart', 'longitude_depart'])
            ->filter(fn ($r) => $this->distanceKm((float) $r->latitude_depart, (float) $r->longitude_depart, (float) $zone->latitude, (float) $zone->longitude) <= (float) $zone->radius_km)
            ->pluck('id')
            ->map(fn ($i) => (string) $i)
            ->values()
            ->all();

        $zone->active_requests = $demand;
        $zone->online_drivers = $supply;
        $zone->order_ids = $activeIds;
        $zone->radius_km = (float) $settings->surge_zone_radius;

        if (!$zone->manual_override) {
            $multiplier = 1.0;
            if ($demand >= (int) $settings->surge_zone_ride_count) {
                $multiplier = $this->calculateSurge($demand, $supply, (float) $settings->surge_zone_surge_percentage);
            }
            $zone->surge_multiplier = $multiplier;
        }

        $zone->save();
    }

    protected function countNearbyActiveOrders(float $lat, float $lng, Settings $settings): int
    {
        $cutoff = now()->subMinutes((int) $settings->surge_zone_selection_minutes);
        $radius = (float) $settings->surge_zone_radius;

        return Requests::where('statut', 'new')
            ->where('creer', '>=', $cutoff)
            ->get(['latitude_depart', 'longitude_depart'])
            ->filter(fn ($r) => $this->distanceKm($lat, $lng, (float) $r->latitude_depart, (float) $r->longitude_depart) <= $radius)
            ->count();
    }

    protected function countActiveOrdersInZone(SurgeZone $zone, Settings $settings): int
    {
        $cutoff = now()->subMinutes((int) $settings->surge_zone_selection_minutes);
        return Requests::where('statut', 'new')
            ->where('creer', '>=', $cutoff)
            ->get(['latitude_depart', 'longitude_depart'])
            ->filter(fn ($r) => $this->distanceKm((float) $r->latitude_depart, (float) $r->longitude_depart, (float) $zone->latitude, (float) $zone->longitude) <= (float) $zone->radius_km)
            ->count();
    }

    protected function countOnlineDriversInZone(SurgeZone $zone): int
    {
        return Driver::where('statut', 'yes')
            ->where('online', 'yes')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['latitude', 'longitude'])
            ->filter(fn ($d) => $this->distanceKm((float) $d->latitude, (float) $d->longitude, (float) $zone->latitude, (float) $zone->longitude) <= (float) $zone->radius_km)
            ->count();
    }

    protected function calculateSurge(int $demand, int $supply, float $pct): float
    {
        if ($supply <= 0) {
            return round(1 + $pct / 100, 2);
        }
        $multiplier = 1 + ($pct / 100) * log(($demand / $supply) + 1, 2);
        return round($multiplier, 2);
    }

    protected function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    protected function reverseGeocode(float $lat, float $lng): string
    {
        try {
            $res = Http::withHeaders(['User-Agent' => env('APP_NAME', 'Cabme')])
                ->timeout(5)
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $lat, 'lon' => $lng, 'format' => 'json', 'zoom' => 14, 'addressdetails' => 1,
                ]);
            $addr = $res->json('address') ?? [];
            $area = $addr['neighbourhood'] ?? $addr['suburb'] ?? $addr['city_district'] ?? $addr['locality'] ?? $addr['hamlet'] ?? null;
            $main = $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['municipality'] ?? null;
            $state = $addr['state'] ?? null;

            if ($area && $main) $name = "$area - $main";
            elseif ($main && $state) $name = "$main - $state";
            elseif ($area) $name = $area;
            else $name = 'Unknown Area';

            return ucwords(strtolower(trim(preg_replace('/\s+/', ' ', $name))));
        } catch (\Throwable $e) {
            return 'Unknown Area';
        }
    }

}
