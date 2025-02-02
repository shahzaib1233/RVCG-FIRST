<?php

use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountriesController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LeadSourceController;
use App\Http\Controllers\Admin\LeadTypeController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ListingController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OtherFeatureController;
use App\Http\Controllers\Admin\PackagesController;
use App\Http\Controllers\Messages\Messages as MessagesController;
use App\Http\Controllers\Admin\EmailCampaignController;
use App\Http\Controllers\Admin\PackagesItemsController;
use App\Http\Controllers\Controller;

Route::apiResource('post', PostController::class);
Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
Route::post('Logout',[AuthController::class,'Logout'])->middleware('auth:sanctum');
Route::put('User/update/{id}',[AuthController::class,'update'])->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // List all listings
    Route::get('/listings', [ListingController::class, 'index']);  

    //serch properties listing
    Route::get('/listings/search', [ListingController::class, 'searchProperties']);
    // Create a new listing
    Route::post('/listings', [ListingController::class, 'store']);  
    // Show a specific listing
    Route::get('/listings/{id}', [ListingController::class, 'show']);  
    // Update a specific listing
    Route::put('/listings/{id}', [ListingController::class, 'update']);
    // Delete a specific listing
    Route::delete('/listings/{id}', [ListingController::class, 'destroy']);


    //List of Packages
    Route::get('/packages', [PackagesController::class, 'index']);

    // Create a new package
    Route::post('/packages', [PackagesController::class, 'store']);

    // Update an existing package
    Route::put('/packages/{id}', [PackagesController::class, 'update']);

    // Delete a package
    Route::delete('/packages/{id}', [PackagesController::class, 'destroy']);

    //single Package

    Route::get('/packages/{id}', [PackagesController::class, 'show']);

    // List all offers
    Route::get('/offers', [OfferController::class, 'index']);

    // offer conversions rate
    Route::get('/offerConversionRate/{id}', [OfferController::class, 'offerConversionRate']);

    // Show a specific offer
    Route::get('/offers/{id}', [OfferController::class, 'show']);
    // Create a new offer
    Route::post('/offers', [OfferController::class, 'store']);
    // Update an existing offer
    Route::put('/offers/{id}', [OfferController::class, 'update']);
    // Delete an offer
    Route::delete('/offers/{id}', [OfferController::class, 'destroy']);



    //list of all subscriptions 
    Route::get('/Subscription', [SubscriptionController::class, 'index']); // Get chat contacts
    // Create a new subscription
    Route::post('/Subscription', [SubscriptionController::class, 'store']);
    // Show a specific subscription
    Route::get('/Subscription/{id}', [SubscriptionController::class, 'show']);
    // Update an existing subscription
    Route::put('/Subscription/{id}', [SubscriptionController::class, 'update']);
    // Delete a subscription
    Route::delete('/Subscription/{id}', [SubscriptionController::class, 'destroy']);

    // List all messages
    Route::get('/messages/contacts', [MessagesController::class, 'getChatContacts']); 
    // Get chat contacts
    Route::get('/messages/{userId}', [MessagesController::class, 'getConversationMessages']);   
     // Get chat messages
    Route::post('/messages', [MessagesController::class, 'sendMessage']);  



    //send notification
    Route::post('/notifications', [NotificationController::class, 'store']);












    Route::get('countries', [CountriesController::class, 'index']);  // Get all countries
    Route::post('countries', [CountriesController::class, 'store']); // Create a new country
    Route::get('countries/{id}', [CountriesController::class, 'show']); // Show a specific country
    Route::put('countries/{id}', [CountriesController::class, 'update']); // Update a specific country
    Route::delete('countries/{id}', [CountriesController::class, 'destroy']); 



    // Get all cities along with their country
    Route::get('cities', [CityController::class, 'index']);
    
    // Create a new city
    Route::post('cities', [CityController::class, 'store']);
    
    // Show a specific city by ID
    Route::get('cities/{id}', [CityController::class, 'show']);
    
    // Update a specific city by ID
    Route::put('cities/{id}', [CityController::class, 'update']);
    
    // Delete a specific city by ID
    Route::delete('cities/{id}', [CityController::class, 'destroy']);

   //Get All Properties Type
   Route::get('propertiestypes', [ListingController::class, 'getPropertyType']);

   //Single Property
   Route::get('propertiestypes/{id}', [ListingController::class, 'showSinglePropertyType']);

   //update property Type
   Route::put('propertiestypes/{id}', [ListingController::class, 'UpdatePropertyType']);

   //delete property Type
   Route::delete('propertiestypes/{id}', [ListingController::class, 'DeletePropertyType']);

   //Create Property Type
   Route::post('propertiestypes', [ListingController::class, 'storePropertyType']);





