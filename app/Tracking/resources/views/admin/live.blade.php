@extends('tracking::admin.layout')
@section('screen-title', 'Live now')
@section('screen')
<div class="g4t-grid">
    <section class="g4t-card g4t-span-4">
        <div class="g4t-card-head"><h3>Active now</h3><span class="g4t-faint">updated <span id="g4t-live-updated">{{ $initial['updated'] }}</span></span></div>
        <div class="g4t-card-body">
            <div class="g4t-kpi-value" style="font-size:44px"><span id="g4t-live-count">{{ $fmt::number($initial['count']) }}</span></div>
            <div class="g4t-faint">visitors active in the last {{ config('tracking.live_window_minutes') }} minutes. Refreshes every 15 seconds.</div>
        </div>
    </section>
    <section class="g4t-card g4t-span-8">
        <div class="g4t-card-head"><h3>Pages being read</h3></div>
        <div class="g4t-card-body"><ul class="g4t-bars" id="g4t-live-pages"></ul><div class="g4t-empty" id="g4t-live-pages-empty" hidden>Nobody is on the site right now.</div></div>
    </section>
</div>
<section class="g4t-card">
    <div class="g4t-card-head"><h3>Visitors on the site</h3></div>
    <div class="g4t-card-body" style="padding:8px 0 0">
        <div class="g4t-table-wrap"><table>
            <thead><tr><th>Visitor</th><th>Where</th><th>Network</th><th>On page</th><th>Came from</th><th class="n">Pages</th><th class="n">On site</th><th class="n">Last active</th></tr></thead>
            <tbody id="g4t-live-rows"></tbody>
        </table></div>
        <div class="g4t-empty" id="g4t-live-empty" hidden><strong>Quiet right now</strong>Visitors appear here within seconds of opening a page.</div>
    </div>
</section>
@endsection
@section('screen-script')
<script>
(function () {
    var feedUrl = @json(route('admin.tracking.live.feed'));
    var initial = @json($initial);

    // Every value is written with textContent: visitor data is never HTML.
    function el(tag, text, className) {
        var node = document.createElement(tag);
        if (text !== null && text !== undefined) { node.textContent = text; }
        if (className) { node.className = className; }
        return node;
    }

    function render(data) {
        document.getElementById('g4t-live-count').textContent = Number(data.count).toLocaleString();
        document.getElementById('g4t-live-updated').textContent = data.updated;

        var pages = document.getElementById('g4t-live-pages');
        pages.replaceChildren();
        var max = Math.max.apply(null, [1].concat(data.pages.map(function (p) { return p.visitors; })));
        data.pages.forEach(function (p) {
            var li = el('li');
            li.style.setProperty('--share', (p.visitors / max * 100) + '%');
            li.appendChild(el('span', p.path, 'g4t-bar-name'));
            li.appendChild(el('span', String(p.visitors), 'g4t-bar-value'));
            pages.appendChild(li);
        });
        document.getElementById('g4t-live-pages-empty').hidden = data.pages.length > 0;

        var rows = document.getElementById('g4t-live-rows');
        rows.replaceChildren();
        data.visitors.forEach(function (v) {
            var tr = el('tr');
            var who = el('td');
            var link = el('a', v.name || (v.anonymous ? 'Anonymous visitor' : 'Visitor'), 'g4t-strong');
            link.href = v.url;
            who.appendChild(link);
            if (v.company) { who.appendChild(el('span', v.company, 'g4t-sub')); }
            who.appendChild(el('span', v.device, 'g4t-sub'));
            tr.appendChild(who);
            var where = el('td');
            where.appendChild(el('span', v.flag, 'g4t-flag'));
            where.appendChild(document.createTextNode(v.place));
            tr.appendChild(where);
            var network = el('td', v.network || 'Unknown');
            if (v.network_type === 'business') { network.appendChild(document.createTextNode(' ')); network.appendChild(el('span', 'Business', 'g4t-pill is-business')); }
            tr.appendChild(network);
            var page = el('td', v.path, 'g4t-path');
            page.title = v.title || '';
            tr.appendChild(page);
            tr.appendChild(el('td', v.source));
            tr.appendChild(el('td', String(v.pages), 'n'));
            tr.appendChild(el('td', v.on_site, 'n'));
            tr.appendChild(el('td', v.last_active, 'n'));
            rows.appendChild(tr);
        });
        document.getElementById('g4t-live-empty').hidden = data.visitors.length > 0;
    }

    function refresh() {
        if (document.visibilityState !== 'visible') { return; }
        fetch(feedUrl, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) { if (data) { render(data); } })
            .catch(function () {});
    }

    render(initial);
    setInterval(refresh, 15000);
    document.addEventListener('visibilitychange', refresh);
})();
</script>
@endsection
