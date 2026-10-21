<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gives the customer WEB panel its own brand colour.
 *
 * It had been sharing `website_color` with the customer APP, so editing either
 * one moved both — and because the branding page could no longer write that
 * column, "Customer App Color" saved nothing at all. The new column starts as a
 * copy of the app colour so no live site changes appearance on deploy.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('settings', 'web_panel_color')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->string('web_panel_color', 20)->nullable()->after('website_color');
        });

        DB::table('settings')->update(['web_panel_color' => DB::raw('`website_color`')]);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('settings', 'web_panel_color')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('web_panel_color');
        });
    }
};