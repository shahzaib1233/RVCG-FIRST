<?php

namespace App\Http\Controllers\mls_data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MlsController extends Controller
{
   

    private $apiUrl = 'https://api.homeasap.com/NPlay.Services.NPlayApi/api/listings/search?mo=cril01&ma=eg165&ml=azrmls&ct=1000';

    // Fetch data from external API
    private function fetchData()
    {
        $response = Http::get($this->apiUrl);

        // Log raw response for debugging
        Log::info('Raw API Response:', ['response' => $response->body()]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('API Request Failed', ['status' => $response->status(), 'response' => $response->body()]);
        return null;
    }

    // Index function to return raw data
    public function index()
    {
        $data = $this->fetchData();
        if (!$data) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }
        return response()->json(['results' => $data['Results'] ?? []]);
    }

    // Filter data based on user input
    public function filterData(Request $request)
    {
        $data = $this->fetchData();

        if (!$data || !isset($data['Results']) || !is_array($data['Results'])) {
            return response()->json(['error' => 'Invalid API response', 'data' => $data], 500);
        }

        $results = $data['Results'];

        // Get filter parameters
        $mlsStatus = $request->input('mlsStatus');
        $minDaysOnMarket = $request->input('daysOnMarket.min');
        $maxDaysOnMarket = $request->input('daysOnMarket.max');
        $minListingPrice = $request->input('listingPrice.min');
        $maxListingPrice = $request->input('listingPrice.max');
        $keywords = $request->input('keywords', []);

        // Filter results
        $filteredResults = array_filter($results, function ($item) use ($mlsStatus, $minDaysOnMarket, $maxDaysOnMarket, $minListingPrice, $maxListingPrice, $keywords) {
            if ($mlsStatus && isset($item['Status']) && strcasecmp($item['Status'], $mlsStatus) !== 0) {
                return false;
            }
            if ($minDaysOnMarket && isset($item['DaysOnMarket']) && is_numeric($item['DaysOnMarket']) && $item['DaysOnMarket'] < $minDaysOnMarket) {
                return false;
            }
            if ($maxDaysOnMarket && isset($item['DaysOnMarket']) && is_numeric($item['DaysOnMarket']) && $item['DaysOnMarket'] > $maxDaysOnMarket) {
                return false;
            }
            if ($minListingPrice && isset($item['ListPrice']) && is_numeric($item['ListPrice']) && $item['ListPrice'] < $minListingPrice) {
                return false;
            }
            if ($maxListingPrice && isset($item['ListPrice']) && is_numeric($item['ListPrice']) && $item['ListPrice'] > $maxListingPrice) {
                return false;
            }
            if (!empty($keywords) && isset($item['Tags']) && is_array($item['Tags'])) {
                foreach ($keywords as $keyword) {
                    $found = false;
                    foreach ($item['Tags'] as $tag) {
                        if (isset($tag['Name']) && is_string($tag['Name']) && stripos($tag['Name'], $keyword) !== false) {
                            $found = true;
                            break; // Stop checking once a match is found
                        }
                    }
                    if (!$found) {
                        return false; // If any keyword is not found in Tags' Name field, exclude this item
                    }
                }
            }
            return true;
            
            
            
        });

        return response()->json(["results" => array_values($filteredResults)]);
    }

    // Filter listings by status
    public function filterListings(Request $request)
    {
        $data = $this->fetchData();
        if (!$data || !isset($data['Results']) || !is_array($data['Results'])) {
            return response()->json(['error' => 'Invalid API response', 'data' => $data], 500);
        }

        $status = $request->input('status');
        $filteredResults = array_filter($data['Results'], function ($item) use ($status) {
            return isset($item['Status']) && strcasecmp($item['Status'], $status) === 0;
        });

        return response()->json(["results" => array_values($filteredResults)]);
    }







    public function filter_Data(Request $request)
    {
        $data = $this->fetchData();
    
        if (!$data || !isset($data['Results']) || !is_array($data['Results'])) {
            return response()->json(['error' => 'Invalid API response', 'data' => $data], 500);
        }
    
        $results = $data['Results'];
    
        // Get search query from URL (?query=something)
        $searchQuery = $request->query('query', '');
    
        if (!$searchQuery) {
            return response()->json(["results" => $results]); // Return all if no query
        }
    
        // Filter results dynamically based on the search query
        $filteredResults = array_filter($results, function ($item) use ($searchQuery) {
            foreach ($item as $key => $value) {
                if (is_string($value) && stripos($value, $searchQuery) !== false) {
                    return true; // Found a match in any column
                } elseif (is_numeric($value) && stripos((string) $value, $searchQuery) !== false) {
                    return true; // Match numeric values as well
                }
            }
            return false;
        });
    
        return response()->json(["results" => array_values($filteredResults)]);
    }
    
}
