# Trendora Pro UI Upgrade — Laravel 12

This project has started its conversion to the Trendora Pro / AstraCommerce-style e-commerce experience while keeping the existing Laravel 12 backend intact.

## Completed in foundation pass

- Removed duplicate named routes from `routes/web.php`.
- Added missing `Illuminate\Http\Request` import used by email verification route.
- Split product import GET/POST route names (`admin.products.import` / `admin.products.import.store`).
- Restored missing 2FA routes with unique route names.
- Restored password-confirmation routes with unique route names.
- Updated old verification resend reference to the existing `verification.send` route.
- Unified Stripe/PayPal checkout redirects through the existing `payment.pay` gateway route.
- Replaced storefront shell with a responsive premium dark/glass layout.
- Replaced admin shell with a responsive premium commerce dashboard layout.
- Added shared no-build assets in `public/css` and `public/js`.
- Added `public/images/no-image.png` fallback asset.
- Removed Vite/NPM runtime/build references (`package.json`, `vite.config.js`, `@vite`).
- Updated Composer scripts so setup/dev no longer invoke Node/NPM.
- Kept Bootstrap, Font Awesome and jQuery via CDN for compatibility with existing Blade screens.
- Added reduced-motion accessibility support and mobile admin navigation.

## NPM-free asset architecture

```text
public/
├── css/
│   ├── trendora.css
│   ├── admin.css
│   └── animations.css
├── js/
│   ├── trendora.js
│   └── admin.js
└── images/
    └── no-image.png
```

The Laravel application can render these assets directly with `asset()` and does not require Vite to serve the UI.

## Validation performed

- 121 PHP files checked with `php -l`: no syntax errors.
- Duplicate named routes: none detected by static route audit.
- Named routes referenced from application/views: no missing references detected by static audit.
- `@vite`, `npm install`, `npm run`, and `vite.config` references: none detected outside vendor.

> Note: `php artisan` could not be booted in the build container because that environment does not have the PHP `ZipArchive` extension required by the installed backup package. This is an environment limitation, not a PHP syntax failure in Trendora.

## Next implementation rule

Existing working models/controllers/migrations should be reused rather than rebuilt. New modules from the 115-screen page map should be introduced incrementally with migrations, policies/validation, controllers/services, Blade views, and matching admin/customer navigation.

## Phase 2 completed
Customer experience expansion is documented in `docs/PHASE2_CUSTOMER_EXPERIENCE.md` and includes saved addresses, comparison, four-step checkout, returns/refunds, support tickets, notifications, rewards, verified-purchase reviews and public help/policy pages.
