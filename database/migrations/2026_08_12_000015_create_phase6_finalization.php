<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->string('payout_number')->unique();
            $table->foreignId('affiliate_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('PKR');
            $table->string('method')->default('bank_transfer');
            $table->string('status')->default('pending')->index();
            $table->string('reference')->nullable();
            $table->json('metadata')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('webhook_deliveries', function (Blueprint $table) {
            $table->unsignedSmallInteger('attempt_count')->default(0)->after('status');
            $table->timestamp('next_retry_at')->nullable()->after('attempt_count');
            $table->timestamp('delivered_at')->nullable()->after('next_retry_at');
        });

        $now = now();
        foreach ([
            ['name' => 'Payouts Manage', 'key' => 'payouts.manage', 'group' => 'operations'],
            ['name' => 'Commerce Settings Manage', 'key' => 'commerce_settings.manage', 'group' => 'system'],
        ] as $permission) {
            DB::table('permissions')->updateOrInsert(['key' => $permission['key']], $permission + ['created_at' => $now, 'updated_at' => $now]);
        }

        foreach ([
            'operations-manager' => ['payouts.manage'],
            'marketing-manager' => [],
        ] as $roleSlug => $keys) {
            $roleId = DB::table('admin_roles')->where('slug', $roleSlug)->value('id');
            if (!$roleId) continue;
            $permissionIds = DB::table('permissions')->whereIn('key', $keys)->pluck('id');
            foreach ($permissionIds as $permissionId) {
                DB::table('admin_role_permission')->updateOrInsert(['admin_role_id' => $roleId, 'permission_id' => $permissionId]);
            }
        }

        foreach ([
            ['store_legal_name', 'Trendora Commerce', 'store', 'text', 'Legal business name'],
            ['store_country', 'Pakistan', 'store', 'text', 'Store country'],
            ['store_timezone', 'Asia/Karachi', 'store', 'text', 'Store timezone'],
            ['order_prefix', 'ORD-', 'store', 'text', 'Order number prefix'],
            ['invoice_prefix', 'INV-', 'store', 'text', 'Invoice number prefix'],
            ['minimum_order_amount', '0', 'checkout', 'number', 'Minimum order amount'],
            ['checkout_terms_required', 'on', 'checkout', 'toggle', 'Require terms acceptance'],
            ['checkout_notes_enabled', 'on', 'checkout', 'toggle', 'Enable checkout notes'],
            ['checkout_default_country', 'Pakistan', 'checkout', 'text', 'Default checkout country'],
            ['checkout_cod_enabled', 'on', 'checkout', 'toggle', 'Enable cash on delivery'],
            ['checkout_wallet_enabled', 'on', 'checkout', 'toggle', 'Enable store credit'],
            ['default_shipping_name', 'Standard delivery', 'shipping', 'text', 'Fallback shipping name'],
            ['default_shipping_cost', '0', 'shipping', 'number', 'Fallback shipping cost'],
            ['default_shipping_min_days', '3', 'shipping', 'number', 'Fallback minimum days'],
            ['default_shipping_max_days', '7', 'shipping', 'number', 'Fallback maximum days'],
            ['shipping_fallback_enabled', 'on', 'shipping', 'toggle', 'Enable fallback shipping'],
            ['tax_prices_include_tax', 'off', 'tax', 'toggle', 'Prices include tax'],
            ['tax_display_cart', 'on', 'tax', 'toggle', 'Display tax in cart and checkout'],
            ['low_stock_threshold', '5', 'inventory', 'number', 'Global low stock threshold'],
        ] as [$key, $value, $group, $type, $label]) {
            DB::table('settings')->updateOrInsert(['key' => $key], [
                'value' => json_encode($value), 'group' => $group, 'type' => $type, 'label' => $label,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')->whereIn('key', ['payouts.manage','commerce_settings.manage'])->pluck('id');
        if ($permissionIds->isNotEmpty()) DB::table('admin_role_permission')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();

        Schema::table('webhook_deliveries', function (Blueprint $table) {
            $table->dropColumn(['attempt_count','next_retry_at','delivered_at']);
        });
        Schema::dropIfExists('payouts');
    }
};
