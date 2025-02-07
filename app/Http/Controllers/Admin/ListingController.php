<?php
namespace App\Http\Controllers\admin;
use App\Models\admin\ListingMedia;
use App\Models\admin\propertyFeatures;
use App\Models\admin\PropertyStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\admin\Listing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\admin\PropertyType;
use App\Models\admin\SearchHistory;
use App\Models\admin\cities;
use App\Models\admin\PropertyKpi;
use Illuminate\Support\Facades\Storage;

use  \Illuminate\Support\Facades\Facade;
class ListingController extends Controller
{


    // public function index()
    // {
    //     $listings = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus','features' ])->get();
        
    //     $listings->transform(function ($listing) {
    //         $listing->country_name = $listing->country->name; // Adding country name dynamically
    //         return $listing;
    //     });

    //     return response()->json([$listings],201);
    // }


//     public function index()
// {
//     $listings = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features'])->orderBy('id', 'desc')->get();
    
//     $listings->transform(function ($listing) {
//         $listing->country_name = $listing->country->country_name; // Ensure 'country_name' is accessed correctly
//         return $listing;
//     });

//     return response()->json($listings, 201);
// }

public function index()
{
    // Check if the authenticated user is an admin
    if (Auth::check() && Auth::user()->role === 'admin') {
        $listings = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features'])
            ->orderBy('id', 'desc')
            ->get();
    } else {
        $listings = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features'])
            ->where('user_id', Auth::id()) // Filter by logged-in user's ID
            ->orderBy('id', 'desc')
            ->get();
    }

    $listings->transform(function ($listing) {
        $listing->country_name = $listing->country->country_name; // Ensure 'country_name' is accessed correctly
        return $listing;
    });

    return response()->json($listings, 200);
}



    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Unauthorized. Please log in.',
            ], 401); 
        }
    
        $validatedData = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'country_id' => 'required|exists:countries,id',
            'property_type_id' => 'required|exists:property_types,id',
            'property_status_id' => 'required|exists:property_statuses,id',
            'price' => 'required|numeric',
            'listing_date' => 'required|date',
            'square_foot' => 'nullable|numeric',
            'parking' => 'nullable|string',
            'year_built' => 'nullable|integer',
            'lot_size' => 'nullable|numeric',
            'longitude' => 'nullable|string',
            'latitude' => 'nullable|string',
            'school_district' => 'nullable|string',
            'walkability_score' => 'nullable|integer',
            'crime_rate' => 'nullable|numeric',
            'roi' => 'nullable|numeric',
            'monthly_rent' => 'nullable|numeric',
            'cap_rate' => 'nullable|numeric',
            'address' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'half_bathrooms' => 'nullable|integer',
            'arv' => 'nullable|numeric',
            'gross_margin' => 'nullable|numeric',
            'is_featured' => 'nullable|in:0,1', 
            'is_approved' => 'nullable|in:0,1', 
            'estimated_roi' => 'nullable|numeric',
            'geolocation_coordinates' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'area' => 'nullable|string',
            'gdrp_agreement' => 'nullable|string',
            'other_features' => 'nullable|array',
            'other_features.*' => 'exists:other_features,id', 
            'gdrp_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,zip|max:5120', 
            // 'Listing_media' => 'nullable|array',
            'Listing_media.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,zip|max:5120',

        ]);
              
    
        $user_id = Auth::id();
        
    
        $listing = Listing::create(array_merge($validatedData, ['user_id' => $user_id]));
    
       
        if($listing)
        {
            if($request->file('gdrp_image'))
            {
                $image = $request->file('gdrp_image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/Listings/Image/gdrp'), $imageName);
                $imagepath = 'uploads/Listings/Image/gdrp/' . $imageName;;
                $listing->gdrp_agreement = $imagepath;
                $listing->save();
            }


          
        }

        // if ($request->hasFile('Listing_media')) {
        //     foreach ($request->file('Listing_media') as $file) {
        //         dd($file);

        //         // Ensure the file is valid
        //         if ($file->isValid()) {
        //             // Generate a unique name for the file
        //             $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
        //             // Move the file to the uploads directory
        //             $file->move(public_path('uploads/Listings/Image'), $fileName);
                    
        //             // Save file path to the database
        //             ListingMedia::create([
        //                 'listing_id' => $listing->id,
        //                 'file_name' => $fileName,
        //                 'file_url' => 'uploads/Listings/Image/' . $fileName,
        //                 'media_type' => $file->getClientMimeType(),
        //             ]);
        //         }
        //     }
        // }
        

        if ($request->hasFile('Listing_media')) {
            // Check if it's an array of files or a single file
            $files = $request->file('Listing_media');  // this could be an array or a single file
        
            // Check if it's an array (multiple files)
            if (is_array($files)) {
                // dd($files);
                foreach ($files as $file) {
                    if ($file->isValid()) {
                        
                        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('uploads/Listings/Image'), $fileName);
        
                        ListingMedia::create([
                            'listing_id' => $listing->id,
                            'file_name' => $fileName,
                            'file_url' => 'uploads/Listings/Image/' . $fileName,
                            'media_type' => $file->getClientMimeType(),
                        ]);
                    }
                }
            } else {
                // Handle if only one file is sent
                if ($files->isValid()) {
                    
                    $fileName = time() . '_' . uniqid() . '.' . $files->getClientOriginalExtension();
                    $files->move(public_path('uploads/Listings/Image'), $fileName);
        
                    ListingMedia::create([
                        'listing_id' => $listing->id,
                        'file_name' => $fileName,
                        'file_url' => 'uploads/Listings/Image/' . $fileName,
                        'media_type' => $files->getClientMimeType(),
                    ]);
                }
            }
        } else {
            dd('No files received');
        }
                
        
        if ($listing) {
            if ($request->has('other_features') && count($request->other_features) > 0) {
                $propertyFeatures = [];
                foreach ($validatedData['other_features'] as $feature_id) {
                    $propertyFeatures[] = [
                        'listings_id' => $listing->id,
                        'feature_id' => $feature_id,
                    ];
                }
        
                if (!propertyFeatures::insert($propertyFeatures)) {
                    return response()->json([
                        'error' => 'Error inserting property features.',
                    ], 500); 
                }
            }
    
            return response()->json($listing, 201);
        } else {
            return response()->json([
                'error' => 'Error creating listing. Please try again.',
            ], 500); 
        }
    }
    


