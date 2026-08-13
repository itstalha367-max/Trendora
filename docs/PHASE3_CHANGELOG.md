# Phase 3 Changelog

## Admin experience

The admin sidebar is reorganized into Overview, Catalog, Orders & Service, Inventory & Supply, Customers, Marketing & Content, Finance & Fulfilment, and Organization & System.

New page styles include animated metric cards, status pills, responsive management tables, inventory adjustment modals, permission matrices, operational cards, support conversation UI, and live system health cards.

## Security & access

- Added `AdminPermissionMiddleware`.
- Added permission aliases to Laravel 12 middleware bootstrap.
- Existing admin routes now enforce relevant permissions for custom admin roles.
- Super admins remain backward compatible when `admin_role_id` is null.
- Improved Content Security Policy for the CDN assets already used by the application.

## Catalog integration

Products now support optional brands. Product create/edit controllers and admin forms include brand selection without changing existing category, variation, pricing, image or stock behavior.
