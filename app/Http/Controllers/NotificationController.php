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
            'user_id' => 'required|integer'
        ]);

        $notification = Notification::create([
            'heading' => $request->heading,
            'title' => $request->title,
            'read' => 0,
            'redirect_link' => $request->redirect_link,
            'user_id'=>$request->user_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification created successfully.',
            'notification' => $notification
        ]);
    }

    // Mark Notification as Read
    public function markAsRead(Request $request)
{
    $ids = $request->input('id'); 

    $request->validate([
        'id' => 'required|array|min:1',
        'id.*' => 'integer|exists:notifications,id'
    ]);

    // Get the authenticated user's ID
    $userId = Auth::id();

    Notification::whereIn('id', $ids)
                ->where('user_id', $userId)
                ->update(['read' => 1]);

    return response()->json([
        'status' => 'success',
        'message' => 'Notifications marked as read.'
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
