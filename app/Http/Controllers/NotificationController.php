<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Display All Notifications for Authenticated User
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id()
        )
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        return response()->json($notifications);
    }

    // Store New Notification
    public function store(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'title' => 'required|string',
            'redirect_link' => 'nullable|string',
        ]);

        $notification = Notification::create([
            'heading' => $request->heading,
            'title' => $request->title,
            'read' => 0,
            'redirect_link' => $request->redirect_link
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification created successfully.',
            'notification' => $notification
        ]);
    }

    // Mark Notification as Read
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->read = 1;
        $notification->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read.'
        ]);
    }

    // Get Redirect Link
    public function getRedirectLink($id)
    {
        $notification = Notification::findOrFail($id);

        return response()->json([
            'redirect_link' => $notification->redirect_link
        ]);
    }
}
