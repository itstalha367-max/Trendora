# Phase 6 Changelog

## Added
- Payout model/table/admin workflows.
- Transaction ledger screen.
- Inventory and marketing reports.
- Multi-report CSV exports.
- Store, checkout, shipping and tax settings screens.
- Commerce settings migration and defaults.
- Maintenance-mode middleware and storefront maintenance view.
- Storefront category directory, category listings and search route.
- Storefront/admin custom 404 views.
- Dedicated admin authentication screen.
- Webhook dispatcher with signatures, attempts and retry support.
- Automatic commerce webhook events.

## Changed
- Checkout now enforces operational settings at runtime.
- Order number prefix is configurable.
- Shipping calculator supports configured fallback behavior.
- Product/order/payment/refund/inventory flows emit production webhook events.
- Admin report controller expanded and normalized.
- Finance controller includes a dedicated transactions query/workspace.
- Admin sidebar metrics are cached to reduce repetitive database queries.
- Security headers tightened without breaking local HTTP development.

## Compatibility
- Laravel 12 retained.
- Existing Phase 1–5 data structures and workflows preserved where possible.
- No NPM/Vite runtime or build dependency introduced.
