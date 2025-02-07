<?php

namespace App\Http\Controllers\admin;

use App\Models\admin\Offer;
use App\Models\admin\OfferHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    // Get all offers
   public function index()
{
    if (Auth::check() && Auth::user()->role === 'admin') {
        $offers = Offer::with(['listing:id,title', 'user:id,name'])->get();
    } else {
        $offers = Offer::with(['listing:id,title', 'user:id,name'])
                       ->where('user_id', Auth::id())
                       ->get();
    }

    if ($offers->isEmpty()) {
        return response()->json(['message' => 'No offers found'], 404);
    }

    return response()->json($offers, 200);
}


    // Show a specific offer
    public function show($id)
    {
        $offer = Offer::with(['listing:id,title', 'user:id,name'])->find($id);

        if (!$offer) {
            return response()->json(['message' => 'Offer not found'], 404);
        }

        return response()->json($offer, 200);
    }

    public function show_offer_listing_wise($id)
    {
        $offer = Offer::with(['listing:id,title', 'user:id,name'])->where('listing_id',$id)->get();

        if (!$offer) {
            return response()->json(['message' => 'Offer not found'], 404);
        }

        return response()->json($offer, 200);
    }

    // Create a new offer
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'user_id' => 'required|exists:users,id',
            'offer_price' => 'required|numeric',
            'offer_date' => 'nullable|date',
            'status' => 'nullable|string',
            'message' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'negotiation_comments' => 'nullable|string',
            'accepted_price' => 'nullable|numeric',
            'closing_date' => 'nullable|date',
        ]);
        

        $validatedData['expiry_date'] = Carbon::now()->addMonth(); // Set expiry date to 1 month from now

        $offer = Offer::create($validatedData);

        if ($offer) {
            return response()->json($offer, 201);
        } else {
            return response()->json(['message' => 'Failed to create offer'], 500);
        }
    }

    // Update an existing offer
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'user_id' => 'required|exists:users,id',
            'offer_price' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $offer = Offer::find($id);

        if (!$offer) {
            return response()->json(['message' => 'Offer not found'], 404);
        }

        $updated = $offer->update($validatedData);

        if ($updated) {
            return response()->json($offer, 200);
        } else {
            return response()->json(['message' => 'Failed to update offer'], 500);
        }
    }

    // Delete an offer
    public function destroy($id)
    {
        $offer = Offer::find($id);

        if (!$offer) {
            return response()->json(['message' => 'Offer not found'], 404);
        }

        $deleted = $offer->delete();

        if ($deleted) {
            return response()->json(['message' => 'Offer deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete offer'], 500);
        }
    }


public function offerConversionRate($listing_id)
  {
    $totalOffers = Offer::where('listing_id', $listing_id)->count();
    if ($totalOffers == 0) {
        return response()->json([
            'error' => 'No offer found for the required property',
            'conversion_rate' => 0
        ], 404);
    }
    

    $acceptedOffers = Offer::where('listing_id', $listing_id)
                            ->where('status', 'accepted') 
                            ->count();

    if ($totalOffers == 0) {
        return response()->json(['conversion_rate' => 0], 200);
    }

    $conversionRate = ($acceptedOffers / $totalOffers) * 100;

    return response()->json(['conversion_rate' => $conversionRate], 200);
}







//offer history 
public function createOfferHistory(Request $request, $offer_id)
{
    $validatedData = $request->validate([
        'user_id' => 'required|exists:users,id',
        'listing_owner_id' => 'required|exists:users,id',
        'negotiation_comments' => 'required|string',
        'negotiated_price' => 'nullable|numeric',
        'status' => 'required|string|in:pending,accepted,rejected',
    ]);

    $validatedData['offer_id'] = $offer_id; // Set the offer_id from URL
    $validatedData['negotiated_price'] = $validatedData['negotiated_price'] ?? 0;

    $offerHistory = OfferHistory::create($validatedData);

    if ($offerHistory) {
        return response()->json([
            'message' => 'Offer negotiation history created successfully',
            'offer_history' => $offerHistory
        ], 201);
    } else {
        return response()->json(['message' => 'Failed to create offer negotiation history'], 500);
    }
}


public function showOfferHistory($offer_id)
{
    $offerHistory = OfferHistory::with(['user', 'listingOwner'])->where('offer_id', $offer_id)->get();

    if ($offerHistory->isEmpty()) {
        return response()->json(['message' => 'No negotiation history found'], 404);
    }

    return response()->json($offerHistory, 200);
}



}
