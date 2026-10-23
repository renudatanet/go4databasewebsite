{{-- Shared look and behaviour for the main navbar.

     Included by navbar-new (the homepage) and navbar-variant/navbar-04 (every
     inner page on live), so the two can no longer drift apart. Inline rather
     than a stylesheet for the same reason as the chat widget: production's
     public_html/assets is a separate copy, and a view deploys on its own.

     Everything is scoped to #g4d-header. home.css (homepage only) still carries
     the old navbar rules, several of them !important, and the menu builder
     writes an inline style onto .g4d-nav that absolutely centres it. The id
     outranks both, so these rules hold whether or not home.css is ever
     cleaned up. Where an !important is used below, it is overriding one of
     those, not another rule in this file. --}}
<style>
#g4d-header{
  --g4n-ink:#0b132a;
  --g4n-slate:#556070;
  --g4n-line:#e7ecf1;
  --g4n-green:#6fd943;
  --g4n-green-hover:#63cf37;
  --g4n-deep:#2b6b0e;
  --g4n-tint:#f0fbe9;
  --g4n-ease:cubic-bezier(.2,.8,.2,1);

  position:sticky;
  top:0;
  /* Above page content, below the popup builder's backdrop (999), the
     search popup and the mobile drawer, so those still cover the header. */
  z-index:990;
  background:#fff;
  border-bottom:1px solid rgba(11,19,42,.06);
  transition:background-color .25s ease,box-shadow .25s ease,border-color .25s ease;
}
#g4d-header.scrolled{
  background:rgba(255,255,255,.82);
  -webkit-backdrop-filter:blur(14px) saturate(170%);
  backdrop-filter:blur(14px) saturate(170%);
  border-bottom-color:var(--g4n-line);
  box-shadow:0 12px 32px -20px rgba(11,19,42,.35);
}
@supports not ((backdrop-filter:blur(1px)) or (-webkit-backdrop-filter:blur(1px))){
  #g4d-header.scrolled{background:#fff;}
}

/* Logo, menu and buttons are three grid columns. The two outer ones share
   the leftover space equally, so the menu stays centred on the page, but
   none can shrink below its content, so they push each other apart instead
   of overlapping the way the absolutely centred menu did at 993-1180px. */
#g4d-header .g4d-bar{
  position:relative;
  display:grid;
  grid-template-columns:1fr auto 1fr;
  align-items:center;
  gap:24px;
  max-width:1440px;
  margin:0 auto;
  padding:13px 24px !important;
}
#g4d-header .g4d-logo{grid-column:1;justify-self:start;display:flex;align-items:center;}
#g4d-header .g4d-logo img{display:block;height:34px;width:auto;}
/* Inner pages fall back to the site title when no logo is uploaded. */
#g4d-header .g4d-logo .site-title{margin:0;font-family:'Nunito',sans-serif;font-size:22px;font-weight:800;color:var(--g4n-ink);}

#g4d-header .g4d-nav{
  grid-column:2;
  position:static !important;
  left:auto !important;
  transform:none !important;
  display:flex;
  align-items:center;
  gap:2px !important;
}

/* --- top-level items -------------------------------------------------- */
#g4d-header .nav-dropdown-wrapper{position:relative;display:block;}
#g4d-header .nav-link-item{
  position:relative;
  z-index:1;
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:9px 15px;
  border-radius:999px;
  font-family:'Nunito',sans-serif;
  font-size:15px;
  font-weight:700;
  line-height:1.25;
  color:var(--g4n-ink);
  white-space:nowrap;
  text-decoration:none;
  cursor:pointer;
  transition:color .2s ease,background-color .2s ease;
}
#g4d-header .nav-link-item svg{
  flex:none;
  color:currentColor !important;
  opacity:.55;
  transform:none !important;
  transition:transform .25s var(--g4n-ease),opacity .2s ease;
}
#g4d-header .nav-link-item:hover,
#g4d-header .nav-dropdown-wrapper:hover > .nav-link-item,
#g4d-header .nav-dropdown-wrapper:focus-within > .nav-link-item,
#g4d-header .nav-dropdown-wrapper.active > .nav-link-item,
#g4d-header .nav-link-item.is-current{
  color:var(--g4n-deep) !important;
}
/* Without the script there is no gliding highlight, so each item carries
   its own. With it, the highlight does the job and this is switched off. */
#g4d-header .nav-link-item:hover,
#g4d-header .nav-dropdown-wrapper:hover > .nav-link-item{background:var(--g4n-tint);}
#g4d-header.has-glide .nav-link-item{background:transparent !important;}

