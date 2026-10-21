<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Paytm has been withdrawn from the platform — the apps dropped it, so the
 * admin must stop offering it too. No booking references the method, so the
 * row can go without orphaning any history.
 */
return new class extends Migration
{
    public function up(): void
    {
        $id = DB::table('payment_method')->where('libelle', 'Paytm')->value('id');

        if (! $id) {
            return;
        }

        DB::table('payment_settings')->where('id_payment_method', $id)->delete();
        DB::table('payment_method')->where('id', $id)->delete();
    }

    public function down(): void
    {
        // Intentionally irreversible: the integration has been removed as well.
    }
};