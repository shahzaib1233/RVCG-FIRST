<?php

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use App\Models\User;
// use App\Models\UserLog;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Str;


// class AuthController extends Controller
// {
// //     public function Register(Request $request)
// //     {
// //         $fields = $request->validate([
// //             'name' => 'required|max:255',
// //             'email' => 'required|email|unique:users',
// //             'password' => 'required|confirmed',
// //             'phone_number' => 'nullable|string',  // Add validation for phone number
// //             'address' => 'nullable|string',  // Add validation for address
// //             'social_media_profiles' => 'nullable|array',  // Add validation for social media profiles
// //             'bankruptcy_details' => 'nullable|string',  // Add validation for bankruptcy details
// //             'liens_details' => 'nullable|string',  // Add validation for liens details
// //             'contact_email' => 'nullable|email',  // Add validation for contact email
// //             'dob' => 'nullable|date',  // Add validation for date of birth
// //             'income_level' => 'nullable|string',  // Add validation for income level
// //         ]);
// //         $referralCode = Str::random(10);

// //         $user = User::create([
// //             'name' => $fields['name'],
// //             'email' => $fields['email'],
// //             'password' => Hash::make($fields['password']),
// //             'referral_code' => $referralCode,
// //             'phone_number' => $fields['phone_number'],  // Add phone number
// //             'address' => $fields['address'],  // Add address
// //             'social_media_profiles' => $fields['social_media_profiles'],  // Add social media profiles
// //             'bankruptcy_details' => $fields['bankruptcy_details'],  // Add bankruptcy details
// //             'liens_details' => $fields['liens_details'],  // Add liens details
// //             'contact_email' => $fields['contact_email'],  // Add contact email
// //             'dob' => $fields['dob'],  // Add date of birth
// //             'income_level' => $fields['income_level'],  // Add income level
// //         ]);    
        
// // if ($request->has('referral_code')) {
// //     $referrer = User::where('referral_code', $request->referral_code)->first();

// //     if ($referrer) {
// //         $user->referrer_id = $referrer->id;
// //         $user->save();
// //     }
// // }

// //         $token = $user->createToken($request->name);
        
// //         return [
// //             'user' => $user,
// //             'token' => $token
// //         ];
        

    
// //     }




// public function Register(Request $request)
// {
//     // Validate the required and optional fields
//     $fields = $request->validate([
//         'name' => 'required|max:255',
//         'email' => 'required|email|unique:users',
//         'password' => 'required|confirmed',
//         'phone_number' => 'nullable|string',
//         'address' => 'nullable|string',
//         'social_media_profiles' => 'nullable|array',
//         'bankruptcy_details' => 'nullable|string',
//         'liens_details' => 'nullable|string',
//         'contact_email' => 'nullable|email',
//         'dob' => 'nullable|date',
//         'income_level' => 'nullable|string',
//         'role' => 'nullable|string', 
//     ]);

//     // Generate a referral code
//     $referralCode = Str::random(10);

//     // Create the user with conditional fields
//     $user = User::create([
//         'name' => $fields['name'],
//         'email' => $fields['email'],
//         'password' => Hash::make($fields['password']),
//         'referral_code' => $referralCode,
//         'phone_number' => $fields['phone_number'] ?? null,
//         'address' => $fields['address'] ?? null,
//         'social_media_profiles' => $fields['social_media_profiles'] ?? null,
//         'bankruptcy_details' => $fields['bankruptcy_details'] ?? null,
//         'liens_details' => $fields['liens_details'] ?? null,
//         'contact_email' => $fields['contact_email'] ?? null,
//         'dob' => $fields['dob'] ?? null,
//         'income_level' => $fields['income_level'] ?? null,
//     ]);

//     // Handle referral code if provided
//     if ($request->has('referral_code')) {
//         $referrer = User::where('referral_code', $request->referral_code)->first();
//         if ($referrer) {
//             $user->referrer_id = $referrer->id;
//             $user->save();
//         }
//     }

//     // Create a token for the user
//     $token = $user->createToken($request->name);

//     // Return the response
//     return response()->json([
//         'user' => $user,
//         'token' => $token->plainTextToken,
//     ], 201);
// }



//     public function update(Request $request)
// {
//     $user = $request->user();