#g4d-header .nav-dropdown-wrapper:hover > .nav-link-item svg,
#g4d-header .nav-dropdown-wrapper:focus-within > .nav-link-item svg,
#g4d-header .nav-dropdown-wrapper.active > .nav-link-item svg{
  opacity:1;
  transform:rotate(180deg) !important;
}

/* The highlight that follows the pointer between items and comes to rest on
   the page you are on. Positioned by the script below. */
#g4d-header .g4d-glide{
  position:absolute;
  top:0;
  left:0;
  z-index:0;
  border-radius:999px;
  background:var(--g4n-tint);
  box-shadow:inset 0 0 0 1px rgba(111,217,67,.24);
  opacity:0;
  pointer-events:none;
  transition:transform .32s var(--g4n-ease),width .32s var(--g4n-ease),opacity .2s ease;
}
#g4d-header .g4d-glide.is-on{opacity:1;}
#g4d-header .g4d-glide.is-jump{transition:opacity .2s ease;}

/* --- dropdown cards --------------------------------------------------- */
#g4d-header .nav-dropdown-menu{
  position:absolute;
  top:calc(100% + 20px);
  left:0 !important;
  z-index:10;
  min-width:224px;
  padding:6px !important;
  background:#fff;
  border:1px solid var(--g4n-line) !important;
  border-radius:14px;
  box-shadow:0 24px 48px -16px rgba(11,19,42,.22),0 2px 6px rgba(11,19,42,.05);
  overflow:visible;
  opacity:0;
  visibility:hidden;
  transform:translateY(-6px) scale(.98) !important;
  transform-origin:top left;
  /* A short pause before closing, so cutting a corner on the way down to the
     card does not snap it shut. */
  transition:opacity .15s ease .08s,transform .18s var(--g4n-ease) .08s,visibility 0s linear .26s;
}
/* Bridges the gap between the item and the card so the pointer never leaves
   the dropdown on its way across. Hidden along with the card when closed. */
#g4d-header .nav-dropdown-menu::before{
  content:'';
  position:absolute;
  left:0;
  right:0;
  top:-21px;
  height:21px;
}
#g4d-header .nav-dropdown-wrapper:hover > .nav-dropdown-menu,
#g4d-header .nav-dropdown-wrapper:focus-within > .nav-dropdown-menu,
#g4d-header .nav-dropdown-wrapper.active > .nav-dropdown-menu{
  opacity:1;
  visibility:visible;
  transform:none !important;
  transition:opacity .18s ease,transform .24s var(--g4n-ease),visibility 0s;
}

#g4d-header .simple-dropdown-item{
  display:block;
  padding:10px 14px;
  border:0 !important;
  border-radius:9px;
  background:transparent;
  font-family:'Nunito',sans-serif;
  font-size:14.5px;
  font-weight:700;
  line-height:1.35;
  color:var(--g4n-ink);
  text-align:left;
  text-decoration:none;
  white-space:nowrap;
  transition:background-color .15s ease,color .15s ease;
}
#g4d-header .simple-dropdown-item:hover,
#g4d-header .simple-dropdown-item:focus-visible,
#g4d-header .simple-dropdown-item[aria-current="page"]{
  background:var(--g4n-tint) !important;
  color:var(--g4n-deep) !important;
}

/* --- rich dropdown items ---------------------------------------------- */
/* An icon tile and a one-line description beside each title. Both are drawn
   from data attributes the script below sets, never added as text, so a
   link's own text stays just its title: mobile-nav.js copies that text into
   the phone drawer, and the drawer should not repeat the descriptions. */
#g4d-header .nav-dropdown-menu.is-rich{min-width:356px;padding:8px !important;}
#g4d-header .simple-dropdown-item[data-icon]{
  display:grid;
  grid-template-columns:38px minmax(0,1fr);
  column-gap:13px;
  align-items:center;
  padding:9px 12px 9px 9px;
  border-radius:11px;
  white-space:normal;
}
/* An icon set on the item in the admin menu builder; the tile replaces it. */
#g4d-header .simple-dropdown-item[data-icon] > i{display:none;}
#g4d-header .simple-dropdown-item[data-icon]::before{
  content:'';
  grid-row:1 / span 2;
  width:38px;
  height:38px;
  border-radius:10px;
  background-color:var(--g4n-tint);
  background-position:center;
  background-repeat:no-repeat;
  background-size:19px 19px;
  box-shadow:inset 0 0 0 1px rgba(111,217,67,.26);
  transition:background-color .2s ease,box-shadow .2s ease,transform .3s var(--g4n-ease);
}
#g4d-header .simple-dropdown-item[data-desc]::after{
  content:attr(data-desc);
  grid-column:2;
  margin-top:1px;
  font-size:13px;
  font-weight:600;
  line-height:1.4;
  color:var(--g4n-slate);
  transition:color .15s ease;
}
/* Hovering a row lifts and fills its tile with the brand green. The row
   itself takes a paler tint than usual so the tile stays the focal point. */
