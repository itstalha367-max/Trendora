<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        Schema::create('admin_role_permission', function (Blueprint $table) {
            $table->foreignId('admin_role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['admin_role_id', 'permission_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('admin_role_id')->nullable()->after('role')->constrained('admin_roles')->nullOnDelete();
        });

        $now = now();
        $permissionMap = [
            'catalog' => ['products.manage', 'categories.manage', 'inventory.manage'],
            'orders' => ['orders.manage', 'returns.manage'],
            'customers' => ['customers.manage', 'support.manage'],
            'marketing' => ['marketing.manage', 'content.manage'],
            'operations' => ['shipping.manage', 'tax.manage', 'reports.view'],
            'system' => ['staff.manage', 'settings.manage', 'security.manage'],
        ];
        foreach ($permissionMap as $group => $keys) {
            foreach ($keys as $key) {
                DB::table('permissions')->insert([
                    'name' => ucwords(str_replace(['.', '_'], ' ', $key)),
                    'key' => $key, 'group' => $group, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        $roles = [
            'catalog-manager' => ['Catalog Manager', ['products.manage', 'categories.manage', 'inventory.manage']],
            'operations-manager' => ['Operations Manager', ['orders.manage', 'returns.manage', 'inventory.manage', 'shipping.manage', 'tax.manage', 'reports.view', 'support.manage']],
            'support-agent' => ['Support Agent', ['customers.manage', 'support.manage', 'returns.manage']],
            'marketing-manager' => ['Marketing Manager', ['marketing.manage', 'content.manage', 'reports.view']],
        ];
        foreach ($roles as $slug => [$name, $keys]) {
            $roleId = DB::table('admin_roles')->insertGetId([
                'name' => $name, 'slug' => $slug, 'description' => 'Trendora default role',
                'is_system' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $permissionIds = DB::table('permissions')->whereIn('key', $keys)->pluck('id');
            foreach ($permissionIds as $permissionId) {
                DB::table('admin_role_permission')->insert(['admin_role_id' => $roleId, 'permission_id' => $permissionId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_role_id');
        });
        Schema::dropIfExists('admin_role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('admin_roles');
    }
};
