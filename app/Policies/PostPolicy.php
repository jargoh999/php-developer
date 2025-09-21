<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    
    public function viewAny(?User $user): bool
    {
        
        return true;
    }

    
    public function view(?User $user, Post $post): bool
    {
        
        return true;
    }

    
    public function create(User $user): bool
    {
        
        return true;
    }

    
    public function update(User $user, Post $post): bool
    {
        
        return $user->id === $post->user_id;
    }

    
    public function delete(User $user, Post $post): bool
    {
        
        return $user->id === $post->user_id;
    }

    
    public function restore(User $user, Post $post): bool
    {
        
        return $user->id === $post->user_id;
    }

    
    public function forceDelete(User $user, Post $post): bool
    {
        
        return $user->id === $post->user_id;
    }
}
