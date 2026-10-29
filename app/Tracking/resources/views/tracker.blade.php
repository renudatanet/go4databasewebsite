{{--
  Visit and click tracking (Admin > Tracking). Included once, from the shared
  footer, so every page — today's and any added later — is tracked.

  Nothing here runs until the page has fully loaded, so it costs the first
  paint nothing. It sends small JSON messages to this site's own
  /t/collect: one when the page is viewed, one per click, and running
  totals of engaged time and scroll depth (every minute while the tab is in
  use, and when it is hidden or closed).

  Not printed for logged-in admins or when tracking is off in Settings; the
  server also ignores browsers that have opened the Tracking screens.
--}}
@php
    $trackingSettings = app(\App\Tracking\Support\TrackingSettings::class);
    $trackerConfig = [
        'collect' => route('tracking.collect', [], false),
        'visitorCookie' => config('tracking.cookies.visitor'),
        'optOutCookie' => config('tracking.cookies.opt_out'),
        'consentCookie' => config('tracking.cookies.consent'),
        'appHost' => $trackingSettings->appHost(),
    ];
@endphp
@if($trackingSettings->enabled() && !auth('admin')->check())
<script>
(function (config) {
  'use strict';
  var w = window, d = document, nav = navigator;
  if (w.__g4Tracker || !w.JSON || !d.addEventListener) { return; }
  w.__g4Tracker = true;

  var HEARTBEAT_MS = 60000, IDLE_MS = 60000, TICK_MS = 5000, MAX_SENDS = 120;

  function cookie(name) {
    var match = d.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.$?*|{}()\[\]\\\/+^]/g, '\\$&') + '=([^;]*)'));
    return match ? decodeURIComponent(match[1]) : null;
  }
  if (cookie(config.optOutCookie)) { return; }

  function uuid() {
    if (w.crypto && w.crypto.randomUUID) { return w.crypto.randomUUID(); }
    var b = new Uint8Array(16);
    (w.crypto || w.msCrypto).getRandomValues(b);
    b[6] = (b[6] & 15) | 64; b[8] = (b[8] & 63) | 128;
    var h = Array.prototype.map.call(b, function (x) { return (x + 256).toString(16).slice(1); }).join('');
    return h.slice(0, 8) + '-' + h.slice(8, 12) + '-' + h.slice(12, 16) + '-' + h.slice(16, 20) + '-' + h.slice(20);
  }

  // Until the server sets the visitor cookie, this page uses a temporary id.
  var pageVisitor = uuid(), pageView, engagedMs, maxScroll, lastSentMs, lastInput, queue = [], sends = 0, flushTimer = null;

  function visitorId() { return cookie(config.visitorCookie) || pageVisitor; }

  function payload(events) {
    return JSON.stringify({
      version: 1,
      visitor: visitorId(),
      page_view: pageView,
      consent: cookie(config.consentCookie) === 'true',
      url: location.href,
      title: d.title || null,
      referrer: d.referrer || null,
      device: {
        width: (w.screen && w.screen.width) || null,
        height: (w.screen && w.screen.height) || null,
        language: nav.language || null,
        touch: (nav.maxTouchPoints || 0) > 0,
        automated: !!nav.webdriver
      },
      events: events
    });
  }

  function send(events, urgent) {
    if (!events.length || sends >= MAX_SENDS) { return; }
    sends++;
    var body = payload(events);
    // Leaving the page: a beacon survives the unload. Otherwise fetch, whose
    // response lets the server set the visitor cookie.
    if (urgent && nav.sendBeacon && nav.sendBeacon(config.collect, new Blob([body], { type: 'application/json' }))) { return; }
    try {
      w.fetch(config.collect, { method: 'POST', body: body, keepalive: true, credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } })['catch'](function () {});
    } catch (e) { /* tracking must never break the page */ }
  }

  function flush(urgent) {
    if (flushTimer) { clearTimeout(flushTimer); flushTimer = null; }
    var events = queue.splice(0, 25);
    send(events, urgent);
    if (queue.length) { flush(urgent); }
  }

  function enqueue(event, urgent) {
    queue.push(event);
    if (urgent) { flush(true); } else if (!flushTimer) { flushTimer = setTimeout(function () { flush(false); }, 2000); }
  }

  function engagement(urgent) {
    if (engagedMs === lastSentMs && !queue.length) { return; }
    lastSentMs = engagedMs;
    queue.push({ type: 'engagement', engaged_ms: engagedMs, scroll: maxScroll });
    flush(urgent);
  }

  // Engaged time: the tab is visible and someone touched, scrolled, typed or
  // moved the mouse within the last minute.
  function markInput() { lastInput = Date.now(); }
  function tick() {
    if (d.visibilityState === 'visible' && Date.now() - lastInput < IDLE_MS) { engagedMs += TICK_MS; }
  }
  function measureScroll() {
    var el = d.documentElement, height = Math.max(el.scrollHeight, d.body ? d.body.scrollHeight : 0);
    var seen = height > 0 ? Math.round(((w.scrollY || el.scrollTop) + w.innerHeight) / height * 100) : 100;
    if (seen > maxScroll) { maxScroll = Math.min(100, seen); }
  }

  function startPageView() {
    pageView = uuid(); engagedMs = 0; lastSentMs = 0; maxScroll = 0; lastInput = Date.now();
    measureScroll();
    send([{ type: 'pageview' }], false);
  }

  function label(el) {
    var text = el.getAttribute('data-track') || el.getAttribute('aria-label') || el.innerText || el.value || el.title || '';
    return String(text).replace(/\s+/g, ' ').trim().slice(0, 120) || null;
  }
  function section(el) {
    var holder = el.parentElement && el.parentElement.closest('[id]');
    if (holder && holder !== d.body) { return holder.id.slice(0, 64); }
    var area = el.closest('header,nav,main,footer,aside');
    return area ? area.tagName.toLowerCase() : null;
  }
  function clickable(target) {
    return target && target.closest ? target.closest('a[href],button,[role="button"],input[type="submit"],[data-track]') : null;
  }

  // Sign up / Log in links to the app carry the visitor id, so a sign-up
  // there joins this visitor's journey. Only once the site has given this
  // browser its visitor cookie (never for anonymous visitors).
  function handOver(el) {
    if (!el || el.tagName !== 'A' || el.hostname !== config.appHost) { return; }
    var id = cookie(config.visitorCookie);
    if (!id) { return; }
    try { var url = new URL(el.href); url.searchParams.set('g4v', id); el.href = url.toString(); } catch (e) {}
  }

  function onClick(e) {
    var el = clickable(e.target);
    if (!el) { return; }
    handOver(el);
    var href = el.tagName === 'A' ? el.getAttribute('href') : null;
    if (href && /^\s*javascript:/i.test(href)) { href = null; }
    enqueue({ type: 'click', href: href ? el.href : null, label: label(el), section: section(el) }, true);
  }

  // Events the site already sends to Google Analytics are recorded here too.
  function mirrorAnalytics() {
    if (typeof w.gtag !== 'function' || w.gtag.__g4Wrapped) { return; }
    var original = w.gtag;
    var wrapped = function (command, name, params) {
      if (command === 'event' && typeof name === 'string' && name !== 'page_view') {
        var properties = {};
        if (params && typeof params === 'object') {
          for (var key in params) {
            if (Object.prototype.hasOwnProperty.call(params, key) && /^(string|number|boolean)$/.test(typeof params[key])) { properties[key] = params[key]; }
          }
        }
        enqueue({ type: 'event', name: name.slice(0, 64), properties: properties }, false);
      }
      return original.apply(this, arguments);
    };
    wrapped.__g4Wrapped = true;
    w.gtag = wrapped;
  }

  function start() {
    startPageView();
    mirrorAnalytics();
    ['pointerdown', 'keydown', 'scroll', 'touchstart', 'mousemove', 'wheel'].forEach(function (type) {
      w.addEventListener(type, markInput, { passive: true, capture: true });
    });
    w.addEventListener('scroll', measureScroll, { passive: true });
    d.addEventListener('pointerdown', function (e) { handOver(clickable(e.target)); }, true);
    d.addEventListener('click', onClick, true);
    d.addEventListener('visibilitychange', function () { if (d.visibilityState === 'hidden') { engagement(true); } });
    w.addEventListener('pagehide', function () { engagement(true); });
    // Back/forward cache: the page is shown again without reloading.
    w.addEventListener('pageshow', function (e) { if (e.persisted) { startPageView(); } });
    setInterval(tick, TICK_MS);
    setInterval(function () { if (d.visibilityState === 'visible') { engagement(false); } }, HEARTBEAT_MS);
  }

  function whenIdle() { (w.requestIdleCallback || function (fn) { setTimeout(fn, 1); })(start, { timeout: 3000 }); }
  if (d.readyState === 'complete') { whenIdle(); } else { w.addEventListener('load', whenIdle); }
})(@json($trackerConfig));
</script>
@endif
