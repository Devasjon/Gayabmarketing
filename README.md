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
- Six roles from the PRD are seeded by `RolePermissionSeeder`: Super Admin, Admin, Finance, Content Manager, Support, Customer, with permissions assigned per the PRD's role table (e.g. Support gets `orders.view` + `customers.*` but not `products.manage`; Finance gets `orders.view` for the finance dashboard but not customer/product access).
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

- `/admin/orders` (`orders.view` permission — Admin and Finance roles) is a read-only order list for now. Refund handling and finer payment/dispute management are not built yet — flagged as a follow-up, not silently assumed to exist.

## Transactional email (Resend)

- Mail is sent via Laravel's built-in `resend` transport (`resend/resend-php`, `MAIL_MAILER=resend`, `RESEND_KEY` env var). Locally, `.env` uses `MAIL_MAILER=log` so emails land in `storage/logs/laravel.log` instead of requiring a real key.
- `App\Mail\OrderConfirmationMail` (implements `ShouldQueue`) is dispatched from `PaymentProcessor` right after an order is marked paid — the same "only once, even on duplicate webhooks" guarantee that protects entitlements also protects this email from double-sending.
- Queues now run on the `database` driver (`QUEUE_CONNECTION=database`; `jobs`/`failed_jobs` tables added) instead of `sync` — run `php artisan queue:work` locally to process them, and Forge's queue daemon in production (already restarted on deploy via `queue:restart` in `forge/deploy.sh`).

## Admin: finance, tasks and customers

- **Finance** (`/admin/finance`, `orders.view` — Admin/Finance): total/this-month revenue and a 30-day daily breakdown computed directly from `orders`/`payments` (no separate ledger table), plus a CSV export (`/admin/finance/export`) of every paid order.
- **Tasks** (`/admin/tasks`, `tasks.manage` — all staff roles): a Kanban board (To Do / In Progress / Done). Moving a card is a plain "Move to" `<select>`, not drag-and-drop — this was a deliberate choice to satisfy the PRD's touch/accessibility requirement directly rather than bolting a non-drag fallback onto a drag library. A "due soon" panel lists tasks due within 3 days and flags overdue ones.
- **Customers** (`/admin/customers`, `customers.view` — Admin/Support): a read-only customer list (order count, total spent) with a detail view where staff can leave notes (`customer_notes`, `customers.manage_notes`) — this is the CRM the PRD asks for; it's deliberately just notes on existing `User` records rather than a separate contact model, since customers already are the CRM's contacts.

## Not built this phase (flagged, not silently skipped)

- **Booking** (in-house booking/scheduling) — the PRD doesn't specify what's being booked or its rules closely enough to design a schema without guessing; needs a product decision first.
- **WhatsApp (WaSenderAPI)** — the PRD itself says this comes "after the main flow is stable"; also needs a real API key this environment doesn't have, same situation Billplz was in during Phase 1.
- **Marketing/SEO campaign management** — meta tags/Open Graph/structured data from earlier phases already cover baseline SEO; a campaign-builder UI is a separate, more product-defined piece of work.
- **Refund workflow** from the admin side (see Admin: orders above).

## Accessibility (Phase 5 spot-check, WCAG 2.2 AA)

- **Reduced motion**: a global `prefers-reduced-motion: reduce` rule (`resources/css/app.css`) collapses every transition/animation — custom CSS, Tailwind utilities, and Livewire's injected progress bar — to effectively instant, rather than disabling each one individually.
- **Status messages** (SC 4.1.3): flash messages (`session('status')`/`session('error')`) across auth, profile and admin Product/Category managers now carry `role="status"` (or `role="alert"` for errors), so screen readers announce them without requiring focus to move.
- **Keyboard/ARIA on the account dropdown**: `x-dropdown` (Breeze stock component, used for the header user menu) now closes on <kbd>Escape</kbd> and exposes `aria-haspopup`/`aria-expanded` on its trigger; the trigger was already a real `<button>` so it was keyboard-focusable/activatable before this.
- **Colour contrast** (SC 1.4.3): the brand orange (`#f05a24`) used as small/normal-weight text on the light cream background measured **3.09:1**, and white button text on that same orange background measured **3.39:1** — both fail the 4.5:1 minimum for normal text (large text like the 50–84px hero heading, which also uses the brand orange, already passes at the 3:1 large-text threshold and was left untouched). Fixed with two new CSS custom properties instead of changing the brand color itself:
  - `--orange-text` (`#b23d15`, 5.36:1 on cream) replaces `var(--orange)` on small text uses (`.eyebrow`, `.link`, `.view-all`, `.coming b`, `.grid article .kicker`/`>a`, `.steps li::before`) — overridden back to the original `#f05a24` under `html[data-theme=dark]`, since that vivid orange already passes 5.36:1 against the dark theme's near-black background.
  - `--orange-btn` (`#b23d15`, 5.88:1 with white text) replaces `var(--orange)` as the background on `.btn`/`.product-detail button`/the active language-switch pill — a fixed value regardless of theme, since it's the button's own background rather than page background.
