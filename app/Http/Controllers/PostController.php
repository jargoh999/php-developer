<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    
    public function index()
    {
        try {
            // Debug: Log database connection status
            \Log::info('Database connection:', ['status' => \DB::connection()->getPdo() ? 'Connected' : 'Not connected']);
            
            // Get all posts with eager loading
            $posts = Post::with(['user' => function($query) {
                    $query->select('id', 'name');
                }])
                ->latest('created_at')
                ->paginate(10);
            
            // Debug: Log the retrieved posts
            \Log::info('Retrieved posts:', $posts->toArray());
            
            return view('home', compact('posts'));
            
        } catch (\Exception $e) {
            // Log any errors
            \Log::error('Error in PostController@index: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Return an empty collection if there's an error
            return view('home', ['posts' => collect()]);
        }
    }

    
    public function create()
    {
        return view('posts.form');
    }

    
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
            ]);
            
            // Log the authenticated user
            \Log::info('Authenticated user:', [
                'id' => $request->user() ? $request->user()->id : 'null',
                'name' => $request->user() ? $request->user()->name : 'null'
            ]);
            
            // Create the post
            $post = $request->user()->posts()->create($validated);
            
            // Log the created post
            \Log::info('Post created:', $post->toArray());
            
            return redirect()
                ->route('home')
                ->with('success', 'Post created successfully!');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating post: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            \Log::error('Input data:', $request->all());
            
            return back()
                ->withInput()
                ->with('error', 'Error creating post: ' . $e->getMessage());
        }
    }

    
    public function show(Post $post)
    {
        try {
            // Eager load the user relationship
            $post->load('user');
            return view('posts.show', compact('post'));
            
        } catch (\Exception $e) {
            // Log other errors
            \Log::error('Error in PostController@show: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Redirect to home with an error message
            return redirect()->route('home')
                ->with('error', 'An error occurred while retrieving the post.');
        }
    }

    
    public function edit(Post $post)
    {
        try {
            $this->authorize('update', $post);
            return view('posts.form', ['post' => $post]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'The requested post could not be found.');
        }
    }

    
    public function update(Request $request, Post $post)
    {
        try {
            $this->authorize('update', $post);
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
            ]);
            
            $post->update($validated);
            
            return redirect()
                ->route('posts.show', $post->slug)
                ->with('success', 'Post updated successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Error updating post: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while updating the post.');
        }
    }

    
    public function destroy(Post $post)
    {
        try {
            $this->authorize('delete', $post);
            
            $post->delete();
            
            return redirect()
                ->route('home')
                ->with('success', 'Post deleted successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting post: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while deleting the post.');
        }
    }
}
