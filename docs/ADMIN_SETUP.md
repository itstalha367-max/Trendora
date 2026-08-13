# Trendora Admin Creation

After `.env` is configured and migrations are complete, the recommended method is the interactive command:

```bash
php artisan trendora:admin
```

It asks for:

1. Admin name
2. Admin email
3. Admin password (hidden input, minimum 8 characters)

The account is created as a **super-admin** (`role=admin`, no restricted `admin_role_id`) and can access the full admin workspace.

Admin login page:

```text
http://127.0.0.1:8000/admin/login
```

For scripted/local setup you can also provide options:

```bash
php artisan trendora:admin --name="Store Admin" --email="admin@example.com" --password="Use-A-Strong-Password"
```

Avoid putting real production passwords in shell history; interactive mode is safer.

## Existing demo seeder

A non-interactive seeder is also available, but it no longer contains any hard-coded password. Set these values in `.env` first:

```env
TRENDORA_ADMIN_NAME="Store Admin"
TRENDORA_ADMIN_EMAIL=admin@example.com
TRENDORA_ADMIN_PASSWORD=Use-A-Strong-Password
```

Then run:

```bash
php artisan db:seed --class=AdminUserSeeder
```

For normal manual setup, prefer `php artisan trendora:admin` so the password is entered securely.

## Create additional staff later

Once logged in as super-admin, use the Admin **Staff / Roles** area to create restricted staff accounts and assign role permissions.
