{{-- Styles and script for the blog listing (partials/listing). Inline for
     the same reason as the navbar and footer: production's public_html/assets
     is a separate copy, and a view deploys on its own. Everything is scoped
     to .g4b so none of it reaches the rest of the page. --}}
<style>
.g4b{
  --g4b-ink:#0b132a;
  --g4b-text:#2a3442;
  --g4b-slate:#556070;
  --g4b-line:#e7ecf1;
  --g4b-green:#6fd943;
  --g4b-green-hover:#63cf37;
  /* The homepage's own greens: #1f7a2e for green text, #e8f7ea tint, and
     the #f0fdf4 -> #dcfce7 mint of its promo panels. */
  --g4b-deep:#1f7a2e;
  --g4b-deep-hover:#16601f;
  --g4b-tint:#e8f7ea;
  --g4b-mint:#f0fdf4;
  --g4b-mint-2:#dcfce7;
  --g4b-ease:cubic-bezier(.2,.8,.2,1);
  font-family:'Nunito',sans-serif;
  color:var(--g4b-text);
}
.g4b *{box-sizing:border-box;}
.g4b a{text-decoration:none;}
/* The theme numbers every ol li through a ::before counter (style.css); the
   lists here carry their own numbers, or none. */
.g4b ol li::before{content:none;counter-increment:none;margin:0;}
.g4b li{margin:0;}
.g4b-wrap{max-width:1240px;margin:0 auto;padding:0 32px;}
.g4b-sr{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;}
.g4b a:focus-visible,.g4b button:focus-visible,.g4b input:focus-visible{outline:2px solid var(--g4b-deep);outline-offset:2px;}

/* --- header ------------------------------------------------------------ */
/* A field of faint dots on the right, fading out towards the copy: a
   quiet nod to rows of records without competing with the headline. */
