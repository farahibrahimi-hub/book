<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@tt.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        // Default regular user
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@tt.com',
            'password' => Hash::make('user'),
            'role' => 'user',
        ]);
    }
}
