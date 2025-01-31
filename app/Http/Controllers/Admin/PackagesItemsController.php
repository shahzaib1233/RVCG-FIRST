<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\PackageItem;

class PackagesItemsController extends Controller
{
    // Show all package items
    public function index()
    {
        $packageItems = PackageItem::all();
        return response()->json([
            'success' => true,
            'data' => $packageItems
        ], 200);
    }

    // Show a single package item by ID
    public function show($id)
    {
        $packageItem = PackageItem::find($id);

        if (!$packageItem) {
            return response()->json([
                'success' => false,
                'message' => 'PackageItem not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $packageItem
        ], 200);
    }

    // Store a new package item
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'item_name' => 'required|string|max:255'
        ]);

        // Create the new package item
        $packageItem = PackageItem::create([
            'package_id' => $request->package_id,
            'item_name' => $request->item_name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PackageItem created successfully!',
            'data' => $packageItem
        ], 201);
    }

    // Update an existing package item
    public function update(Request $request, $id)
    {
        $packageItem = PackageItem::find($id);

        if (!$packageItem) {
            return response()->json([
                'success' => false,
                'message' => 'PackageItem not found'
            ], 404);
        }

        // Validate the incoming request data
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'item_name' => 'required|string|max:255'
        ]);

        // Update the package item
        $packageItem->update([
            'package_id' => $request->package_id,
            'item_name' => $request->item_name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PackageItem updated successfully!',
            'data' => $packageItem
        ], 200);
    }

    // Delete an existing package item
    public function destroy($id)
    {
        $packageItem = PackageItem::find($id);

        if (!$packageItem) {
            return response()->json([
                'success' => false,
                'message' => 'PackageItem not found'
            ], 404);
        }

        // Delete the package item
        $packageItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'PackageItem deleted successfully!'
        ], 200);
    }
}
