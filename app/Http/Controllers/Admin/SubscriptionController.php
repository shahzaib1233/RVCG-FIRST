<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Package;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    // Create a new subscription
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

        $subscription = Subscription::create([
            'user_id' => $user_id,  // Use authenticated user's ID
            'package_id' => $request->package_id,
            'paid_amount' => $request->paid_amount,
            'date_of_subscription' => now(),
            'subscription_expiry_date' => $request->subscription_expiry_date,
            'status' => 'active',
        ]);

        return response()->json($subscription, 201);
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

    // Delete a subscription
    public function destroy($id)
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $subscription->delete();

        return response()->json(['message' => 'Subscription deleted successfully'], 200);
    }
}
