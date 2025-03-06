<?php

namespace App\Http\Controllers\mls_data;

use App\Http\Controllers\Controller;
use App\Models\admin\Cities;
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
            $data = $response->json();
    
            // Check if 'Results' key exists and contains listings
            if (isset($data['Results']) && is_array($data['Results'])) {
                foreach ($data['Results'] as &$item) {
                    if (isset($item['Id'])) { // Correcting key case
                        $item['CustomUrl'] = "https://homeasap.com/edwardgreen/agent/{$item['Id']}";
                    }
                }
            }
    
            return $data;
        }
    
        Log::error('API Request Failed', ['status' => $response->status(), 'response' => $response->body()]);
        return null;
    }
    
    // Index function to return formatted data
    // public function index()
    // {
    //     $data = $this->fetchData();
    //     if (!$data) {
    //         return response()->json(['error' => 'Failed to fetch data'], 500);
    //     }
        
    //     // Ensure only the 'Results' array is returned
    //     return response()->json(['results' => $data['Results'] ?? []]);
    // }



    public function index(Request $request)
{
    $data = $this->fetchData();
    
    if (!$data) {
        return response()->json(['error' => 'Failed to fetch data'], 500);
    }

    $results = $data['Results'] ?? [];

    $page = $request->query('page', 1);
    $perPage = 20;

    $offset = ($page - 1) * $perPage;

    $paginatedResults = array_slice($results, $offset, $perPage);

    return response()->json([
        'results' => $paginatedResults,
        'current_page' => $page,
        'total_records' => count($results),
        'total_pages' => ceil(count($results) / $perPage)
    ]);
}


    

    // Filter data based on user input
    // public function filterData(Request $request)
    // {
    //     $data = $this->fetchData();

    //     if (!$data || !isset($data['Results']) || !is_array($data['Results'])) {
    //         return response()->json(['error' => 'Invalid API response', 'data' => $data], 500);
    //     }

    //     $results = $data['Results'];

    //     // Get filter parameters
    //     $mlsStatus = $request->input('mlsStatus');
    //     $minDaysOnMarket = $request->input('daysOnMarket.min');
    //     $maxDaysOnMarket = $request->input('daysOnMarket.max');
    //     $minListingPrice = $request->input('listingPrice.min');
    //     $maxListingPrice = $request->input('listingPrice.max');
    //     $keywords = $request->input('keywords', []);

    //     // Filter results
    //     $filteredResults = array_filter($results, function ($item) use ($mlsStatus, $minDaysOnMarket, $maxDaysOnMarket, $minListingPrice, $maxListingPrice, $keywords) {
    //         if ($mlsStatus && isset($item['Status']) && strcasecmp($item['Status'], $mlsStatus) !== 0) {
    //             return false;
    //         }
    //         if ($minDaysOnMarket && isset($item['DaysOnMarket']) && is_numeric($item['DaysOnMarket']) && $item['DaysOnMarket'] < $minDaysOnMarket) {
    //             return false;
    //         }
    //         if ($maxDaysOnMarket && isset($item['DaysOnMarket']) && is_numeric($item['DaysOnMarket']) && $item['DaysOnMarket'] > $maxDaysOnMarket) {
    //             return false;
    //         }
    //         if ($minListingPrice && isset($item['ListPrice']) && is_numeric($item['ListPrice']) && $item['ListPrice'] < $minListingPrice) {
    //             return false;
    //         }
    //         if ($maxListingPrice && isset($item['ListPrice']) && is_numeric($item['ListPrice']) && $item['ListPrice'] > $maxListingPrice) {
    //             return false;
    //         }
    //         if (!empty($keywords) && isset($item['Tags']) && is_array($item['Tags'])) {
    //             foreach ($keywords as $keyword) {
    //                 $found = false;
    //                 foreach ($item['Tags'] as $tag) {
    //                     if (isset($tag['Name']) && is_string($tag['Name']) && stripos($tag['Name'], $keyword) !== false) {
    //                         $found = true;
    //                         break; // Stop checking once a match is found
    //                     }
    //                 }
    //                 if (!$found) {
    //                     return false; // If any keyword is not found in Tags' Name field, exclude this item
    //                 }
    //             }
    //         }
    //         return true;
            
            
            
    //     });

    //     return response()->json(["results" => array_values($filteredResults)]);
    // }


    public function filterData(Request $request)
    {
        $data = $this->fetchData();
    
        if (!$data || !isset($data['Results']) || !is_array($data['Results'])) {
            return response()->json(['error' => 'Invalid API response', 'data' => $data], 500);
        }
    
        $results = $data['Results'];
    
        // Get filter parameters
        $mlsStatus = $request->input('mlsStatus');
        $minDaysOnMarket = (int) $request->input('DaysOnMarket.min', 0); // Default to 0 if null
        $maxDaysOnMarket = (int) $request->input('DaysOnMarket.max', PHP_INT_MAX); // Default to max integer value if null
        $minListingPrice = (int) $request->input('listingPrice.min', 0);
        $maxListingPrice = (int) $request->input('listingPrice.max', PHP_INT_MAX);
        $keywords = $request->input('keywords', []);
    
        // Filter results
        $filteredResults = array_filter($results, function ($item) use ($mlsStatus, $minDaysOnMarket, $maxDaysOnMarket, $minListingPrice, $maxListingPrice, $keywords) {
            if ($mlsStatus && isset($item['Status']) && strcasecmp($item['Status'], $mlsStatus) !== 0) {
                return false;
            }
            
            if ($minDaysOnMarket || $maxDaysOnMarket) {
                if (!isset($item['DaysOnMarket']) || !is_numeric($item['DaysOnMarket'])) {
                    return false; // Exclude listings where DaysOnMarket is null or non-numeric
                }
        
                if ($minDaysOnMarket && $item['DaysOnMarket'] < $minDaysOnMarket) {
                    return false;
                }
                if ($maxDaysOnMarket && $item['DaysOnMarket'] > $maxDaysOnMarket) {
                    return false;
                }
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
        
    
        // Convert to array (reset indexes)
        $filteredResults = array_values($filteredResults);
    
        // Apply Pagination
        $page = (int) $request->query('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
    
        $paginatedResults = array_slice($filteredResults, $offset, $perPage);
    
        return response()->json([
            'results' => $paginatedResults,
            'current_page' => $page,
            'total_records' => count($filteredResults),
            'total_pages' => ceil(count($filteredResults) / $perPage)
        ]);
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







    // public function filter_Data(Request $request)
    // {
    //     $data = $this->fetchData();
    
    //     if (!$data || !isset($data['Results']) || !is_array($data['Results'])) {
    //         return response()->json(['error' => 'Invalid API response', 'data' => $data], 500);
    //     }
    
    //     $results = $data['Results'];
    
    //     // Get search query from URL (?query=something)
    //     $searchQuery = $request->query('query', '');
    
    //     if (!$searchQuery) {
    //         return response()->json(["results" => $results]); // Return all if no query
    //     }
    
    //     // Filter results dynamically based on the search query
    //     $filteredResults = array_filter($results, function ($item) use ($searchQuery) {
    //         foreach ($item as $key => $value) {
    //             if (is_string($value) && stripos($value, $searchQuery) !== false) {
    //                 return true; // Found a match in any column
    //             } elseif (is_numeric($value) && stripos((string) $value, $searchQuery) !== false) {
    //                 return true; // Match numeric values as well
    //             }
    //         }
    //         return false;
    //     });
    
    //     return response()->json(["results" => array_values($filteredResults)]);
    // }
    




        public function filter_Data(Request $request)
    {
        $data = $this->fetchData();

        if (!$data || !isset($data['Results']) || !is_array($data['Results'])) {
            return response()->json(['error' => 'Invalid API response', 'data' => $data], 500);
        }

        $results = $data['Results'];

        
        $searchQuery = $request->query('query', '');

        if ($searchQuery) {
            
            $results = array_filter($results, function ($item) use ($searchQuery) {
                foreach ($item as $key => $value) {
                    if (is_string($value) && stripos($value, $searchQuery) !== false) {
                        return true; 
                    } elseif (is_numeric($value) && stripos((string) $value, $searchQuery) !== false) {
                        return true; 
                    }
                }
                return false;
            });

            $results = array_values($results); 
        }

        // Pagination logic
        $page = $request->query('page', 1);
        $perPage = 20;
        $totalRecords = count($results);
        $totalPages = ceil($totalRecords / $perPage);
        $offset = ($page - 1) * $perPage;
        $paginatedResults = array_slice($results, $offset, $perPage);

        return response()->json([
            'results' => $paginatedResults,
            'current_page' => $page,
            'total_records' => $totalRecords,
            'total_pages' => $totalPages
        ]);
    }








    public function city_data_Home_page_api()
{
    
    $cities = Cities::whereHas('listings')->distinct()->get();

    if ($cities->isEmpty()) {
        return response()->json(['message' => 'No cities found with listings'], 404);
    }

    return response()->json($cities);
}

}
