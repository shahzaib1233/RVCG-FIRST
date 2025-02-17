<?php

namespace App\Http\Controllers\mls_data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MlsController extends Controller
{
    //

    public function index()
{
    $url = 'https://api.homeasap.com/NPlay.Services.NPlayApi/api/listings/search?mo=cril01&ma=eg165&ml=azrmls&ct=1000';
    
    // Fetching data from the API
    $response = Http::get($url);

    // Check if the request was successful
    if ($response->successful()) {
        $data = $response->json();

        // Loop through each result and add custom URL
        foreach ($data['Results'] as &$result) {
            // Create the custom URL
            $customUrl = "https://homeasap.com/edwardgreen/agent/{$result['Id']}";
            // Add the new field to the JSON object
            $result['CustomUrl'] = $customUrl;
        }

        return response()->json($data);
    } else {
        return response()->json(['error' => 'Failed to fetch data'], 500);
    }
}








public function fetchData()
{
    $url = 'https://api.homeasap.com/NPlay.Services.NPlayApi/api/listings/search?mo=cril01&ma=eg165&ml=azrmls&ct=1000';
    
    // Fetching data from the API
    $response = Http::get($url);

    // Log the API response for debugging
    Log::info('API Response:', $response->json()); // Logging the response

    // Check if the request was successful
    if ($response->successful()) {
        return $response->json(); // Return the full data to be filtered
    } else {
        // Return null if the API request failed
        return null;
    }
}

public function filterListings(Request $request)
{
    // Fetch the data (could be from a cache or database if required)
    $data = $this->fetchData();

    // Check if data is returned from the API
    if (!$data) {
        return response()->json(['error' => 'Failed to fetch data'], 500);
    }

    // Check if 'Results' key exists in the fetched data
    if (!isset($data['Results'])) {
        return response()->json(['error' => 'Results key missing in API response'], 500);
    }

    $results = $data['Results'];

    // Apply filters based on request parameters (if they exist)
    if ($request->has('address')) {
        $results = array_filter($results, function ($listing) use ($request) {
            return stripos($listing['FullStreetAddress'], $request->input('address')) !== false;
        });
    }

    if ($request->has('title')) {
        $results = array_filter($results, function ($listing) use ($request) {
            return stripos($listing['FullStreetAddress'], $request->input('title')) !== false;
        });
    }

    if ($request->has('min_price') && $request->has('max_price')) {
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $results = array_filter($results, function ($listing) use ($minPrice, $maxPrice) {
            return $listing['ListPrice'] >= $minPrice && $listing['ListPrice'] <= $maxPrice;
        });
    }

    // Add custom URL for each listing
    foreach ($results as &$result) {
        $customUrl = "https://homeasap.com/edwardgreen/agent/{$result['Id']}";
        $result['CustomUrl'] = $customUrl;
    }

    // Reassign filtered results back to the data array
    $data['Results'] = array_values($results);

    return response()->json($data);
}



}
