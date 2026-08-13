(()=>{'use strict';
const body=document.body,btn=document.querySelector('[data-admin-menu]'),sidebar=document.getElementById('adminSidebar'),overlay=document.querySelector('[data-admin-overlay]');
const openMenu=()=>{body.classList.add('ad-menu-open');btn?.setAttribute('aria-expanded','true');btn?.setAttribute('aria-label','Close admin menu');setTimeout(()=>sidebar?.querySelector('a,button,input')?.focus(),80)};
const closeMenu=(returnFocus=false)=>{body.classList.remove('ad-menu-open');btn?.setAttribute('aria-expanded','false');btn?.setAttribute('aria-label','Open admin menu');if(returnFocus)btn?.focus()};
btn?.addEventListener('click',()=>body.classList.contains('ad-menu-open')?closeMenu():openMenu());overlay?.addEventListener('click',()=>closeMenu(true));
document.addEventListener('click',e=>{if(innerWidth<992&&body.classList.contains('ad-menu-open')&&e.target.closest('.ad-link'))closeMenu(false)});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&body.classList.contains('ad-menu-open')){e.preventDefault();closeMenu(true)}});
addEventListener('resize',()=>{if(innerWidth>=992&&body.classList.contains('ad-menu-open'))closeMenu(false)},{passive:true});

const search=document.querySelector('[data-admin-nav-search]');
const filterNav=()=>{
  const term=(search?.value||'').trim().toLowerCase();const sections=[...document.querySelectorAll('.ad-nav-section')];
  const links=[...document.querySelectorAll('.ad-sidebar .ad-link')].filter(link=>!link.closest('.ad-sidebar-foot'));
  links.forEach(link=>link.classList.toggle('is-filtered-out',Boolean(term)&&!link.textContent.toLowerCase().includes(term)));
  sections.forEach(section=>{let node=section.nextElementSibling,visible=false;while(node&&!node.classList.contains('ad-nav-section')&&!node.classList.contains('ad-sidebar-foot')){if(node.classList.contains('ad-link')&&!node.classList.contains('is-filtered-out'))visible=true;node=node.nextElementSibling}section.classList.toggle('is-filtered-out',Boolean(term)&&!visible)});
};
search?.addEventListener('input',filterNav);
document.addEventListener('keydown',e=>{const tag=document.activeElement?.tagName?.toLowerCase();const editable=['input','textarea','select'].includes(tag)||document.activeElement?.isContentEditable;if(e.key==='/'&&!editable&&search){e.preventDefault();search.focus();search.select()}});
})();