//     // Validate only the fields that might be updated
//     $validatedData = $request->validate([
//         'name' => 'nullable|max:255',
//         'email' => 'nullable|email|unique:users,email,' . $user->id,
//         'phone_number' => 'nullable|string',
//         'address' => 'nullable|string',
//         'social_media_profiles' => 'nullable|array',
//         'bankruptcy_details' => 'nullable|string',
//         'liens_details' => 'nullable|string',
//         'contact_email' => 'nullable|email',
//         'dob' => 'nullable|date',
//         'income_level' => 'nullable|string',
//         'password' => 'nullable|confirmed', // Only validate password if provided
//     ]);

//     // Update fields based on the request data
//     if ($request->has('name')) {
//         $user->name = $validatedData['name'];
//     }

//     if ($request->has('email')) {
//         $user->email = $validatedData['email'];
//     }

//     if ($request->has('phone_number')) {
//         $user->phone_number = $validatedData['phone_number'];
//     }

//     if ($request->has('address')) {
//         $user->address = $validatedData['address'];
//     }

//     if ($request->has('social_media_profiles')) {
//         $user->social_media_profiles = $validatedData['social_media_profiles'];
//     }

//     if ($request->has('bankruptcy_details')) {
//         $user->bankruptcy_details = $validatedData['bankruptcy_details'];
//     }

//     if ($request->has('liens_details')) {
//         $user->liens_details = $validatedData['liens_details'];
//     }

//     if ($request->has('contact_email')) {
//         $user->contact_email = $validatedData['contact_email'];
//     }

//     if ($request->has('dob')) {
//         $user->dob = $validatedData['dob'];
//     }

//     if ($request->has('income_level')) {
//         $user->income_level = $validatedData['income_level'];
//     }

//     // Update password if provided
//     if ($request->has('password')) {
//         $user->password = Hash::make($validatedData['password']);
//     }

//     // Save the updated user data
//     $user->save();

//     return response()->json(['message' => 'User updated successfully', 'user' => $user]);
// }





//     public function Login(Request $request)
//     {
        
//         $request->validate([
//             'email' => 'required|email|exists:users',
//             'password' => 'required'
//         ]);
//         $user = User::where('email', $request->email)->first();
//         if (!$user || !Hash::check($request->password, $user->password)) {
//             return response()->json([
//                 'message' => 'The provided credentials are not correct',
//                 'status' => 401
//             ], 401);
//         }
              

//         UserLog::create([
//             'user_id' => $user->id,
//             'login_at' => now(),
//             'ip_address' => $request->ip(),
//             'user_agent' => $request->header('User-Agent')
//         ]);

//         $token = $user->createToken($user->name);
        
//         return [
//             'user' => $user,
//             'token' => $token
//         ];
//     }
//     public function Logout(Request $request)
//     {
//         $request->user()->tokens()->delete();
//         return [
//             'message' => 'Logout Successfully'
//         ];
//     }
// }














namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Traits\EmailTrait;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\TextPart;
use Symfony\Component\Mime\Part\HtmlPart;



class AuthController extends Controller
{
    // use EmailTrait;

    
    // public function sendTestEmail($email, $User_name)
    // {
    //     $to = $email; // Recipient email
    //     $subject = 'Welcome to Rvcg First'; 
    //     $body = '<h1>Hello! ' . $User_name . '</h1><p>Thank you for joining our platform. You can Get Amazing Properties and much more here!</p>';
    
    //     // Create a new Email instance
    //     $email = new Email();
    //     $email->to($to)
    //           ->subject($subject)
    //           ->html($body);  // You can also use ->text($plainTextBody) if you need plain text version
    
    //     // Call the sendEmail function
    //     try {
    //         Mail::send($email);  // Send the email using the Mail facade
    //         return response()->json(['message' => 'Email sent successfully'], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Failed to send email', 'details' => $e->getMessage()], 500);
    //     }
    // }
    


   
public function sendTestEmail()
{
    $to = "shahzaibimtiazalone@gmail.com";  // Receiver email address
    $subject = 'Welcome to Rvcg First';  // Email subject

    // Static HTML message content
    $messageContent = '<h1>Hello Shahzaib Imtiaz</h1><p>Thank you for joining our platform. You can get amazing properties and much more here!</p>';

    // Send email
    Mail::send([], [], function ($message) use ($to, $subject, $messageContent) {
        $message->to($to)  // Receiver email
                ->subject($subject)  // Subject of the email
                ->html($messageContent);  // Static content as HTML
    });

    return response()->json(['message' => 'Test email sent successfully'], 200);
}

