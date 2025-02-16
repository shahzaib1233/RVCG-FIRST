<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\admin\Listing;
use App\Models\admin\PropertyKpi;
use App\Models\admin\SavedProperty;
use App\Models\admin\Skiptrace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ListingsController extends Controller
{
    //
//     public function index()
// {
//     $userId = Auth::id();

//     // Initialize an array to store prioritized IDs
//     $prioritizedIds = [];

//     // Check if the user is logged in
//     if ($userId) {
//         // Get IDs of listings saved as favorites by the user
//         $favoriteListingIds = SavedProperty::where('user_id', $userId)
//             ->where('is_favourite', true)
//             ->pluck('listing_id')
//             ->toArray();

//         // Get IDs of listings the user frequently viewed (more than 2 times)
//         $frequentViewedIds = PropertyKpi::where('users_id', $userId)
//             ->where('views', '>', 2)
//             ->orderBy('views', 'desc')
//             ->pluck('listing_id')
//             ->toArray();

//         // Merge and prioritize listings (Favorites first, then Frequently Viewed)
//         $prioritizedIds = array_unique(array_merge($favoriteListingIds, $frequentViewedIds));
//     }

//     // Get all listings
//     $listings = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features'])
//         ->orderBy('id', 'desc')
//         ->get();

//     // Prioritize the listings
//     $listings = $listings->sortBy(function ($listing) use ($prioritizedIds) {
//         // Check if the listing is in the prioritized list
//         $priority = array_search($listing->id, $prioritizedIds);

//         // If found, return the position (lower number = higher priority)
//         // If not found, return a high number to keep it at the end
//         return $priority !== false ? $priority : PHP_INT_MAX;
//     })->values(); // Re-index the collection

//     // Transform listings to add country name
//     $listings->transform(function ($listing) {
//         $listing->country_name = $listing->country->country_name;
//         return $listing;
//     });

//     return response()->json($listings, 200);
// }



public function index(Request $request)
{
    // Get authenticated user using Sanctum
    $user = $request->user();
    $prioritizedIds = [];
    $isLoggedIn = (bool)$user;

    // Check if the user is logged in
    if ($isLoggedIn) {
        $userId = $user->id;

        // Get IDs of listings saved as favorites by the user
        $favoriteListingIds = SavedProperty::where('user_id', $userId)
            ->where('is_favourite', true)
            ->pluck('listing_id')
            ->toArray();

        // Get IDs of listings the user frequently viewed
        $frequentViewedIds = PropertyKpi::where('users_id', $userId)
            ->orderBy('views', 'desc')
            ->pluck('listing_id')
            ->toArray();

        // Merge and prioritize listings (Favorites first, then Frequently Viewed)
        $prioritizedIds = array_unique(array_merge($favoriteListingIds, $frequentViewedIds));

        // **Predictive Recommendations Start Here**
        // Collect user interests
        $interests = PropertyKpi::where('users_id', $userId)
            ->select('listing_id')
            ->with('listing')
            ->get()
            ->pluck('listing')
            ->filter() // Remove nulls if any
            ->toArray();

        $preferredPropertyTypes = array_column($interests, 'property_type_id');
        $preferredCities = array_column($interests, 'city_id');
        $preferredCountries = array_column($interests, 'country_id');
        $preferredPriceRange = array_column($interests, 'price');

        // Get average price range for the user
        if (count($preferredPriceRange) > 0) {
            $averagePrice = array_sum($preferredPriceRange) / count($preferredPriceRange);
            $minPrice = $averagePrice * 0.8;
            $maxPrice = $averagePrice * 1.2;
        }

        // Get recommended listings based on user's interests
        $recommendedListings = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features'])
            ->when(!empty($preferredPropertyTypes), function ($query) use ($preferredPropertyTypes) {
                return $query->whereIn('property_type_id', $preferredPropertyTypes);
            })
            ->when(!empty($preferredCities), function ($query) use ($preferredCities) {
                return $query->whereIn('city_id', $preferredCities);
            })
            ->when(!empty($preferredCountries), function ($query) use ($preferredCountries) {
                return $query->whereIn('country_id', $preferredCountries);
            })
            ->when(isset($minPrice) && isset($maxPrice), function ($query) use ($minPrice, $maxPrice) {
                return $query->whereBetween('price', [$minPrice, $maxPrice]);
            })
            ->pluck('id')
            ->toArray();

        // Merge recommended listings with prioritized ones
        $prioritizedIds = array_unique(array_merge($prioritizedIds, $recommendedListings));
    }

    // Build the query for Listings
    $listingsQuery = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features' , 'media']);

    // If the user is logged in and has prioritized IDs
    if ($isLoggedIn && !empty($prioritizedIds)) {
        // Convert array to a comma-separated string for ORDER BY FIELD
        $prioritizedIdsStr = implode(',', $prioritizedIds);

        // Prioritize favorites, frequently viewed, and recommended properties first
        $listingsQuery->orderByRaw("FIELD(id, $prioritizedIdsStr) DESC");
    }

    // Continue with the default ordering for other listings
    $listings = $listingsQuery->orderBy('id', 'desc')->get();

    // Transform listings to add country name
    $listings->transform(function ($listing) {
        $listing->country_name = $listing->country->country_name;
        return $listing;
    });

    // Prepare response with additional context
    $responseData = [
        'listings' => $listings,
        'is_logged_in' => $isLoggedIn,
    ];

    return response()->json($responseData, 200);
}




public function NotLogin_index(Request $request)
{
    // Get authenticated user using Sanctum
    
    $prioritizedIds = [];

    $listingsQuery = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features' , 'media']);

  
    $listings = $listingsQuery->orderBy('id', 'desc')->get();

    // Transform listings to add country name
    $listings->transform(function ($listing) {
        $listing->country_name = $listing->country->country_name;
        return $listing;
    });

    // Prepare response with additional context
    $responseData = [
        'listings' => $listings,
    ];

    return response()->json($responseData, 200);
}


public function show_non_logedin($id)
{
    $listing = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features' , 'media'])
                      ->find($id);

    if (!$listing) {
        return response()->json(['message' => 'Property Not Found'], 404);
    }

    $listing->country_name = $listing->country ? $listing->country->name : null;

    return response()->json($listing);
}



public function show(Request $request, $id)
{
    // Check if user is logged in
    $user = $request->user();

    // Get skiptrace only if user is logged in
    $skiptrace = $user ? Skiptrace::where('user_id', $user->id)
        ->where('listing_id', $id)
        ->get() : collect(); 

    if ($user && $user->role === 'admin') {
        $listing = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features' , 'media'])
                          ->find($id);
    } 
    else if ($user && !$skiptrace->isEmpty()) {
        $listing = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features' , 'media'])
                          ->find($id);
    }
    else {
        $listing = Listing::select([
                            'id', 
                            'title', 
                            'description', 
                            'city_id', 
                            'country_id', 
                            'property_type_id', 
                            'property_status_id', 
                            'listing_date', 
                            'price', 
                            'square_foot', 
                            'parking', 
                            'year_built', 
                            'lot_size', 
                            'longitude', 
                            'latitude', 
                            'school_district', 
                            'walkability_score', 
                            'crime_rate', 
                            'roi', 
                            'monthly_rent', 
                            'cap_rate', 
                            'geolocation_coordinates', 
                            'zip_code', 
                            'area', 
                            'gdrp_agreement', 
                            'address', 
                            'bedrooms', 
                            'bathrooms', 
                            'half_bathrooms', 
                            'arv', 
                            'gross_margin', 
                            'estimated_roi', 
                            'repair_cost', 
                            'wholesale_fee', 
                            'price_per_square_feet', 
                            'user_id', 
                            'created_at', 
                            'updated_at', 
                            'is_featured', 
                            'is_approved', 
                            'moa',
                            'owner_full_name'
                        ])
                        ->with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features' , 'media'])
                        ->find($id);
    }

    if (!$listing) {
        return response()->json(['message' => 'Property Not Found'], 404);
    }

    $listing->country_name = $listing->country ? $listing->country->name : null;

    // If user is logged in, store property KPI
    if ($user) {
        PropertyKpi::create([
            'users_id' => $user->id,
            'listing_id' => $listing->id,
        ]);
    }

    return response()->json($listing);
}






}
