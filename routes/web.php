<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;




Route::get('/', [PostController::class, 'index'])->name('home');


Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    // This route is now just a fallback, the actual login is handled by the API
    return redirect()->route('login');
})->name('login.post');

Route::post('/logout', function () {
    // This route is now just a fallback, the actual logout is handled by the API
    return redirect('/');
})->name('logout');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);
    
    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
    ]);
    
    Auth::login($user);
    
    return redirect('/')->with('success', 'Registration successful!');
});

// Post Routes
Route::middleware('auth')->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    
    // These routes use route model binding with the 'slug' column
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])
        ->name('posts.edit')
        ->scopeBindings();
        
    Route::put('/posts/{post:slug}', [PostController::class, 'update'])
        ->name('posts.update')
        ->scopeBindings();
        
    Route::delete('/posts/{post:slug}', [PostController::class, 'destroy'])
        ->name('posts.destroy')
        ->scopeBindings();
});

// Public route for viewing a post
Route::get('posts/{post:slug}', [PostController::class, 'show'])
    ->name('posts.show')
    ->scopeBindings();
