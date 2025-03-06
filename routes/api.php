<?php

use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountriesController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LeadSourceController;
use App\Http\Controllers\Admin\LeadTypeController;
use App\Http\Controllers\Admin\skiptraceController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TempDataController;
use App\Http\Controllers\Website\ListingsController;
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
use App\Http\Controllers\Admin\PropertyKpiController;
use App\Http\Controllers\Admin\SavedPropertyController;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\mls_data\MlsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyValuationController;

Route::apiResource('post', PostController::class);
Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
Route::post('Logout',[AuthController::class,'Logout'])->middleware('auth:sanctum');
Route::put('User/update/{id}',[AuthController::class,'update'])->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    Route::get('show_single_user/{id}', [AuthController::class, 'show_single_user']); // Get the authenticated user
    // List all listings
    Route::get('/listings', [ListingController::class, 'index']);  

    //serch properties listing
    Route::post('/listings/search', [ListingController::class, 'searchProperties']);
    // Create a new listing
    Route::post('/listings', [ListingController::class, 'store']);  
    // Show a specific listing
    Route::get('/listings/{id}', [ListingController::class, 'show']);  
    // Update a specific listing
    Route::put('/listings/{id}', [ListingController::class, 'update']);
    // Delete a specific listing
    Route::delete('/listings/{id}', [ListingController::class, 'destroy']);
    //gdrp temp image store
    Route::post('/files_temp', [ListingController::class,'gdrpAggrement_temp']);


    // Route::post('temp_files' , [TempDataController::class, 'tempUpload']);
    //listing image store
    




    //High ROI Zones listings
    Route::get('highroizone', [ListingController::class, 'Get_High_Roi_zone']);
    Route::get('lowroizone', [ListingController::class, 'Low_High_Roi_zone']);






    Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('/payment-success', [PaymentController::class, 'paymentSuccess']);




    // //List of Packages
    // Route::get('/packages', [PackagesController::class, 'index']);

    // // Create a new package
    // Route::post('/packages', [PackagesController::class, 'store']);

    // // Update an existing package
    // Route::put('/packages/{id}', [PackagesController::class, 'update']);

    // // Delete a package
    // Route::delete('/packages/{id}', [PackagesController::class, 'destroy']);

    // //single Package

    // Route::get('/packages/{id}', [PackagesController::class, 'show']);



    // List of Packages
Route::get('/packages', [PackagesController::class, 'index']);

// Create a new package
Route::post('/packages', [PackagesController::class, 'store']);

// Update an existing package
Route::put('/packages/{id}', [PackagesController::class, 'update']);

// Delete a package
Route::delete('/packages/{id}', [PackagesController::class, 'destroy']);

// Show a single package
Route::get('/packages/{id}', [PackagesController::class, 'show']);

// // Create a new subscription for a user
// Route::post('/subscriptions', [SubscriptionController::class, 'store']);  // Create a new subscription and handle Stripe payment

// // Get all subscriptions (with user and package information)
// Route::get('/subscriptions', [SubscriptionController::class, 'index']);  

// // Show a specific subscription
// Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);  

// // Cancel a subscription
// Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancelSubscription']);  // Cancel subscription on Stripe

