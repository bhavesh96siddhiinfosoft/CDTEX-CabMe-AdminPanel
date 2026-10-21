<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Apple Pay was never used on this platform — remove the method and its
 * (placeholder) settings so no client ever offers it.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('payment_settings')->where('id_payment_method', 17)->delete();
        DB::table('payment_method')->where('id', 17)->delete();
    }

    public function down(): void
    {
        // Intentionally irreversible: the method carried only 'test' values.
    }
};
