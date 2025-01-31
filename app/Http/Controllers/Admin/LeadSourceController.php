<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\LeadSource;

class LeadSourceController extends Controller
{
    public function index()
    {
        $leadSources = LeadSource::all();
        return response()->json([
            'success' => true,
            'data' => $leadSources
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $leadSource = LeadSource::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Lead source created successfully!',
            'data' => $leadSource
        ], 201);
    }

    public function show($id)
    {
        $leadSource = LeadSource::find($id);

        if (!$leadSource) {
            return response()->json([
                'success' => false,
                'message' => 'Lead source not found!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $leadSource
        ]);
    }

    public function update(Request $request, $id)
    {
        $leadSource = LeadSource::find($id);

        if (!$leadSource) {
            return response()->json([
                'success' => false,
                'message' => 'Lead source not found!'
            ], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $leadSource->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Lead source updated successfully!',
            'data' => $leadSource
        ]);
    }

    public function destroy($id)
    {
        $leadSource = LeadSource::find($id);

        if (!$leadSource) {
            return response()->json([
                'success' => false,
                'message' => 'Lead source not found!'
            ], 404);
        }

        $leadSource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead source deleted successfully!'
        ]);
    }
}
