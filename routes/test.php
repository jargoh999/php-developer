<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/test-db', function () {
    try {
        
        DB::connection()->getPdo();
        
        
        $userCount = User::count();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Database connection is working',
            'user_count' => $userCount
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database connection failed',
            'error' => $e->getMessage()
        ], 500);
    }
});
