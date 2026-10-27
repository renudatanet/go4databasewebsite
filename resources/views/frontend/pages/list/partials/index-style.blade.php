{{-- Styles for the lists index (list/list), on top of the blog's shared
     styles and tokens (blog/partials/style). Inline so it deploys as a view;
     list.css is left to the single list pages. --}}
<style>
.g4l-badge{display:inline-flex;align-items:center;gap:8px;margin:0 0 18px;padding:6px 14px;border:1px solid #cdeccf;border-radius:999px;background:rgba(255,255,255,.85);font-size:12.5px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--g4b-deep);box-shadow:0 6px 18px -12px rgba(31,122,46,.5);}
.g4l-badge .g4b-new-dot{background:var(--g4b-green);}
.g4l-holds{display:flex;flex-wrap:wrap;justify-content:center;gap:8px 20px;margin:0;padding:0;list-style:none;font-size:14px;font-weight:700;color:var(--g4b-text);}
.g4l-holds li{display:inline-flex;align-items:center;gap:7px;}
.g4l-holds svg{flex:none;width:18px;height:18px;padding:3px;border-radius:50%;background:var(--g4b-tint);fill:none;stroke:var(--g4b-deep);stroke-width:3.2;stroke-linecap:round;stroke-linejoin:round;}
.g4l-topic-signup{border-color:#cdeccf;background:var(--g4b-mint);color:var(--g4b-deep);}
.g4l-topic-signup::after{content:"→";margin-left:2px;}

.g4l-main{padding-top:40px;padding-bottom:56px;}
.g4l-section + .g4l-section{margin-top:44px;}
.g4l-section{scroll-margin-top:90px;}

/* Search results */
.g4l-results{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
.g4l-result{display:flex;align-items:center;gap:12px;padding:16px 18px;border:1px solid var(--g4b-line);border-radius:14px;background:#fff;color:var(--g4b-ink);transition:border-color .2s ease,box-shadow .2s ease,transform .2s var(--g4b-ease);}
.g4l-result:hover{border-color:#c6e9c9;box-shadow:0 12px 28px -20px rgba(11,19,42,.45);transform:translateY(-1px);color:var(--g4b-ink);}
.g4l-result-title{flex:1;min-width:0;font-size:15.5px;font-weight:800;line-height:1.35;}
.g4l-result .g4b-tag{flex:none;}
.g4l-arrow{flex:none;width:18px;height:18px;fill:none;stroke:var(--g4b-deep);stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;transition:transform .2s var(--g4b-ease);}
.g4l-result:hover .g4l-arrow{transform:translateX(3px);}
.g4l-note{margin:14px 0 0;font-size:14px;font-weight:600;color:var(--g4b-slate);}
.g4l-empty{padding:36px 28px;border:1px dashed #cdeccf;border-radius:18px;background:#fbfcfa;text-align:center;}
.g4l-empty h3{margin:0 0 8px;font-size:20px;font-weight:800;color:var(--g4b-ink);}
.g4l-empty p{max-width:56ch;margin:0 auto 18px;font-size:15.5px;line-height:1.6;color:var(--g4b-slate);}

/* Industry cards: a mint head with the icon and count, the sample lists
   as tiles of up to two lines (full title on hover), equal height per row,
   and an all-lists bar. */
.g4l-head{margin:0 0 20px;}
.g4l-head h2{margin:0;font-size:clamp(23px,2.1vw,28px);font-weight:800;line-height:1.25;letter-spacing:-.015em;color:var(--g4b-ink);}
.g4l-head p{margin:6px 0 0;font-size:15px;font-weight:600;color:var(--g4b-slate);}
.g4l-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px;}
.g4l-card{display:flex;flex-direction:column;overflow:hidden;border:1px solid var(--g4b-line);border-radius:20px;background:#fff;scroll-margin-top:90px;transition:border-color .2s ease,box-shadow .25s ease,transform .25s var(--g4b-ease);}
.g4l-card:hover{border-color:#c6e9c9;box-shadow:0 24px 48px -32px rgba(11,19,42,.45);transform:translateY(-2px);}
.g4l-card:target{border-color:var(--g4b-green);box-shadow:0 0 0 4px rgba(111,217,67,.18);}
.g4l-card-head{display:flex;align-items:center;gap:14px;padding:20px 22px;border-bottom:1px solid #e3f2e5;background:linear-gradient(135deg,var(--g4b-mint) 0%,#fff 85%);}
.g4l-icon{flex:none;display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:14px;background:#fff;color:var(--g4b-deep);font-size:20px;box-shadow:0 6px 16px -10px rgba(31,122,46,.55),inset 0 0 0 1px #d6efd8;}
.g4l-icon svg,.g4l-chip-icon svg{width:22px;height:22px;fill:none;stroke:currentColor;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;}
.g4l-card-title{min-width:0;}
.g4l-card-title h3{margin:0;font-size:20px;font-weight:800;line-height:1.25;color:var(--g4b-ink);}
.g4l-card-title span{display:block;margin-top:2px;font-size:13.5px;font-weight:700;color:var(--g4b-deep);}
.g4l-samples{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:0;padding:18px 22px;list-style:none;}
.g4l-samples a{display:flex;align-items:center;gap:10px;height:100%;padding:11px 12px;border:1px solid var(--g4b-line);border-radius:12px;background:#fff;font-size:14px;font-weight:700;line-height:1.3;color:var(--g4b-ink);transition:border-color .2s ease,background-color .2s ease,color .2s ease;}
.g4l-samples li{display:flex;}
.g4l-samples a{flex:1;min-height:58px;}
.g4l-samples a span{flex:1;min-width:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.g4l-mail{flex:none;width:28px;height:28px;padding:6px;border-radius:8px;background:var(--g4b-tint);fill:none;stroke:var(--g4b-deep);stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.g4l-go{flex:none;width:15px;height:15px;fill:none;stroke:var(--g4b-deep);stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;opacity:0;transform:translateX(-4px);transition:opacity .2s ease,transform .2s var(--g4b-ease);}
.g4l-samples a:hover{border-color:#c6e9c9;background:var(--g4b-mint);color:var(--g4b-deep);}
.g4l-samples a:hover .g4l-go,.g4l-samples a:focus-visible .g4l-go{opacity:1;transform:none;}
.g4l-all{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:auto;padding:15px 22px;border-top:1px solid var(--g4b-line);background:#fbfcfa;font-size:14.5px;font-weight:800;color:var(--g4b-deep);transition:background-color .2s ease,color .2s ease;}
.g4l-all svg{flex:none;width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;transition:transform .2s var(--g4b-ease);}
.g4l-all:hover{background:var(--g4b-mint);color:var(--g4b-deep-hover);}
.g4l-all:hover svg{transform:translateX(4px);}

/* Industries with no lists yet */
.g4l-pipeline{margin-top:24px;padding:22px 24px;border:1px solid var(--g4b-line);border-radius:18px;background:#fbfcfa;}
.g4l-pipeline-head{display:flex;flex-wrap:wrap;align-items:baseline;gap:4px 10px;margin:0 0 14px;}
.g4l-pipeline-head strong{font-size:16px;font-weight:800;color:var(--g4b-ink);}
.g4l-pipeline-head span{font-size:14px;font-weight:600;color:var(--g4b-slate);}
.g4l-chips{display:flex;flex-wrap:wrap;gap:8px;margin:0;padding:0;list-style:none;}
.g4l-chips li{display:inline-flex;align-items:center;gap:8px;padding:7px 14px 7px 8px;border:1px solid var(--g4b-line);border-radius:999px;background:#fff;font-size:14px;font-weight:700;color:var(--g4b-text);}
.g4l-chip-icon{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;background:var(--g4b-tint);color:var(--g4b-deep);font-size:12px;}
.g4l-chip-icon svg{width:15px;height:15px;}

/* Call to action, the blog's mint card */
.g4l-cta{position:relative;overflow:hidden;display:grid;grid-template-columns:minmax(0,1fr) auto;gap:28px;align-items:center;margin-top:48px;padding:36px 40px;border:1px solid #cdeccf;border-radius:22px;background:linear-gradient(135deg,var(--g4b-mint) 0%,var(--g4b-mint-2) 60%,#e6f7e9 100%);}
.g4l-cta::after{content:"";position:absolute;right:-60px;top:-60px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(111,217,67,.28),transparent 70%);pointer-events:none;}
.g4l-cta-copy,.g4l-cta-btns{position:relative;z-index:1;}
.g4l-cta h2{margin:0;font-size:clamp(23px,2.2vw,28px);font-weight:800;line-height:1.25;letter-spacing:-.015em;color:var(--g4b-ink);}
.g4l-cta p{max-width:60ch;margin:8px 0 0;font-size:16px;line-height:1.6;color:#475569;}
.g4l-cta-notes{justify-content:flex-start;margin-top:14px;}
.g4l-cta-btns{display:flex;flex-direction:column;gap:10px;min-width:240px;}
.g4l-cta-btns .g4b-btn{justify-content:center;padding:13px 24px;font-size:15px;}

@media (max-width:900px){
  .g4l-grid,.g4l-results{grid-template-columns:minmax(0,1fr);}
  .g4l-cta{grid-template-columns:minmax(0,1fr);padding:28px 24px;}
  .g4l-cta-btns{flex-direction:row;flex-wrap:wrap;min-width:0;}
}
@media (max-width:640px){
  .g4l-badge{margin-bottom:14px;font-size:11.5px;}
  .g4l-holds{gap:6px 14px;font-size:13px;}
  .g4l-main{padding-top:28px;padding-bottom:40px;}
  .g4l-section + .g4l-section{margin-top:32px;}
  .g4l-card-head{padding:16px 18px;}
  .g4l-card-title h3{font-size:18px;}
  .g4l-samples{grid-template-columns:minmax(0,1fr);gap:8px;padding:14px 18px;}
  .g4l-all{padding:14px 18px;}
  .g4l-pipeline{padding:18px;}
  .g4l-cta{margin-top:36px;padding:24px 20px;}
  .g4l-cta-btns .g4b-btn{flex:1 1 100%;}
}
</style>
