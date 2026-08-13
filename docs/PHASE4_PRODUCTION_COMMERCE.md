# Trendora Pro — Phase 4 Production Commerce

Phase 4 upgrades the existing Laravel 12 / Blade / Vanilla JS application without introducing NPM or Vite.

## Added commerce engine

- Address-aware shipping method resolver using Admin → Shipping zones/methods.
- Tax calculator using active Admin → Tax rates, priorities, shipping-taxable rules and compound taxes.
- Server-side checkout quote endpoint; totals are recalculated again during order creation.
- Order snapshots for shipping method, tax name/rate and refunded amount.
- Warehouse-first inventory deduction with stock movement audit and legacy product/variation stock synchronization.
- Inventory restoration when an order is cancelled.
- Order status timeline and database customer notifications.
- Payment transaction ledger and manual payment confirmation for COD/manual reconciliation.
- Partial/full refund accounting records tied to orders and optional return requests.
- Orders are treated as financial audit records and are no longer hard-deleted from Admin.

## Storefront upgrades

- Advanced product filters: brand, collection, stock, sale, featured, minimum rating and expanded sorting.
- Product view counter is recorded on product detail visits.
- Live shipping/tax selection in the four-step checkout.
- Customer order detail now shows fulfilment, tax/shipping snapshot, refund history and status timeline.
- Runtime theme variables for storefront accent colors and card radius.
- CMS pages can be published and automatically surfaced in the footer.

## Admin upgrades

- Commerce Analytics dashboard (30-day revenue pulse, AOV, refunds, repeat customers, abandoned carts, markets and payment mix).
- Finance Ledger with charge/capture/refund events.
- Refund Center for recorded gateway/manual refunds.
- CMS Pages editor with SEO metadata.
- Theme Studio for accent colors and homepage hero content.
- Order Workspace with warehouse source, timeline, finance state and refund history.
- New permissions: `finance.manage` and `theme.manage`.

## Database migration

After replacing/extracting the Phase 4 project, run:

```bash
php artisan migrate
php artisan optimize:clear
```

The new migration is:

`database/migrations/2026_08_12_000013_create_phase4_commerce_engine.php`

It adds order commerce snapshot fields, order item fulfilment fields and creates:

- `payment_transactions`
- `refunds`
- `order_status_histories`
- `cms_pages`

## Important refund note

Trendora records refunds in its own accounting ledger and updates the customer/order state. For Stripe, PayPal, JazzCash, EasyPaisa or another provider, the actual provider-side refund must still be executed through the provider integration/dashboard unless a gateway-specific refund API is implemented later. The UI intentionally labels this as recording a refund rather than pretending to call an unsupported provider API.

## NPM status

No NPM/Vite build is required. Phase 4 continues to use Blade, public CSS, public JavaScript and Composer/PHP dependencies.