#g4d-header .simple-dropdown-item[data-icon]:hover,
#g4d-header .simple-dropdown-item[data-icon]:focus-visible,
#g4d-header .simple-dropdown-item[data-icon][aria-current="page"]{background:#f6fbf3 !important;}
#g4d-header .simple-dropdown-item[data-icon]:hover::before,
#g4d-header .simple-dropdown-item[data-icon]:focus-visible::before,
#g4d-header .simple-dropdown-item[data-icon][aria-current="page"]::before{
  background-color:var(--g4n-green);
  box-shadow:inset 0 0 0 1px rgba(43,107,14,.18),0 6px 14px -6px rgba(63,150,25,.7);
  transform:translateY(-1px) scale(1.06);
}
#g4d-header .simple-dropdown-item[data-desc]:hover::after{color:#3f4a59;}

/* Rows arrive one after another as the card opens. Only the opening is
   staggered, and hover colours are never delayed. On closing, the rows
   reset only once the card itself has faded, or it would empty first. */
#g4d-header .is-rich > .simple-dropdown-item{
  opacity:0;
  transform:translateY(5px);
  transition:opacity 0s linear .3s,transform 0s linear .3s,background-color .15s ease,color .15s ease;
}
#g4d-header .nav-dropdown-wrapper:hover > .is-rich > .simple-dropdown-item,
#g4d-header .nav-dropdown-wrapper:focus-within > .is-rich > .simple-dropdown-item,
#g4d-header .nav-dropdown-wrapper.active > .is-rich > .simple-dropdown-item{
  opacity:1;
  transform:none;
  transition:opacity .22s ease var(--g4n-d,0s),transform .32s var(--g4n-ease) var(--g4n-d,0s),background-color .15s ease,color .15s ease;
}
#g4d-header .is-rich > .simple-dropdown-item:nth-child(2){--g4n-d:.04s;}
#g4d-header .is-rich > .simple-dropdown-item:nth-child(3){--g4n-d:.08s;}
#g4d-header .is-rich > .simple-dropdown-item:nth-child(4){--g4n-d:.12s;}
#g4d-header .is-rich > .simple-dropdown-item:nth-child(n+5){--g4n-d:.16s;}

#g4d-header .simple-dropdown-item[data-icon="stories"]::before{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232b6b0e' stroke-width='1.9' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4Z'/%3E%3Cpath d='M17 5h3v2a3 3 0 0 1-3 3M7 5H4v2a3 3 0 0 0 3 3'/%3E%3C/svg%3E");}
#g4d-header .simple-dropdown-item[data-icon="quote"]::before{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232b6b0e' stroke-width='1.9' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.4A8 8 0 1 1 21 12Z'/%3E%3Cpath d='M8.5 12h.01M12 12h.01M15.5 12h.01'/%3E%3C/svg%3E");}
#g4d-header .simple-dropdown-item[data-icon="verify"]::before{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232b6b0e' stroke-width='1.9' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 11.5V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7.5'/%3E%3Cpath d='m3 7 9 6 9-6'/%3E%3Cpath d='m15.5 19 2 2 4-4'/%3E%3C/svg%3E");}
#g4d-header .simple-dropdown-item[data-icon="finder"]::before{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232b6b0e' stroke-width='1.9' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cpath d='m20 20-3.6-3.6'/%3E%3Ccircle cx='11' cy='9.5' r='2'/%3E%3Cpath d='M7.8 14.2a4 4 0 0 1 6.4 0'/%3E%3C/svg%3E");}
#g4d-header .simple-dropdown-item[data-icon="affiliate"]::before{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232b6b0e' stroke-width='1.9' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M15 9.6c-.5-.9-1.6-1.4-3-1.4-1.7 0-3 .8-3 2s1.3 1.6 3 1.9 3 .8 3 2-1.3 2-3 2c-1.4 0-2.5-.5-3-1.4M12 6.6v1.6M12 15.9v1.5'/%3E%3C/svg%3E");}
#g4d-header .simple-dropdown-item[data-icon="blog"]::before{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232b6b0e' stroke-width='1.9' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M2 5h6a4 4 0 0 1 4 4v11a3 3 0 0 0-3-3H2Z'/%3E%3Cpath d='M22 5h-6a4 4 0 0 0-4 4v11a3 3 0 0 1 3-3h7Z'/%3E%3C/svg%3E");}
#g4d-header .simple-dropdown-item[data-icon="faq"]::before{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232b6b0e' stroke-width='1.9' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M9.2 9.3a3 3 0 0 1 5.7 1c0 2-3 2.6-3 4.2'/%3E%3Cpath d='M12 17.6h.01'/%3E%3C/svg%3E");}
#g4d-header .simple-dropdown-item[data-icon="about"]::before{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232b6b0e' stroke-width='1.9' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16'/%3E%3Cpath d='M16 9h3a1 1 0 0 1 1 1v11M2 21h20M8 7h4M8 11h4M8 15h4'/%3E%3C/svg%3E");}
#g4d-header .simple-dropdown-item[data-icon="link"]::before{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232b6b0e' stroke-width='1.9' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M7 17 17 7M8.5 7H17v8.5'/%3E%3C/svg%3E");}

