<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Vendor;
use App\Models\admin\VendorService;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('services' , 'user' )->get();
        return response()->json($vendors);
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'services' => 'required|array',
            'services.*.service_name' => 'required|string',
            'services.*.description' => 'nullable|string',
            'services.*.price' => 'nullable|numeric',
            'services.*.service_city' => 'required|string',
        ]);

        $vendor = Vendor::create([
            'user_id' => $request->user_id,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
        ]);

        foreach ($request->services as $service) {
            VendorService::create([
                'vendor_id' => $vendor->id,
                'service_name' => $service['service_name'],
                'description' => $service['description'] ?? null,
                'price' => $service['price'] ?? null,
                'service_city' => $service['service_city'],
            ]);
        }

        return response()->json(['message' => 'Vendor created successfully', 'vendor' => $vendor->load('services')], 201);
    }

   
    public function show($id)
    {
        $vendor = Vendor::with('services' , 'user')->findOrFail($id);
        return response()->json($vendor);
    }


    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'services' => 'sometimes|array',
            'services.*.service_name' => 'required|string',
            'services.*.description' => 'nullable|string',
            'services.*.price' => 'nullable|numeric',
            'services.*.service_city' => 'required|string',
        ]);

        $vendor->update([
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
        ]);

        if ($request->has('services')) {
            VendorService::where('vendor_id', $vendor->id)->delete(); 

            foreach ($request->services as $service) {
                VendorService::create([
                    'vendor_id' => $vendor->id,
                    'service_name' => $service['service_name'],
                    'description' => $service['description'] ?? null,
                    'price' => $service['price'] ?? null,
                    'service_city' => $service['service_city'],
                ]);
            }
        }

        return response()->json(['message' => 'Vendor updated successfully', 'vendor' => $vendor->load('services')]);
    }

   
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return response()->json(['message' => 'Vendor deleted successfully']);
    }
}
