<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Driver order assignment mode (ported from Foodie's dispatch):
 *  - sequential  nearest eligible driver first, next one on timeout/reject
 *  - broadcast   all eligible nearby drivers at once, first to accept wins
 *
 * In broadcast mode requete.assigned_driver_id holds a comma-separated list
 * of every driver the offer went to, so the varchar(10) column must grow.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('settings', 'driver_order_assignment')) {
            Schema::table('settings', function ($table) {
                $table->string('driver_order_assignment', 20)->default('sequential');
            });
        }

        // Raw statement: no doctrine/dbal needed, and the column keeps its spot.
        DB::statement('ALTER TABLE requete MODIFY assigned_driver_id VARCHAR(1000) NULL');
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'driver_order_assignment')) {
            Schema::table('settings', function ($table) {
                $table->dropColumn('driver_order_assignment');
            });
        }

        DB::statement('ALTER TABLE requete MODIFY assigned_driver_id VARCHAR(10) NULL');
    }
};