/* The old design's thick strip along the bottom of every card. */
#g4d-header .dropdown-green-bar{display:none;}

/* --- mega menu (B2B Leads on inner pages) ------------------------------ */
/* Measured from the whole bar rather than its own item, so it can sit centred
   under the header at any width instead of being nudged off one edge. */
#g4d-header .mega-dropdown-wrapper{position:static;}
#g4d-header .mega-menu-dropdown{
  top:calc(100% + 7px);
  left:50% !important;
  width:min(1080px,calc(100% - 32px));
  max-width:none;
  max-height:calc(100vh - 96px);
  overflow-y:auto !important;
  overscroll-behavior:contain;
  padding:0 !important;
  border-radius:16px;
  transform:translate(-50%,-6px) scale(.99) !important;
  transform-origin:top center;
}
#g4d-header .mega-menu-dropdown::before{top:-21px;height:21px;}
#g4d-header .nav-dropdown-wrapper:hover > .mega-menu-dropdown,
#g4d-header .nav-dropdown-wrapper:focus-within > .mega-menu-dropdown,
#g4d-header .nav-dropdown-wrapper.active > .mega-menu-dropdown{
  transform:translate(-50%,0) !important;
}
#g4d-header .mega-menu-body{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:28px 36px;
  padding:28px 32px 30px;
  background:transparent;
}
#g4d-header .mega-menu-col{display:flex;flex-direction:column;min-width:0;}
#g4d-header .mega-col-title{
  margin:0 0 10px;
  padding:0 0 10px;
  border-bottom:1px solid var(--g4n-line);
  font-family:'Nunito',sans-serif;
  font-size:14px;
  font-weight:800;
  line-height:1.3;
  letter-spacing:0;
  color:var(--g4n-ink);
}
#g4d-header .mega-col-list{
  display:flex;
  flex-direction:column;
  gap:2px;
  margin:0;
  padding:0;
  list-style:none;
}
#g4d-header .mega-col-list li{margin:0;padding:0;list-style:none;}
#g4d-header .mega-menu-item{
  display:block;
  margin:0 -8px;
  padding:6px 8px;
  border-radius:7px;
  font-family:'Nunito',sans-serif;
  font-size:14px;
  font-weight:600;
  line-height:1.4;
  color:var(--g4n-slate);
  text-decoration:none;
  transform:none !important;
  transition:background-color .15s ease,color .15s ease;
}
#g4d-header .mega-menu-item:hover,
#g4d-header .mega-menu-item:focus-visible,
#g4d-header .mega-menu-item[aria-current="page"]{
  background:var(--g4n-tint);
  color:var(--g4n-deep) !important;
}