// public function store(Request $request)
// {
//     $validatedData = $request->validate([
//         'title' => 'required|string',
//         'description' => 'required|string',
//         'city_id' => 'required|exists:cities,id',
//         'country_id' => 'required|exists:countries,id',
//         'property_type_id' => 'required|exists:property_types,id',
//         'property_status_id' => 'required|exists:property_statuses,id',
//         'price' => 'required|numeric',
//         'listing_date' => 'required|date',
//         'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:2048', // Allow multiple files
//     ]);

//     $user_id = Auth::id();
//     $listing = Listing::create(array_merge($validatedData, ['user_id' => $user_id]));

//     if ($request->hasFile('media')) {
//         foreach ($request->file('media') as $file) {
//             // Generate a unique file name
//             $fileName = time() . '_' . $file->getClientOriginalName();

//             // Move the file to public/row_media
//             $filePath = public_path('row_media');
//             $file->move($filePath, $fileName);

//             // Copy the file to public/listing_media folder
//             $listingMediaPath = public_path('listing_media/' . $fileName);
//             copy($filePath . '/' . $fileName, $listingMediaPath);

//             // Save media metadata in the database
//             $mediaUrl = url('row_media/' . $fileName);
//             $listing->media()->create([
//                 'file_name' => $fileName,
//                 'file_url' => $mediaUrl,
//                 'media_type' => $file->getClientMimeType(),
//             ]);
//         }
//     }

