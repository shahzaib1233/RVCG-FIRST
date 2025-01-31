<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Package;
use App\Models\admin\PackageItem;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    // Get all packages with their items
    public function index()
    {
        $packages = Package::with('items')->get();

        if ($packages->isEmpty()) {
            return response()->json(['message' => 'No packages found'], 404);
        }

        return response()->json($packages, 200);
    }

    // Show a specific package by ID
    public function show($id)
    {
        $package = Package::with('items')->find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        return response()->json($package, 200);
    }

    // Create a new package with items
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packages,name',
            'price' => 'required|numeric',
            'duration' => 'required|integer',
            'items' => 'required|array',
            'items.*' => 'required|string|max:255'
        ]);

        $package = Package::create($validated);

        foreach ($validated['items'] as $item) {
            $package->items()->create(['item_name' => $item]);
        }

        return response()->json($package->load('items'), 201);
    }

    // Update an existing package with items
    public function update(Request $request, $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $packageId = $package->id;
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packages,name,' . $packageId,
            'price' => 'required|numeric',
            'duration' => 'required|integer',
            'items' => 'required|array',
            'items.*' => 'required|string|max:255'
        ]);

        $package->update($validated);
        $existingItems = $package->items->keyBy('item_name'); // Group existing items by name for quick lookup
        $newItems = collect($validated['items']);

        // Delete items that are no longer in the request
        $existingItems
            ->whereNotIn('item_name', $newItems)
            ->each(function ($item) {
                $item->delete();
            });

        // Add or update items
        foreach ($newItems as $itemName) {
            $package->items()->updateOrCreate(
                ['item_name' => $itemName], // Check if the item exists by name
                ['item_name' => $itemName] // If not, create a new one
            );
        }

        return response()->json($package->load('items'), 200);
    }

    // Delete a specific package along with its items
    public function destroy($id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $packageItems = PackageItem::where('package_id', $package->id)->get();

        if ($packageItems->isEmpty()) {
            return response()->json(['message' => 'No items found for this package'], 404);
        }

        // Delete all associated package items
        $packageItems->each(function ($item) {
            $item->delete();
        });

        // Delete the package
        $package->delete();

        return response()->json(['message' => 'Package deleted successfully'], 200);
    }
}
