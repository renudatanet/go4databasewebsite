{{-- Styles and script for a blog post (partials/post), on top of the shared
     blog styles in partials/style. Inline for the same deploy reason. --}}
<style>
/* --- header ------------------------------------------------------------ */
.g4p-hero{padding-bottom:34px;}
.g4p-title{max-width:980px;margin:20px 0 0;font-size:clamp(32px,4.2vw,50px);font-weight:800;line-height:1.1;letter-spacing:-.025em;color:var(--g4b-ink);}
.g4p-lead{max-width:70ch;margin:16px 0 0;font-size:18.5px;line-height:1.6;color:var(--g4b-slate);}
.g4p-meta{display:flex;flex-wrap:wrap;align-items:center;gap:12px 28px;margin-top:26px;font-size:14.5px;font-weight:600;color:var(--g4b-slate);}
.g4p-byline{display:inline-flex;align-items:center;gap:12px;}
.g4p-byline-avatar{width:44px;height:44px;border-radius:50%;object-fit:cover;flex:none;}
.g4p-byline-avatar.g4b-avatar--initial{font-size:16px;}
.g4p-byline-text{display:flex;flex-direction:column;line-height:1.3;}
.g4p-byline-text a,.g4p-byline-text > span:first-child{font-weight:800;color:var(--g4b-ink);}
.g4p-byline-text a:hover{color:var(--g4b-deep);}
.g4p-byline-role{font-size:13px;font-weight:600;color:var(--g4b-slate);}

/* --- article and sidebar ----------------------------------------------- */
.g4p-layout{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:56px;align-items:start;padding-top:40px;padding-bottom:64px;}
.g4p-article{min-width:0;max-width:780px;}
.g4p-figure{position:relative;margin:0 0 36px;border-radius:18px;overflow:hidden;border:1px solid var(--g4b-line);background:linear-gradient(135deg,var(--g4b-mint),var(--g4b-mint-2));}
.g4p-figure img{display:block;width:100%;height:auto;}
.g4p-play{position:absolute;inset:0;margin:auto;width:76px;height:76px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:rgba(255,255,255,.92);box-shadow:0 12px 30px -10px rgba(11,19,42,.5);}
.g4p-play svg{width:30px;height:30px;margin-left:4px;fill:var(--g4b-ink);}

/* The article body is the post's own HTML from the editor or the blog
   agent. Older posts were pasted from documents and carry inline fonts,
   sizes and colours on nearly every span; they are evened out here so every
   post reads the same. Boxes, highlights and alignment are left alone. */
