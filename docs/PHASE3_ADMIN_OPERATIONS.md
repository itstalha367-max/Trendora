# Trendora Pro — Phase 3 Admin Operations

Phase 3 expands the Laravel 12 admin area into a commerce operations suite while keeping the project NPM/Vite-free.

## New operational modules

- Returns & refund review workflow
- Customer support inbox with staff replies
- Contact submission inbox
- Brands and product brand assignment
- Curated collections with product membership
- Warehouses and multi-location inventory records
- Stock adjustments with movement history
- Suppliers and product sourcing relationships
- Marketing campaigns with budget / CTR / conversion signals
- Banner studio for storefront placements
- Shipping zones and shipping methods
- Tax rate configuration
- Staff accounts
- Roles and permission presets with enforced middleware
- Live Store monitor
- System Status health screen

## Database migrations added

- `2026_08_12_000006_create_brands_and_product_brand.php`
- `2026_08_12_000007_create_collections_tables.php`
- `2026_08_12_000008_create_inventory_tables.php`
- `2026_08_12_000009_create_suppliers_tables.php`
- `2026_08_12_000010_create_marketing_tables.php`
- `2026_08_12_000011_create_shipping_tax_tables.php`
- `2026_08_12_000012_create_admin_roles_permissions.php`

The final roles migration seeds useful starter roles and permissions. Existing admins with no `admin_role_id` remain Super Admins for backward compatibility.

## After extracting / replacing the project

Run:

```bash
composer install
php artisan migrate
php artisan storage:link
php artisan optimize:clear
php artisan serve
```

No `npm install`, `npm run dev`, Vite build, or Node runtime is required by this project setup.

## Important environment note

The project already uses `spatie/laravel-backup`, so PHP's ZIP extension (`ZipArchive`) should be enabled. The supplied build environment did not have that extension, so `artisan route:list` could not fully boot there. Routes were independently audited statically instead.

## QA performed for Phase 3

- 175 application/config/migration PHP files passed `php -l`.
- 115 Blade templates compiled to valid PHP.
- 254 unique named routes were identified with 0 duplicate names.
- 429 `route()` references were checked with 0 missing route names.
- `public/js/admin.js` and `public/js/trendora.js` passed JavaScript syntax checks.
- CSP was corrected to allow the project's actual Bootstrap, jQuery and Font Awesome CDN assets.
