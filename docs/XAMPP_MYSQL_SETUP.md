# Trendora — XAMPP / MySQL Local Setup

The release does **not** include a real `.env` file. This protects passwords, APP_KEY values, mail credentials, and payment secrets.

## 1. Create the database

Open phpMyAdmin from XAMPP and create a database named:

```text
trendora
```

## 2. Create `.env`

From the Trendora project folder on Windows CMD:

```bat
copy .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

The provided `.env.example` already uses the common XAMPP MySQL defaults:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=trendora
DB_USERNAME=root
DB_PASSWORD=
```

If your MySQL root user has a password, set it in `DB_PASSWORD`.

## 3. Install and initialize

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan optimize:clear
php artisan trendora:doctor
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

## Existing Phase 7/8 installation

If you already have a working `.env`, keep that existing `.env` and especially its `APP_KEY`. Do **not** generate a new APP_KEY for an existing database containing encrypted Trendora SMTP/payment secrets.
