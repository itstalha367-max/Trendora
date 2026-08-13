<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable(); // Sale price
            $table->integer('stock_quantity')->default(0);
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit
            $table->json('images')->nullable(); // Multiple images
            $table->string('thumbnail')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('status')->default(true);
            $table->integer('views')->default(0);
            $table->timestamps();
            
            // 🔐 Indexes
            $table->index('slug');
            $table->index('category_id');
            $table->index('price');
            $table->index('status');
            $table->index('featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};