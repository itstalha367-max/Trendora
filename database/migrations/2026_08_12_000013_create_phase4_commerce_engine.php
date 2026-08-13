<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_method_id')->nullable()->after('shipping_cost')->constrained('shipping_methods')->nullOnDelete();
            $table->string('shipping_method_name')->nullable()->after('shipping_method_id');
            $table->string('tax_name')->nullable()->after('tax');
            $table->decimal('tax_rate', 7, 4)->default(0)->after('tax_name');
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('discount');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variation_id')->nullable()->after('product_id')->constrained('product_variations')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->after('product_variation_id')->constrained('warehouses')->nullOnDelete();
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway')->nullable();
            $table->string('transaction_id')->nullable()->index();
            $table->enum('type', ['charge', 'capture', 'refund', 'adjustment'])->default('charge');
            $table->enum('status', ['pending', 'succeeded', 'failed'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('PKR');
            $table->json('payload')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'status']);
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('return_request_id')->nullable()->constrained('return_requests')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('refund_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['original', 'manual', 'wallet'])->default('original');
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->string('gateway_reference')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('eyebrow')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $extraPermissions = [
            ['name' => 'Finance Manage', 'key' => 'finance.manage', 'group' => 'operations'],
            ['name' => 'Theme Manage', 'key' => 'theme.manage', 'group' => 'marketing'],
        ];
        foreach ($extraPermissions as $permission) {
            DB::table('permissions')->updateOrInsert(['key' => $permission['key']], $permission + ['created_at' => $now, 'updated_at' => $now]);
        }

        foreach (['operations-manager' => 'finance.manage', 'marketing-manager' => 'theme.manage'] as $roleSlug => $permissionKey) {
            $roleId = DB::table('admin_roles')->where('slug', $roleSlug)->value('id');
            $permissionId = DB::table('permissions')->where('key', $permissionKey)->value('id');
            if ($roleId && $permissionId) DB::table('admin_role_permission')->updateOrInsert(['admin_role_id'=>$roleId,'permission_id'=>$permissionId]);
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')->whereIn('key', ['finance.manage','theme.manage'])->pluck('id');
        if ($permissionIds->isNotEmpty()) DB::table('admin_role_permission')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payment_transactions');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('warehouse_id');
            $table->dropConstrainedForeignId('product_variation_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_method_id');
            $table->dropColumn(['shipping_method_name', 'tax_name', 'tax_rate', 'refunded_amount']);
        });
    }
};
