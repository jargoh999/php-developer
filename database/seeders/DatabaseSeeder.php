<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Post::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // Create regular user if not exists
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        // Create posts
        Post::factory(5)->create([
            'user_id' => $admin->id,
        ]);

        Post::factory(5)->create([
            'user_id' => $user->id,
        ]);

        // Create additional test posts
        if (app()->environment('local')) {
            Post::factory(10)->create([
                'user_id' => $admin->id,
            ]);

            Post::factory(10)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
