(()=>{'use strict';
const reduceMotion=window.matchMedia?.('(prefers-reduced-motion: reduce)').matches??false;
const csrf=document.querySelector('meta[name="csrf-token"]')?.content||'';
const nav=document.querySelector('.tr-navbar');
const onScroll=()=>nav&&nav.classList.toggle('is-scrolled',scrollY>10);onScroll();addEventListener('scroll',onScroll,{passive:true});
const io='IntersectionObserver'in window&&!reduceMotion?new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('is-visible');io.unobserve(e.target)}}),{threshold:.08}):null;
document.querySelectorAll('.tr-reveal').forEach(el=>io?io.observe(el):el.classList.add('is-visible'));

const toast=(message,type='info')=>{
  let wrap=document.querySelector('.tr-toast-wrap');
  if(!wrap){wrap=document.createElement('div');wrap.className='tr-toast-wrap';wrap.setAttribute('role','status');wrap.setAttribute('aria-live','polite');wrap.setAttribute('aria-atomic','true');document.body.appendChild(wrap)}
  const t=document.createElement('div');t.className='tr-toast';
  const strong=document.createElement('strong');strong.style.cssText='display:block;margin-bottom:3px';strong.textContent=type==='success'?'Done':type==='error'?'Something went wrong':'Trendora';
  const span=document.createElement('span');span.style.cssText='color:#94a3b8;font-size:12px';span.textContent=String(message||'');
  t.append(strong,span);wrap.appendChild(t);
  setTimeout(()=>{t.style.opacity='0';t.style.transform='translateY(8px)';setTimeout(()=>t.remove(),reduceMotion?0:250)},3200);
};
window.Trendora={toast};

