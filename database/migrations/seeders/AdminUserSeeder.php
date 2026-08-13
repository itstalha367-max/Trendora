<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin already exists
        $admin = User::where('email', 'admin@trendora.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@trendora.com',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            echo "Admin user created successfully!\n";
        } else {
            // Update role if needed
            $admin->role = 'admin';
            $admin->save();
            echo "Admin user already exists. Role updated.\n";
        }
    }
}