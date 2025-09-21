@extends('layouts.app')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">{{ $post->title }}</h1>
                @auth
                    @if(Auth::id() === $post->user_id)
                        <div>
                            <a href="{{ route('posts.edit', $post->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                            <button type="button" class="text-red-600 hover:text-red-900" onclick="deletePost('{{ $post->slug }}')">
                                Delete
                            </button>
                            
                            <script>
                            async function deletePost(slug) {
                                if (!confirm('Are you sure you want to delete this post?')) {
                                    return;
                                }
                                
                                try {
                                    const token = localStorage.getItem('auth_token');
                                    if (!token) {
                                        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
                                        return;
                                    }
                                    
                                    const response = await fetch(`/api/posts/${slug}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'Authorization': `Bearer ${token}`,
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    });
                                    
                                    if (response.status === 401) {
                                        localStorage.removeItem('auth_token');
                                        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
                                        return;
                                    }
                                    
                                    if (!response.ok) {
                                        const data = await response.json();
                                        throw new Error(data.message || 'Failed to delete post');
                                    }
                                    
                                    alert('Post deleted successfully!');
                                    window.location.href = '/';
                                    
                                } catch (error) {
                                    console.error('Error:', error);
                                    alert(error.message || 'An error occurred while deleting the post');
                                }
                            }
                            </script>
                        </div>
                    @endif
                @endauth
            </div>
            <p class="mt-1 text-sm text-gray-500">
                By {{ $post->user->name }} • {{ $post->created_at->format('F j, Y') }}
            </p>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <div class="prose max-w-none">
                {!! nl2br(e($post->body)) !!}
            </div>
        </div>
        
        <div class="px-4 py-4 sm:px-6 bg-gray-50">
            <a href="{{ url('/') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to all posts</a>
        </div>
    </div>
@endsection
