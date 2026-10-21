<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcel_delivery_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('zone_id')->nullable()->index();
            $table->decimal('delivery_charge_parcel', 10, 2)->nullable();
            $table->decimal('parcel_per_weight_charge', 10, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        // Copy current global settings into a single default row (zone_id NULL)
        $row = DB::table('settings')->select('delivery_charge_parcel', 'parcel_per_weight_charge')->first();
        if ($row && (!is_null($row->delivery_charge_parcel) || !is_null($row->parcel_per_weight_charge))) {
            DB::table('parcel_delivery_charges')->insert([
                'zone_id' => null,
                'delivery_charge_parcel' => $row->delivery_charge_parcel,
                'parcel_per_weight_charge' => $row->parcel_per_weight_charge,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('settings', function (Blueprint $table) {
            foreach (['delivery_charge_parcel', 'parcel_per_weight_charge'] as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'delivery_charge_parcel')) {
                $table->decimal('delivery_charge_parcel', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('settings', 'parcel_per_weight_charge')) {
                $table->decimal('parcel_per_weight_charge', 10, 2)->nullable();
            }
        });

        Schema::dropIfExists('parcel_delivery_charges');
    }
};
