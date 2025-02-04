<?php

    // namespace App\Http\Controllers\Messages;

    // use App\Http\Controllers\Controller;
    // use Illuminate\Http\Request;
    // use App\Models\admin\Message;
    // use Illuminate\Support\Facades\Auth;
    // use Illuminate\Support\Facades\DB;
    // class Messages extends Controller
    // {
    //      // Send a new message
    //      public function sendMessage(Request $request)
    //      {
    //          $request->validate([
    //              'to_user_id' => 'required|exists:users,id',
    //              'message' => 'required|string',
    //              'type' => 'nullable|string|in:text,voice,video',
    //          ]);
    
    //          $message = Message::create([
    //              'from_user_id' => Auth::id(),
    //              'to_user_id' => $request->to_user_id,
    //              'message' => $request->message,
    //              'type' => $request->type ?? 'text',
    //          ]);
    
    //          return response()->json([
    //              'success' => true,
    //              'message' => 'Message sent successfully.',
    //              'data' => $message,
    //          ]);
    //      }


    //      public function getChatContacts()
    // {
    //     $userId = Auth::id(); // Get the authenticated user ID

    //     // Fetch the latest message for each user pair
    //     $messages = DB::table('messages')
    //         ->where(function ($query) use ($userId) {
    //             $query->where('from_user_id', $userId)
    //                   ->orWhere('to_user_id', $userId);
    //         })
    //         ->select('messages.*', 'fromUser.name AS from_user_name', 'toUser.name AS to_user_name')
    //         ->join('users AS fromUser', 'messages.from_user_id', '=', 'fromUser.id')
    //         ->join('users AS toUser', 'messages.to_user_id', '=', 'toUser.id')
    //         ->whereRaw('(messages.id IN (
    //             SELECT MAX(m.id) 
    //             FROM messages m 
    //             WHERE (m.from_user_id = messages.from_user_id AND m.to_user_id = messages.to_user_id)
    //             OR (m.from_user_id = messages.to_user_id AND m.to_user_id = messages.from_user_id)
    //             GROUP BY LEAST(m.from_user_id, m.to_user_id), GREATEST(m.from_user_id, m.to_user_id)
    //         ))')
    //         ->orderBy('messages.created_at', 'desc')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $messages,
    //     ]);
    // }

        

            
        
        
        

    //      // Fetch all messages between two users
    //      public function getMessages($userId)
    // {
    //     $messages = Message::where(function ($query) use ($userId) {
    //         $query->where('from_user_id', Auth::id())
    //               ->where('to_user_id', $userId);
    //     })
    //     ->orWhere(function ($query) use ($userId) {
    //         $query->where('from_user_id', $userId)
    //               ->where('to_user_id', Auth::id());
    //     })
    //     ->orderBy('created_at', 'desc')
    //     ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $messages,
    //     ]);
    // }

    // }




    
