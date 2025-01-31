<?php

namespace App\Traits;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


trait EmailTrait
{
    /**
     * Send email using the provided details.
     *
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return array
     */
    public function sendEmail($to, $subject, $body)
    {
        try {
            // Send email logic
            Mail::send([], [], function ($message) use ($to, $subject, $body) {
                $message->to($to)
                        ->subject($subject)
                        ->setBody($body, 'text/html'); // Specify HTML body
            });

            // Check if the email has been sent successfully
            if (Mail::failures()) {
                return [
                    'success' => false,
                    'message' => 'Email could not be sent.',
                    'failures' => Mail::failures(),
                ];
            }

            return [
                'success' => true,
                'message' => 'Email sent successfully!',
            ];

        } catch (\Exception $e) {
            // Log the error
            Log::error('Email sending failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to send email',
                'failures' => $e->getMessage(),
            ];
        }
    }
}
