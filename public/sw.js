const CACHE_VERSION = 'gbm-v1';
const STATIC_CACHE = `static-${CACHE_VERSION}`;
const PAGES_CACHE = `pages-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    '/offline.html',
    '/manifest.webmanifest',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

// Never cache these: auth/checkout/payment/admin-style endpoints and anything
// carrying a CSRF token or session-specific state.
const NETWORK_ONLY_PATTERNS = [
    /^\/checkout/,
    /^\/billplz/,
    /^\/storage/,
    /^\/locale\//,
    /^\/livewire/,
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE_URLS))
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => key !== STATIC_CACHE && key !== PAGES_CACHE).map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

// Only take over immediately when the user confirms via the "Refresh" banner
// (see resources/js/pwa.js) — otherwise the new worker waits so an update
// never yanks the page out from under someone mid-checkout.
self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') self.skipWaiting();
});

function isNetworkOnly(url) {
    return NETWORK_ONLY_PATTERNS.some((pattern) => pattern.test(url.pathname));
}

function isStaticAsset(url) {
    return url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/') || url.pathname === '/manifest.webmanifest';
}

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin) return;
    if (isNetworkOnly(url)) return;

    // Cache-first for immutable static assets (hashed build files, icons, manifest).
    if (isStaticAsset(url)) {
        event.respondWith(
            caches.match(request).then((cached) => cached || fetch(request).then((response) => {
                const clone = response.clone();
                caches.open(STATIC_CACHE).then((cache) => cache.put(request, clone));
                return response;
            }))
        );
        return;
    }

    // Network-first for page navigations, falling back to cache then the offline
    // page. Deliberately not stale-while-revalidate: these pages embed a CSRF
    // token, and serving a stale one from cache while online would be wrong.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const clone = response.clone();
                    caches.open(PAGES_CACHE).then((cache) => cache.put(request, clone));
                    return response;
                })
                .catch(() => caches.match(request).then((cached) => cached || caches.match(OFFLINE_URL)))
        );
    }
});
