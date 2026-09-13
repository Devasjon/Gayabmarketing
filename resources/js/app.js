import './bootstrap';
import './pwa';

const root = document.documentElement;
const themeBtn = document.getElementById('theme');

const applyTheme = (value) => {
    root.dataset.theme = value;
    if (themeBtn) themeBtn.textContent = value === 'dark' ? '☀' : '☾';
    document.cookie = `gbm_theme=${value};path=/;max-age=31536000;samesite=lax`;
};

themeBtn?.addEventListener('click', () => applyTheme(root.dataset.theme === 'light' ? 'dark' : 'light'));

// newsletter (client-side only placeholder, no backend endpoint yet)
const newsletterForm = document.getElementById('newsletter-form');
newsletterForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    const btn = newsletterForm.querySelector('button');
    const original = btn.textContent;
    btn.textContent = btn.dataset.thankYou || original;
    newsletterForm.reset();
    setTimeout(() => { btn.textContent = original; }, 2500);
});