.g4p-body{font-size:17.5px;line-height:1.75;color:var(--g4b-text);overflow-wrap:break-word;}
.g4p-body *{font-family:inherit !important;}
.g4p-body :is(p,li,td,th,span,strong,b,em,i,u,a,small,label){font-size:inherit !important;line-height:inherit !important;}
.g4p-body :is(p,li,td,span){color:inherit !important;}
.g4p-body > :first-child{margin-top:0 !important;}
.g4p-body p{margin:0 0 22px;}
.g4p-body :is(h2,h3,h4,h5){color:var(--g4b-ink) !important;font-weight:800 !important;line-height:1.25 !important;letter-spacing:-.015em;scroll-margin-top:96px;}
.g4p-body :is(h2,h3,h4,h5) *{color:inherit !important;font-size:inherit !important;}
.g4p-body h2{margin:52px 0 18px;font-size:clamp(24px,2.4vw,30px) !important;}
.g4p-body h3{margin:36px 0 14px;font-size:clamp(20px,2vw,23px) !important;}
.g4p-body :is(h4,h5){margin:28px 0 12px;font-size:19px !important;}
.g4p-body a{color:var(--g4b-deep) !important;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:3px;}
.g4p-body a:hover{color:var(--g4b-deep-hover) !important;text-decoration-thickness:2px;}
.g4p-body strong,.g4p-body b{font-weight:800;color:var(--g4b-ink);}
.g4p-body :is(ul,ol){margin:0 0 24px;padding-left:24px;}
.g4p-body ul{list-style:disc;}
.g4p-body ol{list-style:decimal;}
.g4b .g4p-body li{margin:0 0 10px;padding-left:4px;list-style:inherit;}
.g4p-body li::marker{color:var(--g4b-deep);}
.g4p-body img{max-width:100% !important;height:auto !important;border-radius:12px;margin:8px 0 26px;}
.g4p-body :is(iframe,video){display:block;max-width:100%;width:100%;aspect-ratio:16/9;height:auto;border:0;border-radius:12px;margin:8px 0 26px;}
.g4p-body blockquote,.g4p-body .pro-tip{margin:30px 0;padding:20px 24px;border-left:4px solid var(--g4b-green);border-radius:0 14px 14px 0;background:var(--g4b-mint);font-size:18px;color:var(--g4b-ink);}
.g4p-body blockquote p:last-child,.g4p-body .pro-tip p:last-child{margin-bottom:0;}
.g4p-body hr{margin:40px 0;border:0;border-top:1px solid var(--g4b-line);}
/* Wide tables scroll inside the column instead of widening the page. */
.g4p-body table{display:block;max-width:100%;overflow-x:auto;margin:10px 0 30px;border-collapse:collapse;border:1px solid var(--g4b-line) !important;border-radius:12px;font-size:15px;line-height:1.5;}
.g4p-body :is(th,td){min-width:120px;padding:12px 14px !important;border:1px solid var(--g4b-line) !important;text-align:left;vertical-align:top;}
.g4p-body th,.g4p-body thead td{background:var(--g4b-mint) !important;color:var(--g4b-ink) !important;font-weight:800;}
.g4p-body tr:nth-child(even) td{background:#fbfcfa;}

/* Contents, folded, above the article on small screens. */
.g4p-toc-mobile{display:none;margin:0 0 32px;border:1px solid var(--g4b-line);border-radius:14px;background:#fbfcfa;}
.g4p-toc-mobile summary{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px 18px;cursor:pointer;list-style:none;font-size:16px;font-weight:800;color:var(--g4b-ink);}
.g4p-toc-mobile summary::-webkit-details-marker{display:none;}
.g4p-toc-mobile summary span{font-size:13px;font-weight:700;color:var(--g4b-slate);}
.g4p-toc-mobile ol{margin:0;padding:0 18px 16px 36px;}
.g4p-toc-mobile li{padding:6px 0;list-style:decimal;}
.g4p-toc-mobile a{font-size:15px;font-weight:700;color:var(--g4b-ink);}

/* FAQs, share, author */
.g4p-faq{margin:56px 0 0;padding-top:40px;border-top:1px solid var(--g4b-line);}
.g4p-faq h2{margin:0 0 12px;font-size:clamp(24px,2.4vw,30px);font-weight:800;letter-spacing:-.015em;color:var(--g4b-ink);}
.g4p-faq .g4b-faq-item:first-of-type{border-top:1px solid var(--g4b-line);}
.g4p-faq-extra{margin-top:24px;}

.g4p-share{display:flex;flex-wrap:wrap;align-items:center;gap:14px;margin:44px 0 0;padding:20px 0;border-top:1px solid var(--g4b-line);border-bottom:1px solid var(--g4b-line);}
.g4p-share-label{font-size:15px;font-weight:800;color:var(--g4b-ink);}
.g4p-share ul{display:flex;gap:10px;margin:0;padding:0;list-style:none;}
.g4p-share :is(a,button){position:relative;display:inline-flex;align-items:center;justify-content:center;width:42px;height:42px;padding:0;border:1px solid var(--g4b-line);border-radius:50%;background:#fff;color:var(--g4b-ink);cursor:pointer;transition:background-color .2s ease,border-color .2s ease,color .2s ease;}
.g4p-share :is(a,button):hover{border-color:var(--g4b-green);background:var(--g4b-mint);color:var(--g4b-deep);}
.g4p-share svg{width:18px;height:18px;fill:currentColor;}
.g4p-copy svg{fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;}
.g4p-copy-done{position:absolute;bottom:calc(100% + 8px);left:50%;transform:translateX(-50%);padding:4px 8px;border-radius:6px;background:var(--g4b-ink);color:#fff;font-size:12px;font-weight:700;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .2s ease;}
.g4p-copy-done.is-on{opacity:1;}

.g4p-author{display:grid;grid-template-columns:72px minmax(0,1fr);gap:20px;margin-top:40px;padding:26px 28px;border:1px solid var(--g4b-line);border-radius:18px;background:#fbfcfa;}
.g4p-author-avatar{width:72px;height:72px;border-radius:50%;object-fit:cover;}
.g4p-author-avatar.g4b-avatar--initial{font-size:26px;}
.g4p-author h2{margin:0 0 4px;font-size:13px;font-weight:800;color:var(--g4b-slate);letter-spacing:0;}
.g4p-author-name{display:flex;flex-wrap:wrap;align-items:baseline;gap:4px 10px;margin:0 0 8px;font-size:19px;font-weight:800;color:var(--g4b-ink);}
.g4p-author-name a{color:inherit;}
.g4p-author-name a:hover{color:var(--g4b-deep);}
.g4p-author-name span{font-size:14px;font-weight:600;color:var(--g4b-slate);}
.g4p-author-bio{margin:0 0 12px;font-size:15.5px;line-height:1.65;color:var(--g4b-slate);}
.g4p-text-link{font-size:14.5px;font-weight:800;color:var(--g4b-deep);}
.g4p-text-link:hover{color:var(--g4b-deep-hover);text-decoration:underline;}

/* Sidebar: contents and sign-up stay in view while reading. */
.g4p-aside{align-self:stretch;}
.g4p-sticky{position:sticky;top:92px;display:flex;flex-direction:column;gap:20px;}
.g4p-aside-title{display:flex;justify-content:space-between;align-items:baseline;gap:12px;margin:0 0 10px;font-size:13px;font-weight:800;color:var(--g4b-slate);}
.g4p-toc-pct{font-size:12px;font-weight:800;color:var(--g4b-deep);font-variant-numeric:tabular-nums;opacity:0;transition:opacity .3s ease;}
.g4p-toc-pct.is-on{opacity:1;}
/* The rail is each item's left border; the script sets --g4p-fill (how far
   down the list the reader is, in px) and the green line and its dot follow.
   Both sit inside the list so they scroll with it, 4px in so it can't clip them. */
.g4p-toc ol{position:relative;max-height:min(52vh,460px);overflow-y:auto;margin:0;padding:0;list-style:none;scrollbar-width:thin;}
.g4p-toc ol::before{content:"";position:absolute;z-index:1;top:0;left:4px;width:2px;height:var(--g4p-fill,0px);border-radius:2px;background:linear-gradient(180deg,var(--g4b-green),var(--g4b-deep));pointer-events:none;}
.g4p-toc ol::after{content:"";position:absolute;z-index:2;top:0;left:1px;width:8px;height:8px;border-radius:50%;background:var(--g4b-green);box-shadow:0 0 0 4px rgba(111,217,67,.22);transform:translateY(calc(var(--g4p-fill,0px) - 4px));opacity:var(--g4p-dot,0);transition:opacity .3s ease;pointer-events:none;}
.g4p-toc li{margin:0 0 0 4px;border-left:2px solid var(--g4b-line);}
.g4p-toc a{display:block;margin:1px 0 1px 0;padding:7px 10px 7px 14px;border-radius:0 10px 10px 0;font-size:14.5px;font-weight:700;line-height:1.4;color:var(--g4b-slate);transition:color .25s ease,background-color .25s ease,transform .35s var(--g4b-ease);}
.g4p-toc a:hover{color:var(--g4b-ink);background:#f6f8f5;}
.g4p-toc a.is-read{color:#2f3b4c;}
.g4p-toc a.is-current{color:var(--g4b-deep);background:linear-gradient(90deg,var(--g4b-mint),rgba(240,253,244,0));transform:translateX(4px);}
.g4p-toc-mobile a.is-current{color:var(--g4b-deep);}
.g4p-cta{padding:24px;border:1px solid #cdeccf;border-radius:18px;background:linear-gradient(135deg,var(--g4b-mint) 0%,var(--g4b-mint-2) 100%);}
.g4p-cta-title{margin:0 0 8px;font-size:20px;font-weight:800;line-height:1.25;color:var(--g4b-ink);}
.g4p-cta p:not(.g4p-cta-title){margin:0 0 18px;font-size:15px;line-height:1.6;color:#475569;}
.g4p-cta .g4b-btn{width:100%;justify-content:center;}

/* --- after the article ------------------------------------------------- */
.g4p-related{padding:56px 0 8px;border-top:1px solid var(--g4b-line);}
.g4p-more{padding:56px 0 72px;}
.g4p-more-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:28px;align-items:start;}
.g4p-panel{padding:26px 26px 24px;border:1px solid var(--g4b-line);border-radius:18px;background:#fff;}
.g4p-panel h2{margin:0 0 16px;font-size:20px;font-weight:800;color:var(--g4b-ink);}
.g4p-panel > ul{margin:0 0 16px;padding:0;list-style:none;}
.g4p-lists ul li a{display:block;padding:9px 0;border-bottom:1px solid var(--g4b-line);font-size:14.5px;font-weight:700;color:var(--g4b-ink);}
.g4p-lists ul li a:hover{color:var(--g4b-deep);}
.g4p-tabs{display:flex;gap:6px;margin:0 0 10px;padding:4px;border-radius:12px;background:#f1f5f2;}
.g4p-tabs button{flex:1;padding:9px 10px;border:0;border-radius:9px;background:transparent;font:inherit;font-size:13.5px;font-weight:800;color:var(--g4b-slate);cursor:pointer;}
.g4p-tabs button[aria-selected="true"]{background:#fff;color:var(--g4b-ink);box-shadow:0 2px 6px rgba(11,19,42,.08);}
.g4p-about p{margin:0 0 16px;font-size:15px;line-height:1.65;color:var(--g4b-slate);}
.g4p-stats{display:flex;gap:10px;flex-wrap:wrap;}
.g4p-stats li{padding:8px 14px;border-radius:999px;background:var(--g4b-tint);font-size:14px;font-weight:800;color:var(--g4b-deep);}
.g4p-about .g4p-logos-title{margin:6px 0 10px;font-size:13px;font-weight:800;color:var(--g4b-slate);}
.g4p-logos{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}
.g4p-logos li{display:flex;align-items:center;justify-content:center;height:56px;padding:8px 12px;border:1px solid var(--g4b-line);border-radius:12px;}
.g4p-logos img{max-width:100%;max-height:34px;width:auto;height:auto;object-fit:contain;}
.g4p-cases li{padding:12px 0;border-bottom:1px solid var(--g4b-line);}
.g4p-cases li a{font-size:15.5px;font-weight:800;line-height:1.4;color:var(--g4b-ink);}
.g4p-cases li a:hover{color:var(--g4b-deep);}
.g4p-cases li p{margin:6px 0 0;font-size:14px;line-height:1.55;color:var(--g4b-slate);}

/* --- smaller screens --------------------------------------------------- */
@media (max-width:1100px){
  .g4p-layout{grid-template-columns:minmax(0,1fr);gap:40px;}
  .g4p-article{max-width:none;}
  .g4p-toc{display:none;}
  .g4p-toc-mobile{display:block;}
  .g4p-sticky{position:static;}
  .g4p-more-grid{grid-template-columns:repeat(2,minmax(0,1fr));}
}
@media (max-width:700px){
  .g4p-more-grid{grid-template-columns:minmax(0,1fr);}
  .g4p-body{font-size:16.5px;}
  .g4p-lead{font-size:16.5px;}
  .g4p-author{grid-template-columns:minmax(0,1fr);padding:22px 20px;}
  .g4p-figure{border-radius:14px;margin-bottom:28px;}
}
@media (prefers-reduced-motion:no-preference){
  html:has(.g4p){scroll-behavior:smooth;}
}
@media (prefers-reduced-motion:reduce){
  .g4p-toc a{transition:none;}
  .g4p-toc a.is-current{transform:none;}
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Contents follow the reader: sections already read, the one being read,
  // a green line down the rail that grows through each section, and how
  // much of the article is read. Both lists (sidebar and folded) are marked.
  var list = document.querySelector('.g4p-toc ol');
  var article = document.querySelector('.g4p-body');
  var pct = document.querySelector('.g4p-toc-pct');
  var sections = [];
  [].slice.call(document.querySelectorAll('.g4p-toc a')).forEach(function (a) {
    var id = decodeURIComponent(a.getAttribute('href').slice(1));
    var heading = document.getElementById(id);
    if (!heading) { return; }
    var twins = [].slice.call(document.querySelectorAll('.g4p-toc-mobile a')).filter(function (m) { return m.getAttribute('href') === a.getAttribute('href'); });
    sections.push({ heading: heading, link: a, li: a.parentNode, all: [a].concat(twins) });
  });
  if (list && article && sections.length) {
    var last = -2, queued = false;
    var update = function () {
      queued = false;
      var line = window.innerHeight * 0.35;
      var tops = sections.map(function (s) { return s.heading.getBoundingClientRect().top; });
      var box = article.getBoundingClientRect();
      var current = -1;
      tops.forEach(function (top, i) { if (top <= line) { current = i; } });
      var fill = 0;
      if (current >= 0) {
        var end = current + 1 < tops.length ? tops[current + 1] : box.bottom;
        var part = Math.min(1, Math.max(0, (line - tops[current]) / Math.max(1, end - tops[current])));
        var li = sections[current].li;
        // Kept 4px off either end so the dot is never cut in half.
        fill = Math.min(list.scrollHeight - 4, Math.max(4, li.offsetTop + part * li.offsetHeight));
      }
      list.style.setProperty('--g4p-fill', fill.toFixed(1) + 'px');
      list.style.setProperty('--g4p-dot', current >= 0 ? '1' : '0');
      if (pct) {
        var read = Math.min(1, Math.max(0, (line - box.top) / Math.max(1, box.height)));
        pct.textContent = Math.round(read * 100) + '% read';
        pct.classList.toggle('is-on', read > 0.01);
      }
      if (current === last) { return; }
      last = current;
      sections.forEach(function (s, i) {
        s.all.forEach(function (a) {
          a.classList.toggle('is-current', i === current);
          a.classList.toggle('is-read', i < current);
          if (i === current) { a.setAttribute('aria-current', 'true'); } else { a.removeAttribute('aria-current'); }
        });
      });
      // A long list keeps the current item in its own view (never moves the page).
      if (current >= 0 && list.scrollHeight > list.clientHeight) {
        var item = sections[current].li;
        if (item.offsetTop < list.scrollTop || item.offsetTop + item.offsetHeight > list.scrollTop + list.clientHeight) {
          list.scrollTo({ top: Math.max(0, item.offsetTop - list.clientHeight * 0.3), behavior: 'smooth' });
        }
      }
    };
    var queue = function () { if (!queued) { queued = true; window.requestAnimationFrame(update); } };
    window.addEventListener('scroll', queue, { passive: true });
    window.addEventListener('resize', queue);
    window.addEventListener('load', queue);
    update();
  }

  // The folded contents close once a section is picked.
  var folded = document.querySelector('.g4p-toc-mobile');
  if (folded) {
    folded.addEventListener('click', function (e) { if (e.target.closest('a')) { folded.open = false; } });
  }

  // Mailing-list tabs. Without script both lists simply show.
  var tabs = [].slice.call(document.querySelectorAll('.g4p-tabs [role="tab"]'));
  var select = function (tab) {
    tabs.forEach(function (t) {
      var on = t === tab;
      t.setAttribute('aria-selected', on ? 'true' : 'false');
      t.tabIndex = on ? 0 : -1;
      document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
    });
  };
  if (tabs.length) {
    select(tabs[0]);
    tabs.forEach(function (tab, i) {
      tab.addEventListener('click', function () { select(tab); });
      tab.addEventListener('keydown', function (e) {
        if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') { return; }
        var next = tabs[(i + (e.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length];
        select(next); next.focus();
      });
    });
  }

  // Copy the article's address.
  var copy = document.querySelector('.g4p-copy');
  if (copy && navigator.clipboard) {
    copy.addEventListener('click', function () {
      navigator.clipboard.writeText(copy.getAttribute('data-url')).then(function () {
        var done = copy.querySelector('.g4p-copy-done');
        done.textContent = 'Link copied';
        done.classList.add('is-on');
        setTimeout(function () { done.classList.remove('is-on'); }, 1800);
      });
      if (typeof gtag === 'function') { gtag('event', 'share', { method: 'copy_link', content_type: 'article' }); }
    });
  }

  // GA4: shares and the sign-up card.
  document.addEventListener('click', function (e) {
    if (typeof gtag !== 'function') { return; }
    var share = e.target.closest('.g4p-share a[data-share]');
    if (share) { gtag('event', 'share', { method: share.getAttribute('data-share'), content_type: 'article' }); }
    if (e.target.closest('.g4p-cta a')) { gtag('event', 'blog_post_cta', { location: 'sidebar' }); }
  });
});
</script>
