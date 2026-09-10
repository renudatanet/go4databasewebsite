/* ==========================================================================
   FAQ page: accordion, live search, and a topic rail that tracks scrolling.
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function () {

  if (window.lucide) { lucide.createIcons(); }

  var root = document.querySelector('.fq');
  if (!root) { return; }

  var items  = [].slice.call(root.querySelectorAll('.fq-item')),
      groups = [].slice.call(root.querySelectorAll('.fq-group')),
      links  = [].slice.call(root.querySelectorAll('.fq-rail-link')),
      search = document.getElementById('fq-search'),
      clear  = document.getElementById('fq-clear'),
      empty  = document.getElementById('fq-empty'),
      metaD  = document.getElementById('fq-meta-default'),
      metaS  = document.getElementById('fq-meta-search');

  /* --- accordion -------------------------------------------------------- */
  function panelOf(item) { return item.querySelector('.fq-a'); }

  function open(item) {
    var panel = panelOf(item);
    item.classList.add('is-open');
    item.querySelector('.fq-q').setAttribute('aria-expanded', 'true');
    panel.style.maxHeight = panel.scrollHeight + 'px';
  }

  function close(item) {
    item.classList.remove('is-open');
    item.querySelector('.fq-q').setAttribute('aria-expanded', 'false');
    panelOf(item).style.maxHeight = null;
  }

  items.forEach(function (item) {
    // Questions marked "open by default" in the admin start expanded.
    if (item.classList.contains('is-open')) { open(item); }

    item.querySelector('.fq-q').addEventListener('click', function () {
      item.classList.contains('is-open') ? close(item) : open(item);
    });
  });

  // An open answer changes height when the window does, so remeasure.
  var resizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      items.forEach(function (item) {
        if (item.classList.contains('is-open')) {
          panelOf(item).style.maxHeight = panelOf(item).scrollHeight + 'px';
        }
      });
    }, 140);
  });

  /* --- search ----------------------------------------------------------- */
  if (search) {
    // Cache the searchable text once rather than reading the DOM per keystroke.
    items.forEach(function (item) {
      var q = item.querySelector('.fq-q-tx').textContent,
          a = item.querySelector('.fq-a-inner').textContent;
      item.dataset.hay = (q + ' ' + a).toLowerCase();
    });

    // Remember each topic's full count so it can be restored after a search.
    links.forEach(function (l) {
      l.dataset.total = l.querySelector('em').textContent.trim();
    });

    // Rail counts follow the search, and a topic with no matches stops being
    // clickable so it cannot scroll you to something that is not there.
    var syncRail = function (searching) {
      links.forEach(function (l) {
        var group = document.getElementById(l.dataset.target),
            n = group ? group.querySelectorAll('.fq-item:not([hidden])').length : 0;

        l.classList.toggle('is-muted', searching && n === 0);
        l.querySelector('em').textContent = searching ? n : l.dataset.total;
      });
    };

    var run = function () {
      var term = search.value.trim().toLowerCase();
      clear.hidden = term === '';

      if (term === '') {
        items.forEach(function (i) { i.hidden = false; });
        groups.forEach(function (g) { g.hidden = false; });
        empty.hidden = true;
        metaD.hidden = false;
        metaS.hidden = true;
        syncRail(false);
        return;
      }

      var hits = 0;

      items.forEach(function (item) {
        var match = item.dataset.hay.indexOf(term) !== -1;
        item.hidden = !match;
        if (match) { hits++; } else if (item.classList.contains('is-open')) { close(item); }
      });

      // Hide a topic entirely once every question inside it is filtered out.
      groups.forEach(function (g) {
        g.hidden = g.querySelectorAll('.fq-item:not([hidden])').length === 0;
      });

      syncRail(true);

      empty.hidden = hits > 0;
      metaD.hidden = true;
      metaS.hidden = false;
      metaS.innerHTML = '<strong>' + hits + '</strong> ' +
        (hits === 1 ? 'question matches' : 'questions match') + ' &ldquo;' +
        search.value.trim().replace(/[<>&]/g, '') + '&rdquo;';
    };

    var debounce;
    search.addEventListener('input', function () {
      clearTimeout(debounce);
      debounce = setTimeout(run, 120);
    });

    search.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { search.value = ''; run(); }
    });

    clear.addEventListener('click', function () {
      search.value = '';
      run();
      search.focus();
    });
  }

  /* --- topic rail follows the scroll ------------------------------------ */
  if (links.length && 'IntersectionObserver' in window) {
    var setActive = function (id) {
      links.forEach(function (l) {
        l.classList.toggle('is-active', l.dataset.target === id);
      });
    };

    var spy = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { setActive(e.target.id); }
      });
    }, { rootMargin: '-100px 0px -65% 0px', threshold: 0 });

    groups.forEach(function (g) { spy.observe(g); });

    links.forEach(function (l) {
      l.addEventListener('click', function () { setActive(l.dataset.target); });
    });
  }
});
