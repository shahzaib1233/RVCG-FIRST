<?php
namespace App\Http\Controllers\admin;
use App\Models\admin\ListingMedia;
use App\Models\admin\propertyFeatures;
use App\Models\admin\PropertyStatus;
use App\Models\admin\SavedProperty;
use App\Models\admin\Skiptrace;
use App\Models\TempData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\admin\Listing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\admin\PropertyType;
use App\Models\admin\SearchHistory;
use App\Models\admin\cities;
use App\Models\admin\PropertyKpi;
use App\Models\Notification;
use App\Models\User;
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
    $listings = Listing::with(['city','media', 'user', 'country', 'propertyType', 'propertyStatus', 'features' , 'leadtypes'])
        ->orderBy('id', 'desc')
        ->get();

    $listings->transform(function ($listing) {
       
        $listing->country_name = $listing->country->country_name; 
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
            'monthly_rent' => 'nullable|numeric',
            'cap_rate' => 'nullable|numeric',
            'address' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'half_bathrooms' => 'nullable|integer',
            'gross_margin' => 'nullable|numeric',
            'is_featured' => 'nullable|in:0,1', 
            'is_approved' => 'nullable|in:0,1', 
            'estimated_roi' => 'nullable|numeric',
            'geolocation_coordinates' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'area' => 'nullable|string',
            'other_features' => 'nullable|array',
            'other_features.*' => 'exists:other_features,id', 
            'repair_cost' => 'nullable|numeric', // New Validation
            'wholesale_fee' => 'nullable|numeric', // New Validation
            'Listing_media' => 'nullable|array',
            'Listing_media.*' => 'exists:temp_data,id',
            'price_per_square_feet' => 'nullable|numeric',
            'owner_full_name' => 'required|string|max:255',
            'owner_age' => 'nullable|numeric',
            'owner_contact_number' => 'nullable|string|max:20',
            'owner_email_address' => 'nullable|email|max:255',
            'owner_government_id_proof' => 'nullable|string',
            'owner_property_ownership_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'owner_ownership_type' => 'nullable|in:Freehold,Leasehold,Joint Ownership',
            'owner_property_documents' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'lead_types_id'=> 'required|exists:lead_types,id',
        ]);
        $request->merge(['gdrp_agreement' => $request->gdrp_image]);

        if ($request->hasFile('owner_property_documents')) {
            $file = $request->file('owner_property_documents');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/listings/owner_property_documents'), $filename);
            $validatedData['owner_property_documents'] = 'uploads/listings/owner_property_documents/' . $filename;
        }
        


    //     $avg_price_per_sq_ft = Listing::where('city_id', $validatedData['city_id'])
    //     ->where('property_type_id', $validatedData['property_type_id'])
    //     ->where('square_foot', '>', 0)
    //     ->avg(DB::raw('price / square_foot'));

    //     $arv = $avg_price_per_sq_ft ? ($avg_price_per_sq_ft * $validatedData['square_foot']) : null;


    // // if (!empty($validatedData['price']) && !empty($validatedData['square_foot']) && $validatedData['square_foot'] > 0) {
    // //     $validatedData['price_per_square_feet'] = $validatedData['price'] / $validatedData['square_foot'];
    // // } else {
    // //     $validatedData['price_per_square_feet'] = null;
    // // }

    // if($arv > 0)
    // {
    // $moa = ($arv*0.70) + (($validatedData['repair_cost'] +$validatedData['wholesale_fee'] )  ?? 0); 
    // }
    // else
    // $moa = 0;
    // $roi = $arv && $validatedData['price'] ? (($arv - ($validatedData['repair_cost'] ?? 0)) / $validatedData['price']) * 100 : null;

        $user_id = Auth::id();
        
    
        $listing = Listing::create(array_merge($validatedData, ['user_id' => $user_id ]));
    
       
        if ($listing) {
            // Check if gdrp_agreement ID is provided
            if ($request->filled('gdrp_agreement')) {
                // Find the temp data using the provided ID
                $tempData = TempData::find($request->gdrp_agreement);
    
                if ($tempData) {
                    $tempFilePath = public_path($tempData->file_url);
                    $finalPath = public_path('uploads/Listings/Image/gdrp/');
                    
                    // Check if directory exists, otherwise create it
                    if (!is_dir($finalPath)) {
                        mkdir($finalPath, 0777, true);
                    }
    
                    // Move the file to the final destination
                    $newFileName = time() . '_' . uniqid() . '.' . pathinfo($tempFilePath, PATHINFO_EXTENSION);
                    $finalFilePath = $finalPath . $newFileName;
                    if (file_exists($tempFilePath)) {
                        rename($tempFilePath, $finalFilePath);
                        $listing->gdrp_agreement = 'uploads/Listings/Image/gdrp/' . $newFileName;
                        $listing->save();
                        
                        // Delete the temp data record
                        $tempData->delete();
                    }
                }
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
        

        if ($request->filled('Listing_media') && is_array($request->Listing_media)) {
            // Loop through each ID

            foreach ($request->Listing_media as $tempId) {
                // Find the temp data using the provided ID
                $tempData = TempData::find($tempId);

                if ($tempData) {
                    $tempFilePath = public_path($tempData->file_url);
                    $finalPath = public_path('uploads/Listings/Image/');

                    // Check if directory exists, otherwise create it
                    if (!is_dir($finalPath)) {
                        mkdir($finalPath, 0777, true);
                    }

                    // Move the file to the final destination
                    $newFileName = time() . '_' . uniqid() . '.' . pathinfo($tempFilePath, PATHINFO_EXTENSION);
                    $finalFilePath = $finalPath . $newFileName;
                    
                    if (file_exists($tempFilePath)) {
                        rename($tempFilePath, $finalFilePath);

                        // Save the image in ListingMedia table
                        ListingMedia::create([
                            'listing_id' => $listing->id,
                            'file_name' => $newFileName,
                            'file_url' => 'uploads/Listings/Image/' . $newFileName,
                            'media_type' => mime_content_type($finalFilePath),
                        ]);

                        // Delete the temp data record
                        $tempData->delete();
                    }
                }
            }
        }

                
        
        if ($listing) {
            if ($request->has('other_features') && is_array($validatedData['other_features']) && count($validatedData['other_features']) > 0) {
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
            $this->notifyUsersAboutNewListing($listing);

    
            return response()->json($listing, 201);
        } else {
            return response()->json([
                'error' => 'Error creating listing. Please try again.',
            ], 500); 
        }
    }
    


    //create notificastion if it si matching to users search and likes
    private function notifyUsersAboutNewListing($listing)
{
    $users = User::all();

    foreach ($users as $user) {
        $userId = $user->id;

        $favoriteListingIds = SavedProperty::where('user_id', $userId)
            ->where('is_favourite', true)
            ->pluck('listing_id')
            ->toArray();

        $frequentViewedIds = PropertyKpi::where('users_id', $userId)
            ->orderBy('views', 'desc')
            ->pluck('listing_id')
            ->toArray();

        $prioritizedIds = array_unique(array_merge($favoriteListingIds, $frequentViewedIds));

        $interests = PropertyKpi::where('users_id', $userId)
            ->select('listing_id')
            ->with('listing')
            ->get()
            ->pluck('listing')
            ->filter()
            ->toArray();

        $preferredPropertyTypes = array_column($interests, 'property_type_id');
        $preferredCities = array_column($interests, 'city_id');
        $preferredCountries = array_column($interests, 'country_id');
        $preferredPriceRange = array_column($interests, 'price');

        $matches = false;

        if (in_array($listing->property_type_id, $preferredPropertyTypes)) {
            $matches = true;
        }

        if (in_array($listing->city_id, $preferredCities)) {
            $matches = true;
        }

        if (in_array($listing->country_id, $preferredCountries)) {
            $matches = true;
        }

        // Optional: Check if price is within user's preferred price range
        if (!empty($preferredPriceRange)) {
            $averagePrice = array_sum($preferredPriceRange) / count($preferredPriceRange);
            $priceDifference = abs($listing->price - $averagePrice);

            if ($priceDifference <= ($averagePrice * 0.2)) { // 20% price range flexibility
                $matches = true;
            }
        }

        // Step 4: Notify Users if a Match is Found
        if ($matches) {
            Notification::create([
                'user_id' => $userId,
                'listing_id' => $listing->id,
                'heading' => 'New Listing Match',
                'title' => 'A new property that matches your preferences has been listed!',
                'message' => 'A new property that matches your preferences has been listed!',
            ]);
        }
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

    $skiptrace = Skiptrace::where('user_id', Auth::id())
    ->where('listing_id', $id)
    ->get();
if (Auth::user()->role === 'admin') {
        $listing = Listing::with(['city', 'user', 'country', 'propertyType', 'propertyStatus', 'features' , 'leadtypes'])
                          ->find($id);
    } 
    else if(!$skiptrace->isEmpty())
    {
        $listing = Listing::with(['city', 'leadtypes','user', 'country', 'propertyType', 'propertyStatus', 'features'])
        ->find($id);
    }
    else {
        $listing = Listing::select([
                            'id', 
                            'title', 
                            'description', 
                            'city_id', 
                            'country_id', 
                            'property_type_id', 
                            'property_status_id', 
                            'listing_date', 
                            'price', 
                            'square_foot', 
                            'parking', 
                            'year_built', 
                            'lot_size', 
                            'longitude', 
                            'latitude', 
                            'school_district', 
                            'walkability_score', 
                            'crime_rate', 
                            'roi', 
                            'monthly_rent', 
                            'cap_rate', 
                            'geolocation_coordinates', 
                            'zip_code', 
                            'area', 
                            'gdrp_agreement', 
                            'address', 
                            'bedrooms', 
                            'bathrooms', 
                            'half_bathrooms', 
                            'arv', 
                            'gross_margin', 
                            'estimated_roi', 
                            'repair_cost', 
                            'wholesale_fee', 
                            'price_per_square_feet', 
                            'user_id', 
                            'created_at', 
                            'updated_at', 
                            'is_featured', 
                            'is_approved', 
                            'moa',
                            'owner_full_name'
                        ])
                        ->with(['city','leadtypes', 'user', 'country', 'propertyType', 'propertyStatus', 'features'])
                        ->find($id);
    }

    if (!$listing) {
        return response()->json(['message' => 'Property Not Found'], 404);
    }

    $listing->country_name = $listing->country ? $listing->country->name : null;

    if (Auth::check()) {
        PropertyKpi::create([
            'users_id' => Auth::id(),
            'listing_id' => $listing->id,
        ]);
    }

    return response()->json(
        $listing
   );
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
        'monthly_rent' => 'nullable|numeric',
        'cap_rate' => 'nullable|numeric',
        'address' => 'nullable|string',
        'bedrooms' => 'nullable|integer',
        'bathrooms' => 'nullable|integer',
        'half_bathrooms' => 'nullable|integer',
        'gross_margin' => 'nullable|numeric',
        'is_featured' => 'nullable|in:0,1', 
        'is_approved' => 'nullable|in:0,1', 
        'estimated_roi' => 'nullable|numeric',
        'geolocation_coordinates' => 'nullable|string',
        'zip_code' => 'nullable|string',
        'area' => 'nullable|string',
        'gdrp_agreement' => 'nullable|numeric|exists:temp_data,id',  // Now accepting ID
        'other_features' => 'nullable|array',
        'other_features.*' => 'exists:other_features,id', 
        'repair_cost' => 'nullable|numeric', // New Validation
        'wholesale_fee' => 'nullable|numeric', // New Validation
        'Listing_media' => 'nullable|array',
        'Listing_media.*' => 'exists:temp_data,id',
        'price_per_square_feet' => 'nullable|numeric',
        'owner_full_name' => 'required|string|max:255',
        'owner_age' => 'nullable|numeric',
        'owner_contact_number' => 'nullable|string|max:20',
        'owner_email_address' => 'nullable|email|max:255',
        'owner_government_id_proof' => 'nullable|string',
        'owner_property_ownership_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'owner_ownership_type' => 'nullable|in:Freehold,Leasehold,Joint Ownership',
        'owner_property_documents' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'lead_types_id' => `required|exists:lead_types,id`,

    ]);

    if ($request->hasFile('owner_property_documents')) {
        $file = $request->file('owner_property_documents');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/listings/owner_property_documents'), $filename);
        $validatedData['owner_property_documents'] = 'uploads/listings/owner_property_documents/' . $filename;
    }
 

    // $avg_price_per_sq_ft = Listing::where('city_id', $validatedData['city_id'])
    // ->where('property_type_id', $validatedData['property_type_id'])
    // ->where('square_foot', '>', 0)
    // ->avg(DB::raw('price / square_foot'));

    // // Calculate ARV directly
    // $arv = $avg_price_per_sq_ft ? ($avg_price_per_sq_ft * $validatedData['square_foot']) : null;


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
    $listing->update(array_merge($validatedData, ['user_id' => $user_id  ]));

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
    $query = Listing::with(['city', 'media', 'user', 'country', 'propertyType', 'propertyStatus', 'features', 'leadtypes']);
        
    // Price range filter
   // Price filter
if ($request->filled('price_min') && $request->price_min !== "") {
    $query->where('price', '>=', $request->price_min);
}
if ($request->filled('price_max') && $request->price_max !== "") {
    $query->where('price', '<=', $request->price_max);
}

// Address filter (city_id)
if ($request->filled('city') && $request->city !== "") {
    $query->where('city_id', '=', $request->city);
}

// Property type filter
if ($request->filled('property_type') && $request->property_type !== "") {
    $query->where('property_type_id', '=', $request->property_type);
}

// Bedrooms filter
if ($request->filled('bedrooms') && $request->bedrooms !== "") {
    $query->where('bedrooms', '>=', $request->bedrooms);
}

// Bathrooms filter
if ($request->filled('bathrooms') && $request->bathrooms !== "") {
    $query->where('bathrooms', '>=', $request->bathrooms);
}

// Square Foot filter
if ($request->filled('area_min') && $request->area_min !== "") {
    $query->where('square_foot', '>=', $request->area_min);
}

// Lead Type filter
if ($request->filled('lead_types_id') && !empty($request->lead_types_id)) {
    $query->whereIn('lead_types_id', $request->lead_types_id);
}

    

    // Check if user is logged in
    if (Auth::check()) {
        // Save search history for logged-in users
        SearchHistory::create([
            'user_id' => Auth::id(),
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'city_id' => $request->city,
            'area_min' => $request->area_min,
        ]);
    }

    // Pagination for better performance
    $properties = $query->get(); // 10 results per page

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












public function Get_High_Roi_zone()
{
//     $highROIListings = Listing::where(DB::raw('CAST(roi AS DECIMAL(10,2))'), '>', 15)
//     ->orderBy('roi', 'DESC')
//     ->get();
   
      
//     if ($highROIListings->isEmpty()) {
//         return response()->json(['message' => 'No high ROI zones found'], 404);
//     }

//     return response()->json($highROIListings);
// }
 $highROIListings = Listing::all()->filter(function ($listing) {
        return $listing->roi > 15; // Using accessor here
    })->sortByDesc('roi')->values(); // Sort and reset array keys
   
    if ($highROIListings->isEmpty()) {
        return response()->json(['message' => 'No high ROI zones found'], 404);
    }

    return response()->json($highROIListings);
}









public function Low_High_Roi_zone()
{
//     $highROIListings = Listing::where(DB::raw('CAST(roi AS DECIMAL(10,2))'), '>', 15)
//     ->orderBy('roi', 'DESC')
//     ->get();
   
      
//     if ($highROIListings->isEmpty()) {
//         return response()->json(['message' => 'No high ROI zones found'], 404);
//     }

//     return response()->json($highROIListings);
// }
$highROIListings = Listing::with('city') // Load city relationship
    ->get()
    ->filter(function ($listing) {
        return $listing->roi < 15; // Using accessor here
    })
    ->sortByDesc('roi')
    ->values(); // Sort and reset array keys

   
    if ($highROIListings->isEmpty()) {
        return response()->json(['message' => 'No high ROI zones found'], 404);
    }

    return response()->json($highROIListings);
}



}