// // Update an existing subscription
// Route::put('/subscriptions/{id}', [SubscriptionController::class, 'update']);  // Update subscription details




    // List all offers
    Route::get('/offers', [OfferController::class, 'index']);

    // offer conversions rate
    Route::get('/offerConversionRate/{id}', [OfferController::class, 'offerConversionRate']);

    // Show a specific offer
    Route::get('/offers/{id}', [OfferController::class, 'show']);
    //show offer listing id wise
    Route::get('/offers/listing/{id}', [OfferController::class, 'show_offer_listing_wise']);

    //offer history create
    Route::post('/offer/{offer_id}/history', [OfferController::class, 'createOfferHistory']);
    //show offer history
    Route::get('/offer/{offer_id}/history', [OfferController::class, 'showOfferHistory']);

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
    // Get All Messages for admin only
    Route::get('/getAllConversations', [MessagesController::class, 'getAllConversations']);



    //send notification
    Route::post('/notifications', [NotificationController::class, 'store']);
    //single user notification 
    Route::get('/notifications/{id}', [NotificationController::class, 'index']);
    //mark as read
    Route::put('/notifications', [NotificationController::class, 'markAsRead']);
    












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

    //show kpi data
    Route::get('Kpi-show/{id}', [PropertyKpiController::class,'show']);
    //show accepted offer percentage
    Route::get('offer-accept/{id}', [PropertyKpiController::class,'getAcceptedOfferPercentage']);
    

    Route::get('/user-logs', [AuthController::class, 'UserLog']);       

    // Route to get a single user log (any authenticated user)
    Route::get('/user-logs/{id}', [AuthController::class, 'UserLog_single']);
    
    //Payment History
    Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);

    //create subscription
    Route::post('/create-subscription', [PaymentController::class, 'createSubscription']);



    Route::get('/vendors', [VendorController::class, 'index']);
    Route::post('/vendors', [VendorController::class, 'store']); 
    Route::get('/vendors/{id}', [VendorController::class, 'show']);
    Route::put('/vendors/{id}', [VendorController::class, 'update']); 
    Route::delete('/vendors/{id}', [VendorController::class, 'destroy']);



        // Show saved properties based on user role
        Route::get('/saved-properties', [SavedPropertyController::class, 'savedProperties']);
    
        // Add Saved Property
        Route::post('/saved-properties/add', [SavedPropertyController::class, 'addSavedProperty']);
    
        // Delete Saved Property
        Route::delete('/saved-properties/delete/{id}', [SavedPropertyController::class, 'deleteSavedProperty']);
    

        //skip trace 
        Route::post('/skiptrace', [SkipTraceController::class, 'store']);
        Route::put('/skiptrace/{id}', [skiptraceController::class, 'update']);
        Route::delete('/skiptrace/{id}', [SkiptraceController::class, 'destroy']);
        Route::get('/skiptrace', [SkiptraceController::class, 'index']);



        Route::post('/payment/create-intent', [SkipTraceController::class, 'createPaymentIntent']);
        Route::post('/payment/store-transaction', [SkipTraceController::class, 'storeTransaction']);

        //notification working

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications', [NotificationController::class, 'store']);
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::get('/notifications/{id}/redirect', [NotificationController::class, 'getRedirectLink']);
    


        //mls data third party
        Route::get('/mls-data', [MlsController::class, 'index']);
        //mls data search filter
        Route::get('/mls-data/search', [MlsController::class, 'filter_Data']);

        //get all mls data
        Route::post('mls/filter-data' ,  [MlsController::class,  'filterData']);
});



//webiste apis will run on website
// Route::prefix('website')->group(function () {
//     Route::get('listings', [ListingsController::class, 'index']);
//     Route::get('listings/{id}', [ListingsController::class, 'show']);
// });


//website login routes

Route::prefix('website')->middleware('auth:sanctum')->group(function () {
    Route::get('listings/auth', [ListingsController::class, 'index']);
    Route::get('listings/auth/{id}', [ListingsController::class, 'show']);
});



Route::prefix('website')->group(function () {
    Route::get('listing', [ListingsController::class, 'NotLogin_index']);
    Route::get('listing/{id}', [ListingsController::class, 'show']);
      //mls data third party
      Route::get('/mls-data-website', [MlsController::class, 'index']);
      //mls data search filter
      Route::get('/mls-data-website/search', [MlsController::class, 'filter_Data']);

      //get all mls data
      Route::post('mls-website/filter-data' ,  [MlsController::class,  'filterData']);

      Route::get('cities/{id}', [CityController::class, 'show']);

      Route::get('cities', [CityController::class, 'index']);

    //   Property types
      Route::get('propertiestypes', [ListingController::class, 'getPropertyType']);

      //lead type
      Route::get('/lead-types', [LeadTypeController::class, 'GetLeadType']);

          //serch properties listing
      Route::post('/listings/search', [ListingController::class, 'searchProperties']);


    // property valuation record
    Route::post('/property-valuation', [PropertyValuationController::class, 'store']);

    //get all valuations records

    Route::get('/property-valuation', [PropertyValuationController::class, 'index']);

    //get single valuation record
    Route::get('/property-valuation/{id}', [PropertyValuationController::class, 'show']);

    //contact form
    Route::post('/contact-form', [ContactFormController::class, 'store']);

    //get all contact form
    Route::get('/contact-form', [ContactFormController::class, 'index']);

    //get single contact form
    Route::get('/contact-form/{id}', [ContactFormController::class, 'show']);

    //get cities whose listings exist
    Route::get('/cities_home', [MlsController::class, 'city_data_Home_page_api']);

});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');






    Route::post('admin/temp_files' , [TempDataController::class, 'tempUpload']);
    //listing image store




// Route::get("/", function () {
//     return "APi";
// });