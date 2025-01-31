<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\OtherFeature;
use Illuminate\Http\Request;

class OtherFeatureController extends Controller
{
    // Get all other features
    public function index()
    {
        $otherFeature = OtherFeature::all();

        if ($otherFeature->isEmpty()) {
            return response()->json(['message' => 'No features found'], 404);
        }

        return response()->json($otherFeature, 200);
    }

    // Create a new other feature
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:other_features,name|max:255', // Ensure name is unique and has a max length
        ]);

        $feature = OtherFeature::create([
            'name' => $validatedData['name']
        ]);

        if ($feature) {
            return response()->json([
                'message' => 'Feature created successfully',
                'data' => $feature
            ], 201);
        } else {
            return response()->json(['message' => 'Failed to create feature'], 500);
        }
    }

    // Update an existing other feature
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:other_features,name,' . $request->id . '|max:255', // Ensure uniqueness but exclude current record
        ]);

        $feature = OtherFeature::find($request->id);

        if (!$feature) {
            return response()->json(['message' => 'Feature not found'], 404);
        }

        $feature->name = $validatedData['name'];
        $feature->save();

        return response()->json([
            'message' => 'Feature updated successfully',
            'data' => $feature
        ], 200);
    }

    // Delete a specific other feature
    public function destroy($id)
    {
        $feature = OtherFeature::find($id);

        if (!$feature) {
            return response()->json(['message' => 'Feature not found'], 404);
        }

        $feature->delete();

        return response()->json([
            'message' => 'Feature deleted successfully'
        ], 200);
    }
}
