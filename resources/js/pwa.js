function showBanner(text, actionLabel, onAction) {
    const bar = document.createElement('div');
    bar.setAttribute('role', 'status');
    bar.style.cssText = 'position:fixed;left:0;right:0;bottom:0;z-index:9999;background:#171813;color:#fff;padding:14px 18px;display:flex;gap:16px;align-items:center;justify-content:center;font:13px Arial,sans-serif;flex-wrap:wrap';
    const label = document.createElement('span');
    label.textContent = text;
    const action = document.createElement('button');
    action.textContent = actionLabel;
    action.style.cssText = 'background:#f05a24;color:#fff;border:0;padding:8px 14px;font-weight:800;cursor:pointer';
    action.addEventListener('click', () => { onAction(); bar.remove(); });
    const dismiss = document.createElement('button');
    dismiss.textContent = '✕';
    dismiss.setAttribute('aria-label', 'Dismiss');
    dismiss.style.cssText = 'background:transparent;color:#fff;border:0;cursor:pointer;font-size:14px';
    dismiss.addEventListener('click', () => bar.remove());
    bar.append(label, action, dismiss);
    document.body.appendChild(bar);
}

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then((registration) => {
            registration.addEventListener('updatefound', () => {
                const installing = registration.installing;
                installing?.addEventListener('statechange', () => {
                    if (installing.state === 'installed' && navigator.serviceWorker.controller) {
                        showBanner('A new version of Gaya B Marketing is available.', 'Refresh', () => {
                            installing.postMessage({ type: 'SKIP_WAITING' });
                            window.location.reload();
                        });
                    }
                });
            });
        }).catch(() => { /* offline support degrades gracefully without a service worker */ });
    });
}

let deferredInstallPrompt = null;
window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
    showBanner('Install Gaya B Marketing for quicker access.', 'Install', () => {
        deferredInstallPrompt?.prompt();
        deferredInstallPrompt = null;
    });
});

const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
const isInStandaloneMode = 'standalone' in navigator && navigator.standalone;
if (isIos && !isInStandaloneMode && !sessionStorage.getItem('gbm-ios-tip-dismissed')) {
    showBanner('To install: tap Share, then "Add to Home Screen".', 'Got it', () => {
        sessionStorage.setItem('gbm-ios-tip-dismissed', '1');
    });
}
