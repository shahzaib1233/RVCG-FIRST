<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\countries;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    // Get all countries
    public function index()
    {
        $countries = Countries::all();

        if ($countries->isEmpty()) {
            return response()->json(['message' => 'No countries found'], 404);
        }

        return response()->json($countries, 200);
    }

    // Add a new country
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'country_name' => 'required|string|max:255',
        ]);

        $country = Countries::create($validatedData);

        if ($country) {
            return response()->json($country, 201);
        } else {
            return response()->json(['message' => 'Failed to create country'], 500);
        }
    }

    // Show a specific country
    public function show($id)
    {
        $country = Countries::find($id);

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        return response()->json($country, 200);
    }

    // Edit/update a specific country
    public function update(Request $request, $id)
    {
        $country = Countries::find($id);

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $validatedData = $request->validate([
            'country_name' => 'required|string|max:255',
        ]);

        $updated = $country->update($validatedData);

        if ($updated) {
            return response()->json($country, 200);
        } else {
            return response()->json(['message' => 'Failed to update country'], 500);
        }
    }

    // Delete a specific country
    public function destroy($id)
    {
        $country = Countries::find($id);

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $deleted = $country->delete();

        if ($deleted) {
            return response()->json(['message' => 'Country deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete country'], 500);
        }
    }
}
