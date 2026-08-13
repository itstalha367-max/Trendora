<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\AbandonedCartController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\WishlistController as AdminWishlistController;
use App\Http\Controllers\Admin\ProductVariationController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Frontend\WishlistController as FrontendWishlistController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Read-only Trendora API — bearer keys are generated from Admin → API & Webhooks.
Route::prefix('api/trendora/v1')->middleware('throttle:60,1')->group(function () {
    Route::get('/products', [App\Http\Controllers\Api\StoreApiController::class, 'products'])->middleware('trendora.api:catalog.read')->name('api.trendora.products');
    Route::get('/orders', [App\Http\Controllers\Api\StoreApiController::class, 'orders'])->middleware('trendora.api:orders.read')->name('api.trendora.orders');
});

// ============================================
// 🏠 FRONTEND ROUTES
// ============================================

Route::get('/', [HomeController::class, 'index'])->name('home');

// ✅ Product Routes (Frontend) - FIXED
Route::get('/products', [FrontendProductController::class, 'index'])->name('products.index');
Route::get('/categories', [FrontendProductController::class, 'categories'])->name('categories.index');
Route::get('/category/{category:slug}', [FrontendProductController::class, 'category'])->name('categories.show');
Route::get('/search', [FrontendProductController::class, 'search'])->name('search.results');
Route::get('/product/{slug}', [FrontendProductController::class, 'show'])->name('products.show');
Route::post('/product/{product}/questions', [App\Http\Controllers\Frontend\ProductQuestionController::class, 'store'])->middleware('throttle:6,1')->name('products.questions.store');

// ============================================
// 🔐 AUTH ROUTES
// ============================================

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'adminLogin'])->middleware('throttle:5,1')->name('admin.login.submit');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,1');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');


// Account security routes
Route::middleware(['auth'])->group(function () {
    Route::get('/2fa/setup', [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.submit');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');

    Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm'])->name('password.confirm.store');
});

// ============================================
// ❤️ Wishlist Routes
// ============================================

Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [FrontendWishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{productId}', [FrontendWishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{id}', [FrontendWishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/toggle/{productId}', [FrontendWishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist/count', [FrontendWishlistController::class, 'count'])->name('wishlist.count');
    Route::get('/wishlist/check/{productId}', function($productId) {
        $exists = App\Models\Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->exists();
        return response()->json(['in_wishlist' => $exists]);
    })->name('wishlist.check');
});