/* --- log in / sign up -------------------------------------------------- */
#g4d-header .g4d-actions{
  grid-column:3;
  justify-self:end;
  display:flex;
  align-items:center;
  gap:6px;
  margin-left:0;
}
#g4d-header .nav-login-btn,
#g4d-header .nav-signup-btn{
  display:inline-flex;
  align-items:center;
  border-radius:999px;
  font-family:'Nunito',sans-serif;
  font-size:15px;
  line-height:1.25;
  white-space:nowrap;
  text-decoration:none;
}
#g4d-header .nav-login-btn{
  padding:10px 16px;
  font-weight:700;
  color:var(--g4n-ink);
  transform:none !important;
  transition:background-color .2s ease,color .2s ease;
}
#g4d-header .nav-login-btn:hover{background:#f1f4f7;color:var(--g4n-ink);}
#g4d-header .nav-signup-btn{
  padding:10px 20px;
  font-weight:800;
  color:var(--g4n-ink) !important;
  background:var(--g4n-green);
  box-shadow:inset 0 1px 0 rgba(255,255,255,.45),0 6px 16px -8px rgba(63,150,25,.7);
  transition:background-color .2s ease,box-shadow .2s ease,transform .2s var(--g4n-ease);
}
#g4d-header .nav-signup-btn:hover{
  background:var(--g4n-green-hover) !important;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.45),0 10px 22px -8px rgba(63,150,25,.75) !important;
  transform:translateY(-1px);
}
#g4d-header .nav-signup-btn:active{transform:translateY(0);}

/* --- keyboard focus ---------------------------------------------------- */
#g4d-header a:focus{outline:none;}
#g4d-header a:focus-visible{outline:2px solid var(--g4n-deep);outline-offset:2px;}
#g4d-header .simple-dropdown-item:focus-visible,
#g4d-header .mega-menu-item:focus-visible{outline-offset:-2px;}

/* Anchor links land below the header instead of under it. */
html{scroll-padding-top:80px;}

/* --- smaller screens --------------------------------------------------- */
/* Below this width the five items no longer fit between the logo and the
   buttons, so the menu moves into the drawer that mobile-nav.js builds.
   mobile-nav.css switches at 992px, so the burger and drawer are brought
   forward to match here. */
@media (max-width:1100px){
  #g4d-header .g4d-bar{padding:11px 24px !important;}
  #g4d-header .g4d-nav,
  #g4d-header .g4d-glide{display:none !important;}
  #g4d-header .g4m-burger{display:inline-flex;}
  #g4d-header .g4m-hide-sm{display:none !important;}
  html body .g4m-drawer{display:flex;}
  html body .g4m-backdrop{display:block;}
}
@media (max-width:768px){
  #g4d-header .g4d-bar{padding:10px 16px !important;gap:12px;}
}
/* On a 390px phone the logo, sign-up button and burger came to 403px. */
@media (max-width:480px){
  #g4d-header .g4d-logo img{height:28px;}
  #g4d-header .nav-signup-btn{padding:9px 14px;font-size:14px;}
  #g4d-header .g4m-burger{margin-left:2px;}
}
/* Narrower than this there is no room for both, and the drawer repeats the
   sign-up button at its foot. */
@media (max-width:340px){
  #g4d-header .nav-signup-btn{display:none;}
}

