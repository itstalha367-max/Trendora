# Trendora Pro — Phase 6 Finalization

Phase 6 closes the remaining production-oriented gaps identified after Phase 5 while keeping the application on Laravel 12 with direct public CSS/JavaScript assets and no NPM/Vite requirement.

## What Phase 6 adds

### Finance & Payout Operations
- Dedicated transaction ledger.
- Affiliate payout batches with pending/paid/failed/cancelled workflows.
- Referral state synchronization with payout processing.
- Existing refund center and finance overview remain integrated.

### Deeper Reports
- Inventory report with stock/value/filtering views.
- Marketing report covering campaigns, promotions, affiliates and commissions.
- CSV exports for sales, products, customers, inventory and marketing reports.

### Commerce Settings
- Store details and operational identity.
- Checkout rules, payment method toggles and minimum order amount.
- Shipping fallback settings.
- Tax display/pricing settings.
- Dynamic order prefix.
- Runtime maintenance mode.

### Production Webhooks
- Automatic webhook dispatch for order, product, inventory and refund events.
- HMAC SHA-256 signed webhook requests.
- Delivery attempt history.
- Retry scheduling metadata and manual retry workflow.
- Public HTTPS destination validation.

### Checkout / Inventory Hardening
- Shipping and tax continue to come from configured zones/rates.
- Checkout settings now control COD, wallet, terms, notes and minimum order rules.
- Warehouse inventory changes can emit low-stock events.
- Order creation, payment, status changes, tracking and refunds emit operational events.

### Storefront Completion
- Dedicated categories directory.
- Category listing routes.
- Dedicated search-results route.
- Storefront 404 experience.
- Maintenance mode experience.

### Admin Completion
- Dedicated Admin Login.
- Dedicated Admin 404.
- Transactions screen.
- Payout Center.
- Inventory Report.
- Marketing Report.
- Store Details settings.
- Checkout settings.
- Shipping settings.
- Tax settings.

### Performance & Security
- Cached sidebar operational counts.
- Rate limits on authentication POST endpoints.
- Safer security headers / CSP.
- HSTS only on HTTPS requests.
- Sensitive admin/checkout/account pages use no-store cache headers.

## Runtime Requirements

Trendora remains intentionally NPM-free. It expects a normal Laravel 12/PHP environment with Composer dependencies installed. Because the existing project includes Spatie Backup and Laravel relies on mbstring helpers, PHP should have at least `zip`/`ZipArchive` and `mbstring` enabled.

## Deployment Commands

```bash
composer install
php artisan migrate
php artisan optimize:clear
php artisan storage:link
php artisan serve
```

Do not run `npm install` or `npm run dev`; the project uses assets directly from `public/css` and `public/js`.
