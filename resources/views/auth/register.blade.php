@extends('layouts.app')

@section('content')
<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create a new account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    sign in to your existing account
                </a>
            </p>
        </div>
        <form id="registerForm" class="mt-8 space-y-6" onsubmit="handleRegister(event)">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="name" class="sr-only">Name</label>
                    <input id="name" name="name" type="text" autocomplete="name" required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Name" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Email address" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Confirm Password">
                </div>
            </div>

            <div>
                <button type="submit" id="registerBtn"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign up
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function handleRegister(event) {
        event.preventDefault();
        
        const form = event.target;
        const registerBtn = document.getElementById('registerBtn');
        const password = form.password.value;
        const passwordConfirmation = form.password_confirmation.value;
        
        if (password !== passwordConfirmation) {
            alert('Passwords do not match');
            return;
        }
        
        try {
            // Disable register button
            registerBtn.disabled = true;
            registerBtn.innerHTML = 'Creating account...';
            
            // Make API request to register
            const response = await fetch('/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': form._token.value
                },
                body: JSON.stringify({
                    name: form.name.value,
                    email: form.email.value,
                    password: password,
                    password_confirmation: passwordConfirmation
                })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Registration failed. Please try again.');
            }
            
            // If we get a token, store it and log the user in
            if (data.token) {
                localStorage.setItem('auth_token', data.token);
                window.location.href = '/';
            } else {
                // If no token, redirect to login
                alert('Registration successful! Please log in.');
                window.location.href = '/login';
            }
            
        } catch (error) {
            console.error('Registration error:', error);
            alert(error.message || 'An error occurred during registration. Please try again.');
        } finally {
            // Re-enable register button
            registerBtn.disabled = false;
            registerBtn.innerHTML = 'Sign up';
        }
    }
</script>
@endpush
