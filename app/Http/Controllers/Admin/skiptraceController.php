<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Listing;
use App\Models\admin\Skiptrace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class skiptraceController extends Controller
{
    public function index()
    {
        $skiptraces = Skiptrace::where('user_id', Auth::id())->get();

        return response()->json(['data' => $skiptraces], 200);
    }
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'listing_id'   => 'required|exists:listings,id',  // Validate listing ID
    //         'is_paid'      => 'boolean',  // Ensure is_paid is boolean
    //         'payment_id'   => 'required_if:is_paid,true',  // Ensure payment_id is provided if is_paid is true
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     } $userId = Auth::id();

    //     // Get listing details using the provided listing_id
    //     $listing = Listing::find($request->listing_id);
    
    //     // Ensure listing exists
    //     if (!$listing) {
    //         return response()->json(['message' => 'Listing not found'], 404);
    //     }
    
    //     // Prepare skiptrace data
    //     $skiptraceData = [
    //         'listing_id'   => $listing->id,
    //         'user_id'      => $userId,  // Get user_id from Auth
    //         'owner_name'   => $listing->owner_full_name,
    //         'owner_contact'=> $listing->owner_contact_number,
    //         'owner_email'  => $listing->owner_email_address,
    //         'is_paid'      => $request->is_paid,  // Use the 'is_paid' from the request
    //         // 'payment_id'  => $request->payment_id,  // Use the 'payment_id' from the request
    //     ];
    
    //     // Create the skiptrace record
    //     $skiptrace = Skiptrace::create($skiptraceData);
    
    //     return response()->json(['message' => 'Skiptrace created successfully', 'data' => $skiptrace], 201);
    //   }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listing_id'   => 'required|exists:listings,id',  // Validate listing ID
            'is_paid'      => 'boolean',  // Ensure is_paid is boolean
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } 
    
        $userId = Auth::id();
    
        // Get listing details using the provided listing_id
        $listing = Listing::find($request->listing_id);
    
        // Ensure listing exists
        if (!$listing) {
            return response()->json(['message' => 'Listing not found'], 404);
        }
    
        // Stripe Payment Logic
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET')); // Set your Stripe Secret Key in .env
    
        try {
            // Create a payment intent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => 10,  // $0.10 in cents
                'currency' => 'usd',
                'payment_method' => $request->payment_id, // Payment method ID from frontend
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);
    
            // Check if the payment was successful
            if ($paymentIntent->status == 'succeeded') {
                // Prepare skiptrace data
                $skiptraceData = [
                    'listing_id'   => $listing->id,
                    'user_id'      => $userId,  // Get user_id from Auth
                    'owner_name'   => $listing->owner_full_name,
                    'owner_contact'=> $listing->owner_contact_number,
                    'owner_email'  => $listing->owner_email_address,
                    'is_paid'      => true,  // Payment was successful
                    'payment_id'   => $paymentIntent->id,  // Store Stripe payment ID
                ];
    
                // Create the skiptrace record
                $skiptrace = Skiptrace::create($skiptraceData);
    
                return response()->json(['message' => 'Skiptrace created successfully', 'data' => $skiptrace], 201);
            } else {
                return response()->json(['message' => 'Payment failed.'], 400);
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function update(Request $request, $id)
    {
        $skiptrace = Skiptrace::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'listing_id'   => 'exists:listings,id',
            'user_id'      => 'exists:users,id',
            'owner_name'   => 'string|max:255',
            'owner_contact'=> 'string|max:255',
            'owner_email'  => 'email|max:255',
            'is_paid'      => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $skiptrace->update($request->all());

        return response()->json(['message' => 'Skiptrace updated successfully', 'data' => $skiptrace], 200);
    }

    
    public function destroy($id)
    {
        $skiptrace = Skiptrace::findOrFail($id);
        $skiptrace->delete();

        return response()->json(['message' => 'Skiptrace deleted successfully'], 200);
    }
}