// ============================================
// 👑 ADMIN ROUTES
// ============================================

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    
    // 📊 Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // 📦 Products
    Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index')->middleware('admin.permission:products.manage');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create')->middleware('admin.permission:products.manage');
    Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store')->middleware('admin.permission:products.manage');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit')->middleware('admin.permission:products.manage');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('admin.products.update')->middleware('admin.permission:products.manage');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy')->middleware('admin.permission:products.manage');
    
    // 📦 Product Variations
    Route::prefix('products/{product}/variations')->group(function () {
        Route::get('/', [ProductVariationController::class, 'index'])->name('admin.products.variations.index')->middleware('admin.permission:products.manage');
        Route::get('/create', [ProductVariationController::class, 'create'])->name('admin.products.variations.create')->middleware('admin.permission:products.manage');
        Route::post('/', [ProductVariationController::class, 'store'])->name('admin.products.variations.store')->middleware('admin.permission:products.manage');
        Route::get('/{variation}/edit', [ProductVariationController::class, 'edit'])->name('admin.products.variations.edit')->middleware('admin.permission:products.manage');
        Route::put('/{variation}', [ProductVariationController::class, 'update'])->name('admin.products.variations.update')->middleware('admin.permission:products.manage');
        Route::delete('/{variation}', [ProductVariationController::class, 'destroy'])->name('admin.products.variations.destroy')->middleware('admin.permission:products.manage');
        Route::get('/{variation}/toggle', [ProductVariationController::class, 'toggleStatus'])->name('admin.products.variations.toggle')->middleware('admin.permission:products.manage');
    });
    
    // 📦 Product Import/Export
    Route::get('/products/import', [AdminProductController::class, 'importForm'])->name('admin.products.import')->middleware('admin.permission:products.manage');
    Route::post('/products/import', [AdminProductController::class, 'import'])->name('admin.products.import.store')->middleware('admin.permission:products.manage');
    Route::get('/products/export', [AdminProductController::class, 'export'])->name('admin.products.export')->middleware('admin.permission:products.manage');
    Route::get('/products/sample', [AdminProductController::class, 'downloadSample'])->name('admin.products.sample')->middleware('admin.permission:products.manage');
    
    // 📦 Bulk Operations
    Route::post('/products/bulk-delete', [AdminProductController::class, 'bulkDelete'])->name('admin.products.bulk-delete')->middleware('admin.permission:products.manage');
    Route::post('/products/bulk-status', [AdminProductController::class, 'bulkStatus'])->name('admin.products.bulk-status')->middleware('admin.permission:products.manage');
    
    // 🏷️ Categories
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index')->middleware('admin.permission:categories.manage');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('admin.categories.create')->middleware('admin.permission:categories.manage');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store')->middleware('admin.permission:categories.manage');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit')->middleware('admin.permission:categories.manage');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update')->middleware('admin.permission:categories.manage');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy')->middleware('admin.permission:categories.manage');
    
    // 📋 Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index')->middleware('admin.permission:orders.manage');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show')->middleware('admin.permission:orders.manage');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status')->middleware('admin.permission:orders.manage');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy')->middleware('admin.permission:orders.manage');
    Route::get('/orders/{id}/invoice', [OrderController::class, 'generateInvoice'])->name('admin.orders.invoice')->middleware('admin.permission:orders.manage');
    Route::get('/orders/{id}/preview', [OrderController::class, 'previewInvoice'])->name('admin.orders.preview')->middleware('admin.permission:orders.manage');
    Route::post('/orders/{id}/send-email', [OrderController::class, 'sendConfirmationEmail'])->name('admin.orders.send-email')->middleware('admin.permission:orders.manage');
    Route::post('/orders/bulk-delete', [OrderController::class, 'bulkDelete'])->name('admin.orders.bulk-delete')->middleware('admin.permission:orders.manage');
    Route::post('/orders/bulk-status', [OrderController::class, 'bulkStatus'])->name('admin.orders.bulk-status')->middleware('admin.permission:orders.manage');
    
    // 👥 Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index')->middleware('admin.permission:customers.manage');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show')->middleware('admin.permission:customers.manage');
    Route::put('/users/{id}/role', [UserController::class, 'updateRole'])->name('admin.users.role')->middleware('admin.permission:customers.manage');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy')->middleware('admin.permission:customers.manage');
    Route::get('/users/export', [UserController::class, 'export'])->name('admin.users.export')->middleware('admin.permission:customers.manage');
    
    // 🎫 Coupons
    Route::get('/coupons', [CouponController::class, 'index'])->name('admin.coupons.index')->middleware('admin.permission:marketing.manage');
    Route::get('/coupons/create', [CouponController::class, 'create'])->name('admin.coupons.create')->middleware('admin.permission:marketing.manage');
    Route::post('/coupons', [CouponController::class, 'store'])->name('admin.coupons.store')->middleware('admin.permission:marketing.manage');
    Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('admin.coupons.edit')->middleware('admin.permission:marketing.manage');
    Route::put('/coupons/{coupon}', [CouponController::class, 'update'])->name('admin.coupons.update')->middleware('admin.permission:marketing.manage');
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])->name('admin.coupons.destroy')->middleware('admin.permission:marketing.manage');
    Route::get('/coupons/{id}/toggle', [CouponController::class, 'toggleStatus'])->name('admin.coupons.toggle')->middleware('admin.permission:marketing.manage');
    
    // 📝 Blogs
    Route::get('/blogs', [BlogController::class, 'index'])->name('admin.blogs.index')->middleware('admin.permission:content.manage');
    Route::get('/blogs/create', [BlogController::class, 'create'])->name('admin.blogs.create')->middleware('admin.permission:content.manage');
    Route::post('/blogs', [BlogController::class, 'store'])->name('admin.blogs.store')->middleware('admin.permission:content.manage');
    Route::get('/blogs/{blog}/edit', [BlogController::class, 'edit'])->name('admin.blogs.edit')->middleware('admin.permission:content.manage');
    Route::put('/blogs/{blog}', [BlogController::class, 'update'])->name('admin.blogs.update')->middleware('admin.permission:content.manage');
    Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])->name('admin.blogs.destroy')->middleware('admin.permission:content.manage');
    Route::post('/blogs/{id}/toggle', [BlogController::class, 'toggleStatus'])->name('admin.blogs.toggle')->middleware('admin.permission:content.manage');
    Route::get('/blogs/{id}/comments', [BlogController::class, 'comments'])->name('admin.blogs.comments')->middleware('admin.permission:content.manage');
    Route::put('/comments/{id}/{action}', [BlogController::class, 'commentAction'])->name('admin.blogs.comment.action')->middleware('admin.permission:content.manage');
    
    // ⭐ Reviews
    Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index')->middleware('admin.permission:customers.manage');
    Route::get('/reviews/{id}', [ReviewController::class, 'show'])->name('admin.reviews.show')->middleware('admin.permission:customers.manage');
    Route::get('/reviews/{id}/approve', [ReviewController::class, 'approve'])->name('admin.reviews.approve')->middleware('admin.permission:customers.manage');
    Route::get('/reviews/{id}/reject', [ReviewController::class, 'reject'])->name('admin.reviews.reject')->middleware('admin.permission:customers.manage');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy')->middleware('admin.permission:customers.manage');
    
    // 📊 Reports
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports')->middleware('admin.permission:reports.view');
    Route::get('/reports/sales', [App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('admin.reports.sales')->middleware('admin.permission:reports.view');
    Route::get('/reports/products', [App\Http\Controllers\Admin\ReportController::class, 'products'])->name('admin.reports.products')->middleware('admin.permission:reports.view');
    Route::get('/reports/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('admin.reports.users')->middleware('admin.permission:reports.view');
    Route::get('/reports/inventory', [App\Http\Controllers\Admin\ReportController::class, 'inventory'])->name('admin.reports.inventory')->middleware('admin.permission:reports.view');
    Route::get('/reports/marketing', [App\Http\Controllers\Admin\ReportController::class, 'marketing'])->name('admin.reports.marketing')->middleware('admin.permission:reports.view');
    Route::get('/reports/export/{report}', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('admin.reports.export')->middleware('admin.permission:reports.view');
    
    // ⚙️ Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings')->middleware('admin.permission:settings.manage');
    Route::get('/settings/mail', [App\Http\Controllers\Admin\MailSettingsController::class, 'index'])->name('admin.mail-settings.index')->middleware('admin.permission:settings.manage');
    Route::put('/settings/mail', [App\Http\Controllers\Admin\MailSettingsController::class, 'update'])->name('admin.mail-settings.update')->middleware('admin.permission:settings.manage');
    Route::put('/settings', [SettingsController::class, 'update'])->name('admin.settings.update')->middleware('admin.permission:settings.manage');
    Route::get('/settings/reset', [SettingsController::class, 'reset'])->name('admin.settings.reset')->middleware('admin.permission:settings.manage');
    
    // 💳 Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments')->middleware('admin.permission:settings.manage');
    Route::put('/payments', [PaymentController::class, 'update'])->name('admin.payments.update')->middleware('admin.permission:settings.manage');
    Route::post('/payments/test-stripe', [PaymentController::class, 'testStripe'])->name('admin.payments.test-stripe')->middleware('admin.permission:settings.manage');
    Route::post('/payments/test-paypal', [PaymentController::class, 'testPayPal'])->name('admin.payments.test-paypal')->middleware('admin.permission:settings.manage');
    Route::get('/payments/status', [PaymentController::class, 'getGatewayStatus'])->name('admin.payments.status')->middleware('admin.permission:settings.manage');
    
    // 🔍 SEO
    Route::get('/seo', [SeoController::class, 'index'])->name('admin.seo.index')->middleware('admin.permission:content.manage');
    Route::put('/seo', [SeoController::class, 'update'])->name('admin.seo.update')->middleware('admin.permission:content.manage');
    Route::post('/seo/reset/{page}', [SeoController::class, 'reset'])->name('admin.seo.reset')->middleware('admin.permission:content.manage');
    Route::get('/seo/sitemap', [SeoController::class, 'generateSitemap'])->name('admin.seo.sitemap')->middleware('admin.permission:content.manage');
    
    // 💾 Backup
    Route::get('/backup', [BackupController::class, 'index'])->name('admin.backup.index')->middleware('admin.permission:security.manage');
    Route::post('/backup/create', [BackupController::class, 'create'])->name('admin.backup.create')->middleware('admin.permission:security.manage');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('admin.backup.download')->middleware('admin.permission:security.manage');
    Route::delete('/backup/delete/{filename}', [BackupController::class, 'delete'])->name('admin.backup.delete')->middleware('admin.permission:security.manage');
    Route::delete('/backup/delete-all', [BackupController::class, 'deleteAll'])->name('admin.backup.delete-all')->middleware('admin.permission:security.manage');
    Route::post('/backup/restore/{filename}', [BackupController::class, 'restore'])->name('admin.backup.restore')->middleware('admin.permission:security.manage');
    Route::get('/backup/schedule', [BackupController::class, 'schedule'])->name('admin.backup.schedule')->middleware('admin.permission:security.manage');
    Route::put('/backup/schedule', [BackupController::class, 'updateSchedule'])->name('admin.backup.schedule.update')->middleware('admin.permission:security.manage');
    
    // 🛒 Abandoned Carts
    Route::get('/abandoned-carts', [AbandonedCartController::class, 'index'])->name('admin.abandoned-carts.index')->middleware('admin.permission:orders.manage');
    Route::get('/abandoned-carts/{id}', [AbandonedCartController::class, 'show'])->name('admin.abandoned-carts.show')->middleware('admin.permission:orders.manage');
    Route::get('/abandoned-carts/{id}/send-reminder', [AbandonedCartController::class, 'sendReminder'])->name('admin.abandoned-carts.send-reminder')->middleware('admin.permission:orders.manage');
    Route::get('/abandoned-carts/{id}/recover', [AbandonedCartController::class, 'markRecovered'])->name('admin.abandoned-carts.recover')->middleware('admin.permission:orders.manage');
    Route::delete('/abandoned-carts/{id}', [AbandonedCartController::class, 'destroy'])->name('admin.abandoned-carts.destroy')->middleware('admin.permission:orders.manage');
    Route::post('/abandoned-carts/bulk-reminder', [AbandonedCartController::class, 'sendBulkReminder'])->name('admin.abandoned-carts.bulk-reminder')->middleware('admin.permission:orders.manage');
    
    // 📝 Activity Log
    Route::get('/activity', [ActivityLogController::class, 'index'])->name('admin.activity.index')->middleware('admin.permission:security.manage');
    Route::get('/activity/{id}', [ActivityLogController::class, 'show'])->name('admin.activity.show')->middleware('admin.permission:security.manage');
    Route::delete('/activity/clear', [ActivityLogController::class, 'clear'])->name('admin.activity.clear')->middleware('admin.permission:security.manage');
    Route::delete('/activity/clear-old', [ActivityLogController::class, 'clearOld'])->name('admin.activity.clear-old')->middleware('admin.permission:security.manage');
    
    // ❤️ Admin Wishlist
    Route::get('/wishlist', [AdminWishlistController::class, 'index'])->name('admin.wishlist.index')->middleware('admin.permission:customers.manage');
    Route::get('/wishlist/{id}', [AdminWishlistController::class, 'show'])->name('admin.wishlist.show')->middleware('admin.permission:customers.manage');
    Route::delete('/wishlist/{id}', [AdminWishlistController::class, 'destroy'])->name('admin.wishlist.destroy')->middleware('admin.permission:customers.manage');
    Route::delete('/wishlist/user/{userId}/clear', [AdminWishlistController::class, 'clearUser'])->name('admin.wishlist.clear-user')->middleware('admin.permission:customers.manage');
    Route::get('/wishlist/stats', [AdminWishlistController::class, 'getStats'])->name('admin.wishlist.stats')->middleware('admin.permission:customers.manage');


    // ↩️ Returns & Refund Operations
    Route::get('/returns', [App\Http\Controllers\Admin\ReturnManagementController::class, 'index'])->name('admin.returns.index')->middleware('admin.permission:returns.manage');
    Route::get('/returns/{returnRequest}', [App\Http\Controllers\Admin\ReturnManagementController::class, 'show'])->name('admin.returns.show')->middleware('admin.permission:returns.manage');
    Route::put('/returns/{returnRequest}', [App\Http\Controllers\Admin\ReturnManagementController::class, 'update'])->name('admin.returns.update')->middleware('admin.permission:returns.manage');

    // 🎧 Support & Contact Inbox
    Route::get('/support', [App\Http\Controllers\Admin\SupportManagementController::class, 'index'])->name('admin.support.index')->middleware('admin.permission:support.manage');
    Route::get('/support/{ticket}', [App\Http\Controllers\Admin\SupportManagementController::class, 'show'])->name('admin.support.show')->middleware('admin.permission:support.manage');
    Route::post('/support/{ticket}/reply', [App\Http\Controllers\Admin\SupportManagementController::class, 'reply'])->name('admin.support.reply')->middleware('admin.permission:support.manage');
    Route::put('/support/{ticket}', [App\Http\Controllers\Admin\SupportManagementController::class, 'update'])->name('admin.support.update')->middleware('admin.permission:support.manage');
    Route::get('/contacts', [App\Http\Controllers\Admin\ContactManagementController::class, 'index'])->name('admin.contacts.index')->middleware('admin.permission:support.manage');
    Route::get('/contacts/{contact}', [App\Http\Controllers\Admin\ContactManagementController::class, 'show'])->name('admin.contacts.show')->middleware('admin.permission:support.manage');
    Route::put('/contacts/{contact}', [App\Http\Controllers\Admin\ContactManagementController::class, 'update'])->name('admin.contacts.update')->middleware('admin.permission:support.manage');
    Route::delete('/contacts/{contact}', [App\Http\Controllers\Admin\ContactManagementController::class, 'destroy'])->name('admin.contacts.destroy')->middleware('admin.permission:support.manage');

    // 🏷️ Catalog Expansion
    Route::resource('brands', App\Http\Controllers\Admin\BrandController::class, ['as' => 'admin'])->except('show')->middleware('admin.permission:products.manage');
    Route::resource('collections', App\Http\Controllers\Admin\CollectionController::class, ['as' => 'admin'])->except('show')->middleware('admin.permission:products.manage');

    // 🏭 Inventory & Supply Chain
    Route::resource('warehouses', App\Http\Controllers\Admin\WarehouseController::class, ['as' => 'admin'])->except('show')->middleware('admin.permission:inventory.manage');
    Route::get('/inventory', [App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('admin.inventory.index')->middleware('admin.permission:inventory.manage');
    Route::get('/inventory/create', [App\Http\Controllers\Admin\InventoryController::class, 'create'])->name('admin.inventory.create')->middleware('admin.permission:inventory.manage');
    Route::post('/inventory', [App\Http\Controllers\Admin\InventoryController::class, 'store'])->name('admin.inventory.store')->middleware('admin.permission:inventory.manage');
    Route::post('/inventory/{inventory}/adjust', [App\Http\Controllers\Admin\InventoryController::class, 'adjust'])->name('admin.inventory.adjust')->middleware('admin.permission:inventory.manage');
    Route::get('/inventory/{inventory}/movements', [App\Http\Controllers\Admin\InventoryController::class, 'movements'])->name('admin.inventory.movements')->middleware('admin.permission:inventory.manage');
    Route::resource('suppliers', App\Http\Controllers\Admin\SupplierController::class, ['as' => 'admin'])->except('show')->middleware('admin.permission:inventory.manage');

    // 📣 Marketing Studio
    Route::resource('campaigns', App\Http\Controllers\Admin\CampaignController::class, ['as' => 'admin'])->except('show')->middleware('admin.permission:marketing.manage');
    Route::resource('banners', App\Http\Controllers\Admin\BannerController::class, ['as' => 'admin'])->except('show')->middleware('admin.permission:marketing.manage');

    // 🚚 Shipping & Tax
    Route::get('/shipping', [App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('admin.shipping.index')->middleware('admin.permission:shipping.manage');
    Route::post('/shipping/zones', [App\Http\Controllers\Admin\ShippingController::class, 'storeZone'])->name('admin.shipping.zones.store')->middleware('admin.permission:shipping.manage');
    Route::put('/shipping/zones/{zone}', [App\Http\Controllers\Admin\ShippingController::class, 'updateZone'])->name('admin.shipping.zones.update')->middleware('admin.permission:shipping.manage');
    Route::delete('/shipping/zones/{zone}', [App\Http\Controllers\Admin\ShippingController::class, 'destroyZone'])->name('admin.shipping.zones.destroy')->middleware('admin.permission:shipping.manage');
    Route::post('/shipping/zones/{zone}/methods', [App\Http\Controllers\Admin\ShippingController::class, 'storeMethod'])->name('admin.shipping.methods.store')->middleware('admin.permission:shipping.manage');
    Route::delete('/shipping/methods/{method}', [App\Http\Controllers\Admin\ShippingController::class, 'destroyMethod'])->name('admin.shipping.methods.destroy')->middleware('admin.permission:shipping.manage');
    Route::get('/taxes', [App\Http\Controllers\Admin\TaxRateController::class, 'index'])->name('admin.taxes.index')->middleware('admin.permission:tax.manage');
    Route::post('/taxes', [App\Http\Controllers\Admin\TaxRateController::class, 'store'])->name('admin.taxes.store')->middleware('admin.permission:tax.manage');
    Route::put('/taxes/{tax}', [App\Http\Controllers\Admin\TaxRateController::class, 'update'])->name('admin.taxes.update')->middleware('admin.permission:tax.manage');
    Route::delete('/taxes/{tax}', [App\Http\Controllers\Admin\TaxRateController::class, 'destroy'])->name('admin.taxes.destroy')->middleware('admin.permission:tax.manage');

    // 🛡️ Staff, Roles & Operations
    Route::get('/staff', [App\Http\Controllers\Admin\StaffController::class, 'index'])->name('admin.staff.index')->middleware('admin.permission:staff.manage');
    Route::get('/staff/create', [App\Http\Controllers\Admin\StaffController::class, 'create'])->name('admin.staff.create')->middleware('admin.permission:staff.manage');
    Route::post('/staff', [App\Http\Controllers\Admin\StaffController::class, 'store'])->name('admin.staff.store')->middleware('admin.permission:staff.manage');
    Route::get('/staff/{staffUser}/edit', [App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('admin.staff.edit')->middleware('admin.permission:staff.manage');
    Route::put('/staff/{staffUser}', [App\Http\Controllers\Admin\StaffController::class, 'update'])->name('admin.staff.update')->middleware('admin.permission:staff.manage');
    Route::delete('/staff/{staffUser}', [App\Http\Controllers\Admin\StaffController::class, 'destroy'])->name('admin.staff.destroy')->middleware('admin.permission:staff.manage');
    Route::get('/roles', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin.roles.index')->middleware('admin.permission:staff.manage');
    Route::post('/roles', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('admin.roles.store')->middleware('admin.permission:staff.manage');
    Route::put('/roles/{role}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('admin.roles.update')->middleware('admin.permission:staff.manage');
    Route::delete('/roles/{role}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('admin.roles.destroy')->middleware('admin.permission:staff.manage');
    Route::get('/live-store', [App\Http\Controllers\Admin\OperationsController::class, 'live'])->name('admin.operations.live')->middleware('admin.permission:reports.view');
    Route::get('/system-status', [App\Http\Controllers\Admin\OperationsController::class, 'system'])->name('admin.operations.system')->middleware('admin.permission:security.manage');
    Route::get('/production-readiness', [App\Http\Controllers\Admin\ProductionReadinessController::class, 'index'])->name('admin.readiness.index')->middleware('admin.permission:security.manage');
    Route::post('/production-readiness/mail-test', [App\Http\Controllers\Admin\ProductionReadinessController::class, 'testMail'])->name('admin.readiness.mail-test')->middleware(['admin.permission:security.manage','throttle:5,1']);
    Route::post('/production-readiness/gateway-test/{gateway}', [App\Http\Controllers\Admin\ProductionReadinessController::class, 'testGateway'])->name('admin.readiness.gateway-test')->middleware(['admin.permission:security.manage','throttle:10,1']);

    // Phase 4 — Finance, CMS, theme and deeper analytics
    Route::get('/analytics/commerce', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics.commerce')->middleware('admin.permission:reports.view');
    Route::get('/finance', [App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('admin.finance.index')->middleware('admin.permission:finance.manage');
    Route::get('/finance/transactions', [App\Http\Controllers\Admin\FinanceController::class, 'transactions'])->name('admin.transactions.index')->middleware('admin.permission:finance.manage');
    Route::post('/finance/orders/{order}/mark-paid', [App\Http\Controllers\Admin\FinanceController::class, 'markPaid'])->name('admin.finance.mark-paid')->middleware('admin.permission:finance.manage');
    Route::get('/finance/refunds', [App\Http\Controllers\Admin\FinanceController::class, 'refunds'])->name('admin.finance.refunds')->middleware('admin.permission:finance.manage');
    Route::post('/finance/refunds', [App\Http\Controllers\Admin\FinanceController::class, 'storeRefund'])->name('admin.finance.refunds.store')->middleware('admin.permission:finance.manage');
    Route::resource('cms', App\Http\Controllers\Admin\CmsPageController::class, ['as' => 'admin'])->except('show')->middleware('admin.permission:content.manage');
    Route::get('/theme', [App\Http\Controllers\Admin\ThemeController::class, 'index'])->name('admin.theme.index')->middleware('admin.permission:theme.manage');
    Route::put('/theme', [App\Http\Controllers\Admin\ThemeController::class, 'update'])->name('admin.theme.update')->middleware('admin.permission:theme.manage');

    // Phase 5 — Growth, automation, procurement & developer platform
    Route::resource('draft-orders', App\Http\Controllers\Admin\DraftOrderController::class, ['as' => 'admin'])->except(['update'])->middleware('admin.permission:orders.manage');
    Route::put('/draft-orders/{draftOrder}', [App\Http\Controllers\Admin\DraftOrderController::class, 'update'])->name('admin.draft-orders.update')->middleware('admin.permission:orders.manage');
    Route::put('/draft-orders/{draftOrder}/status', [App\Http\Controllers\Admin\DraftOrderController::class, 'status'])->name('admin.draft-orders.status')->middleware('admin.permission:orders.manage');
    Route::post('/draft-orders/{draftOrder}/convert', [App\Http\Controllers\Admin\DraftOrderController::class, 'convert'])->name('admin.draft-orders.convert')->middleware('admin.permission:orders.manage');

    Route::get('/segments', [App\Http\Controllers\Admin\SegmentController::class, 'index'])->name('admin.segments.index')->middleware('admin.permission:growth.manage');
    Route::post('/segments', [App\Http\Controllers\Admin\SegmentController::class, 'store'])->name('admin.segments.store')->middleware('admin.permission:growth.manage');
    Route::put('/segments/{segment}', [App\Http\Controllers\Admin\SegmentController::class, 'update'])->name('admin.segments.update')->middleware('admin.permission:growth.manage');
    Route::post('/segments/{segment}/refresh', [App\Http\Controllers\Admin\SegmentController::class, 'refresh'])->name('admin.segments.refresh')->middleware('admin.permission:growth.manage');
    Route::delete('/segments/{segment}', [App\Http\Controllers\Admin\SegmentController::class, 'destroy'])->name('admin.segments.destroy')->middleware('admin.permission:growth.manage');

    Route::get('/promotions', [App\Http\Controllers\Admin\PromotionController::class, 'index'])->name('admin.promotions.index')->middleware('admin.permission:growth.manage');
    Route::post('/promotions', [App\Http\Controllers\Admin\PromotionController::class, 'store'])->name('admin.promotions.store')->middleware('admin.permission:growth.manage');
    Route::put('/promotions/{promotion}', [App\Http\Controllers\Admin\PromotionController::class, 'update'])->name('admin.promotions.update')->middleware('admin.permission:growth.manage');
    Route::delete('/promotions/{promotion}', [App\Http\Controllers\Admin\PromotionController::class, 'destroy'])->name('admin.promotions.destroy')->middleware('admin.permission:growth.manage');

    Route::get('/gift-cards', [App\Http\Controllers\Admin\GiftCardController::class, 'index'])->name('admin.gift-cards.index')->middleware('admin.permission:growth.manage');
    Route::post('/gift-cards', [App\Http\Controllers\Admin\GiftCardController::class, 'store'])->name('admin.gift-cards.store')->middleware('admin.permission:growth.manage');
    Route::post('/gift-cards/{giftCard}/adjust', [App\Http\Controllers\Admin\GiftCardController::class, 'adjust'])->name('admin.gift-cards.adjust')->middleware('admin.permission:growth.manage');
    Route::post('/gift-cards/{giftCard}/toggle', [App\Http\Controllers\Admin\GiftCardController::class, 'toggle'])->name('admin.gift-cards.toggle')->middleware('admin.permission:growth.manage');
    Route::post('/users/{user}/wallet', [App\Http\Controllers\Admin\GiftCardController::class, 'walletAdjust'])->name('admin.users.wallet')->middleware('admin.permission:customers.manage');

    Route::get('/email-templates', [App\Http\Controllers\Admin\TemplateController::class, 'emails'])->name('admin.templates.emails')->middleware('admin.permission:content.manage');
    Route::put('/email-templates/{template}', [App\Http\Controllers\Admin\TemplateController::class, 'updateEmail'])->name('admin.templates.emails.update')->middleware('admin.permission:content.manage');
    Route::get('/notification-templates', [App\Http\Controllers\Admin\TemplateController::class, 'notifications'])->name('admin.templates.notifications')->middleware('admin.permission:content.manage');
    Route::put('/notification-templates/{template}', [App\Http\Controllers\Admin\TemplateController::class, 'updateNotification'])->name('admin.templates.notifications.update')->middleware('admin.permission:content.manage');

    Route::get('/integrations', [App\Http\Controllers\Admin\IntegrationController::class, 'index'])->name('admin.integrations.index')->middleware('admin.permission:integrations.manage');
    Route::put('/integrations/{integration}', [App\Http\Controllers\Admin\IntegrationController::class, 'update'])->name('admin.integrations.update')->middleware('admin.permission:integrations.manage');
    Route::get('/developer', [App\Http\Controllers\Admin\ApiWebhookController::class, 'index'])->name('admin.developer.index')->middleware('admin.permission:integrations.manage');
    Route::post('/developer/api-keys', [App\Http\Controllers\Admin\ApiWebhookController::class, 'createKey'])->name('admin.developer.keys.store')->middleware('admin.permission:integrations.manage');
    Route::post('/developer/api-keys/{apiKey}/revoke', [App\Http\Controllers\Admin\ApiWebhookController::class, 'revokeKey'])->name('admin.developer.keys.revoke')->middleware('admin.permission:integrations.manage');
    Route::post('/developer/webhooks', [App\Http\Controllers\Admin\ApiWebhookController::class, 'storeWebhook'])->name('admin.developer.webhooks.store')->middleware('admin.permission:integrations.manage');
    Route::put('/developer/webhooks/{webhook}', [App\Http\Controllers\Admin\ApiWebhookController::class, 'updateWebhook'])->name('admin.developer.webhooks.update')->middleware('admin.permission:integrations.manage');
    Route::delete('/developer/webhooks/{webhook}', [App\Http\Controllers\Admin\ApiWebhookController::class, 'destroyWebhook'])->name('admin.developer.webhooks.destroy')->middleware('admin.permission:integrations.manage');
    Route::post('/developer/webhooks/{webhook}/test', [App\Http\Controllers\Admin\ApiWebhookController::class, 'testWebhook'])->name('admin.developer.webhooks.test')->middleware('admin.permission:integrations.manage');

    Route::get('/purchase-orders', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'index'])->name('admin.purchase-orders.index')->middleware('admin.permission:purchase_orders.manage');
    Route::get('/purchase-orders/create', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'create'])->name('admin.purchase-orders.create')->middleware('admin.permission:purchase_orders.manage');
    Route::post('/purchase-orders', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'store'])->name('admin.purchase-orders.store')->middleware('admin.permission:purchase_orders.manage');
    Route::get('/purchase-orders/{purchaseOrder}', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'show'])->name('admin.purchase-orders.show')->middleware('admin.permission:purchase_orders.manage');
    Route::put('/purchase-orders/{purchaseOrder}/status', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'status'])->name('admin.purchase-orders.status')->middleware('admin.permission:purchase_orders.manage');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'receive'])->name('admin.purchase-orders.receive')->middleware('admin.permission:purchase_orders.manage');

    Route::get('/affiliates', [App\Http\Controllers\Admin\AffiliateController::class, 'index'])->name('admin.affiliates.index')->middleware('admin.permission:growth.manage');
    Route::post('/affiliates', [App\Http\Controllers\Admin\AffiliateController::class, 'store'])->name('admin.affiliates.store')->middleware('admin.permission:growth.manage');
    Route::put('/affiliates/{affiliate}', [App\Http\Controllers\Admin\AffiliateController::class, 'update'])->name('admin.affiliates.update')->middleware('admin.permission:growth.manage');
    Route::delete('/affiliates/{affiliate}', [App\Http\Controllers\Admin\AffiliateController::class, 'destroy'])->name('admin.affiliates.destroy')->middleware('admin.permission:growth.manage');
    Route::post('/affiliate-referrals/{referral}/pay', [App\Http\Controllers\Admin\AffiliateController::class, 'pay'])->name('admin.affiliates.pay')->middleware('admin.permission:finance.manage');

    Route::get('/navigation', [App\Http\Controllers\Admin\NavigationController::class, 'index'])->name('admin.navigation.index')->middleware('admin.permission:content.manage');
    Route::post('/navigation', [App\Http\Controllers\Admin\NavigationController::class, 'storeMenu'])->name('admin.navigation.store')->middleware('admin.permission:content.manage');
    Route::post('/navigation/{menu}/items', [App\Http\Controllers\Admin\NavigationController::class, 'storeItem'])->name('admin.navigation.items.store')->middleware('admin.permission:content.manage');
    Route::delete('/navigation/items/{item}', [App\Http\Controllers\Admin\NavigationController::class, 'destroyItem'])->name('admin.navigation.items.destroy')->middleware('admin.permission:content.manage');

    Route::get('/domains', [App\Http\Controllers\Admin\DomainController::class, 'index'])->name('admin.domains.index')->middleware('admin.permission:settings.manage');
    Route::post('/domains', [App\Http\Controllers\Admin\DomainController::class, 'store'])->name('admin.domains.store')->middleware('admin.permission:settings.manage');
    Route::post('/domains/{domain}/primary', [App\Http\Controllers\Admin\DomainController::class, 'primary'])->name('admin.domains.primary')->middleware('admin.permission:settings.manage');
    Route::post('/domains/{domain}/verify', [App\Http\Controllers\Admin\DomainController::class, 'verify'])->name('admin.domains.verify')->middleware('admin.permission:settings.manage');
    Route::delete('/domains/{domain}', [App\Http\Controllers\Admin\DomainController::class, 'destroy'])->name('admin.domains.destroy')->middleware('admin.permission:settings.manage');

    // Phase 6 — Finalization: payouts, dedicated commerce settings and webhook retries
    Route::get('/finance/payouts', [App\Http\Controllers\Admin\PayoutController::class, 'index'])->name('admin.payouts.index')->middleware('admin.permission:payouts.manage');
    Route::post('/finance/payouts', [App\Http\Controllers\Admin\PayoutController::class, 'store'])->name('admin.payouts.store')->middleware('admin.permission:payouts.manage');
    Route::put('/finance/payouts/{payout}/process', [App\Http\Controllers\Admin\PayoutController::class, 'process'])->name('admin.payouts.process')->middleware('admin.permission:payouts.manage');
    Route::post('/finance/payouts/{payout}/cancel', [App\Http\Controllers\Admin\PayoutController::class, 'cancel'])->name('admin.payouts.cancel')->middleware('admin.permission:payouts.manage');

    Route::get('/settings/store-details', [App\Http\Controllers\Admin\CommerceSettingsController::class, 'storeDetails'])->name('admin.commerce-settings.store')->middleware('admin.permission:settings.manage');
    Route::put('/settings/store-details', [App\Http\Controllers\Admin\CommerceSettingsController::class, 'updateStore'])->name('admin.commerce-settings.store.update')->middleware('admin.permission:settings.manage');
    Route::get('/settings/checkout', [App\Http\Controllers\Admin\CommerceSettingsController::class, 'checkout'])->name('admin.commerce-settings.checkout')->middleware('admin.permission:settings.manage');
    Route::put('/settings/checkout', [App\Http\Controllers\Admin\CommerceSettingsController::class, 'updateCheckout'])->name('admin.commerce-settings.checkout.update')->middleware('admin.permission:settings.manage');
    Route::get('/settings/shipping', [App\Http\Controllers\Admin\CommerceSettingsController::class, 'shipping'])->name('admin.commerce-settings.shipping')->middleware('admin.permission:settings.manage');
    Route::put('/settings/shipping', [App\Http\Controllers\Admin\CommerceSettingsController::class, 'updateShipping'])->name('admin.commerce-settings.shipping.update')->middleware('admin.permission:settings.manage');
    Route::get('/settings/tax', [App\Http\Controllers\Admin\CommerceSettingsController::class, 'tax'])->name('admin.commerce-settings.tax')->middleware('admin.permission:settings.manage');
    Route::put('/settings/tax', [App\Http\Controllers\Admin\CommerceSettingsController::class, 'updateTax'])->name('admin.commerce-settings.tax.update')->middleware('admin.permission:settings.manage');

    Route::post('/developer/deliveries/{delivery}/retry', [App\Http\Controllers\Admin\ApiWebhookController::class, 'retryDelivery'])->name('admin.developer.deliveries.retry')->middleware('admin.permission:integrations.manage');

    Route::get('/product-questions', [App\Http\Controllers\Admin\ProductQuestionController::class, 'index'])->name('admin.questions.index')->middleware('admin.permission:support.manage');
    Route::put('/product-questions/{question}', [App\Http\Controllers\Admin\ProductQuestionController::class, 'answer'])->name('admin.questions.answer')->middleware('admin.permission:support.manage');
    Route::delete('/product-questions/{question}', [App\Http\Controllers\Admin\ProductQuestionController::class, 'destroy'])->name('admin.questions.destroy')->middleware('admin.permission:support.manage');

    // Phase 8 — newsletter operations
    Route::get('/newsletter', [App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'index'])->name('admin.newsletter.index')->middleware('admin.permission:marketing.manage');
    Route::post('/newsletter/{subscriber}/toggle', [App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'toggle'])->name('admin.newsletter.toggle')->middleware('admin.permission:marketing.manage');
    Route::delete('/newsletter/{subscriber}', [App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'destroy'])->name('admin.newsletter.destroy')->middleware('admin.permission:marketing.manage');
    Route::get('/newsletter-export.csv', [App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'export'])->name('admin.newsletter.export')->middleware('admin.permission:marketing.manage');

    Route::get('/security-center', [App\Http\Controllers\Admin\SecurityCenterController::class, 'index'])->name('admin.security.index')->middleware('admin.permission:security.manage');
    Route::get('/notifications', [App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications/read', [App\Http\Controllers\Admin\AdminNotificationController::class, 'read'])->name('admin.notifications.read');
    Route::get('/profile', [App\Http\Controllers\Admin\AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/profile', [App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Admin\AdminProfileController::class, 'password'])->name('admin.profile.password');
    Route::fallback(fn () => response()->view('errors.admin-404', [], 404))->name('admin.404');
});
// 🛒 Cart Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [App\Http\Controllers\Frontend\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [App\Http\Controllers\Frontend\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [App\Http\Controllers\Frontend\CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [App\Http\Controllers\Frontend\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [App\Http\Controllers\Frontend\CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [App\Http\Controllers\Frontend\CartController::class, 'count'])->name('cart.count');
});
// 🛒 Checkout Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [App\Http\Controllers\Frontend\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/quote', [App\Http\Controllers\Frontend\CheckoutController::class, 'quote'])->name('checkout.quote');
    Route::post('/checkout', [App\Http\Controllers\Frontend\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/orders/success/{id}', [App\Http\Controllers\Frontend\CheckoutController::class, 'success'])->name('orders.success');
});
// ❤️ User Wishlist Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/account/wishlist', [App\Http\Controllers\Frontend\WishlistController::class, 'index'])->name('user.wishlist');
    Route::delete('/account/wishlist/clear', [App\Http\Controllers\Frontend\WishlistController::class, 'clear'])->name('user.wishlist.clear');
});
// 👤 User Routes (Protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Frontend\UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/orders', [App\Http\Controllers\Frontend\UserController::class, 'orders'])->name('user.orders');
    Route::get('/orders/{id}', [App\Http\Controllers\Frontend\UserController::class, 'orderDetail'])->name('user.order.detail');
    
    // 🔥 Invoice Routes - YEH ADD KAREIN
    Route::get('/orders/{id}/invoice', [App\Http\Controllers\Frontend\UserController::class, 'downloadInvoice'])->name('user.order.invoice');
    Route::get('/orders/{id}/preview', [App\Http\Controllers\Frontend\UserController::class, 'previewInvoice'])->name('user.order.preview');
    
    Route::get('/profile', [App\Http\Controllers\Frontend\UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile', [App\Http\Controllers\Frontend\UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/password', [App\Http\Controllers\Frontend\UserController::class, 'updatePassword'])->name('user.password.update');
});
// 🔐 Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware(['auth'])->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = \App\Models\User::findOrFail($id);
    
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return redirect()->route('verification.notice')->with('error', 'Invalid verification link!');
    }
    
    if ($user->hasVerifiedEmail()) {
        return redirect()->route('home')->with('success', 'Email already verified!');
    }
    
    $user->markEmailAsVerified();
    return redirect()->route('home')->with('success', 'Email verified successfully! 🎉');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 🔐 Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('password.update');
// 🎫 Coupon Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/cart/apply-coupon', [App\Http\Controllers\Frontend\CartController::class, 'applyCoupon'])->name('cart.apply-coupon');
    Route::post('/cart/remove-coupon', [App\Http\Controllers\Frontend\CartController::class, 'removeCoupon'])->name('cart.remove-coupon');
});
// 💳 Payment Routes
Route::get('/payment/{orderId}/{gateway}', [App\Http\Controllers\Frontend\PaymentController::class, 'pay'])->middleware('auth')->name('payment.pay');

// 🔥 CSRF Exception - Callback route ko web middleware se bahar karein
Route::post('/payment/callback', [App\Http\Controllers\Frontend\PaymentController::class, 'callback'])->name('payment.callback')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
// 📦 Order Tracking
Route::middleware(['auth'])->group(function () {
    Route::get('/orders/tracking/{id}', [App\Http\Controllers\Frontend\UserController::class, 'tracking'])->name('user.order.tracking');
});

// ============================================
// ✨ TRENDORA PRO PHASE 2 — CUSTOMER EXPERIENCE
// ============================================

// Public information & support pages
Route::get('/about', [App\Http\Controllers\Frontend\PageController::class, 'about'])->name('pages.about');
Route::get('/contact', [App\Http\Controllers\Frontend\PageController::class, 'contact'])->name('pages.contact');
Route::post('/contact', [App\Http\Controllers\Frontend\PageController::class, 'contactStore'])->middleware('throttle:6,1')->name('pages.contact.store');
Route::get('/faq', [App\Http\Controllers\Frontend\PageController::class, 'faq'])->name('pages.faq');
Route::get('/help', [App\Http\Controllers\Frontend\PageController::class, 'help'])->name('pages.help');
Route::get('/shipping-policy', [App\Http\Controllers\Frontend\PageController::class, 'shipping'])->name('pages.shipping');
Route::get('/returns-policy', [App\Http\Controllers\Frontend\PageController::class, 'returnsPolicy'])->name('pages.returns');
Route::get('/privacy-policy', [App\Http\Controllers\Frontend\PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms', [App\Http\Controllers\Frontend\PageController::class, 'terms'])->name('pages.terms');

// Session-based product comparison (works for guests too)
Route::get('/compare', [App\Http\Controllers\Frontend\CompareController::class, 'index'])->name('compare.index');
Route::post('/compare/{product}', [App\Http\Controllers\Frontend\CompareController::class, 'add'])->name('compare.add');
Route::delete('/compare/{product}', [App\Http\Controllers\Frontend\CompareController::class, 'remove'])->name('compare.remove');
Route::delete('/compare', [App\Http\Controllers\Frontend\CompareController::class, 'clear'])->name('compare.clear');

Route::middleware(['auth'])->prefix('account')->group(function () {
    // Address book
    Route::get('/addresses', [App\Http\Controllers\Frontend\AddressController::class, 'index'])->name('user.addresses');
    Route::post('/addresses', [App\Http\Controllers\Frontend\AddressController::class, 'store'])->name('user.addresses.store');
    Route::put('/addresses/{address}', [App\Http\Controllers\Frontend\AddressController::class, 'update'])->name('user.addresses.update');
    Route::delete('/addresses/{address}', [App\Http\Controllers\Frontend\AddressController::class, 'destroy'])->name('user.addresses.destroy');
    Route::post('/addresses/{address}/default', [App\Http\Controllers\Frontend\AddressController::class, 'makeDefault'])->name('user.addresses.default');

    // Returns & refunds
    Route::get('/returns', [App\Http\Controllers\Frontend\ReturnRequestController::class, 'index'])->name('user.returns');
    Route::post('/returns', [App\Http\Controllers\Frontend\ReturnRequestController::class, 'store'])->name('user.returns.store');
    Route::get('/returns/{returnRequest}', [App\Http\Controllers\Frontend\ReturnRequestController::class, 'show'])->name('user.returns.show');

    // Rewards / notification center / review history
    Route::post('/reviews/product/{product}', [App\Http\Controllers\Frontend\CustomerReviewController::class, 'store'])->name('user.reviews.store');
    Route::get('/rewards', [App\Http\Controllers\Frontend\AccountExtraController::class, 'rewards'])->name('user.rewards');
    Route::get('/wallet', [App\Http\Controllers\Frontend\WalletController::class, 'index'])->name('user.wallet');
    Route::post('/wallet/redeem', [App\Http\Controllers\Frontend\WalletController::class, 'redeem'])->name('user.wallet.redeem');
    Route::get('/notifications', [App\Http\Controllers\Frontend\AccountExtraController::class, 'notifications'])->name('user.notifications');
    Route::post('/notifications/read', [App\Http\Controllers\Frontend\AccountExtraController::class, 'markNotificationsRead'])->name('user.notifications.read');
    Route::get('/reviews', [App\Http\Controllers\Frontend\AccountExtraController::class, 'reviews'])->name('user.reviews');

    // Support center
    Route::get('/support', [App\Http\Controllers\Frontend\SupportController::class, 'index'])->name('support.index');
    Route::get('/support/new', [App\Http\Controllers\Frontend\SupportController::class, 'create'])->name('support.create');
    Route::post('/support', [App\Http\Controllers\Frontend\SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [App\Http\Controllers\Frontend\SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [App\Http\Controllers\Frontend\SupportController::class, 'reply'])->name('support.reply');
});

// Phase 8 — public journal + newsletter
Route::get('/journal', [App\Http\Controllers\Frontend\BlogController::class, 'index'])->name('blogs.index');
Route::get('/journal/{blog:slug}', [App\Http\Controllers\Frontend\BlogController::class, 'show'])->name('blogs.show');
Route::post('/newsletter/subscribe', [App\Http\Controllers\Frontend\NewsletterController::class, 'store'])->middleware('throttle:4,1')->name('newsletter.subscribe');

// Phase 4 public CMS route — intentionally last so fixed storefront routes win.
Route::get('/pages/{slug}', [App\Http\Controllers\Frontend\CmsPageController::class, 'show'])->name('cms.show');

Route::fallback(fn () => response()->view('errors.404', [], 404))->name('storefront.404');
