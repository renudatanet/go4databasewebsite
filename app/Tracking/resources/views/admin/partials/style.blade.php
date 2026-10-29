{{-- Tracking screens' styles. Inline, like the site's other partials, so they
     deploy with the views. Everything is scoped to .g4t. --}}
<style>
.g4t{
  --ink:#16211a; --muted:#5f6c64; --faint:#8a968e; --line:#e2e8e3; --wash:#f5f8f5; --card:#fff;
  --accent:#3d7a1e; --accent-ink:#2c5c14; --accent-soft:#e7f1df; --accent-mid:#9cc57f;
  --up:#2e7a32; --down:#b3412d; --warn:#946000; --warn-soft:#fbf1dc; --info-soft:#e8eef8;
  --radius:10px; --mono:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;
  color:var(--ink); font-size:14px; line-height:1.5;
}
.g4t *{box-sizing:border-box}
.g4t a{color:var(--accent-ink)}
.g4t a:hover{color:var(--accent)}
.g4t .num,.g4t td.n,.g4t th.n{font-variant-numeric:tabular-nums;text-align:right;white-space:nowrap}
.g4t-muted{color:var(--muted)}
.g4t-faint{color:var(--faint);font-size:12.5px}

/* Screen navigation */
.g4t-nav{display:flex;gap:2px;flex-wrap:wrap;border-bottom:1px solid var(--line);margin:0 0 18px}
.g4t-nav a{display:flex;align-items:center;gap:7px;padding:10px 14px;color:var(--muted);font-weight:600;border-bottom:2px solid transparent;margin-bottom:-1px;text-decoration:none}
.g4t-nav a:hover{color:var(--ink)}
.g4t-nav a.is-active{color:var(--accent-ink);border-bottom-color:var(--accent)}
.g4t-nav .g4t-live-dot{width:8px;height:8px;border-radius:50%;background:#2fb344;box-shadow:0 0 0 3px rgba(47,179,68,.18)}

/* Period and filters */
.g4t-toolbar{display:flex;flex-wrap:wrap;gap:10px 12px;align-items:flex-end;background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:12px 14px;margin-bottom:18px}
.g4t-toolbar label{display:flex;flex-direction:column;gap:4px;margin:0;font-size:11.5px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:var(--faint)}
.g4t-toolbar select,.g4t-toolbar input[type=date],.g4t-toolbar input[type=search]{height:34px;border:1px solid var(--line);border-radius:7px;padding:0 10px;font-size:13.5px;color:var(--ink);background:#fff;min-width:0;text-transform:none;letter-spacing:0;font-weight:400}
.g4t-toolbar .g4t-check{flex-direction:row;align-items:center;gap:6px;height:34px;text-transform:none;letter-spacing:0;font-size:13px;color:var(--muted);font-weight:500}
.g4t-toolbar .g4t-period{font-size:13px;color:var(--muted);margin-left:auto;align-self:center}
.g4t-btn{display:inline-flex;align-items:center;gap:6px;height:34px;padding:0 14px;border-radius:7px;border:1px solid var(--accent);background:var(--accent);color:#fff!important;font-weight:600;font-size:13.5px;cursor:pointer;text-decoration:none!important;white-space:nowrap}
.g4t-btn:hover{background:var(--accent-ink);border-color:var(--accent-ink)}
.g4t-btn.is-quiet{background:#fff;color:var(--ink)!important;border-color:var(--line)}
.g4t-btn.is-quiet:hover{border-color:var(--accent-mid)}
.g4t-btn.is-danger{background:#fff;color:var(--down)!important;border-color:#e9c3bb}
.g4t-btn:focus-visible,.g4t-nav a:focus-visible,.g4t a:focus-visible{outline:2px solid var(--accent);outline-offset:2px}

/* Cards and grid */
.g4t-grid{display:grid;gap:16px;grid-template-columns:repeat(12,minmax(0,1fr));margin-bottom:16px}
.g4t-span-12{grid-column:span 12}.g4t-span-8{grid-column:span 8}.g4t-span-6{grid-column:span 6}.g4t-span-4{grid-column:span 4}
@media (max-width:1199px){.g4t-span-8,.g4t-span-4{grid-column:span 12}}
@media (max-width:991px){.g4t-span-6{grid-column:span 12}}
.g4t-card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);min-width:0}
.g4t-card-head{display:flex;align-items:baseline;justify-content:space-between;gap:12px;padding:14px 16px 0}
.g4t-card-head h3{font-size:15px;font-weight:700;margin:0;color:var(--ink)}
.g4t-card-head a{font-size:13px;font-weight:600;white-space:nowrap}
.g4t-card-body{padding:12px 16px 16px}
.g4t-card-note{font-size:12.5px;color:var(--faint);padding:0 16px 14px;margin-top:-6px}

/* Headline figures */
.g4t-kpis{display:grid;gap:12px;grid-template-columns:repeat(4,minmax(0,1fr));margin-bottom:16px}
.g4t-kpis.is-3{grid-template-columns:repeat(3,minmax(0,1fr))}
@media (max-width:991px){.g4t-kpis,.g4t-kpis.is-3{grid-template-columns:repeat(2,minmax(0,1fr))}}
.g4t-kpi{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:12px 14px}
.g4t-kpi-label{font-size:12.5px;color:var(--muted);font-weight:600}
.g4t-kpi-value{font-size:26px;font-weight:700;letter-spacing:-.01em;font-variant-numeric:tabular-nums;line-height:1.2;margin-top:2px}
.g4t-kpi-foot{display:flex;gap:6px;align-items:center;font-size:12px;color:var(--faint);margin-top:2px}
.g4t-change{font-weight:700;font-size:12px;padding:1px 6px;border-radius:999px;background:var(--wash);color:var(--muted)}
.g4t-change.is-good{background:#e5f3e6;color:var(--up)}
.g4t-change.is-bad{background:#f8e6e2;color:var(--down)}
.g4t-kpi.is-live .g4t-kpi-value{color:var(--accent-ink)}

/* Tables */
.g4t-table-wrap{overflow-x:auto}
.g4t table{width:100%;border-collapse:collapse}
.g4t th{font-size:11.5px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--faint);padding:9px 12px;border-bottom:1px solid var(--line);text-align:left;white-space:nowrap;background:var(--wash)}
.g4t td{padding:10px 12px;border-bottom:1px solid var(--line);vertical-align:middle}
.g4t tr:last-child td{border-bottom:0}
.g4t tbody tr:hover td{background:#fafcfa}
.g4t td .g4t-sub{display:block;color:var(--faint);font-size:12.5px;max-width:420px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.g4t td.g4t-path{font-family:var(--mono);font-size:12.5px;max-width:420px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.g4t td.g4t-strong{font-weight:600}

/* Bar lists: label, value, and a share bar behind */
.g4t-bars{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:4px}
.g4t-bars li{position:relative;display:flex;justify-content:space-between;gap:10px;padding:6px 10px;border-radius:6px;overflow:hidden;font-size:13.5px}
.g4t-bars li::before{content:"";position:absolute;inset:0 auto 0 0;width:var(--share,0%);background:var(--accent-soft);z-index:0}
.g4t-bars li > *{position:relative;z-index:1;min-width:0}
.g4t-bars .g4t-bar-name{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.g4t-bars .g4t-bar-value{font-variant-numeric:tabular-nums;font-weight:600;white-space:nowrap}

/* Chips, pills, tabs */
.g4t-pill{display:inline-block;font-size:11.5px;font-weight:700;padding:2px 8px;border-radius:999px;background:var(--wash);color:var(--muted);white-space:nowrap}
.g4t-pill.is-business{background:var(--info-soft);color:#2a4f8f}
.g4t-pill.is-hosting{background:var(--warn-soft);color:var(--warn)}
.g4t-pill.is-known{background:var(--accent-soft);color:var(--accent-ink)}
.g4t-pill.is-app{background:#efe7f8;color:#5b3a8c}
.g4t-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
.g4t-tabs a{padding:5px 12px;border-radius:999px;border:1px solid var(--line);background:#fff;color:var(--muted);font-weight:600;font-size:13px;text-decoration:none}
.g4t-tabs a.is-active{background:var(--accent);border-color:var(--accent);color:#fff}
.g4t-flag{font-size:16px;line-height:1;margin-right:6px;vertical-align:-1px}

/* Empty state */
.g4t-empty{padding:28px 16px;text-align:center;color:var(--muted)}
.g4t-empty strong{display:block;color:var(--ink);font-size:15px;margin-bottom:4px}

/* Journey timeline */
.g4t-visit{border:1px solid var(--line);border-radius:var(--radius);background:var(--card);margin-bottom:12px}
.g4t-visit summary{display:flex;flex-wrap:wrap;gap:6px 14px;align-items:baseline;padding:12px 16px;cursor:pointer;list-style:none}
.g4t-visit summary::-webkit-details-marker{display:none}
.g4t-visit summary h4{font-size:14.5px;font-weight:700;margin:0}
.g4t-steps{list-style:none;margin:0;padding:4px 16px 14px}
.g4t-steps li{display:grid;grid-template-columns:58px 18px minmax(0,1fr);gap:8px;font-size:13.5px}
.g4t-steps .t{font-family:var(--mono);font-size:12px;color:var(--faint);text-align:right;padding-top:2px}
.g4t-steps .dot{position:relative}
.g4t-steps .dot::before{content:"";position:absolute;left:4px;top:6px;width:9px;height:9px;border-radius:50%;background:#c2ccc5}
.g4t-steps .dot::after{content:"";position:absolute;left:8px;top:17px;bottom:-6px;width:1px;background:var(--line)}
.g4t-steps li:last-child .dot::after{display:none}
.g4t-steps li.is-click .dot::before{background:#6e9be0}
.g4t-steps li.is-custom .dot::before{background:#d5a33a}
.g4t-steps li.is-app .dot::before,.g4t-steps li.is-identity .dot::before{background:var(--accent)}
.g4t-steps .e{padding-bottom:9px;min-width:0;overflow-wrap:anywhere}
.g4t-steps .e small{color:var(--faint)}

/* Profile header */
.g4t-profile{display:flex;flex-wrap:wrap;gap:16px 28px;align-items:flex-start;padding:16px}
.g4t-avatar{width:52px;height:52px;border-radius:50%;background:var(--accent-soft);color:var(--accent-ink);display:grid;place-items:center;font-weight:700;font-size:20px;flex:none}
.g4t-facts{display:grid;gap:8px 28px;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));flex:1;min-width:260px}
.g4t-fact-label{display:block;font-size:11.5px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--faint)}

/* Settings */
.g4t-form .form-group{margin-bottom:16px}
.g4t-form label{font-weight:600}
.g4t-form .help{font-size:12.5px;color:var(--faint);margin-top:4px}
.g4t-code{font-family:var(--mono);font-size:12.5px;background:var(--wash);border:1px solid var(--line);border-radius:6px;padding:8px 10px;word-break:break-all;display:block}
.g4t-status{display:flex;align-items:center;gap:8px}
.g4t-status::before{content:"";width:8px;height:8px;border-radius:50%;background:var(--down)}
.g4t-status.is-ok::before{background:#2fb344}

.g4t-map{height:340px;position:relative;overflow:hidden}
.g4t-chart{position:relative;height:260px}
.g4t-credit{font-size:12px;color:var(--faint);text-align:right;margin-top:8px}
.g4t .pagination{margin:12px 16px 4px}
</style>
