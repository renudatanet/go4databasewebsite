{{-- Styles and script for the career pages (board, role, application).
     They build on the blog's tokens, header, search box and chips
     (blog/partials/style, included first); everything here is scoped to
     .g4c. Inline, like the blog's, so a view deploys on its own. --}}
<style>
/* The theme's overflow-x:hidden on body would make it a scroll container
   and stop the role page's summary card from sticking (see CLAUDE.md). */
body:has(.g4c){overflow-x:clip;overflow-y:visible;}

.g4c{--g4c-radius:18px;--g4c-shadow:0 1px 2px rgba(11,19,42,.04),0 12px 32px -20px rgba(11,19,42,.28);}
.g4c svg{fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.g4c-badge{display:inline-flex;align-items:center;gap:8px;margin:0 0 18px;padding:6px 14px;border:1px solid #cdeccf;border-radius:999px;background:rgba(255,255,255,.85);font-size:12.5px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--g4b-deep);box-shadow:0 6px 18px -12px rgba(31,122,46,.5);}
.g4c-badge .g4b-new-dot{background:var(--g4b-green);}
.g4c-main{padding-top:48px;padding-bottom:64px;}
.g4c-section + .g4c-section,.g4c-layout + .g4c-section{margin-top:64px;}
.g4c-head{display:flex;flex-wrap:wrap;align-items:baseline;justify-content:space-between;gap:8px 20px;margin:0 0 20px;}
.g4c-head h2{margin:0;font-size:clamp(24px,2.4vw,30px);font-weight:800;letter-spacing:-.02em;color:var(--g4b-ink);}
.g4c-count{font-size:14px;font-weight:700;color:var(--g4b-slate);}

/* --- why join ---------------------------------------------------------- */
.g4c-perks{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin:0;padding:0;list-style:none;}
.g4c-perk{padding:22px 22px 24px;border:1px solid var(--g4b-line);border-radius:var(--g4c-radius);background:#fff;}
.g4c-perk-icon{display:grid;place-items:center;width:42px;height:42px;margin-bottom:16px;border-radius:12px;background:var(--g4b-tint);color:var(--g4b-deep);}
.g4c-perk-icon svg{width:21px;height:21px;}
.g4c-perk h3{margin:0 0 6px;font-size:17px;font-weight:800;color:var(--g4b-ink);}
.g4c-perk p{margin:0;font-size:14.5px;line-height:1.55;color:var(--g4b-slate);}

/* --- a role in a list ---------------------------------------------------- */
.g4c-roles{display:grid;gap:12px;margin:0;padding:0;list-style:none;}
.g4c-role{
  position:relative;display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:16px 28px;
  padding:22px 26px;border:1px solid var(--g4b-line);border-radius:var(--g4c-radius);background:#fff;
  transition:border-color .2s var(--g4b-ease),box-shadow .2s var(--g4b-ease),transform .2s var(--g4b-ease);
}
.g4c-role:hover{border-color:#c6e9c9;box-shadow:var(--g4c-shadow);transform:translateY(-1px);}
.g4c-role:focus-within{border-color:var(--g4b-green);}
.g4c-role-team{display:block;margin:0 0 6px;font-size:12px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:var(--g4b-deep);}
.g4c-role-title{margin:0;font-size:20px;font-weight:800;line-height:1.3;letter-spacing:-.01em;}
/* The title's link covers the whole card. */
.g4c-role-title a{color:var(--g4b-ink);}
.g4c-role-title a::after{content:"";position:absolute;inset:0;border-radius:inherit;}
.g4c-role-title a:focus-visible{outline:none;}
.g4c-role:hover .g4c-role-title a{color:var(--g4b-deep);}
.g4c-facts{display:flex;flex-wrap:wrap;gap:6px 18px;margin:10px 0 0;padding:0;list-style:none;font-size:14.5px;font-weight:600;color:var(--g4b-slate);}
.g4c-facts li{display:inline-flex;align-items:center;gap:7px;min-width:0;}
.g4c-facts svg{flex:none;width:16px;height:16px;color:#8aa093;}
.g4c-role-side{display:flex;flex-direction:column;align-items:flex-end;gap:10px;text-align:right;}
.g4c-date{font-size:13.5px;font-weight:700;color:var(--g4b-slate);white-space:nowrap;}
.g4c-date.is-soon{color:#b45309;}
.g4c-go{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:999px;background:var(--g4b-mint);border:1px solid #cdeccf;font-size:14px;font-weight:800;color:var(--g4b-deep);white-space:nowrap;transition:background .2s,border-color .2s;}
.g4c-go svg{width:16px;height:16px;transition:transform .2s var(--g4b-ease);}
.g4c-role:hover .g4c-go{background:var(--g4b-green);border-color:var(--g4b-green);color:var(--g4b-ink);}
.g4c-role:hover .g4c-go svg{transform:translateX(3px);}
.g4c-more{display:flex;justify-content:center;margin-top:28px;}
.g4c-more-btn{display:inline-flex;align-items:center;gap:10px;padding:13px 22px;border:1.5px solid var(--g4b-line);border-radius:999px;background:#fff;font-size:15px;font-weight:800;color:var(--g4b-ink);}
.g4c-more-btn:hover{border-color:var(--g4b-green);background:var(--g4b-mint);color:var(--g4b-ink);}
.g4c-more-btn.is-loading{cursor:wait;opacity:.75;pointer-events:none;}
.g4c-empty{padding:44px 24px;border:1.5px dashed #cfe9d2;border-radius:var(--g4c-radius);background:var(--g4b-mint);text-align:center;}
.g4c-empty h3{margin:0 0 8px;font-size:22px;font-weight:800;color:var(--g4b-ink);}
.g4c-empty p{max-width:52ch;margin:0 auto 18px;color:var(--g4b-slate);}

/* --- call to action ------------------------------------------------------ */
.g4c-cta{position:relative;overflow:hidden;display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:20px 40px;padding:40px 44px;border-radius:24px;background:linear-gradient(135deg,#0f3d1c 0%,#1f7a2e 60%,#3fa62a 100%);color:#e9f8ec;}
.g4c-cta::before{content:"";position:absolute;inset:0 0 0 50%;background-image:radial-gradient(rgba(255,255,255,.22) 1.1px,transparent 1.3px);background-size:18px 18px;-webkit-mask-image:linear-gradient(90deg,transparent,#000);mask-image:linear-gradient(90deg,transparent,#000);pointer-events:none;}
.g4c-cta > *{position:relative;}
.g4c-cta h2{margin:0 0 8px;font-size:clamp(24px,2.6vw,32px);font-weight:800;letter-spacing:-.02em;color:#fff;}
.g4c-cta p{max-width:60ch;margin:0;font-size:16.5px;line-height:1.6;color:#d4efd9;}
.g4c-cta .g4b-btn{white-space:nowrap;}

/* --- role page ----------------------------------------------------------- */
.g4c-hero{padding:30px 0 34px;}
.g4c-hero .g4b-hero-copy{max-width:860px;margin-top:22px;}
.g4c-hero .g4b-title{font-size:clamp(34px,4.4vw,54px);line-height:1.08;}
.g4c-tags{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 16px;}
.g4c-tag svg{width:15px;height:15px;}
.g4c-tag{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border:1px solid #cdeccf;border-radius:999px;background:#fff;font-size:13px;font-weight:800;color:var(--g4b-deep);}
a.g4c-tag:hover{background:var(--g4b-tint);color:var(--g4b-deep);}
.g4c-tag--muted{border-color:var(--g4b-line);color:var(--g4b-slate);}
.g4c-tag--closed{border-color:#f5d0c5;background:#fff5f2;color:#b42318;}
.g4c-hero .g4c-facts{margin-top:18px;font-size:16px;gap:8px 24px;}
.g4c-hero .g4c-facts svg{width:18px;height:18px;color:var(--g4b-deep);}
.g4c-actions{display:flex;flex-wrap:wrap;align-items:center;gap:12px;margin-top:26px;}
.g4c-share{display:inline-flex;align-items:center;gap:8px;padding:12px 18px;border:1.5px solid #c6e9c9;border-radius:999px;background:#fff;font:inherit;font-size:15px;font-weight:800;color:var(--g4b-ink);cursor:pointer;}
.g4c-share:hover{background:var(--g4b-mint);border-color:var(--g4b-green);}
.g4c-share svg{width:17px;height:17px;}
.g4c-closed{display:flex;align-items:center;gap:12px;margin-top:26px;padding:14px 18px;border:1px solid #f5d0c5;border-radius:14px;background:#fff5f2;font-weight:700;color:#8a1c12;}
.g4c-closed svg{flex:none;width:20px;height:20px;}

.g4c-layout{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:48px;align-items:start;}
.g4c-layout > *{min-width:0;}
.g4c-aside{position:sticky;top:96px;align-self:start;display:grid;gap:16px;}
.g4c-card{padding:24px;border:1px solid var(--g4b-line);border-radius:var(--g4c-radius);background:#fff;box-shadow:var(--g4c-shadow);}
.g4c-card h2,.g4c-card h3{margin:0 0 16px;font-size:18px;font-weight:800;color:var(--g4b-ink);}
.g4c-overview{display:grid;gap:14px;margin:0 0 22px;}
.g4c-overview div{display:grid;grid-template-columns:36px minmax(0,1fr);gap:0 12px;align-items:center;}
.g4c-overview dt{grid-row:span 2;display:grid;place-items:center;width:36px;height:36px;border-radius:10px;background:var(--g4b-tint);color:var(--g4b-deep);}
.g4c-overview dt svg{width:18px;height:18px;}
.g4c-overview dt span{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);}
.g4c-overview .g4c-k{font-size:12.5px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#7b8794;}
.g4c-overview dd{margin:0;font-size:15px;font-weight:700;line-height:1.4;color:var(--g4b-ink);overflow-wrap:anywhere;}
.g4c-overview dd a{color:var(--g4b-deep);}
.g4c-card .g4b-btn{width:100%;justify-content:center;}
.g4c-note{margin:12px 0 0;font-size:13.5px;text-align:center;color:var(--g4b-slate);}

.g4c-prose{font-size:17px;line-height:1.75;color:var(--g4b-text);-webkit-hyphens:manual;hyphens:manual;overflow-wrap:break-word;}
.g4c-prose *{-webkit-hyphens:manual;hyphens:manual;}
.g4c-prose section + section{margin-top:40px;padding-top:36px;border-top:1px solid var(--g4b-line);}
.g4c-prose > section > h2{margin:0 0 14px;font-size:26px;font-weight:800;letter-spacing:-.02em;color:var(--g4b-ink);}
.g4c-prose h2,.g4c-prose h3,.g4c-prose h4,.g4c-prose h5{margin:28px 0 10px;font-size:19px;font-weight:800;line-height:1.35;color:var(--g4b-ink);}
.g4c-prose p{margin:0 0 14px;}
.g4c-prose strong,.g4c-prose b{font-weight:800;color:var(--g4b-ink);}
.g4c-prose a{color:var(--g4b-deep);text-decoration:underline;text-underline-offset:3px;}
.g4c-prose ul,.g4c-prose ol{margin:0 0 16px;padding:0;list-style:none;}
.g4c-prose li{position:relative;margin:0 0 10px;padding-left:30px;}
.g4c-prose ul > li::before{content:"";position:absolute;left:0;top:.45em;width:18px;height:18px;border-radius:50%;background:var(--g4b-tint) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231f7a2e' stroke-width='3.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9 17l-5-5'/%3E%3C/svg%3E") center/11px no-repeat;}
.g4c-prose ol{counter-reset:g4c;}
.g4c .g4c-prose ol > li::before{counter-increment:g4c;content:counter(g4c);position:absolute;left:0;top:.2em;display:grid;place-items:center;width:20px;height:20px;border-radius:50%;background:var(--g4b-tint);font-size:12px;font-weight:800;color:var(--g4b-deep);}
.g4c-prose table{width:100%;margin:0 0 16px;border-collapse:collapse;font-size:15px;}
.g4c-prose td,.g4c-prose th{padding:8px 10px;border:1px solid var(--g4b-line);text-align:left;}
.g4c-prose img{max-width:100%;height:auto;border-radius:12px;}

/* Phones: the apply button stays in reach at the foot of the screen. */
.g4c-applybar{display:none;}

/* --- application form ---------------------------------------------------- */
.g4c-form .form-group{margin:0 0 18px;}
.g4c .g4c-form .g4c-label{display:block;margin:0 0 7px;font-size:14.5px;font-weight:800;line-height:1.4;color:var(--g4b-ink);}
.g4c-label em{font-style:normal;color:#c2410c;}
.g4c-input{display:block;width:100%;height:auto;padding:13px 15px;border:1.5px solid #dbe3ea;border-radius:12px;background:#fff;font:inherit;font-size:16px;color:var(--g4b-ink);transition:border-color .15s,box-shadow .15s;}
.g4c-input:focus{outline:none;border-color:var(--g4b-green);box-shadow:0 0 0 4px rgba(111,217,67,.2);}
textarea.g4c-input{min-height:140px;resize:vertical;}
.g4c-check{display:flex;gap:10px;align-items:flex-start;font-weight:600;}
.g4c-check input{width:18px;height:18px;margin-top:3px;accent-color:var(--g4b-deep);}
.g4c-drop{position:relative;display:flex;align-items:center;gap:14px;padding:18px;border:1.5px dashed #b9dcbc;border-radius:14px;background:var(--g4b-mint);cursor:pointer;transition:border-color .15s,background .15s;}
.g4c-drop:hover,.g4c-drop.is-over{border-color:var(--g4b-green);background:#e6f9e9;}
.g4c-drop input{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer;}
.g4c-drop input:focus-visible + .g4c-drop-icon{outline:2px solid var(--g4b-deep);outline-offset:2px;}
.g4c-drop-icon{display:grid;place-items:center;flex:none;width:44px;height:44px;border-radius:12px;background:#fff;color:var(--g4b-deep);}
.g4c-drop-icon svg{width:22px;height:22px;}
.g4c-drop-text{display:grid;gap:2px;min-width:0;}
.g4c-drop-text strong{font-size:15px;color:var(--g4b-ink);overflow-wrap:anywhere;}
.g4c-drop-text span{font-size:13.5px;color:var(--g4b-slate);}
.g4c-form button.g4b-btn{border:0;cursor:pointer;}
.g4c-form button.g4b-btn:disabled{opacity:.7;cursor:wait;}
.g4c-form .g4b-btn{width:100%;justify-content:center;padding-top:15px;padding-bottom:15px;font-size:16px;}
.g4c-alert{margin:0 0 20px;padding:14px 16px;border-radius:12px;font-weight:600;}
.g4c-alert ul{margin:0;padding-left:18px;}
.g4c-alert--error{border:1px solid #f5d0c5;background:#fff5f2;color:#8a1c12;}
.g4c-alert--ok{display:flex;gap:12px;align-items:flex-start;border:1px solid #bfe3c4;background:var(--g4b-mint);color:#14532d;}
.g4c-alert--ok svg{flex:none;width:22px;height:22px;margin-top:1px;}
.g4c-steps{display:grid;gap:14px;margin:0;padding:0;list-style:none;counter-reset:g4cs;}
.g4c-steps li{position:relative;padding-left:40px;font-size:15px;font-weight:600;line-height:1.5;color:var(--g4b-text);}
.g4c .g4c-steps li::before{counter-increment:g4cs;content:counter(g4cs);position:absolute;left:0;top:-2px;display:grid;place-items:center;width:28px;height:28px;border-radius:50%;background:var(--g4b-tint);font-size:13px;font-weight:800;color:var(--g4b-deep);}
.g4c-mini-title{margin:0 0 4px;font-size:19px;font-weight:800;line-height:1.3;}
.g4c-mini-title a{color:var(--g4b-ink);}
.g4c-mini-title a:hover{color:var(--g4b-deep);}
.g4c-card .g4c-facts{margin:10px 0 0;flex-direction:column;gap:8px;}
.g4c-back{display:inline-flex;align-items:center;gap:6px;margin-top:16px;font-size:14px;font-weight:800;color:var(--g4b-deep);}
.g4c-back svg{width:16px;height:16px;}
/* The payment gateway picker, for roles with an application fee. */
.g4c-form .payment-gateway-wrapper ul{display:flex;flex-wrap:wrap;gap:10px;margin:0 0 18px;padding:0;list-style:none;}
.g4c-form .payment-gateway-wrapper li{padding:8px;border:1.5px solid var(--g4b-line);border-radius:12px;cursor:pointer;}
.g4c-form .payment-gateway-wrapper li.selected{border-color:var(--g4b-green);box-shadow:0 0 0 3px rgba(111,217,67,.25);}

@media (max-width:1100px){
  .g4c-perks{grid-template-columns:repeat(2,minmax(0,1fr));}
  .g4c-layout{grid-template-columns:minmax(0,1fr) 320px;gap:32px;}
}
@media (max-width:900px){
  .g4c-layout{grid-template-columns:minmax(0,1fr);}
  .g4c-aside{position:static;}
  .g4c-role{grid-template-columns:minmax(0,1fr);padding:20px;}
  .g4c-role-side{flex-direction:row;align-items:center;justify-content:space-between;text-align:left;}
  .g4c-cta{grid-template-columns:minmax(0,1fr);padding:32px 26px;}
  .g4c-role-page .g4c-applybar{position:fixed;left:0;right:0;bottom:0;z-index:50;display:flex;align-items:center;gap:12px;padding:12px 16px calc(12px + env(safe-area-inset-bottom));border-top:1px solid var(--g4b-line);background:rgba(255,255,255,.96);box-shadow:0 -10px 30px -18px rgba(11,19,42,.35);transform:translateY(110%);transition:transform .25s var(--g4b-ease);}
  .g4c-role-page .g4c-applybar.is-shown{transform:none;}
  .g4c-applybar-title{flex:1;min-width:0;font-size:14.5px;font-weight:800;line-height:1.3;color:var(--g4b-ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
  .g4c-applybar .g4b-btn{flex:none;}
  /* The chat bubble rises above the apply bar while it shows. */
  body.g4c-bar-up .g4chat{bottom:88px;transition:bottom .25s var(--g4b-ease);}
}
@media (max-width:640px){
  .g4c-main{padding-top:32px;padding-bottom:48px;}
  .g4c-section + .g4c-section,.g4c-layout + .g4c-section{margin-top:44px;}
  .g4c-perks{grid-template-columns:minmax(0,1fr);gap:12px;}
  .g4c-perk{display:grid;grid-template-columns:42px minmax(0,1fr);gap:0 14px;padding:18px;}
  .g4c-perk-icon{grid-row:span 2;margin:0;}
  .g4c-role-title{font-size:18px;}
  .g4c-facts{font-size:14px;gap:6px 14px;}
  .g4c-go{padding:9px 14px;}
  .g4c-hero{padding:20px 0 26px;}
  .g4c-hero .g4b-title{font-size:30px;}
  .g4c-hero .g4c-facts{font-size:15px;}
  .g4c-actions .g4b-btn{flex:1;justify-content:center;}
  .g4c-prose{font-size:16px;}
  .g4c-prose > section > h2{font-size:22px;}
  .g4c-card{padding:20px;}
}
@media (prefers-reduced-motion:reduce){
  .g4c-role,.g4c-role:hover{transform:none;transition:none;}
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // "Show more roles": a link to the next page, turned into adding that
  // page's roles here. If anything fails it simply follows the link.
  var more = document.querySelector('.g4c-more-btn');
  if (more) {
    more.addEventListener('click', function (e) {
      var next = more.getAttribute('href');
      if (!next || e.metaKey || e.ctrlKey || e.shiftKey) { return; }
      e.preventDefault();
      more.classList.add('is-loading');
      fetch(next, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (res) { return res.ok ? res.text() : Promise.reject(); })
        .then(function (html) {
          var doc = new DOMParser().parseFromString(html, 'text/html');
          var list = document.querySelector('.g4c-roles');
          var added = doc.querySelectorAll('.g4c-roles > li');
          if (!list || !added.length) { throw new Error('no roles'); }
          added.forEach(function (li) { list.appendChild(document.importNode(li, true)); });
          var nextButton = doc.querySelector('.g4c-more-btn');
          if (nextButton) {
            more.setAttribute('href', nextButton.getAttribute('href'));
            more.innerHTML = nextButton.innerHTML;
            more.classList.remove('is-loading');
          } else {
            more.parentNode.remove();
          }
          var first = list.querySelector('li:nth-child(' + (list.children.length - added.length + 1) + ') .g4c-role-title a');
          if (first) { first.focus({ preventScroll: true }); }
        })
        .catch(function () { window.location.href = next; });
    });
  }

  // Share: the phone's own share sheet where there is one, else copy the link.
  document.querySelectorAll('.g4c-share').forEach(function (button) {
    button.addEventListener('click', function () {
      var data = { title: button.getAttribute('data-title') || document.title, url: button.getAttribute('data-url') || location.href };
      var label = button.querySelector('span');
      var done = function (text) {
        if (!label) { return; }
        var before = label.textContent;
        label.textContent = text;
        setTimeout(function () { label.textContent = before; }, 2000);
      };
      if (navigator.share) { navigator.share(data).catch(function () {}); return; }
      if (navigator.clipboard) { navigator.clipboard.writeText(data.url).then(function () { done('Link copied'); }, function () { done('Copy failed'); }); }
      if (typeof gtag === 'function') { gtag('event', 'share', { method: 'copy_link', content_type: 'job', item_id: data.url }); }
    });
  });

  // Phones: the apply bar appears once the page's own apply button has
  // scrolled away, and goes when the summary card's comes into view.
  var bar = document.querySelector('.g4c-applybar');
  var anchors = document.querySelectorAll('[data-apply-anchor]');
  if (bar && anchors.length && 'IntersectionObserver' in window) {
    var visible = new Set();
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) { if (entry.isIntersecting) { visible.add(entry.target); } else { visible.delete(entry.target); } });
      var past = anchors[0].getBoundingClientRect().bottom < 0;
      var shown = past && visible.size === 0;
      bar.classList.toggle('is-shown', shown);
      document.body.classList.toggle('g4c-bar-up', shown);
    });
    anchors.forEach(function (a) { observer.observe(a); });
  }

  // The CV box shows the chosen file's name, and takes a file dropped on it.
  document.querySelectorAll('.g4c-drop').forEach(function (drop) {
    var input = drop.querySelector('input[type=file]');
    var name = drop.querySelector('.g4c-drop-text strong');
    var original = name ? name.textContent : '';
    var show = function () { if (name) { name.textContent = input.files && input.files.length ? input.files[0].name : original; } };
    input.addEventListener('change', show);
    ['dragenter', 'dragover'].forEach(function (t) { drop.addEventListener(t, function () { drop.classList.add('is-over'); }); });
    ['dragleave', 'drop'].forEach(function (t) { drop.addEventListener(t, function () { drop.classList.remove('is-over'); }); });
  });

  window.addEventListener('pageshow', function (e) {
    if (!e.persisted) { return; }
    document.querySelectorAll('.g4c-form button[type=submit]:disabled').forEach(function (b) { b.disabled = false; b.textContent = b.getAttribute('data-label') || b.textContent; });
  });

  // Applying: the button says it is working and can't be pressed twice.
  var form = document.querySelector('.g4c-form');
  if (form) {
    form.addEventListener('submit', function () {
      var submit = form.querySelector('button[type=submit]');
      if (submit) { submit.setAttribute('data-label', submit.textContent); submit.disabled = true; submit.textContent = submit.getAttribute('data-busy') || 'Sending…'; }
      if (typeof gtag === 'function') { gtag('event', 'job_apply_submit', { job: form.getAttribute('data-job') }); }
    });
  }
});
</script>
