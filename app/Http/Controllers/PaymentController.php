<?php

namespace App\Http\Controllers;

use paytm\paytmchecksum\PaytmChecksum;
use Illuminate\Http\Request;
use Braintree;

class PaymentController extends Controller
{
   
    public function getPaypalClienttoken(Request $request){

        
        $input=$request->all();
        $gateway = new Braintree\Gateway([
            'environment' => $input['environment'],
            'merchantId' => $input['merchant_id'],
            'publicKey' => $input['public_key'],
            'privateKey' => $input['private_key']
        ]);

        $clientToken = $gateway->clientToken()->generate();

        return response()->json(array('success'=>true,'data'=>$clientToken));  
    }

    public function createBraintreePayment(Request $request){

        $input=$request->all();
        $nonceFromTheClient=$input['nonceFromTheClient'];
        $amount=$input['amount'];
        $deviceDataFromTheClient=$input['deviceDataFromTheClient'];
        $gateway = new Braintree\Gateway([
            'environment' => $input['environment'],
            'merchantId' => $input['merchant_id'],
            'publicKey' => $input['public_key'],
            'privateKey' => $input['private_key']
        ]);

        $result = $gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $nonceFromTheClient,
            'deviceData' => $deviceDataFromTheClient,
            'options' => [
            'submitForSettlement' => True
            ]
        ]);

        return response()->json(array('success'=>true,'data'=>$result));
    }

    public function createStripePaymentIntent(Request $request)
    {
        
        $input=$request->all();
        $stripeSecret=$input['stripesecret'];
        $amount=$input['amount'];
        $currency=$input['currency'];
        $stripe = new \Stripe\StripeClient($stripeSecret);


        $payment=$stripe->paymentIntents->create(
          [
            'currency' => 'usd',
            'amount' => $amount*1000,
            'payment_method_types' => ['card'],
          ]
        );

        return response()->json(array('success'=>true,'data'=>$payment));

    }
}
