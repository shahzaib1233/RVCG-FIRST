<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Models\admin\EmailCampaign;
    use App\Models\admin\Lead;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Mail;

    class EmailCampaignController extends Controller
    {
        public function sendEmailCampaign(Request $request)
        {
            // Get the array of user IDs from the front end
            $userIds = $request->input('user_id'); 
            $message = $request->input('message'); 
            $subject = $request->input('subject');

            // Validate inputs
            if (empty($userIds) || empty($message) || empty($subject)) {
                return response()->json(['message' => 'User IDs and message content are required.'], 400);
            }

            // Loop through the user IDs and send emails
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user && !empty($user->email)) {
                    try {
                        Mail::send([], [], function ($mail) use ($user, $message, $subject) {
                            $mail->to($user->email)
                                ->subject($subject)
                                ->html($message);
                        });

                        EmailCampaign::create([
                            'user_id' => $user->id,
                            'message' => $message,
                            'status' => 'sent'
                        ]);
                    } catch (\Exception $e) {
                        EmailCampaign::create([
                            'user_id' => $user->id,
                            'message' => $message,
                            'status' => 'failed'
                        ]);
                    }

                    sleep(2);
                }
            }

            return response()->json(['message' => 'Emails sent successfully.'], 200);
        }


        public function getemailrecord()
        {
            $email_campaigns = EmailCampaign::with('user')->get();
        
            if ($email_campaigns->isEmpty()) {
                return response()->json([
                    'message' => 'No email campaigns found.'
                ], 404);
            }
        
            return response()->json([
                'email_campaigns' => $email_campaigns
            ], 200);
        }
        















        public function sendEmailCampaigntoleads(Request $request)
    {
        $leadIds = $request->input('lead_ids');
        $message = $request->input('message'); 
        $subject = $request->input('subject'); 

        if (empty($leadIds) || empty($message) || empty($subject)) {
            return response()->json(['message' => 'Lead IDs, subject, and message content are required.'], 400);
        }

        foreach ($leadIds as $leadId) {
            $lead = Lead::find($leadId);
            if ($lead && !empty($lead->email)) {
                try {
                    Mail::send([], [], function ($mail) use ($lead, $message, $subject) {
                        $mail->to($lead->email)
                            ->subject($subject)
                            ->html($message);
                    });

                    EmailCampaign::create([
                        'lead_id' => $lead->id,
                        'message' => $message,
                        'subject' => $subject,
                        'status' => 'sent'
                    ]);
                } catch (\Exception $e) {
                    EmailCampaign::create([
                        'lead_id' => $lead->id,
                        'message' => $message,
                        'subject' => $subject,
                        'status' => 'failed'
                    ]);
                }

                sleep(2);
            }
        }

        return response()->json(['message' => 'Emails sent successfully.'], 200);
    }







    // public function showemailrecord($id)
    // {
    //     $email_campaigns = EmailCampaign::find($id);
    
    //     if (!$email_campaigns) {
    //         return response()->json([
    //             'message' => 'Not Found'
    //         ], 404);
    //     }
    
    //     return response()->json([
    //         'email_campaigns' => $email_campaigns
    //     ], 200);
    // }
    
    public function showemailrecord($id)
    {
        $email_campaigns = EmailCampaign::find($id);
    
        if (!$email_campaigns) {
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
    
        $plain_text_message = strip_tags($email_campaigns->message);
    
        return response()->json([
            'email_campaigns' => [
                'id' => $email_campaigns->id,
                'user_id' => $email_campaigns->user_id,
                'subject'=> $email_campaigns->subject,
                'message' => $plain_text_message, 
                'status' => $email_campaigns->status,
                'created_at' => $email_campaigns->created_at,
                'updated_at' => $email_campaigns->updated_at,
            ]
        ], 200);
    }
    
    }
