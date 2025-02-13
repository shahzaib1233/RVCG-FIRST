<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Package;
use App\Models\admin\PackageItem;
use App\Models\User; // Assuming you have User model to subscribe
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Stripe\Customer;
use Stripe\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class PackagesController extends Controller
{
    // Get all packages with their items
    public function index()
    {
        $packages = Package::with('items')->get();

        if ($packages->isEmpty()) {
            return response()->json(['message' => 'No packages found'], 404);
        }

        return response()->json($packages, 200);
    }

    // Show a specific package by ID
    public function show($id)
    {
        $package = Package::with('items')->find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        return response()->json($package, 200);
    }

    // Create a new package with items and sync with Stripe
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packages,name',
            'price' => 'required|numeric',
            'duration' => 'required|integer',
            'items' => 'required|array',
            'items.*' => 'required|string|max:255'
        ]);

        // Create the package in the database
        $package = Package::create($validated);

        // Create Product and Price in Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Step 1: Create Product in Stripe
            $stripeProduct = Product::create([
                'name' => $validated['name'],
                'description' => 'Description for ' . $validated['name'],
            ]);

            // Step 2: Create Price for the Product in Stripe
            $stripePrice = Price::create([
                'unit_amount' => $validated['price'] * 100,  // Price in cents
                'currency' => 'usd',
                'recurring' => ['interval' => 'month'],  // Assuming monthly, can make this dynamic
                'product' => $stripeProduct->id,
            ]);

            // Step 3: Save Stripe IDs in your database
            $package->stripe_product_id = $stripeProduct->id;
            $package->stripe_price_id = $stripePrice->id;
            $package->save();

            // Create package items
            foreach ($validated['items'] as $item) {
                $package->items()->create(['item_name' => $item]);
            }

            return response()->json($package->load('items'), 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
        }
    }

    // Update an existing package and sync with Stripe
    public function update(Request $request, $id)
{
    $package = Package::find($id);

    if (!$package) {
        return response()->json(['message' => 'Package not found'], 404);
    }

    $packageId = $package->id;
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:packages,name,' . $packageId,
        'price' => 'required|numeric',
        'duration' => 'required|integer',
        'items' => 'required|array',
        'items.*' => 'required|string|max:255'
    ]);

    // Update the package details
    $package->update($validated);

    // Sync items
    $existingItems = $package->items->keyBy('item_name'); // Group existing items by name for quick lookup
    $newItems = collect($validated['items']);

    // Delete items that are no longer in the request
    $existingItems
        ->whereNotIn('item_name', $newItems)
        ->each(function ($item) {
            $item->delete();
        });

    // Add or update items
    foreach ($newItems as $itemName) {
        $package->items()->updateOrCreate(
            ['item_name' => $itemName], // Check if the item exists by name
            ['item_name' => $itemName] // If not, create a new one
        );
    }

    // Sync with Stripe
    try {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Step 1: Retrieve existing Stripe product
        $stripeProduct = Product::retrieve($package->stripe_product_id);
        $stripeProduct->name = $validated['name']; // Update name
        $stripeProduct->save();

        // Step 2: Create a new Stripe Price for the updated package price
        $newStripePrice = Price::create([
            'unit_amount' => $validated['price'] * 100, // Price in cents
            'currency' => 'usd', // Change currency if necessary
            'product' => $package->stripe_product_id, // Link to the existing product
        ]);

        // Save the new Stripe Price ID to the package
        $package->stripe_price_id = $newStripePrice->id;
        $package->save();

    } catch (\Exception $e) {
        return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
    }

    return response()->json($package->load('items'), 200);
}
    // Delete a specific package along with its items
    
    public function destroy($id)
{
    // Set the Stripe API key
    Stripe::setApiKey(env('STRIPE_SECRET'));

    $package = Package::find($id);

    if (!$package) {
        return response()->json(['message' => 'Package not found'], 404);
    }

    try {
        // Step 1: Cancel any active subscriptions tied to this package's price
        $subscriptions = Subscription::all([
            'price' => $package->stripe_price_id,
            'status' => 'active',
        ]);

        foreach ($subscriptions->data as $subscription) {
            // Cancel the subscription immediately
            $subscription->cancel();
        }

        // Step 2: Deactivate the product and price in Stripe (no longer available for new subscriptions)
        $stripeProduct = Product::retrieve($package->stripe_product_id);
        $stripeProduct->active = false;  // Set product as inactive
        $stripeProduct->save();

        $stripePrice = Price::retrieve($package->stripe_price_id);
        $stripePrice->active = false;  // Set price as inactive
        $stripePrice->save();

        // Step 3: Mark the package as inactive in your database (so no further usage)
        $package->update(['status' => 'inactive']);  // Or set a custom status field for your needs

        // Step 4: Delete associated items
        $packageItems = PackageItem::where('package_id', $package->id)->get();
        $packageItems->each(function ($item) {
            $item->delete();
        });

        // Step 5: Finally, delete the package from your database
        $package->delete();

        return response()->json(['message' => 'Package and its associated data deactivated successfully'], 200);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Error while deactivating package: ' . $e->getMessage()], 500);
    }
}   
    // Subscribe a user to a package and create a Stripe subscription
    public function subscribeToPackage(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id'
        ]);
    
        // Get the authenticated user using auth()
        $user = auth::user();
    
        // Retrieve the package based on the validated package_id
        $package = Package::find($validated['package_id']);
    
        // Step 1: Create Stripe customer if it doesn't exist
        Stripe::setApiKey(env('STRIPE_SECRET'));
    
        try {
            // Check if user already has a Stripe customer ID, if not create a new one
            $stripeCustomer = $user->stripe_customer_id
                ? \Stripe\Customer::retrieve($user->stripe_customer_id)
                : \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
    
            // Step 2: Create subscription
            $stripeSubscription = \Stripe\Subscription::create([
                'customer' => $stripeCustomer->id,
                'items' => [
                    ['price' => $package->stripe_price_id], // Use Stripe price ID from the package
                ],
            ]);
    
            // Step 3: Insert subscription info directly into the subscriptions table
            Subscription::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'paid_amount' => $package->price,  // Assuming price is in the package
                'date_of_subscription' => now(),
                'subscription_expiry_date' => now()->addMonths(1), // Update based on your package interval
                'status' => 'active',
            ]);
    
            return response()->json(['message' => 'Subscription successful!'], 201);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
        }
    }
    
    
    
    
}
