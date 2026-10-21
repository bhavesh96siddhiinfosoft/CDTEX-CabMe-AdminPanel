<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Web-panel settings, mirroring the Web Settings screen in quicklist-admin.
 *
 * The brand colour itself is not added here — `website_color` already exists and
 * is now owned by the Web section rather than Branding, so there stays exactly
 * one place to edit it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'web_panel_url')) {
                $table->string('web_panel_url')->nullable();
            }
            if (! Schema::hasColumn('settings', 'web_footer_description')) {
                $table->text('web_footer_description')->nullable();
            }
            if (! Schema::hasColumn('settings', 'web_map_iframe')) {
                $table->text('web_map_iframe')->nullable();
            }
            if (! Schema::hasColumn('settings', 'web_header_logo')) {
                $table->string('web_header_logo')->nullable();
            }
            if (! Schema::hasColumn('settings', 'web_footer_logo')) {
                $table->string('web_footer_logo')->nullable();
            }
            if (! Schema::hasColumn('settings', 'web_favicon')) {
                $table->string('web_favicon')->nullable();
            }
            // Six social urls and four cookie-consent fields, kept as JSON so the
            // settings row doesn't grow ten more flat columns. `pusher_settings`
            // already sets this precedent.
            if (! Schema::hasColumn('settings', 'web_social')) {
                $table->text('web_social')->nullable();
            }
            if (! Schema::hasColumn('settings', 'web_cookie_consent')) {
                $table->text('web_cookie_consent')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            foreach ([
                'web_panel_url',
                'web_footer_description',
                'web_map_iframe',
                'web_header_logo',
                'web_footer_logo',
                'web_favicon',
                'web_social',
                'web_cookie_consent',
            ] as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