const setNavBadge=(selector,count)=>{
  const action=document.querySelector(selector);if(!action)return;
  let badge=action.querySelector('.tr-badge');
  if(Number(count)>0){if(!badge){badge=document.createElement('span');badge.className='tr-badge';action.appendChild(badge)}badge.textContent=Number(count)>99?'99+':String(count)}else badge?.remove();
};
const fetchJson=async(url,options={})=>{
  const response=await fetch(url,{credentials:'same-origin',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest',...(options.headers||{})},...options});
  if(response.redirected&&response.url.includes('/login')){location.href=response.url;return null}
  const data=await response.json().catch(()=>({success:false,message:'Unexpected server response.'}));
  if(!response.ok&&typeof data.success==='undefined')data.success=false;
  return data;
};

document.addEventListener('click',async event=>{
  const cartButton=event.target.closest('[data-cart-add]');
  if(cartButton){
    event.preventDefault();if(cartButton.disabled)return;
    const original=cartButton.innerHTML;cartButton.disabled=true;cartButton.innerHTML='<i class="fa-solid fa-spinner fa-spin me-2" aria-hidden="true"></i>Adding…';
    try{const data=await fetchJson(cartButton.dataset.url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({product_id:Number(cartButton.dataset.productId),quantity:1})});if(!data)return;if(data.success){toast(data.message||'Added to cart.','success');setNavBadge('.tr-cart-action',data.count)}else toast(data.message||'Could not add this product.','error')}catch(e){toast('Could not reach the store. Please try again.','error')}finally{cartButton.disabled=false;cartButton.innerHTML=original}
    return;
  }
  const wishButton=event.target.closest('[data-wishlist-toggle]');
  if(wishButton){
    event.preventDefault();wishButton.disabled=true;
    try{const data=await fetchJson(wishButton.dataset.url,{method:'POST'});if(!data)return;if(data.success){wishButton.classList.toggle('is-active',Boolean(data.in_wishlist));const icon=wishButton.querySelector('i');if(icon)icon.className=data.in_wishlist?'fa-solid fa-heart':'fa-regular fa-heart';toast(data.message||'Wishlist updated.','success');setNavBadge('.tr-wishlist-action',data.count)}else toast(data.message||'Could not update wishlist.','error')}catch(e){toast('Could not update wishlist. Please try again.','error')}finally{wishButton.disabled=false}
    return;
  }
  const mobileSearch=event.target.closest('[data-mobile-search]');
  if(mobileSearch){event.preventDefault();const input=document.querySelector('[data-global-search] input');const collapse=document.getElementById('trendoraNav');if(collapse&&window.bootstrap?.Collapse&&!collapse.classList.contains('show'))bootstrap.Collapse.getOrCreateInstance(collapse).show();setTimeout(()=>input?.focus(),collapse?180:0)}
});

addEventListener('keydown',event=>{
  if((event.metaKey||event.ctrlKey)&&event.key.toLowerCase()==='k'){
    event.preventDefault();const input=document.querySelector('[data-global-search] input');const collapse=document.getElementById('trendoraNav');if(collapse&&window.bootstrap?.Collapse&&innerWidth<992&&!collapse.classList.contains('show'))bootstrap.Collapse.getOrCreateInstance(collapse).show();setTimeout(()=>{input?.focus();input?.select()},collapse&&innerWidth<992?180:0);
  }
});

const checkout=document.querySelector('[data-checkout-steps]');
if(checkout){
  let step=1;
  const panels=[...document.querySelectorAll('[data-checkout-step]')];
  const buttons=[...document.querySelectorAll('[data-step-button]')];
  const form=document.getElementById('checkoutForm');
  const review=()=>{const get=n=>form?.elements[n]?.value||'';const address=[get('shipping_name'),get('shipping_address'),get('shipping_city'),get('shipping_state'),get('shipping_zip'),get('shipping_country'),get('shipping_phone')].filter(Boolean).join(', ');const addr=document.getElementById('checkoutReviewAddress');if(addr)addr.textContent=address;const p=form?.querySelector('input[name="payment_method"]:checked');const label=p?.closest('.tr-payment-option')?.querySelector('strong')?.textContent||p?.value||'';const pay=document.getElementById('checkoutReviewPayment');if(pay)pay.textContent=label};
  const show=n=>{step=Math.max(1,Math.min(4,n));panels.forEach(p=>p.classList.toggle('d-none',+p.dataset.checkoutStep!==step));buttons.forEach(b=>{const x=+b.dataset.stepButton;b.classList.toggle('active',x===step);b.classList.toggle('done',x<step);b.setAttribute('aria-current',x===step?'step':'false')});window.scrollTo({top:Math.max(0,checkout.getBoundingClientRect().top+scrollY-95),behavior:reduceMotion?'auto':'smooth'});if(step===4)review();document.querySelector(`[data-checkout-step="${step}"]`)?.querySelector('input,select,textarea,button')?.focus({preventScroll:true})};
  const requiredValid=()=>{const panel=document.querySelector(`[data-checkout-step="${step}"]`);const fields=[...(panel?.querySelectorAll('[required]')||[])];for(const field of fields){if(!field.checkValidity()){field.reportValidity();return false}}return true};
  document.querySelectorAll('[data-checkout-next]').forEach(b=>b.addEventListener('click',()=>{if(requiredValid())show(step+1)}));
  document.querySelectorAll('[data-checkout-prev]').forEach(b=>b.addEventListener('click',()=>show(step-1)));
  buttons.forEach(b=>b.addEventListener('click',()=>{const target=+b.dataset.stepButton;if(target<step)show(target)}));
  document.querySelectorAll('.tr-payment-option input').forEach(i=>i.addEventListener('change',()=>{document.querySelectorAll('.tr-payment-option').forEach(x=>x.classList.remove('active'));i.closest('.tr-payment-option')?.classList.add('active')}));
  document.querySelectorAll('[data-address]').forEach(btn=>btn.addEventListener('click',()=>{let data={};try{data=JSON.parse(btn.dataset.address)}catch(e){}document.querySelectorAll('[data-address]').forEach(x=>x.classList.remove('active'));btn.classList.add('active');Object.entries(data).forEach(([k,v])=>{const f=form?.querySelector(`[data-field="${k}"]`);if(f)f.value=v||''})}));
  form?.addEventListener('submit',()=>{const b=document.getElementById('placeOrderBtn');if(b){b.disabled=true;b.innerHTML='<i class="fa-solid fa-spinner fa-spin me-2" aria-hidden="true"></i>Processing order…'}});
}
})();
