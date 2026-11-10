# E-commerce Platform (multi-shop)

A small multi-shop e-commerce management system. The **admin** manages shops, shop owner
accounts, categories and a **shared product catalog**. **Shop owners** browse that catalog,
add products to their own shop with their own price, description and thumbnail, and can add
new products to the catalog. **Customers** (no login) see only products that an active shop
is selling.

**Tech stack:** Laravel 12 · PHP 8.2+ · MySQL / MariaDB · Vue 3 + Inertia.js (Laravel Breeze)
· Vite · Tailwind CSS · Ziggy · L5Modular (modules) · Opcodes Log Viewer

## Requirements

- PHP 8.2+ with the `pdo_mysql`, `gd` and `fileinfo` extensions
- Composer 2
- MySQL 8 (or MariaDB 10.4+)
- Node.js 20+ and npm

## Setup (fresh clone)

```bash
git clone https://github.com/Motawash-Semeen/E-commerce-Platform.git
cd E-commerce-Platform
composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

Create an empty database, then check the `DB_*` values in `.env`:

```sql
CREATE DATABASE ecommerce_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```dotenv
DB_DATABASE=ecommerce_platform
DB_USERNAME=root
DB_PASSWORD=
```

Then:

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Open <http://localhost:8000>.

**Dev mode (optional):** run `npm run dev` in a second terminal for hot reload instead of `npm run build`.

**No terminal for migrations?** `database/export/ecommerce_platform.sql` holds the same tables
and demo data. Import it (phpMyAdmin > Import, or `mysql -u root -p ecommerce_platform < database/export/ecommerce_platform.sql`),
then copy `database/export/product-images/*` into `storage/app/public/products/`.

## Demo logins

All demo accounts use the password `password`.

| Email | Role | What to try |
|-------|------|-------------|
| `admin@example.com` | admin | Create a shop owner, then a shop for them. Add a catalog product. Try deleting a product that a shop sells (blocked). |
| `owner1@example.com` | shop owner (Urban Bazaar) | Browse catalog > **Add to my shop** with your own price. Edit or hide a listing. Create a product with "Also add to my shop". |
| `owner2@example.com` | shop owner (Corner Store) | See owner 1's products in the catalog. Open a listing of another shop by URL (403). |

As a customer (logged out): open a shop from the home page. "Classic Cotton Crew Tee" costs
Tk 899 in Urban Bazaar and Tk 990 in Corner Store. "Rattan Pendant Light" is in the catalog
but no shop sells it, so customers never see it. Corner Store's frying pan listing is hidden
(inactive).

The admin login comes from `ADMIN_EMAIL` / `ADMIN_PASSWORD` in `.env` (set them before seeding).

## Features by requirement

| Requirement | Where |
|-------------|-------|
| Admin authentication | Breeze login; `role:admin` on every `/admin` route; registration disabled; inactive accounts can't log in |
| Shop management (CRUD) | Admin > **Shops**: name, owner, description, logo, active |
| Product management (CRUD) | Admin > **Products**: the shared catalog, with image, category › subcategory and suggested price |
| Shop owner accounts | Admin > **Shop owners**: create, edit (optional new password), activate/deactivate, delete (blocked while they own a shop) |
| Owners browse the catalog and add products | Owner > **Browse catalog** > **Add to my shop** |
| Products by the admin or other owners are in the catalog | Every catalog product is visible to every owner |
| Owners create their own products | Owner > **My products** > **Create product** (joins the catalog right away; optional "Also add to my shop") |
| Owners set price, details and thumbnail per shop | Owner > **My listings** > Edit: stored in `shop_product`, so other shops aren't affected |
| Customers only see assigned products | Public pages read only through `ShopProduct::visible()` (active listing in an active shop) |
| Shop-specific info, prices and thumbnails | Shop and product pages show the listing's price, description and thumbnail (falling back to the catalog's) |

## Database design

| Table | Purpose |
|-------|---------|
| `users` | Breeze users plus `role` (`admin` / `shop_owner`) and `is_active` |
| `categories`, `subcategories` | Catalog groups (subcategory names are unique within a category) |
| `shops` | `owner_id` (unique: one shop per owner), name, slug, description, logo, is_active |
| `products` | The shared catalog: category, subcategory, `created_by`, name, slug, description, image, suggested `new_price` / `old_price` |
| `shop_product` | A shop's **listing** of a catalog product (model `ShopProduct`) |

**`shop_product` (the pivot):** one row per shop and product (`unique(shop_id, product_id)`)
with that shop's `price`, `old_price`, optional `description` and `thumbnail` (which override
the catalog's), and `is_active`. Editing a listing never touches the catalog product, and two
shops can sell the same product at different prices.

Deleting is restricted where data depends on it: a category or subcategory in use, a product
that a shop sells, and an owner who has a shop can't be deleted. Deleting a shop deletes its
listings (cascade) and their image files.

## Design decisions

- **Inertia instead of a separate API:** Laravel keeps routing, validation and session auth; Vue renders every page. No token auth to secure.
- **Shared catalog + per-shop pivot:** products are created once; each shop's price and
  details live in `shop_product`.
- **One shop per owner:** enforced by a unique `owner_id`. An owner's shop is always
  `auth()->user()->shop`; no owner route accepts a shop id.
- **Owners can't delete catalog products:** other shops may sell them. Owners remove their
  own listing instead; only the admin deletes unlisted products.
- **Restrict on delete:** foreign keys stop deletes that would break data; the app shows a
  friendly message first.
- **Modules:** each feature lives in `app/Modules/<Feature>` (controllers, requests, routes,
  migrations) via L5Modular. Vue pages are in `resources/js/Pages/{Admin,Owner,Public}`.

## Security

- `auth` + `role` middleware on every admin and owner route; registration disabled; inactive users blocked at login
- Ownership checks in the owner controllers: owners edit only catalog products they created, and only their own shop's listings (403 otherwise). The admin can't delete a product that a shop sells
- `role`, `created_by`, `owner_id` and `shop_id` are never mass-assignable or read from owner input
- Validation only in Form Request classes; `$fillable` on every model; Eloquent and route model binding only (no raw SQL)
- CSRF handled by Inertia/axios (XSRF-TOKEN cookie)
- No `v-html`: all output is escaped by Vue; descriptions use `white-space: pre-line`
- Inertia props carry only the fields each page needs (shared user: id, name, email, role)
- Uploads: image mime and size checked, stored with random names, old files deleted on replace or delete
- **Production:** set `APP_ENV=production`, `APP_DEBUG=false` and a strong `ADMIN_PASSWORD`

## Tests

```bash
php artisan test
```

The tests run on in-memory SQLite (see `phpunit.xml`), so no test database is needed. They cover
the 13 required cases (role access, inactive login, product and listing ownership, catalog
sharing, duplicate listings, subcategory/category mismatch, customer visibility, per-shop prices,
blocked delete, image upload) and more.

## Troubleshooting

| Problem | Fix |
|---------|-----|
| Images don't show | `php artisan storage:link`, and make sure `APP_URL` matches the address you open (default `http://localhost:8000`) |
| "Vite manifest not found" | `npm install` then `npm run build` |
| Database error on start | Check the `DB_*` values in `.env` and that the database exists |
| Start over with fresh demo data | `php artisan migrate:fresh --seed` |
