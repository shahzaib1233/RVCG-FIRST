<?php

namespace App\Http\Controllers;

use App\Models\PropertyValuation;
use Illuminate\Http\Request;
use App\Models\TempData;
use Illuminate\Support\Facades\File;
class PropertyValuationController extends Controller
{
    //





    public function index()
{
    $properties = PropertyValuation::all();
    return response()->json([
        'success' => true,
        'data' => $properties
    ]);
}


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'property_type' => 'required|string',
            'price' => 'required|numeric',
            'square_foot' => 'required|integer',
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'image_ids' => 'required|array', // Array of image IDs
            'image_ids.*' => 'exists:temp_data,id', // Validate each ID
            'owner_name' => 'required|string',
            'owner_age' => 'required|integer',
            'ownership_type' => 'required|string',
            'owner_email' => 'required|email',
            'govt_id_proof' => 'required|string',
            'owner_contact' => 'required|string',
        ]);

        $imageUrls = [];

        foreach ($request->image_ids as $id) {
            // Retrieve image from temp_data
            $tempImage = TempData::find($id);

            if ($tempImage) {
                $oldPath = public_path($tempImage->file_url); // Path in uploads/temp/
                $newFileName = time() . '_' . uniqid() . '.' . pathinfo($tempImage->file_name, PATHINFO_EXTENSION);
                $newPath = public_path("uploads/valuations/{$newFileName}");

                // Ensure directory exists
                if (!File::exists(public_path('uploads/valuations'))) {
                    File::makeDirectory(public_path('uploads/valuations'), 0777, true, true);
                }

                // Copy file to new folder
                if (File::exists($oldPath)) {
                    File::copy($oldPath, $newPath);
                    $imageUrls[] = "uploads/valuations/{$newFileName}";

                    // Optionally delete from temp after copying
                      // File::delete($oldPath);
                    // $tempImage->delete();
                }
            }
        }

        // Store property details in database
        $property = PropertyValuation::create([
            'title' => $request->title,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'property_type' => $request->property_type,
            'price' => $request->price,
            'square_foot' => $request->square_foot,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'property_images' => json_encode($imageUrls), // Store image URLs in JSON
            'owner_name' => $request->owner_name,
            'owner_age' => $request->owner_age,
            'ownership_type' => $request->ownership_type,
            'owner_email' => $request->owner_email,
            'govt_id_proof' => $request->govt_id_proof,
            'owner_contact' => $request->owner_contact,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property valuation created successfully!',
            'data' => $property
        ], 201);
    }



    public function show($id)
{
    $property = PropertyValuation::find($id);

    if (!$property) {
        return response()->json([
            'success' => false,
            'message' => 'Property valuation not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $property
    ]);

}
}