- **Known limitation, not fixed this phase**: form validation errors (`@error` blocks) aren't wired to their inputs via `aria-describedby`/`aria-invalid` — sighted and keyboard users still see them adjacent to the field, but a screen reader won't associate the error with the specific input automatically. Flagging rather than fixing silently, since it touches every form across auth/profile/admin/checkout and is a larger, separate pass.

## Security posture (Phase 5)

- **Security headers** (`App\Http\Middleware\SecurityHeaders`, applied to the whole `web` group): `Content-Security-Policy`, `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy` (blocks geolocation/microphone/camera).
  - CSP is `default-src 'self'` with a **per-request nonce** (shared with `Vite::useCspNonce()`, so `@vite`, `@livewireStyles` and `@livewireScripts` pick it up automatically) covering `script-src`. `'unsafe-eval'` is included in `script-src` because Alpine.js (bundled with Livewire) evaluates directive expressions via the `Function` constructor — dropping it would require migrating to Livewire's separate CSP-safe Alpine build (`livewire.csp_safe` config), which is a larger, untested change out of scope for this phase.
  - `style-src` uses `'unsafe-inline'` rather than a nonce: Alpine sets the `style` attribute directly for `x-show`/`x-cloak`/`x-transition`, which CSP treats as inline style regardless of a nonce on the element (nonces only cover `<style>` tags, not attribute writes).
  - Any new inline `<script>` (like the no-flash theme snippet in `resources/views/partials/head.blade.php`) must carry `nonce="{{ $cspNonce }}"` or the browser will block it silently.
- **Rate limiting**: `throttle:6,1` on register/forgot-password/reset-password/confirm-password/password-update (`routes/auth.php`); `throttle:60,1` on the Billplz callback (`routes/web.php`); login already had Breeze's own per-email/IP throttling (`LoginRequest::ensureIsNotRateLimited()`). Checkout's `pay()` action is rate-limited manually inside the Livewire component (`App\Livewire\Checkout`, keyed per user via the `RateLimiter` facade) because Livewire actions all share one HTTP endpoint — route-level `throttle:*` middleware can't isolate a single component method.
- **Audit log**: `App\Models\Concerns\Auditable` (applied to `Product`, `Category`, `Order`, `Payment`, `Entitlement` — deliberately not `User`, to avoid needing to redact password/remember-token fields) records created/updated/deleted events to `audit_logs` (who, what changed, IP, when). Viewable at `/admin/audit-log` behind the new `audit_logs.view` permission (Admin/Super Admin only).
- **Pagination**: `ProductManager` and `CategoryManager` (admin Livewire components) now paginate at 20 rows instead of loading the full table — matches `OrderManager`/`CustomerManager`/`AuditLogManager`, which already paginated.
- **PDPA / account deletion**: self-service account deletion (`ProfileController::destroy`) hard-deletes the `User` row. `orders.user_id` and now `entitlements.user_id` are `nullOnDelete` (a migration changed entitlements from `cascadeOnDelete`) — a closed account keeps its order/payment/entitlement history intact (needed for accounting and so a re-registering customer's past purchases/downloads aren't silently destroyed) while the row no longer identifies a live account. `customer_notes` (internal staff notes about a customer) still cascade-delete, which is intentional: once a customer's account and PII are gone, retaining staff commentary about them serves no remaining business purpose.

## Forge setup

