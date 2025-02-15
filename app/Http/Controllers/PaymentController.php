<?php

// app/Http/Controllers/SkipTraceController.php
namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Payment;
use App\Models\admin\SkipTrace;
use Illuminate\Http\Request;


class PaymentController extends Controller
{


    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            $amount = $request->amount;
            
            // Create PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Amount in cents
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paymentSuccess(Request $request)
    {
        // Handle successful payment
        // Update your database, send confirmation emails, etc.
        return response()->json(['status' => 'success']);
    }




    
    public function processPayment(Request $request)
    {
        // Validate the request
        $request->validate([
            'skiptrace_id' => 'required|exists:skiptrace,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required',
        ]);

        // Find the skiptrace record
        $skiptrace = SkipTrace::findOrFail($request->skiptrace_id);

        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create a payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, // Amount in cents
                'currency' => 'usd', // Adjust if necessary
                'payment_method' => $request->payment_method,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            // Check if payment succeeded
            if ($paymentIntent->status == 'succeeded') {
                // Save the payment in the 'payments' table
                $payment = Payment::create([
                    'user_id' => $skiptrace->user_id,
                    'skiptrace_id' => $skiptrace->id,
                    'amount' => $request->amount,
                    'payment_status' => 'succeeded',
                    'transaction_id' => $paymentIntent->id,
                ]);

                // Link the payment to the skiptrace record
                $skiptrace->payment_id = $payment->id;
                $skiptrace->is_paid = true;
                $skiptrace->save();

                return response()->json(['message' => 'Payment successful, details saved!'], 200);
            } else {
                return response()->json(['message' => 'Payment failed'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}


