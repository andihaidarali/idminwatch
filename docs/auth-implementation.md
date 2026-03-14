# MinWatch Auth Implementation Tutorial

This project now uses session-based Laravel auth for admin pages.

## 1. Run setup

```bash
php artisan migrate
php artisan db:seed
```

Seeder creates or updates the default admin account:

- Email: `admin@minwatch.com`
- Password: `password123`

## 2. Login flow

1. Open `/login`.
2. Submit email + password.
3. On success, user is redirected to `/admin/wilayah-tambang`.
4. On failure, an error message is shown.
5. After too many failed attempts from the same IP/email, login is temporarily blocked.

## 3. Route protection

- Guest-only routes:
  - `GET /login`
  - `POST /login`
- Auth-only routes:
  - `/admin/*`
  - `/detail-tambang/*`
  - `POST /logout`

Middleware redirect behavior is configured in `bootstrap/app.php`:

- Guest accessing protected page -> `/login`
- Authenticated user accessing guest page -> `/admin/wilayah-tambang`

## 4. Run auth tests

```bash
php artisan test --filter AuthTest
```

Covered cases:

- Guest cannot open admin route.
- Valid credentials can login.
- Invalid credentials are rejected.
- Authenticated user can logout.

## 5. Next auth upgrades (recommended)

1. Add role-based access (`is_admin` column) so only admin users can enter `/admin`.
2. Add password reset (`password_reset_tokens`) flow with email.
3. Add optional 2FA for admin accounts.
4. Add audit logs for login/logout attempts.
