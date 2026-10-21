<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requete', function (Blueprint $table) {
            if (!Schema::hasColumn('requete', 'surge_multiplier')) {
                $table->decimal('surge_multiplier', 4, 2)->default(1.00)->after('montant');
            }
            if (!Schema::hasColumn('requete', 'surge_zone_id')) {
                $table->unsignedBigInteger('surge_zone_id')->nullable()->after('surge_multiplier');
            }
        });

        Schema::create('surge_zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('zone_id')->index();
            $table->unsignedInteger('demand')->default(0);
            $table->unsignedInteger('supply')->default(0);
            $table->decimal('multiplier', 4, 2)->default(1.00);
            $table->timestamp('computed_at')->useCurrent();
            $table->index(['zone_id', 'computed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surge_zones');

        Schema::table('requete', function (Blueprint $table) {
            if (Schema::hasColumn('requete', 'surge_zone_id')) {
                $table->dropColumn('surge_zone_id');
            }
            if (Schema::hasColumn('requete', 'surge_multiplier')) {
                $table->dropColumn('surge_multiplier');
            }
        });
    }
};
