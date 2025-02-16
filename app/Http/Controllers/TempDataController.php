<?php

namespace App\Http\Controllers;

use App\Models\TempData;
use Illuminate\Http\Request;

class TempDataController extends Controller
{
    public function tempUpload(Request $request)
{
    $validatedData = $request->validate([
        'file' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,zip|max:5120',
    ]);

    $file = $request->file('file');
    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    $file->move(public_path('uploads/temp'), $fileName);

    $tempData = TempData::create([
        'file_name' => $fileName,
        'file_url' => 'uploads/temp/' . $fileName,
        'file_type' => $file->getClientMimeType(),
    ]);

    return response()->json([
        'success' => true,
        'data' => $tempData
    ], 201);
}

}
