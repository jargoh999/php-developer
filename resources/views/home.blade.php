@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h1 class="text-2xl font-bold text-gray-900">Latest Blog Posts</h1>
            @auth
                <div class="mt-4">
                    <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create New Post
                    </a>
                </div>
            @endauth
        </div>
        
        <div class="border-t border-gray-200">
            @if($posts->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($posts as $post)
                        <li class="p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="block hover:bg-gray-50">
                                        <a href="{{ route('posts.show', $post->slug) }}" class="block">
                                            <h3 class="text-lg font-medium text-indigo-600">{{ $post->title }}</h3>
                                            <p class="mt-1 text-sm text-gray-500">
                                                By {{ $post->user->name }} • {{ $post->created_at->diffForHumans() }}
                                            </p>
                                            <p class="mt-2 text-sm text-gray-600">
                                                {{ Str::limit($post->body, 200) }}
                                            </p>
                                        </a>
                                    </div>
                                </div>
                                @auth
                                    @if(Auth::id() === $post->user_id)
                                        <div class="ml-4 flex-shrink-0">
                                            <a href="{{ route('posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            <button type="button" class="text-red-600 hover:text-red-900" onclick="deletePost('{{ $post->slug }}', this)">
                                                Delete
                                            </button>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="px-4 py-5 sm:p-6">
                    <p class="text-gray-500">No posts found.</p>
                </div>
            @endif
        </div>
    </div>
    
    @push('scripts')
    <script>
    async function deletePost(slug, button) {
        if (!confirm('Are you sure you want to delete this post?')) {
            return;
        }
        
        const listItem = button.closest('li');
        
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
            
            // Remove the post from the UI
            listItem.remove();
            alert('Post deleted successfully!');
            
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'An error occurred while deleting the post');
        }
    }
    </script>
    @endpush
@endsection
