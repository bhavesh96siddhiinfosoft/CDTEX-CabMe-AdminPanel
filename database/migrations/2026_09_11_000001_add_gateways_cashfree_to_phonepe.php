<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Six payment gateways cabme was missing next to foodie: Cashfree, Foloosi,
 * Instamojo, MTN MoMo, PayMongo and PhonePe, as ids 21-26.
 *
 * Most of their credentials map onto columns payment_settings already has
 * (clientpublishableKey / secret_key / merchant_key / merchant_Id / key /
 * callback_url). Four have no home, so they are added here:
 *
 *   target_environment   MTN MoMo — sandbox|mtnuganda|mtnghana|…
 *   expiry_time_seconds  MTN MoMo — how long a collection request stays open
 *   salt_key             PhonePe  — signs the request checksum
 *   flow_id              PhonePe  — which payment flow to open
 *
 * The rows are seeded disabled: a gateway with no credentials must not be
 * offered to riders. Filling the keys in and ticking Active turns it on.
 *
 * NOTE: this is the admin/config half only. The rider and driver apps still
 * need each gateway's SDK before a payment can actually be taken.
 */
return new class extends Migration
{
    /** [id, display name, image file] — ids continue the existing sequence. */
    private const GATEWAYS = [
        [21, 'Cashfree',  'cashfree.png'],
        [22, 'Foloosi',   'foloosi.png'],
        [23, 'Instamojo', 'instamojo.png'],
        [24, 'MTN MoMo',  'mtnmomo.png'],
        [25, 'PayMongo',  'paymongo.png'],
        [26, 'PhonePe',   'phonepe.png'],
    ];

    public function up(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('payment_settings', 'target_environment')) {
                $table->string('target_environment', 500)->nullable()->after('accesstoken');
            }
            if (! Schema::hasColumn('payment_settings', 'expiry_time_seconds')) {
                $table->string('expiry_time_seconds', 50)->nullable()->after('target_environment');
            }
            if (! Schema::hasColumn('payment_settings', 'salt_key')) {
                $table->string('salt_key', 500)->nullable()->after('expiry_time_seconds');
            }
            if (! Schema::hasColumn('payment_settings', 'flow_id')) {
                $table->string('flow_id', 500)->nullable()->after('salt_key');
            }
        });

        $now = date('Y-m-d H:i:s');

        foreach (self::GATEWAYS as [$id, $name, $image]) {
            // The app-facing list of methods.
            if (! DB::table('payment_method')->where('id', $id)->exists()) {
                DB::table('payment_method')->insert([
                    'id' => $id,
                    'libelle' => $name,
                    'image' => $image,
                    'statut' => 'yes',
                    'creer' => $now,
                    'modifier' => $now,
                ]);
            }

            // The credentials the admin panel edits. Off until configured.
            if (! DB::table('payment_settings')->where('id_payment_method', $id)->exists()) {
                DB::table('payment_settings')->insert([
                    'id_payment_method' => $id,
                    'isEnabled' => 'false',
                    'isSandboxEnabled' => 'true',
                    'creer' => $now,
                    'modifier' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $ids = array_column(self::GATEWAYS, 0);

        DB::table('payment_settings')->whereIn('id_payment_method', $ids)->delete();
        DB::table('payment_method')->whereIn('id', $ids)->delete();

        Schema::table('payment_settings', function (Blueprint $table) {
            foreach (['target_environment', 'expiry_time_seconds', 'salt_key', 'flow_id'] as $column) {
                if (Schema::hasColumn('payment_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
