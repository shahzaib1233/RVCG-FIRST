<?php
namespace App\Listeners;

use App\Models\UserLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogUserLogin
{
    public function handle(Login $event)
    {
        // Get the logged-in user
        $user = $event->user;

        // Capture the login details
        UserLog::create([
            'user_id' => $user->id,
            'login_at' => now(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'location' => $this->getLocationFromIP(Request::ip()), // Optional: You can implement location service
        ]);
    }

    private function getLocationFromIP($ip)
    {
        // Implement a geolocation service here (using APIs like ipinfo.io or similar)
        return null; // Just returning null for now, you can use a third-party service
    }
}