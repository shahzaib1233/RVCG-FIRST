<?php

namespace App\Http\Controllers\mls_data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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


}