    // public function Register(Request $request)
    // {
    //     // Validate the fields
    //     $fields = $request->validate([
    //         'name' => 'required|max:255',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|confirmed',
    //         'phone_number' => 'nullable|string',
    //         'address' => 'nullable|string',
    //         'bankruptcy_details' => 'nullable|string',
    //         'liens_details' => 'nullable|string',
    //         'contact_email' => 'nullable|email',
    //         'dob' => 'nullable|date',
    //         'income_level' => 'nullable|string',
    //         'role' => 'nullable|string', // Added role field
    //         'email_verified_at' => 'nullable|date', // Added email_verified_at
    //         'social_media_profiles' => 'nullable|array|max:3', // Allow up to 3 social media links
    //         'social_media_profiles.*' => 'url', // Each must be a valid URL
    //         'referral_codes' => 'nullable|array|max:3', // Allow up to 3 referral codes
    //     ]);

    //     // Generate a unique referral code
    //     $referralCode = Str::random(10);

    //     // Create the user and handle optional fields
    //     $user = User::create([
    //         'name' => $fields['name'],
    //         'email' => $fields['email'],
    //         'password' => Hash::make($fields['password']),
    //         'referral_code' => $referralCode,
    //         'phone_number' => $fields['phone_number'] ?? null,
    //         'address' => $fields['address'] ?? null,
    //         'social_media_profiles' => $fields['social_media_profiles'] ?? null,
    //         'bankruptcy_details' => $fields['bankruptcy_details'] ?? null,
    //         'liens_details' => $fields['liens_details'] ?? null,
    //         'contact_email' => $fields['contact_email'] ?? null,
    //         'dob' => $fields['dob'] ?? null,
    //         'income_level' => $fields['income_level'] ?? null,
    //         'role' => $fields['role'] ?? 'user', // Default role as 'user'
    //         'email_verified_at' => $fields['email_verified_at'] ?? null,
    //     ]);

    //     // Handle referral code if provided
    //     if ($request->has('referral_code')) {
    //         $referrer = User::where('referral_code', $request->referral_code)->first();
    //         if ($referrer) {
    //             $user->referrer_id = $referrer->id;
    //             $user->save();
    //         }
    //     }

    //     // Create a token for the user
    //     $token = $user->createToken($request->name);
    //     $this->sendTestEmail($user->email, $user->name);        // Return the response
    //     return response()->json([
    //         'user' => $user,
    //         'token' => $token->plainTextToken,
    //     ], 201);
    // }



