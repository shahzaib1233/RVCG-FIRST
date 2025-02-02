<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


abstract class Controller
{
    //

    public function image(Request $request)
    {
        // Validate the image input
        $request->validate([
    'image' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,zip|max:5120', 
        ]);
    
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Generate unique filename
            $image->move(public_path('uploads/Listings/Image'), $imageName); // Save in the specified folder
    
            return response()->json([
                'message' => 'Image uploaded successfully',
                'image_path' => url('uploads/Listings/Image/' . $imageName), // Return the accessible image URL
            ], 200);
        }
    
        return response()->json(['message' => 'No image uploaded'], 400);
    }
}