//     return response()->json($listing->load('media'), 201);
// }
 




    public function show($id)
    {
        $listing = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus','features' ])
                      ->find($id);

    if ($listing) {
        $listing->country_name = $listing->country ? $listing->country->name : null;
        PropertyKpi::create(
            [
                'users_id'=>Auth::id(),
                'listing_id'=>$listing->id,
            ]
            );

        return response()->json($listing);
    } else {
        return response()->json(['message' => 'Property Not Found'], 404);
    }
    }

    // public function update(Request $request, $id)
    // {
    //     if (!Auth::check()) {
    //         return response()->json([
    //             'error' => 'Unauthorized. Please log in to update the listing.'
    //         ], 401); 
    //     }

    //     $validatedData = $request->validate([
    //         'title' => 'required|string',
    //         'description' => 'required|string',
    //         'city_id' => 'required|exists:cities,id',
    //         'country_id' => 'required|exists:countries,id',
    //         'property_type_id' => 'required|exists:property_types,id',
    //         'property_status_id' => 'required|exists:property_statuses,id',
    //         'price' => 'required|numeric',
    //         'listing_date' => 'required|date',
    //         'square_foot' => 'nullable|numeric',
    //         'parking' => 'nullable|string',
    //         'year_built' => 'nullable|integer',
    //         'lot_size' => 'nullable|numeric',
    //         'longitude' => 'nullable|numeric',
    //         'latitude' => 'nullable|numeric',
    //         'school_district' => 'nullable|string',
    //         'walkability_score' => 'nullable|integer',
    //         'crime_rate' => 'nullable|numeric',
    //         'roi' => 'nullable|numeric',
    //         'monthly_rent' => 'nullable|numeric',
    //         'cap_rate' => 'nullable|numeric',
    //         'address' => 'nullable|string',
    //         'bedrooms' => 'nullable|integer',
    //         'bathrooms' => 'nullable|integer',
    //         'half_bathrooms' => 'nullable|integer',
    //         'arv' => 'nullable|numeric',
    //         'gross_margin' => 'nullable|numeric',
    //         'is_featured' => 'nullable|in:0,1', 
    //         'is_approved' => 'nullable|in:0,1', 
    //         'estimated_roi' => 'nullable|numeric',
    //         'geolocation_coordinates' => 'nullable|string',
    //         'zip_code' => 'nullable|string',
    //         'area' => 'nullable|string',
    //         'gdrp_agreement' => 'nullable|string',
    //         'other_features' => 'nullable|array', 
    //         'other_features.*' => 'exists:other_features,id|integer',
    //         'gdrp_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,zip|max:5120', 
    
    //     ]);

    //     $user_id = Auth::id();
    //     // Find and update the listing
    //     $listing = Listing::find($id);
    //     if (!$listing) {
    //         return response()->json([
    //             'error' => 'Listing not found.'
    //         ], 404); // Not found error code
    //     }


    //     if ($request->hasFile('gdrp_image')) {
    //         $image = $request->file('gdrp_image');
    //         $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension(); // Adding uniqid for uniqueness
    //         $imagePath = public_path('uploads/Listings/Image');
            
    //         // Ensure the directory exists
    //         if (!file_exists($imagePath)) {
    //             mkdir($imagePath, 0775, true); // Create directory if it doesn't exist
    //         }
    
    //         // Move the image to the uploads folder
    //         $image->move($imagePath, $imageName);
    
    //         // Update the gdrp_agreement path in the database
    //         $listing->gdrp_agreement = 'uploads/Listings/Image/' . $imageName;
    //     }

    //     $listing->update(array_merge($validatedData, ['user_id' => $user_id]));
    //     // if ($request->has('other_features') && count($request->other_features) > 0) {
    //     //     // Filter only valid feature IDs
    //     //     $validFeatureIds = propertyFeatures::whereIn('id', $validatedData['other_features'])->pluck('id')->toArray();
    //     //     if ($validFeatureIds) {
    //     //         // Sync the valid features (this detaches old features and attaches the new ones in one step)
    //     //         $listing->propertyFeatures()->sync($validFeatureIds);
    //     //     }
    //     // }
       

    //     if ($request->has('other_features') && is_array($request->other_features)) {
    //         $validFeatureIds = DB::table('other_features')
    //             ->whereIn('id', $validatedData['other_features'])
    //             ->pluck('id')
    //             ->toArray();
        
    //         $dataToInsert = [];
    //         foreach ($validFeatureIds as $featureId) {
    //             $dataToInsert[] = [
    //                 'listings_id' => $id, 
    //                 'feature_id' => $featureId, 
    //             ];
    //         }
        
    //         // Perform a bulk insert into the property_feature table
    //         if (!empty($dataToInsert)) {
    //             DB::table('property_feature')->insert($dataToInsert);
    //         }
    //     }
        


        
    //     return response()->json($listing);
    // }

    //update uncommmet if making any issye




    public function update(Request $request, $id)
{
    if (!Auth::check()) {
        return response()->json([
            'error' => 'Unauthorized. Please log in to update the listing.'
        ], 401); 
    }

    $validatedData = $request->validate([
        'title' => 'required|string',
        'description' => 'required|string',
        'city_id' => 'required|exists:cities,id',
        'country_id' => 'required|exists:countries,id',
        'property_type_id' => 'required|exists:property_types,id',
        'property_status_id' => 'required|exists:property_statuses,id',
        'price' => 'required|numeric',
        'listing_date' => 'required|date',
        'square_foot' => 'nullable|numeric',
        'parking' => 'nullable|string',
        'year_built' => 'nullable|integer',
        'lot_size' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'latitude' => 'nullable|numeric',
        'school_district' => 'nullable|string',
        'walkability_score' => 'nullable|integer',
        'crime_rate' => 'nullable|numeric',
        'roi' => 'nullable|numeric',
        'monthly_rent' => 'nullable|numeric',
        'cap_rate' => 'nullable|numeric',
        'address' => 'nullable|string',
        'bedrooms' => 'nullable|integer',
        'bathrooms' => 'nullable|integer',
        'half_bathrooms' => 'nullable|integer',
        'arv' => 'nullable|numeric',
        'gross_margin' => 'nullable|numeric',
        'is_featured' => 'nullable|in:0,1', 
        'is_approved' => 'nullable|in:0,1', 
        'estimated_roi' => 'nullable|numeric',
        'geolocation_coordinates' => 'nullable|string',
        'zip_code' => 'nullable|string',
        'area' => 'nullable|string',
        'gdrp_agreement' => 'nullable|string',
        'other_features' => 'nullable|array', 
        'other_features.*' => 'exists:other_features,id|integer',
        'gdrp_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,zip|max:5120',
        'Listing_media.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,zip|max:5120', 
    ]);

    $user_id = Auth::id();
    
    // Find and update the listing
    $listing = Listing::find($id);
    if (!$listing) {
        return response()->json([
            'error' => 'Listing not found.'
        ], 404);
    }

    // Handle GDPR Image Update
    if ($request->hasFile('gdrp_image')) {
        // // Delete old GDPR image if exists
        // if ($listing->gdrp_agreement && file_exists(public_path($listing->gdrp_agreement))) {
        //     unlink(public_path($listing->gdrp_agreement));
        // }

        $image = $request->file('gdrp_image');
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $imagePath = 'uploads/Listings/Image/gdrp/';
        
        $image->move(public_path($imagePath), $imageName);

        $listing->gdrp_agreement = $imagePath . $imageName;
    }

    if ($request->hasFile('Listing_media')) {
        $oldMedia = ListingMedia::where('listing_id', $id)->get();
        foreach ($oldMedia as $media) {
            if (file_exists(public_path($media->file_url))) {
                unlink(public_path($media->file_url));
            }
            $media->delete();
        }

        foreach ($request->file('Listing_media') as $file) {
            if ($file->isValid()) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'uploads/Listings/Image/';
                
                $file->move(public_path($filePath), $fileName);

                ListingMedia::create([
                    'listing_id' => $listing->id,
                    'file_name' => $fileName,
                    'file_url' => $filePath . $fileName,
                    'media_type' => $file->getClientMimeType(),
                ]);
            }
        }
    }

    // Update Listing
    $listing->update(array_merge($validatedData, ['user_id' => $user_id]));

    // Update Property Features
    if ($request->has('other_features') && is_array($request->other_features)) {
        // Get valid features
        $validFeatureIds = DB::table('other_features')
            ->whereIn('id', $validatedData['other_features'])
            ->pluck('id')
            ->toArray();

        // Sync Features (this replaces old with new ones)
        $listing->propertyFeatures()->sync($validFeatureIds);
    }

    return response()->json([
        'message' => 'Listing updated successfully.',
        'listing' => $listing
    ], 200);
}






    // public function destroy($id)
    // {
    //     if (!Auth::check()) {
    //         return response()->json([
    //             'error' => 'Unauthorized. Please log in to delete the listing.'
    //         ], 401); // Unauthorized error code
    //     }
    //     // Find the listing
    //     $listing = Listing::findOrFail($id);
    
    //     // Detach all related property features (removes entries from the pivot table)
    //     $listing->propertyFeatures()->detach();
    
    //     // Delete the listing itself
    //     $listing->delete();
    
    //     return response()->json(['message' => 'Listing deleted successfully']);
    // }

    public function destroy($id)
{
    if (!Auth::check()) {
        return response()->json([
            'error' => 'Unauthorized. Please log in to delete the listing.'
        ], 401); 
    }

    $user = Auth::user(); 
    $user_id = $user->id;
    
    $listing = Listing::find($id);
    if (!$listing) {
        return response()->json([
            'error' => 'Listing not found.'
        ], 404); 
    }

    if ($user->role === 'admin' || $listing->user_id === $user_id) {
        $listing->propertyFeatures()->detach();

        $listing->delete();

        return response()->json(['message' => 'Listing deleted successfully']);
    } else {
        return response()->json([
            'error' => 'You are not authorized to delete this listing.'
        ], 403); 
    }
}

    

    public function searchProperties(Request $request)
    {

        if (Auth::check()) {
            SearchHistory::create([
                'user_id' => Auth::id(),
                'price_min' => $request->price_min,
                'price_max' => $request->price_max,
                'city' => $request->city,
                'area_min' => $request->area_min,
                'area_max' => $request->area_max,
            ]);
        }

        $query = Listing::query();

        if ($request->has('price_min') && $request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }
        
        if ($request->has('price_max') && $request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->has('city') && $request->city) {
            $query->where('city_id', '=', $request->city);
        }

        if ($request->has('area_min') && $request->area_min) {
            $query->where('square_foot', '>=', $request->area_min);
        }

        if ($request->has('area_max') && $request->area_max) {
            $query->where('square_foot', '<=', $request->area_max);
        }

        if ($request->has('property_type') && $request->property_type) {
            $query->where('property_type_id', '=', $request->property_type);
        }

        $properties = $query->paginate(10);

        return response()->json($properties);
    }


    public function getUserSearchHistory()
{
    $history = SearchHistory::where('user_id', Auth::id())->get();
    
    return response()->json($history);
}


