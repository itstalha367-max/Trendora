# Trendora Production Deployment Checklist

## Before deployment

- Use PHP 8.2+ and enable `mbstring`, `pdo_mysql`, `openssl`, `fileinfo`, `curl`, `dom/xml`, and `zip` when backups are required.
- Point the web server document root to `public/` only.
- Preserve the existing production `.env` and `APP_KEY` when upgrading an existing Trendora database.
- Never regenerate `APP_KEY` after encrypted SMTP/payment secrets have been stored unless those secrets are intentionally re-entered.
- Configure MySQL, SMTP, payment sandbox/live mode, APP_URL, queue/cache/session stores and filesystem permissions.

## Deploy

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan trendora:doctor --strict
```

Configure the scheduler:

```cron
* * * * * cd /path/to/trendora && php artisan schedule:run >> /dev/null 2>&1
```

If database queues are enabled, run a supervised worker such as:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

## Read-only smoke test

```bash
php artisan trendora:smoke --url=https://your-domain.example
```

Then manually test a sandbox customer journey: register/login → product → cart → promotion → checkout → sandbox payment/COD → order detail → admin fulfilment → notification/email → refund/return as applicable.

## Security

- Force HTTPS at the proxy/web-server layer.
- Keep `.env`, storage backups, database dumps and API secrets outside public access.
- Replace all demo credentials and remove demo users/data before launch.
- Verify staff roles/permissions with non-super-admin accounts.
- Confirm webhook endpoints use HTTPS and inspect failed-delivery logs.
- Test 2FA for administrator accounts.

## Backups

`php-zip` / `ZipArchive` is required for the integrated backup feature. If it is unavailable, Trendora remains bootable but backup creation is disabled with a clear warning. Restore backups through controlled database/filesystem deployment tooling rather than a one-click web restore.
