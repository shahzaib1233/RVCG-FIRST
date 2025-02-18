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
    // Get the query text from the URL
    $queryText = $request->query('query');

    // Check if the query text is provided
    if (!$queryText) {
        return response()->json(['error' => 'Query parameter is required'], 400);
    }

    // Fetch data from the external API
    $data = $this->fetchData();

    // Check if data is available
    if (!$data) {
        return response()->json(['error' => 'Failed to fetch data from API'], 500);
    }

    // Get the results array from the API response
    $results = $data['Results'];

    // Filter the results based on the query text
    $filteredResults = array_filter($results, function ($item) use ($queryText) {
        return (
            (isset($item['CityName']) && stripos($item['CityName'], $queryText) !== false) ||
            (isset($item['FullStreetAddress']) && stripos($item['FullStreetAddress'], $queryText) !== false) ||
            (isset($item['ListPrice']) && is_numeric($queryText) && $item['ListPrice'] >= (float) $queryText) ||
            (isset($item['LivingSquareFeet']) && stripos((string) $item['LivingSquareFeet'], $queryText) !== false) ||
            (isset($item['OriginatingMls']) && stripos($item['OriginatingMls'], $queryText) !== false) ||
            (isset($item['State']) && stripos($item['State'], $queryText) !== false) ||
            (isset($item['StreetAddress']) && stripos($item['StreetAddress'], $queryText) !== false) ||
            (isset($item['YearBuilt']) && stripos((string) $item['YearBuilt'], $queryText) !== false)
        );
    });

    // Reset array keys
    $filteredResults = array_values($filteredResults);

    // Return the filtered results as JSON
    return response()->json([
        'query' => $queryText,
        'num_results' => count($filteredResults),
        'results' => $filteredResults
    ]);
}



}
