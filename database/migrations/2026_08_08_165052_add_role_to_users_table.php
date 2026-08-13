<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 🔐 Add role column with default 'user'
            $table->enum('role', ['user', 'admin', 'vendor'])->default('user')->after('email');
            
            // 🔐 Add phone column (optional)
            $table->string('phone')->nullable()->after('role');
            
            // 🔐 Add address columns (optional)
            $table->text('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('zip')->nullable()->after('state');
            $table->string('country')->nullable()->after('zip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address', 'city', 'state', 'zip', 'country']);
        });
    }
};