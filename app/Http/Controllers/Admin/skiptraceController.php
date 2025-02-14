<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Listing;
use App\Models\admin\Skiptrace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class skiptraceController extends Controller
{
    public function index()
    {
        $skiptraces = Skiptrace::where('user_id', Auth::id())->get();

        return response()->json(['data' => $skiptraces], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listing_id'   => 'required|exists:listings,id',  // Validate listing ID
            'is_paid'      => 'boolean',  // Ensure is_paid is boolean
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } $userId = Auth::id();

        // Get listing details using the provided listing_id
        $listing = Listing::find($request->listing_id);
    
        // Ensure listing exists
        if (!$listing) {
            return response()->json(['message' => 'Listing not found'], 404);
        }
    
        // Prepare skiptrace data
        $skiptraceData = [
            'listing_id'   => $listing->id,
            'user_id'      => $userId,  // Get user_id from Auth
            'owner_name'   => $listing->owner_full_name,
            'owner_contact'=> $listing->owner_contact_number,
            'owner_email'  => $listing->owner_email_address,
            'is_paid'      => $request->is_paid,  // Use the 'is_paid' from the request
        ];
    
        // Create the skiptrace record
        $skiptrace = Skiptrace::create($skiptraceData);
    
        return response()->json(['message' => 'Skiptrace created successfully', 'data' => $skiptrace], 201);
      }


    public function update(Request $request, $id)
    {
        $skiptrace = Skiptrace::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'listing_id'   => 'exists:listings,id',
            'user_id'      => 'exists:users,id',
            'owner_name'   => 'string|max:255',
            'owner_contact'=> 'string|max:255',
            'owner_email'  => 'email|max:255',
            'is_paid'      => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $skiptrace->update($request->all());

        return response()->json(['message' => 'Skiptrace updated successfully', 'data' => $skiptrace], 200);
    }

    
    public function destroy($id)
    {
        $skiptrace = Skiptrace::findOrFail($id);
        $skiptrace->delete();

        return response()->json(['message' => 'Skiptrace deleted successfully'], 200);
    }
}