    public function Register(Request $request)
{
    // Validate the input fields
    $fields = $request->validate([
        'name' => 'required|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed',
        'phone_number' => 'nullable|string',
        'address' => 'nullable|string',
        'social_media_profiles' => 'nullable|array|max:3', // Max 3 social links
        'social_media_profiles.*' => 'url', // Validate each as a valid URL
        'referral_codes' => 'nullable|array|max:3', // Max 3 referral codes
        'referral_codes.*' => 'exists:users,referral_code', // Validate referral codes exist
        'bankruptcy_details' => 'nullable|string',
        'liens_details' => 'nullable|string',
        'contact_email' => 'nullable|email',
        'dob' => 'nullable|date',
        'income_level' => 'nullable|string',
        'role' => 'nullable|string', // Added role field
        'email_verified_at' => 'nullable|date', // Added email_verified_at
        'is_active' => 'nullable|boolean', // Added is_active field
    ]);

    // Generate a unique referral code for the new user
    $referralCode = Str::upper(Str::random(10)); // Example: "ABC123XYZ"

    // Fetch and validate the provided referral codes
    $referrers = [];
    if (!empty($fields['referral_codes'])) {
        $referrers = User::whereIn('referral_code', $fields['referral_codes'])->pluck('id')->toArray();
    }

    // Create the user
    $user = User::create([
        'name' => $fields['name'],
        'email' => $fields['email'],
        'password' => Hash::make($fields['password']),
        'referral_code' => $referralCode,
        'referrers' => $referrers, // Store referrer IDs as JSON
        'social_media_profiles' => $fields['social_media_profiles'] ?? null,
        'phone_number' => $fields['phone_number'] ?? null,
        'address' => $fields['address'] ?? null,
        'bankruptcy_details' => $fields['bankruptcy_details'] ?? null,
        'liens_details' => $fields['liens_details'] ?? null,
        'contact_email' => $fields['contact_email'] ?? null,
        'dob' => $fields['dob'] ?? null,
        'income_level' => $fields['income_level'] ?? null,
        'role' => $fields['role'] ?? 'user', // Default role as 'user'
        'email_verified_at' => $fields['email_verified_at'] ?? null,
        'is_active'=> $fields['is_active'] ?? true, // Default is_active as true
    ]);

    // Optionally reward referrers
    foreach ($referrers as $referrerId) {
        $this->rewardReferrer($referrerId);
    }

    // Generate and return the user token
    $token = $user->createToken($user->name);

    return response()->json([
        'user' => $user,
        'token' => $token->plainTextToken,
    ], 201);
}

protected function rewardReferrer($referrerId)
{
    $referrer = User::find($referrerId);
    if ($referrer) {
        $referrer->increment('reward_points', 10); // Example logic: add 10 reward points
    }
}




   
//get all users
public function GetAllUsers()
{
    $users_rec = Auth::user();
    if($users_rec->role === "admin")
    {
    $users = User::all();
    return response()->json($users);
}
else
{
    return response()->json(['message' => 'Unauthorized'], 403);

}
}



    
public function update(Request $request, $id)
{
    // Retrieve the user by ID
    $userupdate = User::find($id);

    // Check if user exists
    if (!$userupdate) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Validate the fields
    $validatedData = $request->validate([
        'name' => 'nullable|max:255',
        'email' => 'nullable|email|unique:users,email,' . $userupdate->id,
        'phone_number' => 'nullable|string',
        'address' => 'nullable|string',
        'social_media_profiles' => 'nullable|array|max:3', // Max 3 social links
        'social_media_profiles.*' => 'url', // Validate each as a valid URL
        'bankruptcy_details' => 'nullable|string',
        'liens_details' => 'nullable|string',
        'contact_email' => 'nullable|email',
        'dob' => 'nullable|date',
        'income_level' => 'nullable|string',
        'password' => 'nullable|confirmed',
        'role' => 'nullable|string', // Allow updating role
        'email_verified_at' => 'nullable|date', // Allow updating email verification
        'is_active' => 'nullable|boolean', // Allow updating is_active
    ]);

    // Update user fields conditionally
    foreach ($validatedData as $key => $value) {
        if ($request->has($key)) {
            $userupdate->{$key} = $value;
        }
    }

    // Hash the password if provided
    if ($request->has('password')) {
        $userupdate->password = Hash::make($validatedData['password']);
    }

    // Save updated user
    $userupdate->save();

    return response()->json(['message' => 'User updated successfully', 'user' => $userupdate]);
}


    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect',
                'status' => 401
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account is inactive. Please contact support.',
                'status' => 403
            ], 403);
        }


        UserLog::create([
            'user_id' => $user->id,
            'login_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        $token = $user->createToken($user->name);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ]);
    }

    public function Logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout successfully']);
    }

    public function GetAdminusers()
    {
        $user = Auth::user(); // Get the authenticated user
    
        // Check if the user is authenticated and has the 'admin' role
        if ($user && $user->role === 'admin') {
            // Retrieve only users with role 'admin' or 'team_member'
            $users = User::whereIn('role', ['admin', 'team_member'])->get();
    
            return response()->json([
                'success' => true,
                'data' => $users
            ], 200);
        }
    
        // Return unauthorized response if user is not an admin
        return response()->json([
            'success' => false,
            'message' => 'You are not authorized to access this route.'
        ], 403); // Use 403 Forbidden for unauthorized access
    }
    












    public function getUsersWhoseReferralCodeWasUsed()
    {
        $users = User::whereNotNull('referrers')  // Ensure referrers column is not null
                    ->whereJsonLength('referrers', '>', 0)  // Ensure referrers array is not empty
                    ->get();

        return response()->json($users);
    }


    public function UserLog()
    {
        $user = Auth::user();
        if($user->role === "admin")
        {
            $userlog = UserLog::with('user')->get(); 
            return response()->json($userlog);
    }
    else
    {
        return response()->json(['message' => 'Unauthorized'], 403);

    }
    }

    public function UserLog_single($id)
    {
        $user = Auth::user();
        
        $userlog = UserLog::with('user')->where('user_id', $id)->get();
        return response()->json($userlog);
    
    }

   

    


    
}


