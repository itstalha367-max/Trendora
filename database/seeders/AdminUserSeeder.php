<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) env('TRENDORA_ADMIN_EMAIL', ''));
        $password = (string) env('TRENDORA_ADMIN_PASSWORD', '');
        $name = trim((string) env('TRENDORA_ADMIN_NAME', 'Trendora Admin'));

        if ($email === '' || strlen($password) < 8) {
            $this->command?->warn('AdminUserSeeder skipped. Set TRENDORA_ADMIN_EMAIL and TRENDORA_ADMIN_PASSWORD (8+ chars), or run: php artisan trendora:admin');
            return;
        }

        User::updateOrCreate(['email' => strtolower($email)], [
            'name' => $name !== '' ? $name : 'Trendora Admin',
            'role' => 'admin',
            'admin_role_id' => null,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
        ]);
    }
}
