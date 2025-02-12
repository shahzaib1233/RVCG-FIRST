<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\cities;
use App\Models\admin\countries;
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'city_name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
        ]);
        

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
            'city_name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
        ]);
        

        $city = Cities::find($id);

        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        $updated = $city->update($validatedData);

        if ($updated) {
            return response()->json($city, 200);
        } else {
            return response()->json(['message' => 'Failed to update city'], 500);
        }
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
