<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                // Handle successful payment
                Log::info('Payment succeeded for invoice: ' . $invoice->id);
                break;

            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                // Handle failed payment
                Log::warning('Payment failed for invoice: ' . $invoice->id);
                break;

            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                // Handle subscription cancellation
                Log::info('Subscription canceled: ' . $subscription->id);
                break;

            default:
                Log::info('Received unhandled event type: ' . $event->type);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
