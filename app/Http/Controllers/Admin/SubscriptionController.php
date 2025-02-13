<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Package;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Stripe\Customer;

class SubscriptionController extends Controller
{
    // Get all subscriptions with user and package information
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'package'])->get();

        if ($subscriptions->isEmpty()) {
            return response()->json(['message' => 'No subscriptions found'], 404);
        }

        return response()->json($subscriptions, 200);
    }

    // Create a new subscription and handle Stripe payment
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'paid_amount' => 'required|numeric',
            'subscription_expiry_date' => 'required|date',  // Ensure valid date format
        ]);

        // Get the authenticated user's ID
        $user_id = Auth::id();

        // Check if the package exists
        $package = Package::find($request->package_id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        // Stripe configuration
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Retrieve the authenticated user's Stripe customer ID
            $user = Auth::user();
            $user_id = $user->id;
            $stripeCustomerId = $user->stripe_customer_id;  // Assuming this is saved in the user model

            if (!$stripeCustomerId) {
                $stripeCustomer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);

                $stripeCustomerId = $stripeCustomer->id;
                $user_id->stripe_customer_id = $stripeCustomerId;
                $user_id->save();
            }

            $stripeSubscription = StripeSubscription::create([
                'customer' => $stripeCustomerId,
                'items' => [
                    ['price' => $package->stripe_price_id],  // Assuming you store the Stripe price ID for the package
                ],
                'expand' => ['latest_invoice.payment_intent'], // Optional: to get the payment status immediately
            ]);

            // Create subscription in the database
            $subscription = Subscription::create([
                'user_id' => $user_id,  // Use authenticated user's ID
                'package_id' => $request->package_id,
                'paid_amount' => $request->paid_amount,
                'date_of_subscription' => now(),
                'subscription_expiry_date' => $request->subscription_expiry_date,
                'status' => 'active',
                'stripe_subscription_id' => $stripeSubscription->id,  // Store Stripe subscription ID
                'stripe_price_id' => $package->stripe_price_id,  // Assuming price ID is stored
            ]);

            return response()->json($subscription, 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
        }
    }

    // Show a specific subscription
    public function show($id)
    {
        $subscription = Subscription::with(['user', 'package'])->find($id);

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        return response()->json($subscription, 200);
    }

    // Update an existing subscription
    public function update(Request $request, $id)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'paid_amount' => 'required|numeric',
            'subscription_expiry_date' => 'required|date',  // Ensure valid date format
        ]);

        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        // Check if the package exists
        $package = Package::find($request->package_id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $subscription->update([
            'package_id' => $request->package_id,
            'paid_amount' => $request->paid_amount,
            'date_of_subscription' => now(),
            'subscription_expiry_date' => $request->subscription_expiry_date,
            'status' => 'active',
        ]);

        return response()->json($subscription, 200);
    }

    // Cancel a subscription on Stripe and update the database
    public function cancelSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id'
        ]);

        $subscription = Subscription::find($request->subscription_id);

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        // Stripe configuration
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Retrieve the Stripe subscription
            $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);

            // Cancel the subscription on Stripe
            $stripeSubscription->cancel();

            // Update the subscription status in the database
            $subscription->update([
                'status' => 'inactive', // Set the status to inactive
                'subscription_expiry_date' => now(), // Update expiry date to now or based on current period end
            ]);

            return response()->json(['message' => 'Subscription cancelled successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
        }
    }

    // Delete a subscription (in case you want to remove it completely)
    public function destroy($id)
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        // Cancel the subscription on Stripe if not already done
        if ($subscription->status === 'active') {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            try {
                $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);
                $stripeSubscription->cancel();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
            }
        }

        $subscription->delete();

        return response()->json(['message' => 'Subscription deleted successfully'], 200);
    }

    // Handle Stripe Webhook for automatic subscription renewal and failed payments
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');  // Set this in your .env file

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            switch ($event->type) {
                case 'invoice.payment_succeeded':
                    $invoice = $event->data->object; // Contains a \Stripe\Invoice
                    // Handle successful payment and update subscription status
                    $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
                    if ($subscription) {
                        $subscription->update([
                            'status' => 'active',
                            'subscription_expiry_date' => now()->addMonth() // Example for monthly renewal
                        ]);
                    }
                    break;

                case 'invoice.payment_failed':
                    break;

           }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
