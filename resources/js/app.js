import './bootstrap';
const root=document.documentElement;const langEn=document.getElementById('lang-en');const langBm=document.getElementById('lang-bm');const theme=document.getElementById('theme');

const applyLang=(value)=>{
 root.lang=value==='BM'?'ms':'en';
 document.querySelectorAll('[data-en]').forEach(el=>el.textContent=el.dataset[value.toLowerCase()]);
 document.querySelectorAll('[data-en-placeholder]').forEach(el=>el.placeholder=el.dataset[value==='BM'?'bmPlaceholder':'enPlaceholder']);
 langEn?.classList.toggle('is-active',value==='EN');langBm?.classList.toggle('is-active',value==='BM');
 localStorage.setItem('gbm-language',value);
};
const applyTheme=(value)=>{root.dataset.theme=value;theme.textContent=value==='dark'?'☀':'☾';localStorage.setItem('gbm-theme',value)};

applyLang(localStorage.getItem('gbm-language')||'EN');
applyTheme(localStorage.getItem('gbm-theme')||'light');

langEn?.addEventListener('click',()=>applyLang('EN'));
langBm?.addEventListener('click',()=>applyLang('BM'));
theme?.addEventListener('click',()=>applyTheme(root.dataset.theme==='light'?'dark':'light'));

// product category filters
const filterBtns=document.querySelectorAll('.filter-btn');
const productCards=document.querySelectorAll('.grid article');
filterBtns.forEach(btn=>btn.addEventListener('click',()=>{
 filterBtns.forEach(b=>b.classList.remove('is-active'));
 btn.classList.add('is-active');
 const filter=btn.dataset.filter;
 productCards.forEach(card=>{
  card.style.display=(filter==='all'||card.dataset.category===filter)?'':'none';
 });
}));

// FAQ accordion
document.querySelectorAll('.faq-item button').forEach(btn=>btn.addEventListener('click',()=>{
 const item=btn.closest('.faq-item');
 const open=item.classList.toggle('is-open');
 btn.setAttribute('aria-expanded',open?'true':'false');
}));

// newsletter (client-side only, no backend endpoint)
const newsletterForm=document.getElementById('newsletter-form');
newsletterForm?.addEventListener('submit',(e)=>{
 e.preventDefault();
 const btn=newsletterForm.querySelector('button');
 const lang=root.lang==='ms'?'bm':'en';
 btn.textContent=lang==='bm'?'Terima kasih! ✓':'Thank you! ✓';
 newsletterForm.reset();
 setTimeout(()=>{btn.textContent=btn.dataset[lang]||btn.textContent;},2500);
});
