<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Models\Admin\EmailCampaign;
    use App\Models\admin\Lead;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Mail;

    class EmailCampaignController extends Controller
    {
        public function sendEmailCampaign(Request $request)
        {
            // Get the array of user IDs from the front end
            $userIds = $request->input('user_id'); // Array of lead IDs
            $message = $request->input('message');  // The dynamic email body content
            $subject = $request->input('subject');

            // Validate inputs
            if (empty($userIds) || empty($message) || empty($subject)) {
                return response()->json(['message' => 'User IDs and message content are required.'], 400);
            }

            // Loop through the user IDs and send emails
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user && !empty($user->email)) {
                    // Send the email
                    try {
                        // Send email
                        Mail::send([], [], function ($mail) use ($user, $message, $subject) {
                            $mail->to($user->email)
                                ->subject($subject)
                                ->html($message);
                        });

                        // Store in the email_campaigns table
                        EmailCampaign::create([
                            'user_id' => $user->id,
                            'message' => $message,
                            'status' => 'sent'
                        ]);
                    } catch (\Exception $e) {
                        // If sending fails, store the record with status 'failed'
                        EmailCampaign::create([
                            'user_id' => $user->id,
                            'message' => $message,
                            'status' => 'failed'
                        ]);
                    }

                    // Wait for 2 seconds before sending another email
                    sleep(2);
                }
            }

            return response()->json(['message' => 'Emails sent successfully.'], 200);
        }


        public function getemailrecord()
        {
            $email_campaigns = EmailCampaign::all();
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
        // Get the array of lead IDs from the front end
        $leadIds = $request->input('lead_ids'); // Array of lead IDs
        $message = $request->input('message');  // The dynamic email body content
        $subject = $request->input('subject');  // The dynamic subject content

        // Validate inputs
        if (empty($leadIds) || empty($message) || empty($subject)) {
            return response()->json(['message' => 'Lead IDs, subject, and message content are required.'], 400);
        }

        // Loop through the lead IDs and send emails
        foreach ($leadIds as $leadId) {
            $lead = Lead::find($leadId);
            if ($lead && !empty($lead->email)) {
                // Send the email
                try {
                    // Send email
                    Mail::send([], [], function ($mail) use ($lead, $message, $subject) {
                        $mail->to($lead->email)
                            ->subject($subject)
                            ->html($message);
                    });

                    // Store in the email_campaigns table
                    EmailCampaign::create([
                        'lead_id' => $lead->id,
                        'message' => $message,
                        'subject' => $subject,
                        'status' => 'sent'
                    ]);
                } catch (\Exception $e) {
                    // If sending fails, store the record with status 'failed'
                    EmailCampaign::create([
                        'lead_id' => $lead->id,
                        'message' => $message,
                        'subject' => $subject,
                        'status' => 'failed'
                    ]);
                }

                // Wait for 2 seconds before sending another email
                sleep(2);
            }
        }

        return response()->json(['message' => 'Emails sent successfully.'], 200);
    }










    }
