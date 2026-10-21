<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            foreach (['surge_enabled', 'surge_window_minutes', 'surge_min_demand', 'surge_cap', 'surge_cache_ttl', 'surge_tiers'] as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('requete', function (Blueprint $table) {
            if (Schema::hasColumn('requete', 'surge_zone_id')) {
                $table->dropColumn('surge_zone_id');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'surge_zone_radius')) {
                $table->decimal('surge_zone_radius', 6, 2)->default(2.00)->after('active_services');
            }
            if (!Schema::hasColumn('settings', 'surge_zone_ride_count')) {
                $table->unsignedInteger('surge_zone_ride_count')->default(3)->after('surge_zone_radius');
            }
            if (!Schema::hasColumn('settings', 'surge_zone_selection_minutes')) {
                $table->unsignedInteger('surge_zone_selection_minutes')->default(10)->after('surge_zone_ride_count');
            }
            if (!Schema::hasColumn('settings', 'surge_zone_duration_minutes')) {
                $table->unsignedInteger('surge_zone_duration_minutes')->default(30)->after('surge_zone_selection_minutes');
            }
            if (!Schema::hasColumn('settings', 'surge_zone_surge_percentage')) {
                $table->decimal('surge_zone_surge_percentage', 5, 2)->default(20.00)->after('surge_zone_duration_minutes');
            }
        });

        Schema::dropIfExists('surge_zones');

        Schema::create('surge_zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable();
            $table->decimal('latitude', 11, 7);
            $table->decimal('longitude', 11, 7);
            $table->decimal('radius_km', 6, 2);
            $table->unsignedInteger('active_requests')->default(0);
            $table->unsignedInteger('online_drivers')->default(0);
            $table->decimal('surge_multiplier', 4, 2)->default(1.00);
            $table->boolean('manual_override')->default(false);
            $table->longText('order_ids')->nullable();
            $table->string('publish', 10)->default('yes');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surge_zones');

        Schema::table('settings', function (Blueprint $table) {
            foreach (['surge_zone_radius', 'surge_zone_ride_count', 'surge_zone_selection_minutes', 'surge_zone_duration_minutes', 'surge_zone_surge_percentage'] as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
