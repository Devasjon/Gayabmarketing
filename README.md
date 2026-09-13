# Gaya B Marketing

Laravel storefront for **GAYA BORNEO ENTERPRISE** — digital products and publishing from Borneo, Malaysia.

## Stack

- Laravel 13, PHP `^8.4` (dev/CI run PHP 8.4 and 8.5; production targets 8.5 once verified on Forge, with 8.4 as fallback)
- Livewire 4 for interactive components (product catalog filter), Alpine (bundled with Livewire) for lightweight UI (FAQ accordion)
- Tailwind CSS 4 + Vite
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
