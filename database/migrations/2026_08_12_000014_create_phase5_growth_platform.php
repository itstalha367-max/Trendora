<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('store_credit', 12, 2)->default(0)->after('admin_role_id');
        });

        Schema::create('draft_orders', function (Blueprint $table) {
            $table->id();
            $table->string('draft_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_zip', 30)->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('currency', 10)->default('PKR');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('shipping', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('converted_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('draft_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variation_id')->nullable()->constrained('product_variations')->nullOnDelete();
            $table->string('title');
            $table->string('sku')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });

        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('rules')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('customer_segment_user', function (Blueprint $table) {
            $table->foreignId('customer_segment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['customer_segment_id', 'user_id']);
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->string('type')->default('percentage');
            $table->decimal('value', 12, 2)->default(0);
            $table->decimal('minimum_order', 12, 2)->default(0);
            $table->decimal('maximum_discount', 12, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->json('rules')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('initial_balance', 12, 2);
            $table->decimal('current_balance', 12, 2);
            $table->string('currency', 10)->default('PKR');
            $table->string('status')->default('active')->index();
            $table->foreignId('purchaser_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('gift_card_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gift_card_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('subject');
            $table->longText('content');
            $table->json('variables')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('title');
            $table->text('content');
            $table->json('channels')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('general');
            $table->boolean('enabled')->default(false);
            $table->longText('encrypted_config')->nullable();
            $table->string('health_status')->default('not_configured');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key_prefix', 24)->index();
            $table->string('key_hash', 64)->unique();
            $table->json('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->longText('encrypted_secret')->nullable();
            $table->json('events')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->json('payload')->nullable();
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('draft')->index();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->date('ordered_at')->nullable();
            $table->date('expected_at')->nullable();
            $table->date('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_variation_id')->nullable()->constrained('product_variations')->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('received_quantity')->default(0);
            $table->decimal('cost', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('code')->unique();
            $table->decimal('commission_rate', 6, 2)->default(5);
            $table->string('status')->default('active');
            $table->json('payout_details')->nullable();
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('conversions')->default(0);
            $table->decimal('revenue', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('affiliate_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('order_amount', 12, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('navigation_menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->cascadeOnDelete();
            $table->string('label');
            $table->string('url');
            $table->string('target')->default('_self');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('store_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->boolean('primary')->default(false);
            $table->string('ssl_status')->default('pending');
            $table->string('verification_status')->default('pending');
            $table->timestamps();
        });

        Schema::create('product_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->foreignId('answered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('answered_at')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamps();
        });

        $now = now();
        $extraPermissions = [
            ['name'=>'Growth Manage','key'=>'growth.manage','group'=>'marketing'],
            ['name'=>'Integrations Manage','key'=>'integrations.manage','group'=>'system'],
            ['name'=>'Purchase Orders Manage','key'=>'purchase_orders.manage','group'=>'operations'],
        ];
        foreach ($extraPermissions as $permission) {
            DB::table('permissions')->updateOrInsert(['key'=>$permission['key']], $permission + ['created_at'=>$now,'updated_at'=>$now]);
        }

        $roleGrants = [
            'marketing-manager' => ['growth.manage'],
            'operations-manager' => ['purchase_orders.manage'],
        ];
        foreach ($roleGrants as $slug => $keys) {
            $roleId = DB::table('admin_roles')->where('slug',$slug)->value('id');
            if (!$roleId) continue;
            $ids = DB::table('permissions')->whereIn('key',$keys)->pluck('id');
            foreach ($ids as $id) DB::table('admin_role_permission')->updateOrInsert(['admin_role_id'=>$roleId,'permission_id'=>$id]);
        }

        foreach ([
            ['order_confirmation','Order confirmation','Your Trendora order {{order_number}} is confirmed','Hi {{customer_name}}, your order {{order_number}} has been confirmed. Total: {{order_total}}.'],
            ['order_shipped','Order shipped','Your order {{order_number}} is on the way','Good news {{customer_name}} — order {{order_number}} has shipped. Tracking: {{tracking_number}}.'],
            ['password_reset','Password reset','Reset your Trendora password','Use your secure reset link to choose a new password.'],
        ] as [$key,$name,$subject,$content]) {
            DB::table('email_templates')->updateOrInsert(['key'=>$key],[
                'name'=>$name,'subject'=>$subject,'content'=>$content,
                'variables'=>json_encode(['customer_name','order_number','order_total','tracking_number']),
                'status'=>true,'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        foreach ([
            ['order_status','Order status update','Order {{order_number}} is now {{status}}'],
            ['refund_processed','Refund processed','A refund of {{amount}} was recorded for {{order_number}}'],
            ['support_reply','Support reply','Your support ticket {{ticket_number}} has a new reply'],
        ] as [$key,$name,$content]) {
            DB::table('notification_templates')->updateOrInsert(['key'=>$key],[
                'name'=>$name,'title'=>$name,'content'=>$content,'channels'=>json_encode(['database']),
                'status'=>true,'created_at'=>$now,'updated_at'=>$now,
            ]);
        }

        foreach ([
            ['Main Navigation','header'],
            ['Footer Shop','footer_shop'],
            ['Footer Company','footer_company'],
        ] as [$name,$location]) {
            DB::table('navigation_menus')->updateOrInsert(['location'=>$location],[
                'name'=>$name,'status'=>true,'created_at'=>$now,'updated_at'=>$now,
            ]);
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')->whereIn('key',['growth.manage','integrations.manage','purchase_orders.manage'])->pluck('id');
        if ($permissionIds->isNotEmpty()) DB::table('admin_role_permission')->whereIn('permission_id',$permissionIds)->delete();
        DB::table('permissions')->whereIn('id',$permissionIds)->delete();

        Schema::dropIfExists('product_questions');
        Schema::dropIfExists('store_domains');
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('navigation_menus');
        Schema::dropIfExists('affiliate_referrals');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('integrations');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('gift_card_transactions');
        Schema::dropIfExists('gift_cards');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('customer_segment_user');
        Schema::dropIfExists('customer_segments');
        Schema::dropIfExists('draft_order_items');
        Schema::dropIfExists('draft_orders');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('store_credit');
        });
    }
};
