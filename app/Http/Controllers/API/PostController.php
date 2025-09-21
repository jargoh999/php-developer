<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    
    public function index()
    {
        $posts = Post::with('user')->latest()->paginate(10);
        return response()->json($posts);
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = $request->user()->posts()->create($validator->validated());

        return response()->json($post->load('user'), 201);
    }

    
    public function show(Post $post)
    {
        return response()->json($post->load('user'));
    }

    
    public function update(Request $request, Post $post)
    {
        
        if (auth()->id() !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post->update($validator->validated());

        return response()->json($post->load('user'));
    }

    
    public function destroy(Post $post)
    {
        
        if (auth()->id() !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(null, 204);
    }
    
    
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $posts = Post::where('title', 'like', "%{$query}%")
                    ->orWhere('body', 'like', "%{$query}%")
                    ->with('user')
                    ->paginate(10);
                    
        return response()->json($posts);
    }
}
