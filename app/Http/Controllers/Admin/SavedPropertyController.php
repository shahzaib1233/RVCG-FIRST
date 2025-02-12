<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\SavedProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SavedPropertyController extends Controller
{
    public function savedProperties()
    {
        // Check if the user is an admin
        if (Auth::user()->role === 'admin') {
            // Admin: Show all users' saved properties
            $savedProperties = SavedProperty::with(['user', 'listing'])->get();
            return response()->json(['savedProperties' => $savedProperties], 200); // OK status
        } else {
            // User: Show only the user's saved properties
            $savedProperties = SavedProperty::where('user_id', Auth::id())
                                            ->with('listing')
                                            ->get();
            return response()->json(['savedProperties' => $savedProperties], 200); // OK status
        }
    }

    // Add Saved Property
    public function addSavedProperty(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
        ]);

        // Check if property already saved
        $savedProperty = SavedProperty::where('user_id', Auth::id())
                                      ->where('listing_id', $request->listing_id)
                                      ->first();

        if ($savedProperty) {
            return response()->json(['success' => false, 'message' => 'Property already in saved list'], 400); // Bad Request
        }

        // Add property to saved
        SavedProperty::create([
            'user_id' => Auth::id(),
            'listing_id' => $request->listing_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Property added to saved properties'], 201); // Created status
    }

    // Delete Saved Property
    public function deleteSavedProperty($id)
    {
        // Find saved property
        $savedProperty = SavedProperty::where('user_id', Auth::id())
                                      ->where('listing_id', $id)
                                      ->first();

        if (!$savedProperty) {
            return response()->json(['success' => false, 'message' => 'Property not found in saved list'], 404); // Not Found status
        }

        // Delete property from saved list
        $savedProperty->delete();

        return response()->json(['success' => true, 'message' => 'Property removed from saved properties'], 200); // OK status
    }
}