.g4b-hero{
  position:relative;
  z-index:3;
  padding:34px 0 30px;
  background:linear-gradient(180deg,var(--g4b-mint) 0%,#fff 100%);
  border-bottom:1px solid var(--g4b-line);
}
.g4b-hero::before{
  content:'';
  position:absolute;
  inset:0 0 0 45%;
  background-image:radial-gradient(#bfe3ad 1.1px,transparent 1.3px);
  background-size:18px 18px;
  -webkit-mask-image:linear-gradient(90deg,transparent,#000 60%);
  mask-image:linear-gradient(90deg,transparent,#000 60%);
  opacity:.55;
  pointer-events:none;
}
.g4b-hero .g4b-wrap{position:relative;}
.g4b-crumbs ol{display:flex;flex-wrap:wrap;gap:8px;margin:0;padding:0;list-style:none;}
.g4b-crumbs ol{font-size:13.5px;font-weight:600;color:var(--g4b-slate);}
.g4b-crumbs li+li::before{content:'/';margin-right:8px;color:#b4bdc8;}
.g4b-crumbs a{color:var(--g4b-slate);}
.g4b-crumbs a:hover{color:var(--g4b-deep);}
.g4b-crumbs [aria-current]{color:var(--g4b-ink);}

.g4b-title{margin:0;font-size:clamp(40px,5.2vw,64px);font-weight:800;line-height:1.02;letter-spacing:-.03em;color:var(--g4b-ink);}
.g4b-lead{max-width:62ch;margin:16px 0 0;font-size:18px;line-height:1.6;color:var(--g4b-slate);}
/* The listing's header: centred, one column. The newest post as a "New"
   chip, the heading with its highlighted words, the summary,
   then a wide search box and the topics, over a slow green glow. */
.g4b-hero--center{padding:30px 0 34px;overflow:hidden;text-align:center;}
.g4b-hero--center::before{inset:0;-webkit-mask-image:radial-gradient(ellipse 70% 90% at 50% 40%,transparent 30%,#000 75%);mask-image:radial-gradient(ellipse 70% 90% at 50% 40%,transparent 30%,#000 75%);opacity:.6;}
.g4b-glow{position:absolute;inset:-30% -10% auto;height:130%;pointer-events:none;
  background:radial-gradient(38% 46% at 30% 40%,rgba(111,217,67,.22),transparent 70%),radial-gradient(32% 40% at 72% 30%,rgba(34,197,94,.16),transparent 70%),radial-gradient(28% 34% at 55% 75%,rgba(187,247,208,.5),transparent 70%);}
.g4b-hero--center .g4b-crumbs ol{justify-content:center;}
.g4b-hero--center .g4b-hero-copy{max-width:860px;margin:18px auto 0;}
.g4b-new{display:inline-flex;align-items:center;gap:10px;max-width:100%;margin:0 0 18px;padding:5px 14px 5px 5px;border:1px solid #cdeccf;border-radius:999px;background:rgba(255,255,255,.85);box-shadow:0 6px 18px -12px rgba(31,122,46,.5);font-size:14px;font-weight:700;color:var(--g4b-ink);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);transition:border-color .2s ease,transform .2s var(--g4b-ease);}
.g4b-new:hover{border-color:var(--g4b-green);color:var(--g4b-ink);transform:translateY(-1px);}
.g4b-new-tag{display:inline-flex;align-items:center;gap:6px;flex:none;padding:4px 10px;border-radius:999px;background:var(--g4b-green);font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;color:var(--g4b-ink);}
.g4b-new-dot{width:6px;height:6px;border-radius:50%;background:var(--g4b-ink);box-shadow:0 0 0 0 rgba(11,19,42,.5);}
.g4b-new-title{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.g4b-new svg{flex:none;width:16px;height:16px;fill:none;stroke:var(--g4b-deep);stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;transition:transform .2s var(--g4b-ease);}
.g4b-new:hover svg{transform:translateX(3px);}
.g4b-hero--center .g4b-title{font-size:clamp(34px,4.6vw,58px);line-height:1.08;}
.g4b-mark{position:relative;display:inline-block;color:var(--g4b-deep);background:linear-gradient(90deg,#1f7a2e,#3fa62a 55%,#6fd943);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;white-space:nowrap;}
.g4b-mark::after{content:"";position:absolute;left:2%;right:2%;bottom:-.06em;height:.12em;border-radius:99px;background:linear-gradient(90deg,var(--g4b-green),rgba(111,217,67,.25));transform-origin:left;}
.g4b-hero--center .g4b-lead{max-width:64ch;margin:16px auto 0;}
.g4b-hero--center .g4b-search-wrap{max-width:720px;margin:28px auto 0;}
.g4b-hero--center .g4b-search-hint{text-align:center;}
.g4b-hero--center .g4b-topics{justify-content:center;margin-top:22px;}
@keyframes g4b-pulse{0%{box-shadow:0 0 0 0 rgba(11,19,42,.45);}70%{box-shadow:0 0 0 6px rgba(11,19,42,0);}100%{box-shadow:0 0 0 0 rgba(11,19,42,0);}}
@keyframes g4b-drift{0%{transform:translate3d(0,0,0) scale(1);}50%{transform:translate3d(3%,4%,0) scale(1.06);}100%{transform:translate3d(0,0,0) scale(1);}}
@media (prefers-reduced-motion:no-preference){
  .g4b-glow{animation:g4b-drift 16s ease-in-out infinite;}
  .g4b-new-dot{animation:g4b-pulse 2s ease-out infinite;}
}

.g4b-search-wrap{position:relative;z-index:5;}
.g4b-search{
  position:relative;
  display:flex;
  align-items:center;
  gap:6px;
  height:62px;
  padding:7px 7px 7px 50px;
  background:#fff;
  border:1.5px solid #cfe5d1;
  border-radius:16px;
  box-shadow:0 16px 36px -22px rgba(11,19,42,.4);
  transition:border-color .2s ease,box-shadow .2s ease;
}
.g4b-search:focus-within{border-color:var(--g4b-green);box-shadow:0 0 0 5px rgba(111,217,67,.2),0 16px 36px -22px rgba(11,19,42,.4);}
.g4b-search-icon{position:absolute;left:18px;width:21px;height:21px;fill:none;stroke:var(--g4b-deep);stroke-width:2.2;stroke-linecap:round;}
.g4b-search input{flex:1;min-width:0;height:100%;padding:0;border:0;outline:0;background:transparent;font:inherit;font-size:16px;font-weight:600;color:var(--g4b-ink);}
.g4b-search input::placeholder{color:#7b8594;font-weight:500;}
/* The box's own ring shows focus. The theme gives every focused search
   field a grey border (input[type=search]:focus in style.css), and a
   second outline doubled it. */
.g4b .g4b-search input,.g4b .g4b-search input:focus{border:0;box-shadow:none;outline:none;}
/* The box has its own clear button. */
.g4b-search input::-webkit-search-cancel-button,.g4b-search input::-webkit-search-decoration{-webkit-appearance:none;appearance:none;}
.g4b-search-clear{
  flex:none;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:32px;
  height:32px;
  padding:0;
  border:0;
  border-radius:50%;
  background:#f1f5f2;
  color:var(--g4b-slate);
  cursor:pointer;
  transition:background-color .2s ease,color .2s ease;
}
.g4b-search-clear[hidden]{display:none;}
.g4b-search-clear:hover{background:var(--g4b-tint);color:var(--g4b-deep);}
.g4b-search-clear svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2.4;stroke-linecap:round;}
.g4b-search-key{
  flex:none;
  min-width:26px;
  padding:3px 8px;
  border:1px solid #dbe4dc;
  border-bottom-width:2px;
  border-radius:7px;
  background:#f8faf8;
  font-family:inherit;
  font-size:12.5px;
  font-weight:800;
  line-height:1.2;
  text-align:center;
  color:var(--g4b-slate);
  box-shadow:none;
}
.g4b-search:focus-within .g4b-search-key{display:none;}
.g4b-search-go{
  flex:none;
  height:100%;
  padding:0 22px;
  border:0;
  border-radius:11px;
  background:var(--g4b-green);
  color:var(--g4b-ink);
  font:inherit;
  font-size:15px;
  font-weight:800;
  cursor:pointer;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.45);
  transition:background-color .2s ease;
}
.g4b-search-go:hover{background:var(--g4b-green-hover);}
/* While instant results load, a thin bar runs along the foot of the box. */
.g4b-search::after{
  content:'';
  position:absolute;
  left:16px;
  right:16px;
  bottom:-1.5px;
  height:2px;
  border-radius:2px;
  background:linear-gradient(90deg,transparent,var(--g4b-green),transparent);
  background-size:40% 100%;
  background-repeat:no-repeat;
  opacity:0;
  transition:opacity .2s ease;
}
.g4b-search.is-loading::after{opacity:1;animation:g4b-scan 1s linear infinite;}
@keyframes g4b-scan{from{background-position:-40% 0;}to{background-position:140% 0;}}
.g4b-search-hint{margin:10px 2px 0;font-size:13px;font-weight:600;color:var(--g4b-slate);}

/* Instant results */
.g4b-suggest{
  position:absolute;
  top:calc(100% + 10px);
  left:0;
  right:0;
  z-index:50;
  max-height:min(460px,70vh);
  overflow-y:auto;
  padding:8px;
  background:#fff;
  border:1px solid var(--g4b-line);
  border-radius:16px;
  box-shadow:0 28px 60px -24px rgba(11,19,42,.38),0 2px 6px rgba(11,19,42,.05);
}
.g4b-suggest[hidden]{display:none;}
.g4b-suggest-head{padding:8px 10px 6px;font-size:12.5px;font-weight:800;color:var(--g4b-slate);}
.g4b-suggest-item{display:grid;grid-template-columns:56px minmax(0,1fr);align-items:center;gap:12px;padding:8px 10px;border-radius:11px;color:var(--g4b-ink);}
.g4b-suggest-item:hover,.g4b-suggest-item.is-active{background:var(--g4b-mint);}
.g4b-suggest-thumb{width:56px;height:42px;border-radius:8px;object-fit:cover;background:linear-gradient(135deg,var(--g4b-mint),var(--g4b-mint-2));}
.g4b-suggest-text{display:flex;flex-direction:column;gap:3px;min-width:0;}
.g4b-suggest-title{font-size:14.5px;font-weight:800;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.g4b-suggest-title mark{padding:0 1px;border-radius:3px;background:var(--g4b-mint-2);color:var(--g4b-deep);}
.g4b-suggest-meta{font-size:12.5px;font-weight:600;color:var(--g4b-slate);}
.g4b-suggest-empty{padding:14px 10px;font-size:14px;font-weight:600;color:var(--g4b-slate);}
.g4b-suggest-all{display:block;margin-top:4px;padding:11px 10px;border-top:1px solid var(--g4b-line);border-radius:0 0 10px 10px;font-size:14px;font-weight:800;color:var(--g4b-deep);}
.g4b-suggest-all:hover,.g4b-suggest-all.is-active{background:var(--g4b-mint);color:var(--g4b-deep-hover);}

/* Topics: wrap on wide screens, one swipeable row on small ones. */
.g4b-topics{display:flex;flex-wrap:wrap;gap:10px;margin-top:28px;}
.g4b-topic{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:9px 16px;
  border:1px solid var(--g4b-line);
  border-radius:999px;
  background:#fff;
  font-size:14.5px;
  font-weight:700;
  line-height:1.2;
  color:var(--g4b-ink);
  white-space:nowrap;
  transition:background-color .2s ease,border-color .2s ease,color .2s ease;
}
.g4b-topic span{font-size:12.5px;font-weight:800;color:var(--g4b-slate);}
.g4b-topic:hover{border-color:#c6e9c9;background:var(--g4b-tint);color:var(--g4b-deep);}
.g4b-topic.is-active{border-color:var(--g4b-green);background:var(--g4b-green);color:var(--g4b-ink);box-shadow:0 6px 16px -10px rgba(63,150,25,.8);}
.g4b-topic.is-active span{color:rgba(11,19,42,.7);}

/* --- posts ------------------------------------------------------------- */
.g4b-main{padding:44px 0 72px;}

.g4b-top{display:grid;grid-template-columns:minmax(0,1.85fr) minmax(0,1fr);gap:28px;align-items:stretch;margin-bottom:56px;}
.g4b-top.is-solo{grid-template-columns:minmax(0,1fr);}
/* With no most-read list beside it, the lead story runs across the page,
   image beside the text rather than a banner-sized image above it. */
@media (min-width:1101px){
  .g4b-top.is-solo .g4b-card--featured,.g4b-card.is-across{flex-direction:row;}
  .g4b-top.is-solo .g4b-card--featured .g4b-card-media,.g4b-card.is-across .g4b-card-media{flex:0 0 56%;aspect-ratio:auto;min-height:340px;}
  .g4b-top.is-solo .g4b-card--featured .g4b-card-body,.g4b-card.is-across .g4b-card-body{justify-content:center;}
  .g4b-card.is-across .g4b-card-media{flex-basis:48%;min-height:300px;}
}

.g4b-card{
  position:relative;
  display:flex;
  flex-direction:column;
  min-width:0;
  background:#fff;
  border:1px solid var(--g4b-line);
  border-radius:18px;
  overflow:hidden;
  transition:transform .3s var(--g4b-ease),box-shadow .3s var(--g4b-ease),border-color .3s ease;
}
.g4b-card:hover{transform:translateY(-3px);border-color:#cfe8d2;box-shadow:0 22px 44px -26px rgba(11,19,42,.38);}
.g4b-card-media{display:block;aspect-ratio:16/9;overflow:hidden;background:linear-gradient(135deg,var(--g4b-mint),var(--g4b-mint-2));}
.g4b-card-media img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .6s var(--g4b-ease);}
.g4b-card:hover .g4b-card-media img{transform:scale(1.045);}
.g4b-card-body{display:flex;flex:1;flex-direction:column;align-items:flex-start;gap:12px;padding:22px 24px 24px;}

.g4b-tag{
  position:relative;
  z-index:2;
  display:inline-block;
  padding:5px 11px;
  border-radius:999px;
  background:var(--g4b-tint);
  font-size:12.5px;
  font-weight:800;
  line-height:1.2;
  color:var(--g4b-deep);
}
.g4b-tag:hover{background:var(--g4b-mint-2);color:var(--g4b-deep-hover);}

.g4b-card-title{margin:0;font-size:19px;font-weight:800;line-height:1.32;letter-spacing:-.01em;color:var(--g4b-ink);}
.g4b-card-title a{color:inherit;background-image:linear-gradient(var(--g4b-green),var(--g4b-green));background-size:0 2px;background-repeat:no-repeat;background-position:0 100%;transition:background-size .35s var(--g4b-ease),color .2s ease;}
/* The title link covers the whole card, so the card is one big target; the
   topic and author links sit above it. */
.g4b-card-title a::after{content:'';position:absolute;inset:0;z-index:1;}
.g4b-card:hover .g4b-card-title a{color:var(--g4b-deep);background-size:100% 2px;}
.g4b-card-excerpt{margin:0;font-size:15px;line-height:1.62;color:var(--g4b-slate);display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}

.g4b-card-meta{display:flex;flex-wrap:wrap;align-items:center;gap:8px 16px;margin-top:auto;padding-top:6px;font-size:13.5px;font-weight:600;color:var(--g4b-slate);}
.g4b-facts{display:inline-flex;align-items:center;white-space:nowrap;}
.g4b-facts > span + span::before{content:'';display:inline-block;width:4px;height:4px;margin:0 10px 2px;border-radius:50%;background:#c4ccd6;vertical-align:middle;}
.g4b-author{position:relative;z-index:2;display:inline-flex;align-items:center;gap:8px;color:var(--g4b-ink);font-weight:700;}
.g4b-author a{color:inherit;}
.g4b-author a:hover{color:var(--g4b-deep);}
.g4b-avatar{width:28px;height:28px;border-radius:50%;object-fit:cover;flex:none;}
.g4b-avatar--initial{display:inline-flex;align-items:center;justify-content:center;background:var(--g4b-tint);color:var(--g4b-deep);font-size:12.5px;font-weight:800;}

/* The lead story: larger image and title, and the admin's read-more text. */
.g4b-card--featured .g4b-card-media{aspect-ratio:16/8.4;}
.g4b-card--featured .g4b-card-body{padding:28px 32px 32px;gap:14px;}
.g4b-card--featured .g4b-card-title{font-size:clamp(24px,2.5vw,32px);line-height:1.2;letter-spacing:-.02em;}
.g4b-card--featured .g4b-card-excerpt{font-size:16.5px;-webkit-line-clamp:2;}
.g4b-card--featured .g4b-card-meta{margin-top:4px;}

.g4b-btn{
  position:relative;
  z-index:2;
  display:inline-flex;
  align-items:center;
  padding:12px 22px;
  border-radius:999px;
  background:var(--g4b-green);
  font-size:14.5px;
  font-weight:800;
  color:var(--g4b-ink);
  box-shadow:inset 0 1px 0 rgba(255,255,255,.45),0 8px 18px -10px rgba(63,150,25,.8);
  transition:background-color .2s ease,transform .2s var(--g4b-ease);
}
.g4b-btn:hover{background:var(--g4b-green-hover);color:var(--g4b-ink);transform:translateY(-1px);}

/* Most read: a ranked list, so the numbers carry the order. */
.g4b-popular{display:flex;flex-direction:column;padding:24px 24px 14px;border:1px solid var(--g4b-line);border-radius:18px;background:#fbfcfa;}
/* A label, not a heading (the side box isn't a section of the page). */
.g4b-popular-head{margin:0 0 6px;font-size:20px;font-weight:800;line-height:1.3;color:var(--g4b-ink);}
.g4b-popular ol{display:flex;flex:1;flex-direction:column;justify-content:space-between;margin:0;padding:0;list-style:none;}
.g4b-popular li + li{border-top:1px solid var(--g4b-line);}
.g4b-popular li a{display:grid;grid-template-columns:34px minmax(0,1fr) 64px;align-items:center;gap:14px;padding:16px 0;color:var(--g4b-ink);}
.g4b-rank{font-size:26px;font-weight:800;line-height:1;color:#a7dfa9;transition:color .2s ease;}
.g4b-popular-text{display:flex;flex-direction:column;gap:5px;min-width:0;}
.g4b-popular-title{font-size:15px;font-weight:800;line-height:1.38;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;transition:color .2s ease;}
.g4b-popular-views{font-size:13px;font-weight:600;color:var(--g4b-slate);}
.g4b-popular img{width:64px;height:64px;border-radius:12px;object-fit:cover;}
.g4b-popular li a:hover .g4b-popular-title{color:var(--g4b-deep);}
.g4b-popular li a:hover .g4b-rank{color:var(--g4b-green);}

.g4b-section-head{display:flex;align-items:baseline;justify-content:space-between;gap:16px;margin:0 0 22px;}
.g4b-section-head h2{margin:0;font-size:26px;font-weight:800;letter-spacing:-.015em;color:var(--g4b-ink);}
.g4b-section-head span{font-size:14px;font-weight:700;color:var(--g4b-slate);}

.g4b-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:28px;}
/* On wide screens, six columns so the first page can open with two wide
   cards and then three regular ones; every other page is rows of three. A
   short last row would leave a hole, so two leftover cards share it and a
   single one (marked is-across by the listing) runs the full width. */
@media (min-width:1101px){
  .g4b-grid{grid-template-columns:repeat(6,minmax(0,1fr));}
  .g4b-grid > .g4b-card{grid-column:span 2;}
  .g4b-grid--front > .g4b-card:nth-child(-n+2){grid-column:span 3;}
  .g4b-grid--front > .g4b-card:nth-child(-n+2) .g4b-card-title{font-size:22px;}
  .g4b-grid:not(.g4b-grid--front) > .g4b-card:nth-child(3n+1):nth-last-child(2),
  .g4b-grid:not(.g4b-grid--front) > .g4b-card:nth-child(3n+1):nth-last-child(2) + .g4b-card,
  .g4b-grid--front > .g4b-card:nth-child(3n):nth-last-child(2),
  .g4b-grid--front > .g4b-card:nth-child(3n):nth-last-child(2) + .g4b-card{grid-column:span 3;}
  .g4b-grid > .g4b-card.is-across{grid-column:1 / -1;}
}

/* --- show more -------------------------------------------------------- */
.g4b-grid + .g4b-grid{margin-top:28px;}
.g4b-more{display:flex;justify-content:center;margin-top:44px;}
.g4b-more-btn{
  display:inline-flex;
  align-items:center;
  gap:10px;
  padding:14px 28px;
  border:1.5px solid #c6e9c9;
  border-radius:999px;
  background:#fff;
  font:inherit;
  font-size:15px;
  font-weight:800;
  color:var(--g4b-ink);
  cursor:pointer;
  transition:background-color .2s ease,border-color .2s ease,transform .2s var(--g4b-ease);
}
.g4b-more-btn:hover{border-color:var(--g4b-green);background:var(--g4b-mint);transform:translateY(-1px);}
.g4b-more-btn:hover{color:var(--g4b-ink);}
.g4b-more-btn.is-loading{cursor:wait;opacity:.75;transform:none;pointer-events:none;}
.g4b-more-left{padding:3px 9px;border-radius:999px;background:var(--g4b-tint);font-size:12.5px;color:var(--g4b-deep);}

/* --- FAQs -------------------------------------------------------------- */
.g4b-faq{padding:0 0 72px;}
.g4b-faq-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.7fr);gap:56px;align-items:start;padding-top:56px;border-top:1px solid var(--g4b-line);}
.g4b-faq-intro h2{margin:0;font-size:clamp(26px,2.6vw,34px);font-weight:800;line-height:1.15;letter-spacing:-.02em;color:var(--g4b-ink);}
.g4b-faq-intro p{margin:12px 0 22px;font-size:16px;line-height:1.6;color:var(--g4b-slate);}
.g4b-btn--ghost{background:#fff;border:1.5px solid #c6e9c9;box-shadow:none;}
.g4b-btn--ghost:hover{background:var(--g4b-mint);border-color:var(--g4b-green);}
.g4b-faq-list{border-top:1px solid var(--g4b-line);}
.g4b-faq-item{border-bottom:1px solid var(--g4b-line);}
.g4b-faq-item summary{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:20px;
  padding:20px 4px;
  list-style:none;
  cursor:pointer;
  font-size:17px;
  font-weight:800;
  line-height:1.4;
  color:var(--g4b-ink);
  transition:color .2s ease;
}
.g4b-faq-item summary::-webkit-details-marker{display:none;}
.g4b-faq-item summary:hover{color:var(--g4b-deep);}
.g4b-faq-item summary:focus-visible{outline:2px solid var(--g4b-deep);outline-offset:2px;border-radius:6px;}
.g4b-faq-item summary svg{flex:none;width:30px;height:30px;padding:7px;border-radius:50%;background:var(--g4b-tint);fill:none;stroke:var(--g4b-deep);stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;transition:transform .3s var(--g4b-ease),background-color .2s ease;}
.g4b-faq-item[open] summary svg{transform:rotate(180deg);background:var(--g4b-green);stroke:var(--g4b-ink);}
.g4b-faq-answer{padding:0 52px 22px 4px;font-size:15.5px;line-height:1.7;color:var(--g4b-slate);}
.g4b-faq-answer p{margin:0 0 10px;}
.g4b-faq-answer p:last-child{margin-bottom:0;}
.g4b-faq-answer a{color:var(--g4b-deep);text-decoration:underline;}

/* --- nothing to show --------------------------------------------------- */
.g4b-empty{max-width:560px;margin:24px auto;padding:44px 32px;text-align:center;border:1px dashed #c6e9c9;border-radius:18px;background:var(--g4b-mint);}
.g4b-empty h2{margin:0 0 8px;font-size:24px;font-weight:800;color:var(--g4b-ink);}
.g4b-empty p{margin:0 0 22px;font-size:16px;color:var(--g4b-slate);}

/* --- newsletter -------------------------------------------------------- */
.g4b-news{padding:0 0 80px;}
.g4b-news-card{
  position:relative;
  overflow:hidden;
  display:grid;
  grid-template-columns:minmax(0,1fr) minmax(320px,460px);
  gap:32px;
  align-items:center;
  padding:40px 44px;
  border:1px solid #cdeccf;
  border-radius:22px;
  background:linear-gradient(135deg,var(--g4b-mint) 0%,var(--g4b-mint-2) 60%,#e6f7e9 100%);
  color:var(--g4b-ink);
}
.g4b-news-card::after{
  content:'';
  position:absolute;
  inset:0;
  background-image:radial-gradient(rgba(31,122,46,.16) 1px,transparent 1.2px);
  background-size:18px 18px;
  -webkit-mask-image:linear-gradient(270deg,#000,transparent 70%);
  mask-image:linear-gradient(270deg,#000,transparent 70%);
  pointer-events:none;
}
.g4b-news-copy,.g4b-news-form{position:relative;z-index:1;}
.g4b-news h2{margin:0;font-size:clamp(24px,2.4vw,30px);font-weight:800;line-height:1.2;letter-spacing:-.015em;color:var(--g4b-ink);}
.g4b-news-copy p{margin:10px 0 0;font-size:16px;line-height:1.6;color:#475569;}
.g4b-news-form{display:flex;flex-wrap:wrap;gap:10px;}
.g4b-news-form input{flex:1 1 220px;min-width:0;height:52px;padding:0 18px;border:1px solid #c6e3c8;border-radius:12px;background:#fff;font:inherit;font-size:15.5px;color:var(--g4b-ink);outline:0;}
.g4b-news-form input:focus{border-color:var(--g4b-deep);box-shadow:0 0 0 4px rgba(111,217,67,.22);}
.g4b-news-form button{height:52px;padding:0 24px;border:0;border-radius:12px;background:var(--g4b-green);font:inherit;font-size:15px;font-weight:800;color:var(--g4b-ink);cursor:pointer;box-shadow:inset 0 1px 0 rgba(255,255,255,.45),0 8px 18px -10px rgba(63,150,25,.8);transition:background-color .2s ease;}
.g4b-news-form button:hover{background:var(--g4b-green-hover);}
.g4b-news-form button[disabled]{opacity:.7;cursor:wait;}
.g4b-news-msg{flex-basis:100%;min-height:1.4em;margin:2px 0 0;font-size:14px;font-weight:700;color:var(--g4b-deep);}
.g4b-news-msg.is-error{color:#b91c1c;}

/* --- smaller screens --------------------------------------------------- */
@media (max-width:1100px){
  .g4b-top{grid-template-columns:minmax(0,1fr);}
  .g4b-popular ol{justify-content:flex-start;}
}
@media (max-width:900px){
  .g4b-search-key{display:none;}
  .g4b-faq-grid{grid-template-columns:minmax(0,1fr);gap:24px;padding-top:40px;}
  .g4b-news-card{grid-template-columns:minmax(0,1fr);padding:32px 28px;}
}
@media (max-width:640px){
  .g4b-wrap{padding:0 16px;}
  .g4b-hero{padding:24px 0 22px;}
  .g4b-lead{font-size:16px;}
  .g4b-topics,.g4b-hero--center .g4b-topics{flex-wrap:nowrap;justify-content:flex-start;overflow-x:auto;margin:20px -16px 0;padding:0 16px 4px;scrollbar-width:none;}
  .g4b-hero--center{padding:20px 0 24px;}
  .g4b-hero--center .g4b-hero-copy{margin-top:14px;}
  .g4b-new{margin-bottom:14px;font-size:13px;}
  .g4b-hero--center .g4b-title{font-size:32px;}
  .g4b-hero--center .g4b-search-wrap{margin-top:22px;}
  .g4b-topics::-webkit-scrollbar{display:none;}
  .g4b-main{padding:28px 0 56px;}
  .g4b-top{margin-bottom:40px;}
  .g4b-grid,.g4b-grid--front{grid-template-columns:minmax(0,1fr);gap:20px;}
  .g4b-card-body,.g4b-card--featured .g4b-card-body{padding:18px 18px 20px;}
  .g4b-card--featured .g4b-card-media{aspect-ratio:16/10;}
  .g4b-section-head h2{font-size:22px;}
  .g4b-news{padding-bottom:56px;}
  .g4b-search{height:56px;padding-left:44px;}
  .g4b-search-icon{left:15px;}
  .g4b-search-go{padding:0 16px;}
  .g4b-search-hint{display:none;}
  .g4b-faq{padding-bottom:56px;}
  .g4b-faq-item summary{font-size:16px;padding:18px 2px;}
  .g4b-faq-answer{padding-right:8px;}
}
@media (prefers-reduced-motion:reduce){
  .g4b *,.g4b *::after{transition-duration:.01ms !important;}
  .g4b-card:hover,.g4b-btn:hover{transform:none;}
  .g4b-card:hover .g4b-card-media img{transform:none;}
  .g4b-search.is-loading::after{animation:none;}
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Newsletter sign-up, posted to the same route as the old sidebar widget
  // (frontend.subscribe.newsletter), so addresses land in Newsletter Manage.
  var form = document.querySelector('.g4b-news-form');
  if (!form) { return; }
  var msg = form.querySelector('.g4b-news-msg');
  var button = form.querySelector('button');

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var email = form.email.value.trim();
    msg.classList.remove('is-error');
    if (!/^\S+@\S+\.\S+$/.test(email)) {
      msg.textContent = 'Enter a valid email address.';
      msg.classList.add('is-error');
      form.email.focus();
      return;
    }
    button.disabled = true;
    fetch(form.action, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: new FormData(form)
    }).then(function (res) {
      return res.json().catch(function () { return {}; }).then(function (data) { return { ok: res.ok, data: data }; });
    }).then(function (r) {
      if (r.ok) {
        msg.textContent = 'You’re subscribed.';
        form.reset();
        if (typeof gtag === 'function') { gtag('event', 'newsletter_signup', { location: 'blog' }); }
        return;
      }
      // Laravel's validation errors, e.g. "This Email Already Registered".
      var errors = r.data && r.data.errors && r.data.errors.email;
      msg.textContent = errors ? errors[0] : 'That didn’t go through. Please try again.';
      msg.classList.add('is-error');
    }).catch(function () {
      msg.textContent = 'That didn’t go through. Please try again.';
      msg.classList.add('is-error');
    }).then(function () { button.disabled = false; });
  });
});
document.addEventListener('DOMContentLoaded', function () {
  // Instant results under the search box, from the blog search route asked
  // for JSON (FrontendController::blog_search_page). With nothing typed it
  // offers the most-read posts.
  var form = document.querySelector('.g4b-search');
  if (!form) { return; }
  var input = form.querySelector('input[type="search"]');
  var panel = form.querySelector('.g4b-suggest');
  var clear = form.querySelector('.g4b-search-clear');
  var endpoint = form.getAttribute('data-suggest-url');
  var cache = {}, items = [], active = -1, timer = null, seq = 0;

  function el(tag, cls, text) {
    var node = document.createElement(tag);
    if (cls) { node.className = cls; }
    if (text) { node.textContent = text; }
    return node;
  }

  // The title with each typed word marked; built from text nodes, never HTML.
  function highlighted(title, query) {
    var box = el('span', 'g4b-suggest-title');
    var words = query.toLowerCase().split(/\s+/).filter(function (w) { return w.length > 1; });
    if (!words.length) { box.textContent = title; return box; }
    var pattern = new RegExp('(' + words.map(function (w) { return w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }).join('|') + ')', 'ig');
    title.split(pattern).forEach(function (part) {
      if (!part) { return; }
      box.appendChild(words.indexOf(part.toLowerCase()) !== -1 ? el('mark', '', part) : document.createTextNode(part));
    });
    return box;
  }

  function setActive(i) {
    active = i;
    items.forEach(function (item, n) {
      item.classList.toggle('is-active', n === i);
      item.setAttribute('aria-selected', n === i ? 'true' : 'false');
    });
    if (i >= 0) { input.setAttribute('aria-activedescendant', items[i].id); }
    else { input.removeAttribute('aria-activedescendant'); }
  }

  function setOpen(open) {
    panel.hidden = !open;
    input.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (!open) { setActive(-1); }
  }

  function render(data) {
    panel.textContent = '';
    items = [];
    // The list page reuses this box: data-noun names what it searches.
    var noun = form.getAttribute('data-noun') || 'article';
    var heading = data.popular ? (form.getAttribute('data-popular-label') || 'Most read') : (data.total === 1 ? '1 ' + noun : data.total + ' ' + noun + 's');
    if (data.results.length) { panel.appendChild(el('div', 'g4b-suggest-head', heading)); }
    else { panel.appendChild(el('div', 'g4b-suggest-empty', 'No ' + noun + 's match “' + data.query + '”. Try a shorter search.')); }

    data.results.forEach(function (post, i) {
      var link = el('a', 'g4b-suggest-item');
      link.href = post.url;
      link.id = 'g4b-suggest-' + i;
      link.setAttribute('role', 'option');
      var thumb;
      if (post.image) { thumb = el('img', 'g4b-suggest-thumb'); thumb.src = post.image; thumb.alt = ''; thumb.width = 56; thumb.height = 42; }
      else { thumb = el('span', 'g4b-suggest-thumb'); }
      var text = el('span', 'g4b-suggest-text');
      text.appendChild(highlighted(post.title, data.popular ? '' : data.query));
      text.appendChild(el('span', 'g4b-suggest-meta', [post.topic, post.date].filter(Boolean).join('  ·  ')));
      link.appendChild(thumb);
      link.appendChild(text);
      panel.appendChild(link);
      items.push(link);
    });

    if (!data.popular && data.total > data.results.length) {
      var all = el('a', 'g4b-suggest-all', 'See all ' + data.total + ' results for “' + data.query + '”');
      all.href = data.all_url;
      all.id = 'g4b-suggest-all';
      all.setAttribute('role', 'option');
      panel.appendChild(all);
      items.push(all);
    }
    setOpen(true);
  }

  function lookup(query) {
    if (cache[query]) { render(cache[query]); return; }
    var mine = ++seq;
    form.classList.add('is-loading');
    fetch(endpoint + '?' + (form.getAttribute('data-param') || 'search') + '=' + encodeURIComponent(query), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
      .then(function (data) {
        cache[query] = data;
        // Only the latest request may draw, and only while the box has focus.
        if (mine === seq && document.activeElement === input) { render(data); }
      })
      .catch(function () {})
      .then(function () { if (mine === seq) { form.classList.remove('is-loading'); } });
  }

  function update() {
    var query = input.value.trim();
    clear.hidden = !input.value;
    clearTimeout(timer);
    if (!query) { lookup(''); return; }
    if (query.length < 2) { setOpen(false); return; }
    timer = setTimeout(function () { lookup(query); }, 180);
  }

  input.addEventListener('input', update);
  input.addEventListener('focus', update);
  input.addEventListener('keydown', function (e) {
    // Escape in a search box also clears it natively, which would reopen the
    // list straight away; here it only closes the list.
    if (e.key === 'Escape') { if (!panel.hidden) { e.preventDefault(); } setOpen(false); return; }
    if (panel.hidden || !items.length) { return; }
    if (e.key === 'ArrowDown') { e.preventDefault(); setActive(active + 1 < items.length ? active + 1 : 0); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); setActive(active > 0 ? active - 1 : items.length - 1); }
    else if (e.key === 'Enter' && active >= 0) { e.preventDefault(); window.location.href = items[active].href; }
  });
  form.addEventListener('focusout', function (e) { if (!form.contains(e.relatedTarget)) { setOpen(false); } });
  document.addEventListener('click', function (e) { if (!form.contains(e.target)) { setOpen(false); } });
  clear.addEventListener('click', function () { input.value = ''; input.focus(); update(); });
  form.addEventListener('submit', function () {
    if (typeof gtag === 'function') { gtag('event', 'search', { search_term: input.value.trim() }); }
  });
  clear.hidden = !input.value;

  // "/" jumps to the search box, unless the reader is typing somewhere else.
  document.addEventListener('keydown', function (e) {
    if (e.key !== '/' || e.metaKey || e.ctrlKey || e.altKey) { return; }
    var t = e.target;
    if (t.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName)) { return; }
    e.preventDefault();
    input.focus();
    input.select();
  });
});

document.addEventListener('DOMContentLoaded', function () {
  // "Show more articles": a link to the next page, which this turns into
  // adding that page's grid here instead, so a reader goes through every post
  // on one page and the address never changes. If anything fails it simply
  // follows the link.
  var button = document.querySelector('.g4b-more-btn');
  if (!button) { return; }
  var results = button.closest('.g4b-results');
  var busy = false;

  button.addEventListener('click', function (e) {
    var next = button.getAttribute('href');
    if (!next || e.metaKey || e.ctrlKey || e.shiftKey) { return; }   // a new tab still opens the page
    e.preventDefault();
    if (busy) { return; }
    busy = true;
    button.classList.add('is-loading');
    button.textContent = 'Loading…';
    fetch(next, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (res) { return res.ok ? res.text() : Promise.reject(); })
      .then(function (html) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var grid = doc.querySelector('.g4b-results .g4b-grid');
        if (!grid) { throw new Error('no grid'); }
        var added = document.importNode(grid, true);
        results.insertBefore(added, button.parentNode);

        var count = results.querySelector('.g4b-count'), fetched = doc.querySelector('.g4b-count');
        if (count && fetched) {
          count.textContent = count.dataset.first + '–' + fetched.dataset.last + ' of ' + Number(fetched.dataset.total).toLocaleString() + ' articles';
        }

        var nextButton = doc.querySelector('.g4b-more-btn');
        if (nextButton) {
          button.setAttribute('href', nextButton.getAttribute('href'));
          button.innerHTML = nextButton.innerHTML;
          button.classList.remove('is-loading');
          busy = false;
        } else {
          button.parentNode.remove();
        }

        // Move keyboard and screen-reader users to the first new article.
        var first = added.querySelector('.g4b-card-title a');
        if (first) { first.focus({ preventScroll: true }); }
        if (typeof gtag === 'function') { gtag('event', 'blog_show_more', { page: next }); }
      })
      .catch(function () { window.location.href = next; });
  });
});
</script>
