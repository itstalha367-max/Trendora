<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'trendora:admin
        {--name= : Administrator display name}
        {--email= : Administrator email address}
        {--password= : Password (omit to enter it securely)}';

    protected $description = 'Create a new Trendora super-admin or promote/update an existing user.';

    public function handle(): int
    {
        $name = trim((string) ($this->option('name') ?: $this->ask('Admin name', 'Trendora Admin')));
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Admin email'))));
        $password = (string) ($this->option('password') ?: $this->secret('Admin password (minimum 8 characters)'));

        if ($password === '') {
            $this->error('Password is required.');
            return self::FAILURE;
        }

        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', Password::min(8)],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $existing = User::where('email', $email)->first();
        if ($existing && $existing->role !== 'admin') {
            if (!$this->confirm('This email belongs to a customer/vendor. Promote it to super-admin?', false)) {
                $this->warn('No changes were made.');
                return self::SUCCESS;
            }
        }

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => 'admin',
                'admin_role_id' => null,
                'email_verified_at' => now(),
                'password' => Hash::make($password),
            ]
        );

        $this->newLine();
        $this->info(($existing ? 'Admin updated' : 'Admin created').': '.$admin->email);
        $this->line('Role: super-admin (all admin permissions)');
        $this->line('Login: '.url('/admin/login'));

        return self::SUCCESS;
    }
}
