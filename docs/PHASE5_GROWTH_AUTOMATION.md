# Trendora Pro — Phase 5 Growth & Automation

Phase 5 extends the Laravel 12 / Blade / vanilla JavaScript commerce stack without adding NPM or Vite.

## Growth & customer value
- Draft orders that can be converted into live inventory-backed orders.
- Dynamic customer segments with manual membership management.
- Promotion codes with percentage, fixed and free-shipping rules.
- Gift cards with balance ledger, expiry/status controls and customer redemption.
- Customer store credit wallet with immutable transaction history and wallet checkout.
- Affiliates and referral records.
- Moderated product questions and answers.

## Operations
- Purchase orders connected to suppliers, warehouses and inventory receiving.
- Navigation manager for header and footer menus.
- Store domain registry.
- Admin notification center and admin profile workspace.
- Security Center covering sessions/account posture and operational security links.

## Messaging & automation
- Editable email templates for order confirmation and shipped-order messaging.
- Editable notification templates for order status, refunds and support replies.
- Runtime variable replacement using protected template keys.

## Developer platform
- Integration registry with encrypted configuration storage.
- Scoped API keys stored as hashes; the plaintext token is shown only once.
- Read-only product/order API endpoints protected by API-key abilities.
- Signed outbound webhook configuration, delivery log and HTTPS-only test delivery protection.

## Safety & integrity
- Store-credit and gift-card balance mutations use database row locks.
- Wallet checkout rechecks the balance inside the order transaction.
- Promotion usage limits are revalidated and row-locked during checkout.
- Navigation URLs reject unsafe schemes and submenu parents must belong to the same menu.
- Converted draft orders use the existing inventory service, preserving stock movement history.

## Deployment

```bash
composer install
php artisan migrate
php artisan optimize:clear
php artisan storage:link
php artisan serve
```

No `npm install` or `npm run dev` command is required.
