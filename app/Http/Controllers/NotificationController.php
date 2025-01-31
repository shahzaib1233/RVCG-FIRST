<?php


namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'send_to_all' => 'boolean',
            'scheduled_at' => 'nullable|date',
        ]);

        Notification::create($data);

        return response()->json(['message' => 'Notification scheduled successfully.']);
    }
}