public function getPropertyType()
{
    $propertyType = PropertyType::all();
    return response()->json($propertyType); 
}
public function storePropertyType(Request $request)
{
    $validatedData = $request->validate([
        'title' => 'required|string|unique:property_types,title',
    ]);

    $propertyType = PropertyType::create($validatedData);

    return response()->json($propertyType, 201);
}


public function UpdatePropertyType(Request $request, $id)
{
    $validatedData = $request->validate([
        'title' => 'required|string|unique:property_types,title,' . $id,
    ]);

    $propertyType = PropertyType::findOrFail($id);
    $propertyType->update($validatedData,201);

    return response()->json($propertyType);
}

public function DeletePropertyType($id)
{
    $propertyType = PropertyType::findOrFail($id);
    $propertyType->delete();

    return response()->json(['message' => 'Property type deleted successfully']);   
}

public function showSinglePropertyType($id)
{
    $propertyType = PropertyType::findOrFail($id);

    return response()->json($propertyType);

}

public function property_Status(Request $request)
{
    $propertyStatus = PropertyStatus::all();
    return response()->json($propertyStatus); 
}

public function storePropertyStatus(Request $request)
{
    $validatedData = $request->validate([
        'status' => 'required|string|unique:property_statuses,status',
    ]);

    $propertyStatus = PropertyStatus::create($validatedData);

    return response()->json($propertyStatus, 201);  
}


public function updatePropertyStatus(Request $request, $id)
{
    $validatedData = $request->validate([
        'status' => 'required|string|unique:property_statuses,status,' . $id,
    ]);

    $propertyStatus = PropertyStatus::findOrFail($id);
    $propertyStatus->update($validatedData);

    return response()->json($propertyStatus);
}

public function deletePropertyStatus(Request $request, $id)
{
    $propertyStatus = PropertyStatus::findOrFail($id);
    $propertyStatus->delete();

    return response()->json(['message' => 'Property status deleted successfully']);
}
public function show_single_Status($id)
{
    $propertyStatus = PropertyStatus::findOrFail($id);

    return response()->json($propertyStatus);
}



public function gdrpAggrement_temp(Request $request)
{
    $request->validate([
    'files' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,zip|max:5120', 
    ]);

    
    if ($request->hasFile('files')) {
        $image = $request->file('files');
        $imageName = time() . '_' . $image->getClientOriginalName(); 
        $image->move(public_path('uploads/Listings/temp'), $imageName); 

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image_path' => url('uploads/Listings/temp/' . $imageName), 
        ], 200);
    }

    return response()->json(['message' => 'No image uploaded'], 400);
}

}