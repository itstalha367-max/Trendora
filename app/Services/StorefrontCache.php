<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class StorefrontCache
{
    public const HOME = 'trendora.storefront.home.v2';
    public const CATALOG_FILTERS = 'trendora.storefront.catalog-filters.v2';
    public const CATEGORIES = 'trendora.storefront.categories.v2';

    public static function clear(): void
    {
        foreach ([self::HOME, self::CATALOG_FILTERS, self::CATEGORIES] as $key) {
            Cache::forget($key);
        }
        Cache::forget('trendora.admin.sidebar-counts');
    }
}