Route::get('/property-statuses', [ListingController::class, 'property_Status']); // Get all property statuses
Route::post('/property-statuses', [ListingController::class, 'storePropertyStatus']); // Store a new property status
Route::put('/property-statuses/{id}', [ListingController::class, 'updatePropertyStatus']); // Update a specific property status
Route::delete('/property-statuses/{id}', [ListingController::class, 'deletePropertyStatus']); // Delete a specific property status
Route::get('/property-statuses/{id}', [ListingController::class, 'show_single_Status']); // Show a single property status




//other features

Route::get('/other-features', [OtherFeatureController::class, 'index']); // List all other features
Route::post('/other-features', [OtherFeatureController::class, 'store']); // Create a new feature
Route::put('/other-features/{id}', [OtherFeatureController::class, 'update']); // Update an existing feature
Route::delete('/other-features/{id}', [OtherFeatureController::class, 'destroy']); // Delete a feature




//leads

   // Leads Routes
   Route::get('leads', [LeadController::class, 'index']); // Get all leads
   Route::post('leads', [LeadController::class, 'store']); // Create a new lead
   Route::get('leads/{id}', [LeadController::class, 'show']); // Show a specific lead
   Route::put('leads/{id}', [LeadController::class, 'update']); // Update a specific lead
   Route::delete('leads/{id}', [LeadController::class, 'destroy']); // Delete a specific lead

   // Lead History Routes
   Route::post('/leads/history/{id}', [LeadController::class, 'addHistory']);

    // Get all lead histories
    Route::get('/leads/history/{id}', [LeadController::class, 'ViewHistoryLeads']);
   //update history of leads
   Route::put('leads/history/{lead_id}/{history_id}', [LeadController::class, 'updateHistory']);

    //test email campaigns
    Route::get('testemaillogin', [AuthController::class, 'sendTestEmail']);

    //get al emails
    
    Route::get('getallusers', [AuthController::class, 'GetAllUsers']);
    Route::post('/send-email-campaign', [EmailCampaignController::class, 'sendEmailCampaign']);
    Route::get('/get-email-record', [EmailCampaignController::class, 'getemailrecord']);
    Route::get('/showemailrecord/{id}', [EmailCampaignController::class, 'showemailrecord']);

    //Lead Types Routes
    Route::get('/lead-types', [LeadTypeController::class, 'GetLeadType']);

    // Route for adding a new lead type
    Route::post('/lead-types', [LeadTypeController::class, 'AddLeadType']);

    // Route for editing an existing lead type
    Route::put('/lead-types/{id}', [LeadTypeController::class, 'EditLeadType']);

    // Route for deleting an existing lead type
    Route::delete('/lead-types/{id}', [LeadTypeController::class, 'DeleteLeadType']);


    Route::get('lead-source', [LeadSourceController::class, 'index']);
    Route::post('lead-source', [LeadSourceController::class, 'store']);
    Route::get('lead-source/{id}', [LeadSourceController::class, 'show']);
    Route::put('lead-source/{id}', [LeadSourceController::class, 'update']);
    Route::delete('lead-source/{id}', [LeadSourceController::class, 'destroy']);


    //packages items routes

    Route::get('Package-items', [PackagesItemsController::class, 'index']); // List all package items
    Route::post('Package-items', [PackagesItemsController::class, 'store']); // Create new package item
    Route::get('Package-items/{id}', [PackagesItemsController::class, 'show']); // Show a package item
    Route::put('Package-items/{id}', [PackagesItemsController::class, 'update']); // Update a package item
    Route::delete('Package-items/{id}', [PackagesItemsController::class, 'destroy']); // Delete a package item



    //route for all users
    Route::get('GetAdminusers', [AuthController::class, 'GetAdminusers']);





    Route::get('/referrals/users/used', [AuthController::class, 'getUsersWhoseReferralCodeWasUsed']);

    Route::post('admin/testimage',[ListingController::class,'image']);

});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




// Route::get("/", function () {
//     return "APi";
// });