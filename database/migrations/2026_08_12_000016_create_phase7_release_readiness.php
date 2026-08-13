<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_test_runs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60)->index();
            $table->string('status', 20)->index();
            $table->string('message', 1000);
            $table->json('context')->nullable();
            $table->foreignId('tested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_test_runs');
    }
};
