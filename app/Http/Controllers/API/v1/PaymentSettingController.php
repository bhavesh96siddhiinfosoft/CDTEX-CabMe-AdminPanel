<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PaymentSettingController extends Controller
{
    public function __construct()
    {
        $this->limit = 20;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getData(Request $request)
    {
        $sql = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.secret_key',
                'payment_settings.public_key',
                'payment_settings.encryption_key',
                'payment_settings.isEnabled',
                'payment_settings.isSandboxEnabled',
                'payment_settings.id_payment_method',
                'payment_method.libelle',
                'payment_method.image'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'FlutterWave')
            ->get();
        foreach ($sql as $row) {
            $row->id = (string) $row->id;
        }
        $sql_cash = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.isEnabled',
                'payment_method.libelle',
                'payment_method.image',
                'payment_settings.id_payment_method'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'Cash')
            ->get();
        foreach ($sql_cash as $row_cash) {
            $row_cash->id = (string) $row_cash->id;
        }
        $sql_payfast = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.merchant_Id',
                'payment_settings.merchant_key',
                'payment_settings.cancel_url',
                'payment_settings.notify_url',
                'payment_settings.return_url',
                'payment_settings.isEnabled',
                'payment_settings.isSandboxEnabled',
                'payment_settings.id_payment_method',
                'payment_method.libelle',
                'payment_method.image'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'PayFast')
            ->get();
        foreach ($sql_payfast as $row_payfast) {
            $row_payfast->id = (string) $row_payfast->id;
        }
        $sql_wallet = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.isEnabled',
                'payment_method.libelle',
                'payment_method.image',
                'payment_settings.id_payment_method'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'Wallet')
            ->get();
        foreach ($sql_wallet as $row_wallet) {
            $row_wallet->id = (string) $row_wallet->id;
        }
        $sql_strip = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.key',
                'payment_settings.clientpublishableKey',
                'payment_settings.secret_key',
                'payment_settings.isEnabled',
                'payment_settings.isSandboxEnabled',
                'payment_settings.id_payment_method',
                'payment_method.libelle',
                'payment_method.image'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'Stripe')
            ->get();
        foreach ($sql_strip as $row_strip) {
            $row_strip->id = (string) $row_strip->id;
        }
        $sql_paystack = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.secret_key',
                'payment_settings.public_key',
                'payment_settings.callback_url',
                'payment_settings.isEnabled',
                'payment_settings.isSandboxEnabled',
                'payment_settings.id_payment_method',
                'payment_method.libelle',
                'payment_method.image'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'PayStack')
            ->get();
        foreach ($sql_paystack as $row_paystack) {
            $row_paystack->id = (string) $row_paystack->id;
        }
        $sql_rezorpay = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.key',
                'payment_settings.secret_key',
                'payment_settings.isEnabled',
                'payment_settings.isSandboxEnabled',
                'payment_settings.id_payment_method',
                'payment_method.libelle',
                'payment_method.image'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'Razorpay')
            ->get();
        foreach ($sql_rezorpay as $row_rezorpay) {
            $row_rezorpay->id = (string) $row_rezorpay->id;
        }
        $sql_paypal = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.app_id',
                'payment_settings.secret_key',
                'payment_settings.merchant_Id',
                'payment_settings.private_key',
                'payment_settings.public_key',
                'payment_settings.tokenization_key',
                'payment_settings.isEnabled',
                'payment_settings.isLive',
                'payment_settings.id_payment_method',
                'payment_settings.username',
                'payment_settings.password',
                'payment_method.libelle',
                'payment_method.image'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'PayPal')
            ->get();
        foreach ($sql_paypal as $row_paypal) {
            $row_paypal->id = (string) $row_paypal->id;
        }
        $sql_tax = DB::table('tax')
            ->select(
                'id',
                'libelle',
                'type',
                'value'
            )
            ->where('statut', '=', 'yes')
            ->get();
        foreach ($sql_tax as $tax) {
            $tax->name = $tax->libelle;
            $tax->tax_type = $tax->type;
            $tax->tax_amount = $tax->value;
            $tax->id = (string) $tax->id;
            unset($tax->libelle);
            unset($tax->type);
            unset($tax->value);
        }
        $sql_mercadopago = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.public_key',
                'payment_settings.accesstoken',
                'payment_settings.isEnabled',
                'payment_settings.isSandboxEnabled',
                'payment_settings.id_payment_method',
                'payment_method.libelle',
                'payment_method.image'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'Mercadopago')
            ->get();
        foreach ($sql_mercadopago as $row_mercadopago) {
            $row_mercadopago->id = (string) $row_mercadopago->id;
        }
        $sql_midtrans = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.key',
                'payment_settings.isEnabled',
                'payment_settings.isSandboxEnabled',
                'payment_settings.id_payment_method',
                'payment_method.libelle',
                'payment_method.image'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'Midtrans')
            ->get();
        foreach ($sql_midtrans as $row_midtrans) {
            $row_midtrans->id = (string) $row_midtrans->id;
        }
        $sql_orangePay = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.key',
                'payment_settings.clientpublishableKey',
                'payment_settings.secret_key',
                'payment_settings.merchant_key',
                'payment_settings.cancel_url',
                'payment_settings.notify_url',
                'payment_settings.return_url',
                'payment_settings.isEnabled',
                'payment_settings.isSandboxEnabled',
                'payment_settings.id_payment_method',
                'payment_method.libelle',
                'payment_method.image'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'OrangePay')
            ->get();
        foreach ($sql_orangePay as $row_orangePay) {
            $row_orangePay->id = (string) $row_orangePay->id;
        }
        $sql_xendit = DB::table('payment_settings')
            ->crossJoin('payment_method')
            ->select(
                'payment_settings.id',
                'payment_settings.key',
                'payment_settings.isEnabled',
                'payment_settings.isSandboxEnabled',
                'payment_settings.id_payment_method',
                'payment_method.libelle',
                'payment_method.image'
            )
            ->where('payment_settings.id_payment_method', '=', DB::raw('payment_method.id'))
            ->where('payment_method.libelle', '=', 'Xendit')
            ->get();
        foreach ($sql_xendit as $row_xendit) {
            $row_xendit->id = (string) $row_xendit->id;
        }
        // The selects carry a bare filename; hand clients a usable url so the
        // web panel and the apps can render the real gateway logo. Each $row_*
        // is a single row left over from the loops above, not a collection.
        foreach ([$row_strip, $row_cash, $row_payfast, $row_wallet, $row_paystack, $row,
                  $row_rezorpay, $row_mercadopago, $row_paypal, $row_midtrans,
                  $row_orangePay, $row_xendit] as $logoRow) {
            if (!is_object($logoRow) || !isset($logoRow->image)) {
                continue;
            }
            $logoRow->image = (!empty($logoRow->image) && file_exists(public_path('assets/images/payment_method/' . $logoRow->image)))
                ? asset('assets/images/payment_method/' . $logoRow->image)
                : null;
        }


        // Newer gateways (added to payment_settings after the original API):
        // exposed the same way so the web panel and apps share one source.
        $newBuckets = [
            "Cashfree" => ["payment_settings.clientpublishableKey", "payment_settings.secret_key"],
            "PhonePe" => ["payment_settings.clientpublishableKey", "payment_settings.secret_key", "payment_settings.merchant_Id", "payment_settings.salt_key", "payment_settings.flow_id"],
            "MTN MoMo" => ["payment_settings.key", "payment_settings.secret_key", "payment_settings.target_environment", "payment_settings.expiry_time_seconds", "payment_settings.callback_url"],
            "Foloosi" => ["payment_settings.merchant_key"],
            "Instamojo" => ["payment_settings.clientpublishableKey", "payment_settings.secret_key"],
            "PayMongo" => ["payment_settings.secret_key"],
        ];
        $newRows = [];
        foreach ($newBuckets as $bucketLabel => $fields) {
            $select = array_merge($fields, [
                "payment_settings.id", "payment_settings.isEnabled", "payment_settings.isSandboxEnabled",
                "payment_settings.id_payment_method", "payment_method.libelle", "payment_method.image",
            ]);
            $bucketRows = DB::table("payment_settings")
                ->crossJoin("payment_method")
                ->select($select)
                ->where("payment_settings.id_payment_method", "=", DB::raw("payment_method.id"))
                ->where("payment_method.libelle", "=", $bucketLabel)
                ->get();
            $last = null;
            foreach ($bucketRows as $bucketRow) {
                $bucketRow->id = (string) $bucketRow->id;
                $bucketRow->image = (!empty($bucketRow->image) && file_exists(public_path("assets/images/payment_method/" . $bucketRow->image)))
                    ? asset("assets/images/payment_method/" . $bucketRow->image)
                    : null;
                $last = $bucketRow;
            }
            $newRows[$bucketLabel] = $last;
        }

        if ($row_cash) {
            $response['success'] = 'success';
            $response['error'] = null;
            $response['message'] = 'successfully';
            $response['Strip'] = $row_strip;
            $response['Cash'] = $row_cash;
            $response['PayFast'] = $row_payfast;
            $response['My Wallet'] = $row_wallet;
            $response['PayStack'] = $row_paystack;
            $response['FlutterWave'] = $row;
            $response['Razorpay'] = $row_rezorpay;
            $response['Mercadopago'] = $row_mercadopago;
            $response['PayPal'] = $row_paypal;
            $response['Midtrans'] = $row_midtrans;
            $response['OrangePay'] = $row_orangePay;
            $response['Xendit'] = $row_xendit;
            foreach ($newRows as $bucketLabel => $bucketRow) {
                if ($bucketRow) {
                    $response[$bucketLabel] = $bucketRow;
                }
            }
        }
        return response()->json($response);
    }
}