namespace App\Http\Controllers\Messages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Messages extends Controller
{
    // Send a new message
    public function sendMessage(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'to_user_id' => 'required|exists:users,id', // Ensure recipient exists
            'message' => 'required|string', // Validate message
            'type' => 'nullable|string|in:text,voice,video', // Optional message type
        ]);

        // Store the message in the database
        $message = Message::create([
            'from_user_id' => Auth::id(), // Get the current user's ID
            'to_user_id' => $request->to_user_id, // Recipient's ID
            'message' => $request->message, // Message content
            'type' => $request->type ?? 'text', // Default to text type if none specified
        ]);

        // Return a successful response with the created message data
        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $message,
        ], 200); // HTTP status code 200
    }


    public function getConversationMessages(Request $request, $id)
    {
        
    
        $fromUserId = Auth::id(); 
        $toUserId = $id;    
        $messages = Message::where(function ($query) use ($fromUserId, $toUserId) {
                $query->where('from_user_id', $fromUserId)
                      ->where('to_user_id', $toUserId);
            })
            ->orWhere(function ($query) use ($fromUserId, $toUserId) {
                $query->where('from_user_id', $toUserId)
                      ->where('to_user_id', $fromUserId);
            })
            ->orderBy('created_at', 'desc') 
            ->get();
    
        if ($messages->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No conversation messages found between these users.',
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => $messages,
        ], 200);
    }


    

    public function getChatContacts()
    {
        $userId = Auth::id(); 

        if (!$userId) {
            return response()->json([
                'error' => 'Unauthorized. Please log in.',
            ], 401); 
        }

        $messages = DB::table('messages')
            ->select(
                DB::raw('LEAST(messages.from_user_id, messages.to_user_id) AS user1'),
                DB::raw('GREATEST(messages.from_user_id, messages.to_user_id) AS user2'),
                DB::raw('MAX(messages.created_at) AS last_message_time'),
                DB::raw('GROUP_CONCAT(messages.message ORDER BY messages.created_at DESC LIMIT 1) AS message'), // Latest message
                'fromUser.name AS from_user_name',
                'toUser.name AS to_user_name'
            )
            ->join('users AS fromUser', 'messages.from_user_id', '=', 'fromUser.id')
            ->join('users AS toUser', 'messages.to_user_id', '=', 'toUser.id')
            ->where(function ($query) use ($userId) {
                $query->where('messages.from_user_id', $userId)
                      ->orWhere('messages.to_user_id', $userId);
            })
            ->groupBy('user1', 'user2', 'fromUser.name', 'toUser.name') // Group by user pair
            ->orderBy('last_message_time', 'desc') // Sort by latest message time
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ], 200); // HTTP status code 200
    }

    // Fetch all messages in a conversation (for normal users and admins)
   
    public function getAllConversations()
    {
        // Ensure only admins can access this
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can view this.',
            ], 403);
        }
    
        // Fetch distinct conversations with the latest message
        $conversations = Message::selectRaw(
                'LEAST(from_user_id, to_user_id) as user1, 
                 GREATEST(from_user_id, to_user_id) as user2, 
                 MAX(id) as latest_message_id'
            )
            ->groupBy('user1', 'user2')
            ->orderBy('latest_message_id', 'desc') // Show latest messages first
            ->get();
    
        // Attach the latest message text & timestamp
        foreach ($conversations as $conversation) {
            $latestMessage = Message::where('id', $conversation->latest_message_id)->first();
            $conversation->latest_message = $latestMessage->message ?? 'No messages yet';
            $conversation->timestamp = $latestMessage->created_at ?? null;
        }
    
        return response()->json([
            'success' => true,
            'data' => $conversations,
        ], 200);
    }
    





     public function getAdminChats()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json([
                'error' => 'Unauthorized. Only admins can view this.',
            ], 403);
        }

        $allChats = DB::table('messages')
            ->select(
                DB::raw('LEAST(messages.from_user_id, messages.to_user_id) AS user1'),
                DB::raw('GREATEST(messages.from_user_id, messages.to_user_id) AS user2'),
                DB::raw('MAX(messages.created_at) AS last_message_time'),
                'messages.message',
                'fromUser.name AS from_user_name',
                'toUser.name AS to_user_name'
            )
            ->join('users AS fromUser', 'messages.from_user_id', '=', 'fromUser.id')
            ->join('users AS toUser', 'messages.to_user_id', '=', 'toUser.id')
            ->groupBy('user1', 'user2')
            ->orderBy('last_message_time', 'desc') // Sort by the latest message time
            ->get();

        return response()->json([
            'success' => true,
            'data' => $allChats,
        ], 200); // HTTP status code 200
    }

    // Fetch all messages in a conversation for admins (full message history)
    public function getAdminConversationMessages($user1, $user2)
    {
        // Ensure the user is an admin
        if (!Auth::user()->is_admin) {
            return response()->json([
                'error' => 'Unauthorized. Only admins can view this.',
            ], 403); // HTTP status code 403 for forbidden access
        }

        // Fetch the full conversation between two users for admin
        $conversationMessages = Message::where(function ($query) use ($user1, $user2) {
                $query->where('from_user_id', $user1)
                      ->where('to_user_id', $user2);
            })
            ->orWhere(function ($query) use ($user1, $user2) {
                $query->where('from_user_id', $user2)
                      ->where('to_user_id', $user1);
            })
            ->orderBy('created_at', 'asc') // Sort by creation date
            ->get();

        // If no conversation is found, return an error message
        if ($conversationMessages->isEmpty()) {
            return response()->json([
                'error' => 'No conversation found.',
            ], 404); // HTTP status code 404 for not found
        }

        return response()->json([
            'success' => true,
            'data' => $conversationMessages,
        ], 200); // HTTP status code 200
    }
}

 

