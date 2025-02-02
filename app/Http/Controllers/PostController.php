<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

// use App\Http\Requests\StorePostRequest;
// use App\Http\Requests\UpdatePostRequest;
// use GuzzleHttp\Psr7\Request;

class PostController extends Controller implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     */

public static function middleware()
{
    return [
        (new Middleware('auth:sanctum'))->except(['index', 'show']),
    ];
}

     

    public function index()
    {
        return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $field=$request->validate([
            'title'=> 'required|max:255',
            'body'=> 'required'
        ]);
        $post = $request->user()->posts()->create($field);
        return ['post'=>$post];
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return ['post' => $post, 'user' => $post->user];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('modify', $post);

        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        $post->update($fields);

        return ['post' => $post, 'user' => $post->user];
        // return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('modify', $post);
        Gate::authorize('modify', $post);

        $post->delete();

        return ['message' => 'The post was deleted'];
    }


    public function image(Request $request)
    {
        // Validate the image input
        $request->validate([
    'image' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,zip|max:5120', 
        ]);
    
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Generate unique filename
            $image->move(public_path('uploads/Listings/Image'), $imageName); // Save in the specified folder
    
            return response()->json([
                'message' => 'Image uploaded successfully',
                'image_path' => url('uploads/Listings/Image/' . $imageName), // Return the accessible image URL
            ], 200);
        }
    
        return response()->json(['message' => 'No image uploaded'], 400);
    }

    
}
