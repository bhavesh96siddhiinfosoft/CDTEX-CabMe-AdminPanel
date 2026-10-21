<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'web_app_store_link')) {
                $table->string('web_app_store_link', 500)->nullable()->after('web_panel_url');
            }
            if (! Schema::hasColumn('settings', 'web_play_store_link')) {
                $table->string('web_play_store_link', 500)->nullable()->after('web_app_store_link');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'web_play_store_link')) {
                $table->dropColumn('web_play_store_link');
            }
            if (Schema::hasColumn('settings', 'web_app_store_link')) {
                $table->dropColumn('web_app_store_link');
            }
        });
    }
};