@media (prefers-reduced-motion:reduce){
  #g4d-header,
  #g4d-header *,
  #g4d-header *::before{transition-duration:.01ms !important;transition-delay:0s !important;}
  #g4d-header .nav-signup-btn:hover{transform:none;}
}
</style>
<script>
(function () {
  'use strict';

  function ready(fn) {
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', fn) : fn();
  }

  ready(function () {
    var header = document.getElementById('g4d-header');
    if (!header) { return; }

    // Frosted glass and a shadow once the page has moved under the header.
    var onScroll = function () { header.classList.toggle('scrolled', window.scrollY > 8); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    var bar = header.querySelector('.g4d-bar');
    var menu = header.querySelector('.g4d-nav');
    if (!bar || !menu) { return; }

    // Top-level items only: plain links, and the trigger of each dropdown.
    var items = [].slice.call(menu.children).map(function (node) {
      return node.matches('a') ? node : node.querySelector(':scope > .nav-link-item');
    }).filter(Boolean);
    if (!items.length) { return; }

    /* --- which item is the current page ---------------------------------- */
    // Links mix absolute production URLs with relative ones, so compare paths,
    // and only for links that stay on this site (log in goes to the app).
    var site = location.hostname.replace(/^www\./, '');
    function pathOf(href) {
      if (!href || href.charAt(0) === '#') { return null; }
      try {
        var u = new URL(href, location.href);
        var host = u.hostname.replace(/^www\./, '');
        if (host !== site && host !== 'go4database.com') { return null; }
        return u.pathname.replace(/\/+$/, '') || '/';
      } catch (e) { return null; }
    }
    var here = location.pathname.replace(/\/+$/, '') || '/';
    var current = null;

    items.forEach(function (trigger) {
      var own = pathOf(trigger.getAttribute('href'));
      var wrapper = trigger.parentElement !== menu ? trigger.parentElement : null;
      var hit = own === here && own !== '/';

      if (wrapper) {
        [].slice.call(wrapper.querySelectorAll('.nav-dropdown-menu a')).forEach(function (a) {
          if (pathOf(a.getAttribute('href')) === here) {
            a.setAttribute('aria-current', 'page');
            hit = true;
          }
        });
        // A section page such as /b2b/... belongs to the item that links to /b2b.
        if (!hit && own && own !== '/' && here.indexOf(own + '/') === 0) { hit = true; }
      }

      if (hit && !current) {
        current = trigger;
        trigger.classList.add('is-current');
        if (own === here) { trigger.setAttribute('aria-current', 'page'); }
      }
    });

    /* --- icons and descriptions for dropdown items ------------------------ */
    // The menu builder has no description field, so the inner pages' admin
    // menu and the homepage's hardcoded one are matched here, by destination
    // first and then by label (some slugs are admin-configurable and differ
    // between environments). An item added later that matches neither still
    // gets a tile, just with a generic icon and no description.
    var details = {
      '/case-study':      ['stories',   'How customers use our data'],
      '/our-testimonial': ['quote',     'What customers say about us'],
      '/email-verifier':  ['verify',    'Check an address is real before you send'],
      '/email-finder':    ['finder',    'Find a work email from a name and company'],
      '/affiliate':       ['affiliate', 'Earn monthly commission on referrals'],
      '/blog':            ['blog',      'Guides on B2B data and outreach'],
      '/faq':             ['faq',       'Account, data, orders and billing'],
      '/about':           ['about',     'The team behind Go4Database']
    };
    var byLabel = {
      'success stories': '/case-study', 'our testimonial': '/our-testimonial',
      'testimonials': '/our-testimonial', 'email verifier': '/email-verifier',
      'email finder': '/email-finder', 'affiliate program': '/affiliate',
      'blog': '/blog', 'faq': '/faq', 'faqs': '/faq', 'about': '/about', 'about us': '/about'
    };

    [].slice.call(header.querySelectorAll('.nav-dropdown-menu:not(.mega-menu-dropdown)')).forEach(function (card) {
      var links = [].slice.call(card.querySelectorAll(':scope > .simple-dropdown-item'));
      if (!links.length) { return; }
      links.forEach(function (a) {
        var label = (a.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
        var info = details[pathOf(a.getAttribute('href'))] || details[byLabel[label]];
        a.setAttribute('data-icon', info ? info[0] : 'link');
        if (info) { a.setAttribute('data-desc', info[1]); }
      });
      card.classList.add('is-rich');
    });

    /* --- gliding highlight ------------------------------------------------ */
    var glide = document.createElement('span');
    glide.className = 'g4d-glide';
    glide.setAttribute('aria-hidden', 'true');
    bar.insertBefore(glide, bar.firstChild);
    header.classList.add('has-glide');

    var shownOn = null;

    function place(el) {
      var b = bar.getBoundingClientRect();
      var r = el.getBoundingClientRect();
      glide.style.width = r.width + 'px';
      glide.style.height = r.height + 'px';
      glide.style.transform = 'translate(' + (r.left - b.left) + 'px,' + (r.top - b.top) + 'px)';
    }

    function moveTo(el) {
      if (!el) { return hide(); }
      if (!shownOn) {
        // Appearing from nothing: jump into place, then fade in, rather than
        // flying in from the left edge.
        glide.classList.add('is-jump');
        place(el);
        void glide.offsetWidth;
        glide.classList.remove('is-jump');
      } else {
        place(el);
      }
      glide.classList.add('is-on');
      shownOn = el;
    }

    function hide() {
      glide.classList.remove('is-on');
      shownOn = null;
    }

    function rest() { moveTo(current); }

    items.forEach(function (trigger) {
      var zone = trigger.parentElement !== menu ? trigger.parentElement : trigger;
      zone.addEventListener('pointerenter', function () { moveTo(trigger); });
      zone.addEventListener('focusin', function () { moveTo(trigger); });
    });
    menu.addEventListener('pointerleave', rest);
    menu.addEventListener('focusout', function (e) {
      if (!menu.contains(e.relatedTarget)) { rest(); }
    });

    var reflow = function () { if (shownOn) { place(shownOn); } };
    window.addEventListener('resize', reflow);
    // The webfont can widen the labels after first layout.
    if (document.fonts && document.fonts.ready) { document.fonts.ready.then(function () { rest(); }); }
    rest();
  });
})();
</script>
