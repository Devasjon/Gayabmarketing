# Gaya B Marketing

Laravel storefront for **GAYA BORNEO ENTERPRISE** — digital products and publishing from Borneo, Malaysia.

## Current safety state

- All seeded products are `draft`.
- Billplz uses the sandbox endpoint.
- Checkout is disabled by default with `BILLPLZ_CHECKOUT_ENABLED=false`.
- No API keys or product files are committed.

## Forge setup

1. Connect this repository and select the `develop/laravel-storefront` branch for initial review.
2. Set the web directory to `/public`.
3. Create a MySQL database and copy `.env.example` values into Forge Environment.
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

## Business identity

GAYA BORNEO ENTERPRISE  
Registration: 202603150299 (KT0615457-D)  
Email: admin@gayabmarketing.com

