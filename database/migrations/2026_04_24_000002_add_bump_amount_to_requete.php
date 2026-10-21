<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requete', function (Blueprint $table) {
            if (!Schema::hasColumn('requete', 'bump_amount')) {
                $table->decimal('bump_amount', 10, 2)->default(0)->after('montant');
            }
        });
    }

    public function down(): void
    {
        Schema::table('requete', function (Blueprint $table) {
            if (Schema::hasColumn('requete', 'bump_amount')) {
                $table->dropColumn('bump_amount');
            }
        });
    }
};
