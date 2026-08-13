# Trendora Phase 8 — Release Candidate

Phase 8 is the final release-candidate polish pass for the Laravel 12, Blade-first, NPM-free Trendora commerce application.

## Release-candidate improvements

- Rebuilt the legacy storefront homepage on the shared premium Trendora design system.
- Added real category and journal navigation instead of placeholder links.
- Added persistent newsletter subscriptions plus an admin subscriber workspace and CSV export.
- Added configurable social links and removed dead social placeholders.
- Added a responsive mobile storefront dock and improved keyboard/focus behavior.
- Unified customer authentication screens (login, registration, password recovery, email verification and 2FA) under one accessible dark UI.
- Improved admin mobile navigation, focus states, overlays and sidebar search.
- Replaced unsafe state-changing GET endpoints with POST/DELETE actions protected by CSRF.
- Added a safe Backup Schedule screen and disabled misleading/destructive one-click web restore.
- Fixed Spatie Backup boot behavior: missing `php-zip` no longer prevents unrelated Artisan/application boot.
- Added `trendora:smoke` for read-only deployment HTTP checks.
- Added persistent Laravel runtime directories in the distributable.
- Normalized storefront currency display and removed remaining NPM/Vite runtime references.

## Commands

```bash
php artisan trendora:doctor
php artisan trendora:smoke --url=https://your-store.example
```

`trendora:smoke` performs GET-only checks and does not create orders, payments, refunds or other writes.

## Production verification boundary

Static QA and DB-independent application-shell rendering are included in the release process. Real checkout, email, payment, webhook, inventory concurrency and queue behavior must still be tested against the deployment's actual MySQL server, SMTP provider and payment sandbox credentials.
