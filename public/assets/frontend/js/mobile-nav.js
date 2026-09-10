/* ==========================================================================
   Mobile navigation drawer.

   Reads whatever navigation is already in the page and mirrors it into a
   drawer. Nothing here assumes a particular navbar template, because the live
   server's navbar is hand-edited and cannot be replaced from the repo. Add a
   menu item there and it appears here on its own.
   ========================================================================== */
(function () {
  'use strict';

  function ready(fn) {
    document.readyState === 'loading'
      ? document.addEventListener('DOMContentLoaded', fn)
      : fn();
  }

  ready(function () {

    var nav = document.querySelector('.g4d-nav');
    if (!nav || document.querySelector('.g4m-drawer')) { return; }

    var bar = nav.parentElement;                 // the <nav> holding logo, menu, buttons
    var actions = nav.nextElementSibling;        // log in / sign up
    var clean = function (s) { return (s || '').replace(/\s+/g, ' ').trim(); };

    /* --- read the existing menu ----------------------------------------- */

    // Compare destinations by origin and path, ignoring tracking parameters,
    // so the same page linked twice is recognised as one.
    function keyOf(href) {
      try {
        var u = new URL(href, location.href);
        return u.origin + u.pathname.replace(/\/$/, '');
      } catch (e) { return href; }
    }

    // The header already repeats its log in and sign up links as buttons, and
    // they get their own row at the foot of the drawer. Listing them a second
    // time in the menu itself just pads it out.
    var actionKeys = actions
      ? [].slice.call(actions.querySelectorAll('a'))
          .map(function (a) { return a.getAttribute('href'); })
          .filter(Boolean)
          .map(keyOf)
      : [];

    var entries = [];

    [].slice.call(nav.children).forEach(function (node) {
      // A plain link at the top level.
      if (node.matches('a')) {
        var href = node.getAttribute('href');
        if (!clean(node.textContent) || !href) { return; }
        if (actionKeys.indexOf(keyOf(href)) !== -1) { return; }
        entries.push({ label: clean(node.textContent), href: href, subs: [] });
        return;
      }

      // A dropdown. Its first link is the heading, the rest are the contents.
      var links = [].slice.call(node.querySelectorAll('a'));
      if (!links.length) { return; }

      var trigger = node.querySelector(':scope > a') || links[0];
      var label = clean(trigger.textContent) ||
                  clean((node.querySelector(':scope > button, :scope > span, :scope > div') || {}).textContent);
      if (!label) { return; }

      var subs = [];
      links.forEach(function (a) {
        if (a === trigger) { return; }
        var t = clean(a.textContent), h = a.getAttribute('href');
        // Skip icon-only and placeholder links.
        if (!t || !h || h === '#') { return; }
        // The same destination twice in one dropdown helps nobody.
        if (subs.some(function (s) { return s.href === h; })) { return; }
        subs.push({ label: t, href: h });
      });

      var ownHref = trigger.getAttribute('href');
      entries.push({
        label: label,
        href: (ownHref && ownHref !== '#') ? ownHref : null,
        subs: subs
      });
    });

    if (!entries.length) { return; }

    /* --- build the drawer ------------------------------------------------ */
    var backdrop = document.createElement('div');
    backdrop.className = 'g4m-backdrop';

    var drawer = document.createElement('aside');
    drawer.className = 'g4m-drawer';
    drawer.setAttribute('role', 'dialog');
    drawer.setAttribute('aria-modal', 'true');
    drawer.setAttribute('aria-label', 'Site menu');

    var head = document.createElement('div');
    head.className = 'g4m-head';
    head.innerHTML = '<span class="g4m-head-label">Menu</span>' +
      '<button type="button" class="g4m-close" aria-label="Close menu">' +
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">' +
      '<path d="M18 6L6 18M6 6l12 12"/></svg></button>';

    var list = document.createElement('nav');
    list.className = 'g4m-list';

    entries.forEach(function (item) {
      if (!item.subs.length) {
        var a = document.createElement('a');
        a.className = 'g4m-link';
        a.href = item.href || '#';
        a.textContent = item.label;
        list.appendChild(a);
        return;
      }

      var group = document.createElement('div');
      group.className = 'g4m-group';

      var trigger = document.createElement('button');
      trigger.type = 'button';
      trigger.className = 'g4m-trigger';
      trigger.setAttribute('aria-expanded', 'false');
      trigger.innerHTML = '<span></span><span class="g4m-caret" aria-hidden="true"></span>';
      trigger.firstChild.textContent = item.label;

      var sub = document.createElement('div');
      sub.className = 'g4m-sub';
      var inner = document.createElement('div');
      inner.className = 'g4m-sub-inner';

      // The heading itself is a real page, so keep a way to reach it.
      if (item.href) {
        var overview = document.createElement('a');
        overview.href = item.href;
        overview.textContent = 'All ' + item.label;
        inner.appendChild(overview);
      }

      item.subs.forEach(function (s) {
        var link = document.createElement('a');
        link.href = s.href;
        link.textContent = s.label;
        inner.appendChild(link);
      });

      sub.appendChild(inner);
      group.appendChild(trigger);
      group.appendChild(sub);
      list.appendChild(group);

      trigger.addEventListener('click', function () {
        var isOpen = group.classList.toggle('is-open');
        trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        sub.style.maxHeight = isOpen ? inner.scrollHeight + 'px' : null;
      });
    });

    drawer.appendChild(head);
    drawer.appendChild(list);

    // Mirror the log in / sign up buttons to the bottom of the drawer.
    if (actions) {
      var btns = [].slice.call(actions.querySelectorAll('a')).filter(function (a) {
        return clean(a.textContent) && a.getAttribute('href');
      });

      if (btns.length) {
        var foot = document.createElement('div');
        foot.className = 'g4m-actions';
        btns.forEach(function (a, i) {
          var b = document.createElement('a');
          b.className = 'g4m-btn ' + (i === btns.length - 1 ? 'g4m-btn--solid' : 'g4m-btn--ghost');
          b.href = a.getAttribute('href');
          b.textContent = clean(a.textContent);
          foot.appendChild(b);
        });
        drawer.appendChild(foot);

        // Everything except the last button is duplicated in the drawer and
        // only crowds the logo on a narrow screen, so hide it there.
        btns.slice(0, -1).forEach(function (a) { a.classList.add('g4m-hide-sm'); });
      }
    }

    /* --- burger ----------------------------------------------------------- */
    var burger = document.createElement('button');
    burger.type = 'button';
    burger.className = 'g4m-burger';
    burger.setAttribute('aria-label', 'Open menu');
    burger.setAttribute('aria-expanded', 'false');
    burger.innerHTML = '<span class="g4m-burger-box"><span></span><span></span><span></span></span>';

    (actions || bar).appendChild(burger);
    document.body.appendChild(backdrop);
    document.body.appendChild(drawer);

    /* --- open / close ----------------------------------------------------- */
    var lastFocus = null;

    function open() {
      lastFocus = document.activeElement;
      document.documentElement.classList.add('g4m-open');
      document.body.classList.add('g4m-locked');
      burger.setAttribute('aria-expanded', 'true');
      burger.setAttribute('aria-label', 'Close menu');
      setTimeout(function () { drawer.querySelector('.g4m-close').focus(); }, 260);
    }

    function close() {
      document.documentElement.classList.remove('g4m-open');
      document.body.classList.remove('g4m-locked');
      burger.setAttribute('aria-expanded', 'false');
      burger.setAttribute('aria-label', 'Open menu');
      if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
    }

    function toggle() {
      document.documentElement.classList.contains('g4m-open') ? close() : open();
    }

    burger.addEventListener('click', toggle);
    backdrop.addEventListener('click', close);
    drawer.querySelector('.g4m-close').addEventListener('click', close);

    // Following a link should not leave the drawer open behind the new page.
    drawer.addEventListener('click', function (e) {
      if (e.target.closest('a')) { close(); }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && document.documentElement.classList.contains('g4m-open')) { close(); }
    });

    // Keep tabbing inside the drawer while it is open.
    drawer.addEventListener('keydown', function (e) {
      if (e.key !== 'Tab') { return; }
      var focusable = drawer.querySelectorAll('a[href], button:not([disabled])');
      if (!focusable.length) { return; }
      var first = focusable[0], last = focusable[focusable.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });

    // Back on a wide screen the drawer is hidden, so drop the scroll lock.
    window.addEventListener('resize', function () {
      if (window.innerWidth > 992 && document.documentElement.classList.contains('g4m-open')) { close(); }
    });
  });
})();
