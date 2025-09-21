@extends('layouts.app')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ isset($post) ? 'Edit Post' : 'Create New Post' }}</h1>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <form id="postForm" onsubmit="handleSubmit(event)">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $post->title ?? '') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="body" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea name="body" id="body" rows="10" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('body', $post->body ?? '') }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end">
                    <a href="{{ url()->previous() }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ isset($post) ? 'Update' : 'Create' }} Post
                    </button>
                </div>
                
                @push('scripts')
                <script>
                async function handleSubmit(event) {
                    event.preventDefault();
                    
                    const form = event.target;
                    const submitBtn = document.getElementById('submitBtn');
                    const isEdit = {{ isset($post) ? 'true' : 'false' }};
                    const postSlug = '{{ $post->slug ?? '' }}';
                    const url = isEdit ? `/api/posts/${postSlug}` : '/api/posts';
                    const method = isEdit ? 'PUT' : 'POST';
                    
                    // Get form data
                    const formData = {
                        title: form.title.value,
                        body: form.body.value,
                        _token: form._token.value
                    };
                    
                    try {
                        // Disable submit button
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = 'Saving...';
                        
                        // Get the auth token from localStorage
                        const token = localStorage.getItem('auth_token');
                        
                        if (!token) {
                            throw new Error('You need to be logged in to perform this action');
                        }
                        
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': form._token.value,
                                'Authorization': `Bearer ${token}`,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(formData)
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            if (response.status === 401) {
                                // Token might be expired, clear it and redirect to login
                                localStorage.removeItem('auth_token');
                                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
                                return;
                            }
                            throw new Error(data.message || 'Something went wrong!');
                        }
                        
                        // Show success message
                        alert(isEdit ? 'Post updated successfully!' : 'Post created successfully!');
                        
                        // Redirect to the post or home page
                        // Use the slug from the response if available, otherwise use the current slug
                        const slug = data.slug || '{{ $post->slug ?? '' }}';
                        window.location.href = isEdit ? `/posts/${slug}` : '/';
                        
                    } catch (error) {
                        console.error('Error:', error);
                        
                        // Show error message in a better way
                        const existingError = document.getElementById('form-error');
                        if (existingError) {
                            existingError.textContent = error.message || 'An error occurred. Please try again.';
                        } else {
                            const errorElement = document.createElement('p');
                            errorElement.id = 'form-error';
                            errorElement.className = 'mt-4 text-sm text-red-600';
                            errorElement.textContent = error.message || 'An error occurred. Please try again.';
                            form.appendChild(errorElement);
                        }
                    } finally {
                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = isEdit ? 'Update Post' : 'Create Post';
                    }
                }
                </script>
                @endpush
            </form>
        </div>
    </div>
@endsection
