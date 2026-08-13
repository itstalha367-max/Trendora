<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Setting;
use App\Services\StorefrontCache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Public catalog fragments are cached for speed. These model hooks prevent stale storefront data.
        foreach ([Product::class, Category::class, Brand::class, Collection::class] as $model) {
            $model::saved(fn () => StorefrontCache::clear());
            $model::deleted(fn () => StorefrontCache::clear());
        }

        // Optional database-backed SMTP override. Wrapped defensively so first install/migrations still boot.
        try {
            if (Schema::hasTable('settings') && Setting::get('mail_override_enabled', 'off') === 'on') {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => Setting::get('mail_host', config('mail.mailers.smtp.host')),
                    'mail.mailers.smtp.port' => (int) Setting::get('mail_port', config('mail.mailers.smtp.port')),
                    'mail.mailers.smtp.username' => Setting::get('mail_username', config('mail.mailers.smtp.username')),
                    'mail.mailers.smtp.password' => Setting::get('mail_password', config('mail.mailers.smtp.password')),
                    'mail.mailers.smtp.scheme' => Setting::get('mail_scheme', config('mail.mailers.smtp.scheme')),
                    'mail.from.address' => Setting::get('mail_from_address', config('mail.from.address')),
                    'mail.from.name' => Setting::get('mail_from_name', config('mail.from.name')),
                ]);
            }
        } catch (\Throwable $e) {
            // Fall back to .env/config mail settings if the database is unavailable.
        }
    }
}
