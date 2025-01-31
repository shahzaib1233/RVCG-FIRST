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
}
