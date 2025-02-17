<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\LeadType;
use Illuminate\Http\Request;

class LeadTypeController extends Controller
{
    //
  



    public function GetLeadType()
{
    $leadTypes = LeadType::all();
    return response()->json($leadTypes, 200); // Return the data directly, no need for array wrapping
}

public function AddLeadType(Request $request)
{
    $validate = $request->validate([
        'type_name' => 'required|string|max:255',
         'description' => 'nullable|string'
    ]);
    
    // Create new lead type using validated data
    $leadType = LeadType::create($validate);    
    return response()->json(['leadType' => $leadType], 201); // Use 201 Created for successful resource creation
}

public function EditLeadType(Request $request , $id)
{
    $leadType = LeadType::find($id);
    if (empty($leadType)) {
        return response()->json(['message' => 'Lead Not Found'], 404); // Better message key
    }

    // Validate and update the lead type
    $validate = $request->validate([
        'type_name' => 'required|string|max:255',
        'description' => 'nullable|string'
    ]);
    
    $leadType->update($validate);
    return response()->json(['leadType' => $leadType], 200); // Return updated lead type
}

public function DeleteLeadType(Request $request , $id)
{
    $leadType = LeadType::find($id);
    if (empty($leadType)) {
        return response()->json(['message' => 'Lead Not Found'], 404); // Better message key
    }

    $leadType->delete();
    return response()->json(['message' => 'Lead Type Deleted Successfully'], 200); // Clear success message
}





    

}
