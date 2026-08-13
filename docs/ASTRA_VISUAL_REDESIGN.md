# Trendora × AstraCommerce — Full Visual Redesign

This release keeps the Laravel 12 commerce backend from Phase 8 and replaces the legacy Trendora visual language with the initial AstraCommerce Pro UI design system.

## Design source

The visual tokens mirror the first AstraCommerce UI prototype:

- Background: `#090b10`
- Secondary background: `#0d1119`
- Card surface: `#111722`
- Secondary surface: `#151d2a`
- Primary: `#8b5cf6`
- Secondary primary: `#5b7cff`
- Cyan accent: `#22d3ee`
- Success: `#10b981`
- Warning: `#f59e0b`
- Danger: `#ef4444`
- Card radius: `22px`
- Glass borders, radial ambient glow, dark gradients, hover lift and page-in motion are shared globally.

## Coverage

The shared visual system is loaded after Bootstrap and legacy structural CSS so it wins the cascade without removing backend-compatible page structure.

- 43 storefront/account templates inherit `layouts.app`
- 100 admin templates inherit `layouts.admin`
- 8 authentication templates inherit `layouts.auth`
- Admin login uses the Astra visual layer directly
- Maintenance mode uses the Astra visual layer directly
- Customer/admin invoices use Astra on screen and retain a white print/PDF treatment
- Shared navbar, footer, mobile dock and partial components inherit the same design tokens

## Legacy design cleanup

Legacy Blade palette values such as `#667eea`, `#764ba2`, old light card backgrounds, light Bootstrap-like separators, and older green/yellow/red accents were normalized to the Astra palette. No legacy white surface declaration remains in website Blade templates outside printable invoices/emails.

## Main stylesheet

`public/css/astra-commerce.css`

This file is deliberately loaded last in the storefront, admin and auth shells. Existing `trendora.css`, `admin.css` and `auth.css` remain as structural/layout compatibility layers for the large Laravel view set; AstraCommerce owns the final visual presentation.

## Backend preservation

The redesign does not replace the Phase 8 controllers, models, routes, migrations, checkout logic, inventory logic, payments, reports, API/webhooks or production-readiness tools. Only visual defaults and the Theme Studio fallback palette were adjusted to the Astra defaults.

## NPM

No NPM/Vite runtime dependency was introduced. Assets continue to load from `public/css` and `public/js`.
