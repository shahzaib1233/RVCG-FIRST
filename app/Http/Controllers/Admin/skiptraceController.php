<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Listing;
use App\Models\admin\Skiptrace;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;

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


    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }


//     public function store(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'listing_id' => 'required|array', 
//         'listing_id.*' => 'exists:listings,id', 
//         'amount' => 'required|numeric|min:1', // Use amount from request
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['errors' => $validator->errors()], 422);
//     } 

//     $userId = Auth::id();
//     $amount = $request->amount * 100; // Convert to cents for Stripe

//     // Stripe Payment Logic
//     \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

//     try {
//         // Create PaymentIntent
//         $paymentIntent = \Stripe\PaymentIntent::create([
//             'amount' => $amount,  
//             'currency' => 'usd',
//             'automatic_payment_methods' => [
//                 'enabled' => true,
//                 'allow_redirects' => 'never',
//             ],
//         ]);

//         // Confirm the PaymentIntent
//         // $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntent->id);
//         // $paymentIntent->confirm();

//         // Check if payment was successful
//         if ($paymentIntent->status === 'succeeded') {
//             // Payment successful, store in payments table
//             $payment = Payment::create([
//                 'user_id' => $userId,
//                 'amount' => $request->amount, // Store in dollars as received
//                 'payment_status' => true,
//                 'transaction_id' => $paymentIntent->client_secret,
//             ]);

//             // Insert into skiptrace table
//             foreach ($request->listing_id as $listingId) {
//                 $listing = Listing::find($listingId);

//                 Skiptrace::create([
//                     'listing_id' => $listing->id,
//                     'user_id' => $userId,
//                     'owner_name' => $listing->owner_name,
//                     'owner_contact' => $listing->owner_contact,
//                     'owner_email' => $listing->owner_email,
//                     'is_paid' => true,
//                     'payment_id' => $payment->id,
//                 ]);
//             }

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Payment successful and skiptrace records created.',
//             ]);
//         } else {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Payment failed. Please try again.',
//             ]);
//         }
//     } catch (\Stripe\Exception\ApiErrorException $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }

    
    
    
    
    public function createPaymentIntent(Request $request)
{
    $validator = Validator::make($request->all(), [
        'listing_id' => 'required|array', 
        'listing_id.*' => 'exists:listings,id', 
        'amount' => 'required|numeric|min:1', 
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $amount = $request->amount * 100; // Convert to cents for Stripe

    // Stripe Payment Logic
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    try {
        // Create PaymentIntent
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount,  
            'currency' => 'usd',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        // Return client_secret to the frontend
        return response()->json([
            'status' => 'success',
            'client_secret' => $paymentIntent->client_secret,
            'payment_intent_id' => $paymentIntent->id,
            'message' => 'Payment initiated. Confirm the payment on the frontend.'
        ]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}





public function storeTransaction(Request $request)
{
    $validator = Validator::make($request->all(), [
        'payment_intent_id' => 'required|string',
        'listing_id' => 'required|array', 
        'listing_id.*' => 'exists:listings,id', 
        'amount' => 'required|numeric|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
    $listing_id = $request->listing_id;
     

    $userId = Auth::id();

    // Stripe Payment Logic
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    try {
        // Retrieve PaymentIntent
        $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);

        // Check if payment was successful
        if ($paymentIntent->status === 'succeeded') {
            // Payment successful, store in payments table
            $payment = Payment::create([
                'user_id' => $userId,
                'amount' => $request->amount, 
                'payment_status' => true,
                'transaction_id' => $paymentIntent->id,
            ]);

            // Insert into skiptrace table
            foreach ($request->listing_id as $listingId) {
                $listing = Listing::find($listingId);

                Skiptrace::create([
                    'listing_id' => $listing->id,
                    'user_id' => $userId,
                    'owner_name' => $listing->owner_name,
                    'owner_contact' => $listing->owner_contact,
                    'owner_email' => $listing->owner_email,
                    'is_paid' => true,
                    'payment_id' => $payment->id,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment successful and skiptrace records created.',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not completed. Please try again.',
            ]);
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

    public function paymentSuccess(Request $request)
    {
        // Handle successful payment
        // Update your database, send confirmation emails, etc.
        return response()->json(['status' => 'success']);
    }

    
}
