<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\cities;
use App\Models\admin\countries;
use App\Models\TempData;
use Illuminate\Http\Request;

class CityController extends Controller
{
    // Get all cities along with country information
    public function index()
    {
        $cities = Cities::with('country')->get();

        if ($cities->isEmpty()) {
            return response()->json(['message' => 'No cities found'], 404);
        }

        $cities = $cities->map(function ($city) {
            $city->country_name = $city->country->country_name; // Dynamically add country_name
            return $city;
        });

        return response()->json($cities, 200);
    }

    // Add a new city
    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'city_name' => 'required|string|max:255',
    //         'country_id' => 'required|exists:countries,id',
    //         'latitude' => 'nullable|string|max:50',
    //         'longitude' => 'nullable|string|max:50',
    //         'img_id'     => 'nullable|exists:temp_data,id',
    //     ]);
        

    //     $city = Cities::create($validatedData);

    //     if ($city) {
    //         return response()->json($city, 201);
    //     } else {
    //         return response()->json(['message' => 'Failed to create city'], 500);
    //     }
    // }


    public function store(Request $request)
{
    $validatedData = $request->validate([
        'city_name'  => 'required|string|max:255',
        'country_id' => 'required|exists:countries,id',
        'latitude'   => 'nullable|string|max:50',
        'longitude'  => 'nullable|string|max:50',
        'img_id'     => 'nullable|exists:temp_data,id', // Ensure img_id exists in temp_data
    ]);

    // Initialize image path as null
    $imageUrl = null;

    // If img_id is provided, process the image
    if ($request->filled('img_id')) {
        $tempData = TempData::find($request->img_id);

        if ($tempData) {
            $tempFilePath = public_path($tempData->file_url);
            $finalPath = public_path('uploads/cities/');

            // Check if directory exists, otherwise create it
            if (!is_dir($finalPath)) {
                mkdir($finalPath, 0777, true);
            }

            // Generate a unique filename
            $newFileName = time() . '_' . uniqid() . '.' . pathinfo($tempFilePath, PATHINFO_EXTENSION);
            $finalFilePath = $finalPath . $newFileName;

            // Move the file and store path if it exists
            if (file_exists($tempFilePath)) {
                rename($tempFilePath, $finalFilePath);
                $imageUrl = 'uploads/cities/' . $newFileName;

                // Optionally delete the temp record
                // $tempData->delete();
            }
        }
    }

    // Add the image path to validated data
    $validatedData['img'] = $imageUrl;

    // Create the city record
    $city = Cities::create($validatedData);

    if ($city) {
        return response()->json($city, 201);
    } else {
        return response()->json(['message' => 'Failed to create city'], 500);
    }
}



    // Show a specific city by ID, along with country information
    public function show($id)
    {
        $city = Cities::with('country')->where('country_id', $id)->get();
        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        return response()->json($city, 200);
    }

    // Update a specific city by ID
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'city_name'  => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'latitude'   => 'nullable|string|max:50',
            'longitude'  => 'nullable|string|max:50',
            'img_id'     => 'nullable|exists:temp_data,id', // Nullable image ID
        ]);
    
        $city = Cities::find($id);
    
        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }
    
        // Handle image update if img_id is provided
        if ($request->filled('img_id')) {
            $tempData = TempData::find($request->img_id);
    
            if ($tempData) {
                $tempFilePath = public_path($tempData->file_url);
                $finalPath = public_path('uploads/cities/');
    
                if (!is_dir($finalPath)) {
                    mkdir($finalPath, 0777, true);
                }
    
                $newFileName = time() . '_' . uniqid() . '.' . pathinfo($tempFilePath, PATHINFO_EXTENSION);
                $finalFilePath = $finalPath . $newFileName;
    
                if (file_exists($tempFilePath)) {
                    rename($tempFilePath, $finalFilePath);
                    $validatedData['img'] = 'uploads/cities/' . $newFileName;
    
                    // Optionally delete the old image (if needed)
                    if ($city->img && file_exists(public_path($city->img))) {
                        unlink(public_path($city->img));
                    }
    
                    // Optionally delete the temp record
                    // $tempData->delete();
                }
            }
        }
    
        // Update the city record with new data
        $updated = $city->update($validatedData);
    
        return $updated
            ? response()->json($city, 200)
            : response()->json(['message' => 'Failed to update city'], 500);
    }
    

    // Delete a specific city by ID
    public function destroy($id)
    {
        $city = Cities::find($id);

        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        $deleted = $city->delete();

        if ($deleted) {
            return response()->json(['message' => 'City deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete city'], 500);
        }
    }
}
