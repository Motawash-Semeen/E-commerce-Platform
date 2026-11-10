# E-commerce Platform

A simple e-commerce app built with **Laravel 12**. One admin manages categories,
subcategories and products (with images). Visitors browse the shop without logging in.

**Built with:** Laravel 12, PHP 8.2+, MySQL, Blade + Bootstrap 5, Laravel Breeze
(login), L5Modular (modules) and Opcodes Log Viewer.

## Requirements

- PHP 8.2+ (with `pdo_mysql`, `gd`, `fileinfo`)
- Composer
- MySQL 8+ or MariaDB 10.4+

No Node.js or npm needed: Bootstrap is loaded from a CDN.

## Database

- **Database name:** `ecommerce_platform`
- **SQL file:** [`database/export/ecommerce_platform.sql`](database/export/ecommerce_platform.sql)
- **Product images for the SQL file:** `database/export/product-images/`

The SQL file contains all the tables and the demo data: 1 admin, 3 categories,
6 subcategories and 14 products.

| Table | Main columns | Notes |
|-------|--------------|-------|
| `categories` | `name`, `slug` | name is unique |
| `subcategories` | `category_id`, `name`, `slug` | name is unique within its category |
| `products` | `category_id`, `subcategory_id`, `name`, `slug`, `description`, `image`, `old_price`, `new_price` | `image` is a file path |
| `users` | `name`, `email`, `password` | the admin account |

A category can't be deleted while it has subcategories or products, and a
subcategory can't be deleted while it has products. The database enforces this
as well as the app. The other tables (`sessions`, `cache`, `jobs`, `migrations`…)
are Laravel's standard ones.

## Setup

**1. Install and configure**

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create the database:

```sql
CREATE DATABASE ecommerce_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Check the connection in `.env`:

```dotenv
DB_DATABASE=ecommerce_platform
DB_USERNAME=root
DB_PASSWORD=
```

**2. Fill the database (choose one)**

- **Option A: run migrations and seeders**

  ```bash
  php artisan migrate --seed
  ```

- **Option B: import the SQL file**

  ```bash
  mysql -u root -p ecommerce_platform < database/export/ecommerce_platform.sql
  ```

  Or in phpMyAdmin: select `ecommerce_platform`, then **Import** the file.
  Then copy the images from `database/export/product-images/` into
  `storage/app/public/products/`.

**3. Run it**

```bash
php artisan storage:link
php artisan serve
```

- Shop: <http://127.0.0.1:8000>
- Admin: <http://127.0.0.1:8000/admin/dashboard>

## Logins and roles

| Email | Password | Role |
|-------|----------|------|
| `admin@example.com` | `password` | admin |

There are two roles. **Admins** use `/admin` (dashboard, catalogue, logs);
**shop owners** use `/owner`. Each role gets a 403 page in the other's area, and
after login each lands on its own dashboard. Disabled accounts can't log in.
Registration is turned off. To change the admin's login details, set
`ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env` before seeding.

## Features

1. **Categories:** add, edit, delete and list them, with a subcategory count.
2. **Subcategories:** each belongs to a category, with the same actions.
3. **Products:** name, description, old and new price, category, subcategory and
   an image. The subcategory list updates when you pick a category.
4. **Public shop:** products grouped by "Category › Subcategory", plus a page for
   each product.
5. **Roles and security:** admins and shop owners each have their own area and
   dashboard; every form is validated and CSRF-protected.
6. **Log viewer:** the admin can read the app logs at `/admin/log-viewer`
   (the **Logs** link in the menu).

## Project structure

Each feature has its own module in `app/Modules`, which holds its controller,
validation, model, migration, views and routes:

```text
app/Modules/
├── Category/      # admin: categories
├── Subcategory/   # admin: subcategories
├── Product/       # admin: products and images
└── Shop/          # public pages

resources/views/layouts/   # shared page layout
public/css/shop.css        # the design
database/seeders/          # admin and demo data
database/export/           # SQL file and its images
tests/Feature/             # automated tests
```

## How it works

- **Validation** is done in Form Request classes, so controllers stay short.
- **Web addresses use slugs** made from the name, e.g. `/products/led-desk-lamp`.
- **Images** are saved with random file names. Old images are deleted when a
  product's image is replaced or the product is deleted.
- **Deleting** a category or subcategory that is still in use shows an error
  message instead of breaking the data.
- **Security:** login is required for `/admin` and the log viewer, every form has
  CSRF protection, and all output is escaped.

## Tests

```bash
php artisan test
```

The tests use a separate in-memory database, so your data is never touched.

## Going live

- Set `APP_DEBUG=false` and `APP_ENV=production` in `.env`.
- Change the admin password. The SQL file comes with the demo password.
