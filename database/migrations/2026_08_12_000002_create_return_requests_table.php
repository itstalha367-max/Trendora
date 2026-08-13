<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('request_number')->unique();
            $table->enum('type', ['return', 'refund'])->default('return');
            $table->string('reason');
            $table->text('details')->nullable();
            $table->decimal('requested_amount', 10, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'received', 'refunded', 'closed'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