1. Connect this repository and select the target branch for initial review.
2. Set the web directory to `/public`.
3. Create a MySQL 8.4 database and copy `.env.example` values into Forge Environment.
4. Add Billplz sandbox credentials in Forge; never commit them.
5. Run the deployment script in `forge/deploy.sh`.
6. Confirm the domain is `www.gayabmarketing.com` and enable SSL.
7. Keep checkout disabled until real products, files, prices and legal pages are approved.

### Required PHP extensions

Laravel 13 core: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `session`, `tokenizer`, `xml`. This app additionally needs:

- `pdo_mysql` — production database driver.
- `bcmath` — used by Laravel's decimal/money-safe helpers.
- `intl` — locale-aware number/date formatting (`en`/`ms`).
- `zip` — reading/validating uploaded `.zip` product files.
- `gd` — image MIME validation on cover uploads.

Forge's default PHP 8.4/8.5 install already includes all of the above; nothing extra to enable.

### Scheduler

No commands are scheduled yet (`routes/console.php` only defines the stock `inspire` command), but add the cron entry now so nothing has to be remembered later when one is added (e.g. `queue:prune-failed`, `model:prune`):

```
* * * * * php /home/forge/www.gayabmarketing.com/artisan schedule:run >> /dev/null 2>&1
```

Forge adds this automatically when a site is created with "Laravel" as the project type — confirm it's present under the site's **Scheduler** tab.

### Migration / rollback strategy

- `forge/deploy.sh` runs `php artisan migrate --force` on every deploy — forward-only, never `migrate:fresh` or `migrate:rollback` in an automated deploy.
- It seeds only `RolePermissionSeeder` (`php artisan db:seed --class="Database\Seeders\RolePermissionSeeder" --force`), not the full `db:seed`. The full seeder also re-syncs `DatabaseSeeder`'s 4 demo catalog products via `updateOrCreate` — running that on every deploy would silently overwrite real admin edits (price, category, status) on those rows. `RolePermissionSeeder` is safe to rerun (idempotent `Role::findOrCreate` + additive `givePermissionTo`).
- Rollback: `php artisan migrate:rollback --step=N --force` for the last N migrations, run manually over SSH after confirming which migrations shipped in the bad deploy — never as part of the automated script. Take a database snapshot (see Backup below) before rolling back a migration that dropped or renamed a column.

### Backup and restore checklist

- Enable Forge's scheduled MySQL backups (or a provider-level snapshot, e.g. RDS automated backups) before go-live — daily, retained at least 30 days.
- Before every deploy that includes a migration: confirm the most recent automated backup succeeded (Forge's backup log / provider console).
- `storage/app/public` (cover images) and `storage/app/private` or equivalent local-disk path (`product_files`, paid digital downloads) are **not** covered by a database backup — back them up separately (Forge's file backup add-on, or an S3 sync cron) since losing them means customers can't re-download what they paid for.
- Restore drill (run at least once before launch, and periodically after): restore the latest DB backup to a scratch database, run `php artisan migrate:status` against it to confirm it's not behind head, and spot-check a handful of `orders`/`entitlements` rows.
- `.env` itself is never in the backup/version-control story — Forge holds the only copy; keep a secure secondary copy (e.g. in a password manager) so a lost server isn't also a lost `APP_KEY`/`BILLPLZ_*`/`RESEND_KEY`.

### Production environment variables (names only — see `.env.example` for the full list with production defaults)

`APP_KEY`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `SESSION_DRIVER`, `QUEUE_CONNECTION`, `CACHE_STORE`, `MAIL_MAILER`, `RESEND_KEY`, `MAIL_FROM_ADDRESS`, `BILLPLZ_ENDPOINT`, `BILLPLZ_API_KEY`, `BILLPLZ_COLLECTION_ID`, `BILLPLZ_X_SIGNATURE`, `BILLPLZ_CHECKOUT_ENABLED`.

## Billplz

Required environment variables:

- `BILLPLZ_API_KEY`
- `BILLPLZ_COLLECTION_ID`
- `BILLPLZ_X_SIGNATURE`
- `BILLPLZ_ENDPOINT=https://www.billplz-sandbox.com/api`
- `BILLPLZ_CHECKOUT_ENABLED=false`

## Resend

Required environment variables:

- `MAIL_MAILER=resend`
- `RESEND_KEY`

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
