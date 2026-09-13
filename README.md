# Gaya B Marketing

Laravel storefront for **GAYA BORNEO ENTERPRISE** — digital products and publishing from Borneo, Malaysia.

## Stack

- Laravel 13, PHP `^8.4` (dev/CI run PHP 8.4 and 8.5; production targets 8.5 once verified on Forge, with 8.4 as fallback)
- Livewire 4 for interactive components (product/admin catalog filters and CRUD), Alpine (bundled with Livewire) for lightweight UI (FAQ accordion)
- Tailwind CSS 4 + Vite
- Authentication via Laravel Breeze's plain Blade stack (traditional controllers, no Livewire/Volt involved — kept separate from the app's own Livewire 4 usage)
- Authorization via `spatie/laravel-permission` (roles/permissions) and Laravel Policies
- MySQL 8.4 in production; SQLite for local development
- PWA: web app manifest, versioned service worker, offline fallback page

## Current safety state

- All seeded products are `draft` in production until approved.
- Billplz uses the sandbox endpoint.
- Checkout is disabled by default with `BILLPLZ_CHECKOUT_ENABLED=false`.
- No API keys or product files are committed.
- Privacy, Terms, Refund Policy and Digital Product License pages are live but marked as **draft, pending legal review** — see the notice on each page.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # if using the sqlite driver locally
php artisan migrate --seed
npm install
npm run build   # or `npm run dev` while developing
php artisan serve
```

## Localization

- Default locale: English (`en`); secondary: Bahasa Melayu (`ms`).
- UI copy lives in `lang/en/*.php` and `lang/ms/*.php` (no inline `data-en`/`data-bm` duplication).
- Switching language hits `GET /locale/{locale}`, which sets a long-lived cookie and redirects back to the current page.

## Theme (light/dark)

- Persisted via the `gbm_theme` cookie (exempted from cookie encryption in `bootstrap/app.php` since it's read/written directly by JS and by the Blade layout).
- The layout reads the cookie server-side to render the correct `data-theme` before any CSS loads (no flash). A tiny inline script in `<head>` only handles the very first visit, before a cookie exists, using `prefers-color-scheme`.

## PWA

- Manifest: `public/manifest.webmanifest`; icons in `public/icons/` (192/512/maskable placeholders — swap for final branded artwork before launch).
- Service worker: `public/sw.js` — cache-first for hashed `/build` assets and icons, network-first-with-offline-fallback for page navigations, and explicitly untouched (network-only) for `/checkout`, `/billplz`, `/storage`, `/locale`, `/livewire`.
- Offline fallback: `public/offline.html` (static, no Blade/session dependency).
- `resources/js/pwa.js` registers the service worker, shows an install prompt once the browser fires `beforeinstallprompt`, shows an iOS "Add to Home Screen" tip, and prompts the user to refresh when a new service worker version is ready.
- Service worker registration could not be verified inside this sandbox's embedded preview browser (registration was blocked there, likely an automation/CDP restriction) — verify manually in a real Chrome/Edge/mobile Safari session before launch.

## Authentication, roles and the admin area

- Registration, login, password reset, email verification and profile management are Breeze's standard Blade stack (`routes/auth.php`), reskinned to the brand palette (`resources/views/layouts/guest.blade.php`, `layouts/navigation.blade.php`) instead of the default Tailwind indigo theme.
- New registrations are automatically assigned the `Customer` role via `App\Listeners\AssignCustomerRoleToNewUser` (listens for `Illuminate\Auth\Events\Registered`).
- Six roles from the PRD are seeded by `RolePermissionSeeder`: Super Admin, Admin, Finance, Content Manager, Support, Customer. Only a small permission set exists so far (`admin.access`, `products.view`, `products.manage`, `categories.manage`) — expand this as later phases add finance/CRM/booking modules.
- `/admin/*` routes (`routes/admin.php`) require `auth`, `verified`, and the `permission:admin.access` middleware (spatie's middleware aliases are registered in `bootstrap/app.php`). Individual actions are further gated by `App\Policies\ProductPolicy` / `CategoryPolicy` inside the Livewire components (`App\Livewire\Admin\*`) — route access and action authorization are deliberately separate checks.
- The admin dashboard, product manager and category manager are full-page Livewire 4 components using `layouts.authenticated` (distinct from the public `layouts.app` to avoid clashing with Breeze's own `layouts.app` convention).

## Catalog data model

- `categories` and `tags` are first-class tables now (`products.category` was a plain string in Phase 1; a migration backfilled it into `categories` and dropped the column).
- Product name/description are stored per-locale in `product_translations` (replacing the old `name_en`/`name_bm`/`description_en`/`description_bm` columns) — see `Product::translation()`/`localizedName()`/`localizedDescription()`.
- Product cover images upload through the admin Product Manager onto the `public` disk (`storage/app/public/products/covers`, validated as an image, ≤2MB) — run `php artisan storage:link` locally.
- `product_files` holds versioned downloadable files, uploaded from the same admin Product Manager (validated to `pdf,zip,docx,xlsx,csv`, ≤50MB) onto the **`local`** (private) disk — never the public one.

## Cart, checkout and Billplz

- Cart/checkout requires login (`carts` / `cart_items`, one cart per user). Add-to-cart lives on the product page (`App\Livewire\AddToCartButton`); `/cart` and `/checkout` are full-page Livewire components.
- An order is multi-item (`orders` + `order_items`, snapshotting product name/price at checkout time — price changes later don't affect past orders).
- `BillplzService::createBill()` builds the bill from the order's items/total; `App\Services\PaymentProcessor` is the single place that turns a Billplz notification into a paid order — both `BillplzController::callback` (server-to-server webhook) and `::redirect` (browser return) funnel through it, so the hardening only has to be written once:
  - Every notification is recorded in `payment_events` (with `x_signature` stripped) before anything else, valid or not — an audit trail regardless of outcome.
  - Marking an order paid happens inside a `DB::transaction` with `lockForUpdate()` on the order and payment rows, and only if the payment isn't already `paid` — safe against duplicate and out-of-order webhooks.
  - The bill's reported amount is compared against the stored `payments.amount_cents`; a mismatch refuses to mark paid rather than trusting it.
  - On success: `entitlements` are granted per order item (`firstOrCreate`, so re-processing is a no-op), an `invoices` row is created, and the user's cart is cleared.
- See `tests/Feature/BillplzWebhookTest.php` for the valid/invalid-signature/unpaid/duplicate/amount-mismatch/unknown-bill scenarios this is tested against.

## My Library and secure downloads

- `/library` (`App\Livewire\MyLibrary`) lists the current user's `entitlements` with a download link per `product_files` row.
- `GET /library/download/{entitlement}/{productFile}` (`App\Http\Controllers\DownloadController`) checks the entitlement belongs to the requesting user and the file belongs to the entitlement's product, then streams it from the private disk (the real storage path is never exposed) and logs a `downloads` row (IP + user agent) before returning it.
- `/orders/{order}/invoice` renders a simple line-item invoice (`invoices.invoice_number` is `INV-{year}-{order id}`) — ownership-checked the same way.

## Admin: orders

- `/admin/orders` (`orders.view` permission — Admin and Finance roles) is a read-only order list for now. Refund handling and finer payment/dispute management are not built yet — flagged as Phase 4/5 follow-up, not silently assumed to exist.

## Forge setup

1. Connect this repository and select the target branch for initial review.
2. Set the web directory to `/public`.
3. Create a MySQL 8.4 database and copy `.env.example` values into Forge Environment.
4. Add Billplz sandbox credentials in Forge; never commit them.
5. Run the deployment script in `forge/deploy.sh`.
6. Confirm the domain is `www.gayabmarketing.com` and enable SSL.
7. Keep checkout disabled until real products, files, prices and legal pages are approved.

## Billplz

Required environment variables:

- `BILLPLZ_API_KEY`
- `BILLPLZ_COLLECTION_ID`
- `BILLPLZ_X_SIGNATURE`
- `BILLPLZ_ENDPOINT=https://www.billplz-sandbox.com/api`
- `BILLPLZ_CHECKOUT_ENABLED=false`

## Testing

```bash
vendor/bin/pint --test        # formatting
vendor/bin/phpstan analyse    # static analysis (Larastan, level 3)
php artisan test              # feature/unit tests
```

CI (`.github/workflows/ci.yml`) runs all three against PHP 8.4 and 8.5 with a MySQL 8.4 service on every push/PR.

## Business identity

GAYA BORNEO ENTERPRISE
Registration: 202603150299 (KT0615457-D)
Email: admin@gayabmarketing.com
