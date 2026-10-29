@extends('tracking::admin.layout')
@section('screen-title', 'Page')
@section('screen')
@include('tracking::admin.partials.toolbar', ['context' => $context])
@php
    $now = $summary['current'];
    $before = $summary['previous'];
    $query = $context->toQuery();
@endphp
<div style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px;align-items:baseline;margin-bottom:14px">
    <div style="min-width:0">
        <a href="{{ route('admin.tracking.pages', $query) }}" class="g4t-faint">← All pages</a>
        <h2 style="font-size:20px;margin:4px 0 2px;font-family:var(--mono);overflow-wrap:anywhere">{{ $page->path }}</h2>
        <div class="g4t-muted">{{ $page->title }}</div>
    </div>
    <a class="g4t-btn is-quiet" href="{{ url($page->path) }}" target="_blank" rel="noopener">Open page ↗</a>
</div>

<div class="g4t-kpis is-3">
    @include('tracking::admin.partials.kpi', ['label' => 'Views', 'value' => $fmt::number($now['views']), 'current' => $now['views'], 'previous' => $before['views']])
    @include('tracking::admin.partials.kpi', ['label' => 'Visitors', 'value' => $fmt::number($now['visitors']), 'current' => $now['visitors'], 'previous' => $before['visitors']])
    @include('tracking::admin.partials.kpi', ['label' => 'Engaged time', 'value' => $fmt::duration($now['avg_engaged_ms']), 'current' => $now['avg_engaged_ms'], 'previous' => $before['avg_engaged_ms']])
    @include('tracking::admin.partials.kpi', ['label' => 'Scrolled', 'value' => $now['avg_scroll'] . '%', 'note' => 'on average'])
    @include('tracking::admin.partials.kpi', ['label' => 'Click-through', 'value' => $fmt::percent($now['ctr']), 'current' => $now['ctr'], 'previous' => $before['ctr'], 'note' => $fmt::number($now['clicks']) . ($now['clicks'] === 1 ? ' click' : ' clicks')])
    @include('tracking::admin.partials.kpi', ['label' => 'Exit rate', 'value' => $fmt::percent($now['exit_rate']), 'current' => $now['exit_rate'], 'previous' => $before['exit_rate'], 'lowerIsBetter' => true])
</div>

<section class="g4t-card" style="margin-bottom:16px">
    <div class="g4t-card-head"><h3>Views {{ $context->range->isHourly() ? 'by hour' : 'by day' }}</h3><span class="g4t-faint">dashed: the period before</span></div>
    <div class="g4t-card-body"><div class="g4t-chart" style="height:220px"><canvas id="g4t-page-chart" aria-label="Views of this page over time"></canvas></div></div>
</section>

<div class="g4t-grid">
    <section class="g4t-card g4t-span-6">
        <div class="g4t-card-head"><h3>Came from</h3></div>
        <div class="g4t-card-body">
            <div class="g4t-faint" style="margin-bottom:6px">The page before, on this site</div>
            @include('tracking::admin.partials.bars', ['items' => array_map(fn ($r) => ['name' => $fmt::path($r['path'], 50), 'value' => $r['views'], 'href' => route('admin.tracking.pages.show', ['page' => $r['page_id']] + $query)], $flow['previous_pages']), 'empty' => 'No visitor reached this page from another page.'])
            <div class="g4t-faint" style="margin:14px 0 6px">Started their visit here, from</div>
            @include('tracking::admin.partials.bars', ['items' => array_map(fn ($r) => ['name' => $r['source'] ?: $fmt::channel($r['channel']), 'value' => $r['views'], 'extra' => $r['source'] ? $fmt::channel($r['channel']) : null], $flow['sources']), 'empty' => 'No visit started on this page.'])
        </div>
    </section>
    <section class="g4t-card g4t-span-6">
        <div class="g4t-card-head"><h3>Went next</h3></div>
        <div class="g4t-card-body">
            @php
                $next = array_map(fn ($r) => ['name' => $fmt::path($r['path'], 50), 'value' => $r['views'], 'href' => route('admin.tracking.pages.show', ['page' => $r['page_id']] + $query)], $flow['next_pages']);
                if ($flow['exits'] > 0) { $next[] = ['name' => 'Left the site', 'value' => $flow['exits']]; }
                usort($next, fn ($a, $b) => $b['value'] <=> $a['value']);
            @endphp
            @include('tracking::admin.partials.bars', ['items' => $next, 'empty' => 'Nothing recorded after this page yet.'])
        </div>
    </section>
</div>

<section class="g4t-card">
    <div class="g4t-card-head"><h3>Links and buttons on this page</h3><a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}">Download CSV</a></div>
    @if(empty($links))
        <div class="g4t-empty">Nothing on this page was clicked in this period.</div>
    @else
    <div class="g4t-card-body" style="padding:8px 0 0"><div class="g4t-table-wrap"><table>
        <thead><tr><th>Link</th><th>Goes to</th><th>Where on the page</th><th class="n">Clicks</th><th class="n">Visitors</th><th class="n">Click-through</th></tr></thead>
        <tbody>
        @foreach($links as $link)
            <tr>
                <td class="g4t-strong">{{ $link['label'] ?: '(no text)' }}
                    @if($link['name'] === 'signup_click')<span class="g4t-pill is-known">Sign up</span>@elseif($link['name'] === 'login_click')<span class="g4t-pill is-app">Log in</span>@endif
                </td>
                <td class="g4t-path" title="{{ $link['href'] }}">{{ $link['href'] ? $fmt::href($link['href'], 60) : 'Button' }}</td>
                <td class="g4t-muted">{{ $link['section'] ? '#' . $link['section'] : '–' }}</td>
                <td class="n">{{ $fmt::number($link['clicks']) }}</td>
                <td class="n">{{ $fmt::number($link['clickers']) }}</td>
                <td class="n">{{ $fmt::percent($link['ctr']) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table></div></div>
    @endif
</section>
@endsection
@section('screen-script')
    @include('tracking::admin.partials.chart-script')
    <script>
        g4tLineChart('g4t-page-chart', @json(array_column($series, 'label')), [
            { label: 'Views', data: @json(array_column($series, 'page_views')) },
            { label: 'Previous period', data: @json(array_column($previousSeries, 'page_views')) }
        ]);
    </script>
@endsection
