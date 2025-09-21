@extends('layouts.app')

@section('content')
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Sign in to your account
                </h2>
            </div>
            @if(request()->has('redirect'))
                <div class="mb-4 p-4 bg-blue-50 text-blue-800 rounded-md">
                    Please log in to continue
                </div>
            @endif
            
            <form id="loginForm" class="mt-8 space-y-6" onsubmit="handleLogin(event)">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="Email address" value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="Password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit" id="loginBtn"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign in
                    </button>
                </div>
                
                @push('scripts')
                <script>
                async function handleLogin(event) {
                    event.preventDefault();
                    
                    const form = event.target;
                    const loginBtn = document.getElementById('loginBtn');
                    
                    try {
                        // Disable login button
                        loginBtn.disabled = true;
                        loginBtn.innerHTML = 'Signing in...';
                        
                        // Clear any existing errors
                        const errorElements = document.querySelectorAll('.text-red-600');
                        errorElements.forEach(el => el.remove());
                        
                        // Make API request to login
                        const response = await fetch('/api/login', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': form._token.value
                            },
                            body: JSON.stringify({
                                email: form.email.value,
                                password: form.password.value
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            // Show error message
                            const errorElement = document.createElement('p');
                            errorElement.className = 'mt-2 text-sm text-red-600';
                            errorElement.textContent = data.message || 'Login failed. Please check your credentials.';
                            
                            // Add after the form
                            form.appendChild(errorElement);
                            
                            throw new Error(data.message || 'Login failed. Please check your credentials.');
                        }
                        
                        // Store the token in localStorage
                        if (data.access_token) {
                            localStorage.setItem('auth_token', data.access_token);
                            
                            // Redirect to the intended page or home
                            const redirectTo = new URLSearchParams(window.location.search).get('redirect') || '/';
                            window.location.href = redirectTo;
                        } else {
                            throw new Error('No access token received');
                        }
                        
                    } catch (error) {
                        console.error('Login error:', error);
                        if (!form.querySelector('.text-red-600')) {
                            const errorElement = document.createElement('p');
                            errorElement.className = 'mt-2 text-sm text-red-600';
                            errorElement.textContent = error.message || 'An error occurred during login. Please try again.';
                            form.appendChild(errorElement);
                        }
                    } finally {
                        // Re-enable login button
                        loginBtn.disabled = false;
                        loginBtn.innerHTML = 'Sign in';
                    }
                }
                
                // Check if user is already logged in
                document.addEventListener('DOMContentLoaded', function() {
                    const token = localStorage.getItem('auth_token');
                    if (token) {
                        // Verify token is still valid
                        fetch('/api/me', {
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                window.location.href = '/';
                            }
                        });
                    }
                });
                </script>
                @endpush
                
                <div class="text-sm text-center">
                    <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Don't have an account? Sign up
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
