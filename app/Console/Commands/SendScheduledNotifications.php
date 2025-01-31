<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled';
    protected $description = 'Send scheduled notifications to users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();

        // Fetch notifications scheduled for now or earlier and not sent
        $notifications = Notification::where('scheduled_at', '<=', $now)
            ->where('is_sent', false)
            ->get();

        foreach ($notifications as $notification) {
            if ($notification->send_to_all) {
                // Send to all users
                $users = User::all();
                foreach ($users as $user) {
                    $this->sendNotification($user, $notification);
                }
            } else {
                // Send to a specific user
                $user = $notification->user; // Define a relationship if needed
                if ($user) {
                    $this->sendNotification($user, $notification);
                }
            }

            // Mark notification as sent
            $notification->update(['is_sent' => true]);

            $this->info("Notification ID {$notification->id} sent successfully.");
        }

        $this->info('All scheduled notifications have been processed.');
    }

    protected function sendNotification($user, $notification)
    {
        $this->info("Notification sent to User ID: {$user->id} - {$notification->title}");
    }
}
