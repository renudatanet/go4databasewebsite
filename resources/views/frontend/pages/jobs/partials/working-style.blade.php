{{-- Styles for /career/working-at-go4database (pages/jobs/working). Built on
     the blog's tokens (blog/partials/style, included first) and scoped to
     .g4w; inline, so the view deploys on its own. The script drives the
     quotes carousel. --}}
<style>
.g4w{--g4w-brand:linear-gradient(to right,#ff5858 0%,#ec4899 25%,#a855f7 50%,#6366f1 75%,#5468ff 100%);--g4w-brand-soft:linear-gradient(135deg,#fff1f6 0%,#f5f0ff 50%,#eef1ff 100%);--g4w-on-brand:rgba(255,255,255,.92);--g4w-radius:18px;}
.g4w,.g4w *{-webkit-hyphens:manual;hyphens:manual;}
.g4w svg{fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.g4w h2,.g4w h3{letter-spacing:-.02em;}

/* Header: the homepage hero's own banner (home.css .g4d-hero): its pink,
   purple and blue gradient with rounded corners, the slow pulse, the dot
   grid fading out to the right, the green glow and the dashed data lines.
   With quote cards, the banner leaves room at its foot for them to overlap
   its edge onto the white page, like the homepage's search card. */
.g4w-hero{position:relative;overflow:hidden;max-width:1440px;margin:0 auto;padding:36px 0 52px;border-radius:22px;background:var(--g4w-brand);color:#fff;text-align:center;box-shadow:0 20px 50px -16px rgba(168,85,247,.3);}
.g4w-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 85% 20%,rgba(84,104,255,.4) 0%,transparent 65%),radial-gradient(circle at 15% 85%,rgba(255,88,88,.35) 0%,transparent 65%);animation:g4wPulse 6s ease-in-out infinite alternate;pointer-events:none;}
.g4w-dots{position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.35) 1.5px,transparent 1.5px);background-size:26px 26px;opacity:.85;animation:g4wDots 12s linear infinite;pointer-events:none;
  -webkit-mask-image:linear-gradient(to right,#000 0%,#000 45%,rgba(0,0,0,.2) 65%,transparent 85%);mask-image:linear-gradient(to right,#000 0%,#000 45%,rgba(0,0,0,.2) 65%,transparent 85%);}
.g4w-hero--cards{padding-bottom:110px;}
.g4w-orb{position:absolute;border-radius:50%;filter:blur(40px);pointer-events:none;animation:g4wOrb 7s ease-in-out infinite;}
.g4w-orb--1{top:-40px;right:20%;width:220px;height:220px;background:radial-gradient(circle,rgba(34,197,94,.18),transparent 70%);}
.g4w-orb--2{bottom:-20px;right:8%;width:160px;height:160px;background:radial-gradient(circle,rgba(16,185,129,.15),transparent 70%);animation-duration:5.5s;animation-delay:2s;}
.g4w .g4w-lines{position:absolute;top:0;right:0;width:65%;height:100%;pointer-events:none;}
.g4w .g4w-line{fill:none;stroke:rgba(22,163,74,.35);stroke-width:1.5;stroke-dasharray:6 10;animation:g4wLine 3s linear infinite;}
.g4w .g4w-line--faint{stroke:rgba(22,163,74,.2);stroke-width:1;stroke-dasharray:4 14;animation-duration:5s;animation-direction:reverse;}
@keyframes g4wOrb{0%,100%{transform:scale(1);opacity:.7;}50%{transform:scale(1.3);opacity:1;}}
@keyframes g4wLine{to{stroke-dashoffset:-100;}}
@keyframes g4wPulse{from{opacity:.7;}to{opacity:1;}}
@keyframes g4wDots{from{background-position:0 0;}to{background-position:26px 26px;}}
.g4w-hero .g4b-wrap{position:relative;}
.g4w-hero .g4b-btn{background:#fff;color:#111827;box-shadow:0 10px 24px -10px rgba(17,24,39,.45);}
.g4w-hero .g4b-btn:hover{background:#f8fafc;color:#111827;}
.g4w-crumbs ol{display:flex;flex-wrap:wrap;justify-content:center;gap:6px 8px;margin:0;padding:0;list-style:none;font-size:12.5px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;}
.g4w-crumbs li{color:#fff;}
.g4w-crumbs li+li::before{content:'›';margin-right:8px;color:var(--g4w-on-brand);}
.g4w-crumbs a{color:var(--g4w-on-brand);text-decoration:underline;text-underline-offset:3px;}
.g4w-crumbs a:hover{color:#fff;}
.g4w-title{margin:18px auto 0;max-width:16ch;font-size:clamp(36px,5vw,60px);font-weight:800;line-height:1.06;color:#fff;text-shadow:0 2px 18px rgba(80,20,120,.25);}
.g4w-intro{max-width:64ch;margin:18px auto 28px;font-size:17.5px;line-height:1.7;color:var(--g4w-on-brand);}

/* Quotes from the team: a carousel with the middle card in focus (the
   script sets each card's data-pos: 0 in the middle, ±1 beside it, ±2 at
   the edges). Without script, .is-ready is never set and the cards sit in
   a row that scrolls sideways. */
.g4w-people{position:relative;z-index:2;margin-top:-90px;padding-bottom:8px;}
.g4w-carousel{position:relative;margin:0 -32px;text-align:left;}
.g4w-track{display:flex;gap:18px;padding:8px 32px 16px;overflow-x:auto;scroll-snap-type:x mandatory;}
.g4w-qcard{position:relative;flex:0 0 min(380px,84vw);display:flex;flex-direction:column;min-height:500px;margin:0;padding:24px 24px 0;overflow:hidden;border-radius:16px;
  background-color:#fbfaf6;background-image:linear-gradient(rgba(11,19,42,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(11,19,42,.045) 1px,transparent 1px);background-size:22px 22px;
  color:var(--g4b-ink);scroll-snap-align:center;border:1px solid rgba(15,23,42,.07);box-shadow:0 24px 50px -24px rgba(76,29,149,.45),0 4px 18px rgba(15,23,42,.06);}
.g4w .g4w-qcard blockquote{position:relative;z-index:2;margin:0;padding:0;border:0;background:none;}
.g4w-qhead{margin:0 0 8px;font-size:18px;font-weight:600;line-height:1.3;letter-spacing:-.01em;color:var(--g4b-ink);}
.g4w-qbody{margin:0 0 6px;font-size:13px;line-height:1.5;color:#3a4453;}
/* Like ZoomInfo's cards: the name sits on the left, level with the top of
   the worker's photo, which fills the card's lower right half in front of a
   large circle that the card's edges cut off. */
.g4w-qcard figcaption{position:relative;z-index:2;display:block;max-width:54%;margin:12px 0 -96px;}
.g4w-qname{display:block;font-family:Georgia,'Times New Roman',serif;font-size:28px;line-height:1.08;letter-spacing:-.01em;color:#2b3442;}
.g4w-qrole{display:block;margin-top:8px;font-size:11px;font-weight:700;line-height:1.45;letter-spacing:.1em;text-transform:uppercase;color:#6b7684;}
.g4w-qphoto{position:relative;z-index:1;align-self:flex-end;width:70%;margin:auto -24px 0 0;aspect-ratio:4/5;}
.g4w-qphoto::before{content:'';position:absolute;left:-14%;bottom:-34%;width:112%;aspect-ratio:1;border-radius:50%;background:linear-gradient(135deg,#ec4899 0%,#a855f7 60%,#6366f1 100%);}
.g4w-qphoto img{position:absolute;display:block;}
/* A cut-out (transparent PNG) stands in front of the circle, cut off by the card's bottom edge */
.g4w-qphoto.is-cutout img{inset:0;width:100%;height:100%;object-fit:contain;object-position:bottom center;filter:drop-shadow(0 10px 18px rgba(17,24,39,.18));}
/* An ordinary photo is cropped to a big round portrait over the circle */
.g4w-qphoto.is-round img{left:0;bottom:-8%;width:96%;aspect-ratio:1;border-radius:50%;object-fit:cover;object-position:center 20%;border:6px solid rgba(255,255,255,.85);}
.g4w-qphoto span{position:absolute;left:-14%;bottom:-34%;display:grid;place-items:center;width:112%;aspect-ratio:1;padding-bottom:22%;font-family:Georgia,serif;font-size:110px;line-height:1;color:#fff;}

/* With script: the coverflow */
.g4w-carousel.is-ready .g4w-track{display:block;position:relative;height:540px;padding:0;overflow:visible;}
.g4w-carousel.is-ready .g4w-qcard{position:absolute;left:50%;top:50%;width:min(380px,84vw);
  transform:translate(-50%,-50%) translateX(var(--x,0)) scale(var(--s,1));opacity:var(--o,1);z-index:var(--z,1);
  transition:transform .55s var(--g4b-ease),opacity .55s var(--g4b-ease);cursor:pointer;}
.g4w-qcard[data-pos="0"]{--x:0px;--s:1;--o:1;--z:5;cursor:default;}
.g4w-qcard[data-pos="1"]{--x:370px;--s:.74;--o:1;--z:4;}
.g4w-qcard[data-pos="-1"]{--x:-370px;--s:.74;--o:1;--z:4;}
.g4w-qcard[data-pos="2"]{--x:640px;--s:.6;--o:1;--z:3;}
.g4w-qcard[data-pos="-2"]{--x:-640px;--s:.6;--o:1;--z:3;}
.g4w-qcard[data-pos="far"]{--o:0;--s:.5;--z:1;pointer-events:none;}
.g4w-carousel.is-ready .g4w-qcard:not([data-pos="0"]) *{pointer-events:none;}
.g4w-qnav{display:none;align-items:center;justify-content:center;gap:14px;margin-top:18px;}
.g4w-carousel.is-ready .g4w-qnav{display:flex;}
.g4w-qbtn{display:grid;place-items:center;width:42px;height:42px;padding:0;border:1px solid #d9dee8;border-radius:50%;background:#fff;color:var(--g4b-ink);cursor:pointer;box-shadow:0 4px 12px rgba(15,23,42,.06);}
.g4w-qbtn:hover{border-color:#a855f7;color:#a855f7;}
.g4w-qbtn svg{width:18px;height:18px;}
.g4w-qdots{display:flex;gap:8px;}
.g4w-qdots button{width:8px;height:8px;padding:0;border:0;border-radius:99px;background:#cfd5e1;cursor:pointer;transition:width .3s,background .3s;}
.g4w-qdots button[aria-current="true"]{width:24px;background:var(--g4w-brand);}

/* Customer testimonials */
.g4w .g4w-review blockquote{border:0;padding:0;background:none;margin:0;font-size:15.5px;line-height:1.65;font-weight:500;color:var(--g4b-ink);}
.g4w .g4w-review blockquote::before{content:'“';display:block;width:max-content;height:30px;font-family:Georgia,serif;font-size:56px;line-height:1;background:var(--g4w-brand);-webkit-background-clip:text;background-clip:text;color:transparent;}
.g4w-review figcaption{display:flex;align-items:center;gap:12px;}
.g4w-review figcaption img,.g4w-initial{flex:none;width:46px;height:46px;border-radius:50%;object-fit:cover;}
.g4w-initial{display:grid;place-items:center;background:var(--g4w-brand-soft);box-shadow:inset 0 0 0 1px rgba(168,85,247,.16);font-weight:800;color:#8b3fd9;}
.g4w-review figcaption strong{display:block;font-size:15px;color:var(--g4b-ink);}
.g4w-review figcaption small{display:block;font-size:13.5px;color:var(--g4b-slate);}

/* Sections */
.g4w-section{padding:76px 0;}
.g4w-section--tint{background:#f4f7fa;}
.g4w-head{max-width:720px;margin:0 0 36px;}
.g4w-head--center{margin-left:auto;margin-right:auto;text-align:center;}
.g4w-head h2,.g4w-story h2{margin:0;font-size:clamp(30px,3.4vw,42px);font-weight:800;line-height:1.12;color:var(--g4b-ink);}
.g4w-head p{margin:14px 0 0;font-size:17px;line-height:1.65;color:var(--g4b-slate);}

/* Who we are: heading beside the story */
.g4w-story{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.6fr);gap:24px 64px;align-items:start;}
.g4w-story p{margin:0 0 16px;font-size:18px;line-height:1.75;color:var(--g4b-text);}
.g4w-story p:first-child{font-size:20px;font-weight:700;color:var(--g4b-ink);}

/* What we do */
.g4w-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:20px;margin:0;padding:0;list-style:none;}
.g4w-card{display:flex;flex-direction:column;padding:26px;border:1px solid var(--g4b-line);border-radius:var(--g4w-radius);background:#fff;}
.g4w-icon{display:grid;place-items:center;width:46px;height:46px;margin-bottom:18px;border-radius:13px;background:var(--g4w-brand-soft);color:#8b3fd9;box-shadow:inset 0 0 0 1px rgba(168,85,247,.14);}
.g4w-icon svg{width:22px;height:22px;}
.g4w-card h3{margin:0 0 8px;font-size:19px;font-weight:800;color:var(--g4b-ink);}
.g4w-card p{flex:1;margin:0;font-size:15px;line-height:1.6;color:var(--g4b-slate);}

/* How we work: the brand gradient, softened, numbered in the gradient */
.g4w-values{background:var(--g4w-brand-soft);}
.g4w-value-list{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:20px;margin:0;padding:0;list-style:none;counter-reset:g4wv;}
.g4w-value-list li{padding:28px 26px 26px;border:1px solid rgba(168,85,247,.14);border-radius:var(--g4w-radius);background:#fff;box-shadow:0 14px 30px -24px rgba(99,102,241,.45);}
.g4w .g4w-value-list li::before{counter-increment:g4wv;content:counter(g4wv,decimal-leading-zero);display:inline-block;margin:0 0 14px;font-size:15px;font-weight:800;letter-spacing:.08em;background:var(--g4w-brand);-webkit-background-clip:text;background-clip:text;color:transparent;}
.g4w-value-list h3{margin:0 0 8px;font-size:19px;font-weight:800;color:var(--g4b-ink);}
.g4w-value-list p{margin:0;font-size:15px;line-height:1.6;color:var(--g4b-slate);}

/* What our customers say */
.g4w-reviews{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;margin:0;padding:0;list-style:none;}
.g4w-review{display:flex;flex-direction:column;justify-content:space-between;gap:20px;height:100%;margin:0;padding:26px;border:1px solid var(--g4b-line);border-radius:var(--g4w-radius);background:#fff;box-shadow:0 1px 2px rgba(11,19,42,.04),0 14px 30px -24px rgba(11,19,42,.3);}

/* Offices and teams */
.g4w-grid{display:grid;gap:24px;margin:0;padding:0;list-style:none;}
.g4w-grid--1{grid-template-columns:minmax(0,420px);justify-content:center;}
.g4w-grid--2{grid-template-columns:repeat(2,minmax(0,1fr));max-width:860px;margin:0 auto;}
.g4w-grid--3{grid-template-columns:repeat(3,minmax(0,1fr));}
.g4w-media{display:block;width:100%;max-width:100%;aspect-ratio:16/10;border-radius:var(--g4w-radius);object-fit:cover;background:var(--g4b-tint);}
/* No photo yet: a short panel in the header's colours instead of a big empty block */
.g4w-media--blank{position:relative;display:grid;place-items:center;aspect-ratio:16/7;overflow:hidden;background:var(--g4w-brand-soft);color:#8b3fd9;}
.g4w-media--blank::before{content:'';position:absolute;inset:0;background-image:radial-gradient(circle,rgba(139,63,217,.16) 1.2px,transparent 1.2px);background-size:20px 20px;}
.g4w-media--blank svg{position:relative;width:40px;height:40px;stroke-width:1.6;}
.g4w-office h3,.g4w-team h3{margin:18px 0 4px;font-size:21px;font-weight:800;color:var(--g4b-ink);}
.g4w-office p{margin:4px 0 0;color:var(--g4b-slate);line-height:1.55;}
.g4w-sub{margin:0;font-size:14.5px;font-weight:600;color:var(--g4b-slate);}
.g4w-team{overflow:hidden;border:1px solid var(--g4b-line);border-radius:var(--g4w-radius);background:#fff;}
.g4w-team .g4w-media{border-radius:0;}
.g4w-team-body{padding:4px 24px 24px;}
.g4w-link{display:inline-flex;align-items:center;gap:6px;margin-top:14px;font-size:14.5px;font-weight:800;color:var(--g4b-deep);text-decoration:underline;text-underline-offset:3px;}
.g4w-link svg{width:15px;height:15px;transition:transform .2s;}
.g4w-link:hover{color:var(--g4b-deep-hover);}
.g4w-link:hover svg{transform:translateX(3px);}
.g4w-band .g4w-head{max-width:640px;}
.g4w-band .g4b-btn{margin-top:22px;}

/* Closing banner */
.g4w-join{padding:72px 0;background:#fff;}
.g4w-join-card{position:relative;overflow:hidden;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:20px 40px;padding:56px;border-radius:26px;background:var(--g4w-brand);color:#fff;box-shadow:0 20px 50px -16px rgba(168,85,247,.35);}
.g4w-join-card::after{content:'';position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.3) 1.5px,transparent 1.5px);background-size:26px 26px;-webkit-mask-image:linear-gradient(to left,#000,transparent 70%);mask-image:linear-gradient(to left,#000,transparent 70%);pointer-events:none;}
.g4w-join-card .g4b-btn{background:#fff;color:#111827;}
.g4w-join-card .g4b-btn:hover{background:#f8fafc;color:#111827;}
.g4w-join-card > *{position:relative;}
.g4w-join-card h2{margin:0;font-size:clamp(32px,4vw,50px);font-weight:800;color:#fff;}
.g4w-join-card p{margin:10px 0 0;font-size:17px;color:var(--g4w-on-brand);}

@media (max-width:900px){
  .g4w-story{grid-template-columns:minmax(0,1fr);}
  .g4w-grid--3{grid-template-columns:repeat(2,minmax(0,1fr));}
}
@media (max-width:640px){
  /* Customer quotes swipe sideways instead of stacking into a long column */
  .g4w-reviews{display:flex;gap:14px;margin:0 -16px;padding:4px 16px 14px;overflow-x:auto;scroll-snap-type:x mandatory;scrollbar-width:none;}
  .g4w-reviews::-webkit-scrollbar{display:none;}
  .g4w-reviews > li{flex:0 0 86%;scroll-snap-align:center;}
  .g4w-hero{margin:0 10px;padding:28px 0 48px;border-radius:16px;}
  .g4w-hero--cards{padding-bottom:90px;}
  .g4w-people{margin-top:-70px;}
  .g4w-intro{font-size:16px;}
  .g4w-carousel{margin:0 -16px;}
  .g4w-track{padding:8px 16px 16px;}
  .g4w-carousel.is-ready .g4w-track{height:500px;}
  .g4w-carousel.is-ready .g4w-qcard{width:min(320px,80vw);}
  .g4w-qcard{min-height:460px;}
  .g4w-qname{font-size:24px;}
  .g4w-qcard[data-pos="1"]{--x:62vw;--s:.8;--o:.5;}
  .g4w-qcard[data-pos="-1"]{--x:-62vw;--s:.8;--o:.5;}
  .g4w-qcard[data-pos="2"],.g4w-qcard[data-pos="-2"]{--o:0;pointer-events:none;}
  .g4w-qhead{font-size:16px;}
  .g4w-section,.g4w-join{padding:52px 0;}
  .g4w-story p{font-size:16.5px;}
  .g4w-story p:first-child{font-size:18px;}
  .g4w-grid--2,.g4w-grid--3{grid-template-columns:minmax(0,1fr);}
  .g4w-join-card{padding:36px 26px;}
}
@media (min-width:641px) and (max-width:1100px){
  .g4w-qcard[data-pos="1"]{--x:290px;}
  .g4w-qcard[data-pos="-1"]{--x:-290px;}
  .g4w-qcard[data-pos="2"],.g4w-qcard[data-pos="-2"]{--o:0;pointer-events:none;}
}
@media (prefers-reduced-motion:reduce){.g4w-link svg,.g4w-carousel.is-ready .g4w-qcard{transition:none;}.g4w-hero::before,.g4w-dots,.g4w-orb,.g4w .g4w-line{animation:none;}}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // The quotes carousel: the middle card is the active one; the others sit
  // beside it by distance (data-pos), wrapping round when there are 3 or
  // more. Arrows, dots, a click on a side card, a swipe and the arrow keys
  // move it; it also moves on by itself every 7 seconds, pausing while the
  // pointer or focus is on it, never for visitors who ask for less motion.
  var carousel = document.querySelector('.g4w-carousel');
  if (!carousel) { return; }
  var cards = Array.prototype.slice.call(carousel.querySelectorAll('.g4w-qcard'));
  var dots = Array.prototype.slice.call(carousel.querySelectorAll('.g4w-qdots button'));
  var count = cards.length, active = 0, timer = null, paused = false;
  var wrap = count >= 3;
  var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function offset(i) {
    var d = i - active;
    if (wrap) {
      if (d > count / 2) { d -= count; }
      if (d < -count / 2) { d += count; }
    }
    return d;
  }
  function show(i) {
    active = wrap ? (i + count) % count : Math.max(0, Math.min(count - 1, i));
    cards.forEach(function (card, n) {
      var d = offset(n);
      card.setAttribute('data-pos', Math.abs(d) <= 2 ? String(d) : 'far');
      card.setAttribute('aria-hidden', d === 0 ? 'false' : 'true');
    });
    dots.forEach(function (dot, n) { dot.setAttribute('aria-current', n === active ? 'true' : 'false'); });
  }
  function restart() {
    clearInterval(timer);
    if (!still && count > 1) { timer = setInterval(function () { if (!paused) { show(active + 1); } }, 7000); }
  }

  carousel.classList.add('is-ready');
  show(Math.floor((count - 1) / 2));
  restart();

  carousel.addEventListener('click', function (e) {
    var step = e.target.closest('[data-step]'), go = e.target.closest('[data-go]'), card = e.target.closest('.g4w-qcard');
    if (step) { show(active + Number(step.getAttribute('data-step'))); }
    else if (go) { show(Number(go.getAttribute('data-go'))); }
    else if (card && card.getAttribute('data-pos') !== '0') { show(Number(card.getAttribute('data-index'))); }
    else { return; }
    restart();
  });
  carousel.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowRight') { show(active + 1); restart(); }
    else if (e.key === 'ArrowLeft') { show(active - 1); restart(); }
  });
  ['mouseenter', 'focusin'].forEach(function (t) { carousel.addEventListener(t, function () { paused = true; }); });
  ['mouseleave', 'focusout'].forEach(function (t) { carousel.addEventListener(t, function () { paused = false; }); });

  var startX = null;
  carousel.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
  carousel.addEventListener('touchend', function (e) {
    if (startX === null) { return; }
    var dx = e.changedTouches[0].clientX - startX;
    if (Math.abs(dx) > 40) { show(active + (dx < 0 ? 1 : -1)); restart(); }
    startX = null;
  });
});
</script>
