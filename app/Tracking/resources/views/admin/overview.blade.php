@extends('tracking::admin.layout')
@section('screen-title', 'Overview')
@section('screen')
@php
    $now = $totals['current'];
    $before = $totals['previous'];
    $filterQuery = $context->toQuery();
@endphp
@include('tracking::admin.partials.toolbar', ['context' => $context])

<div class="g4t-kpis">
    @include('tracking::admin.partials.kpi', ['label' => 'Visitors', 'value' => $fmt::number($now['visitors']), 'current' => $now['visitors'], 'previous' => $before['visitors']])
    @include('tracking::admin.partials.kpi', ['label' => 'Visits', 'value' => $fmt::number($now['sessions']), 'current' => $now['sessions'], 'previous' => $before['sessions']])
    @include('tracking::admin.partials.kpi', ['label' => 'Page views', 'value' => $fmt::number($now['page_views']), 'current' => $now['page_views'], 'previous' => $before['page_views'], 'note' => $now['pages_per_session'] . ' per visit'])
    @include('tracking::admin.partials.kpi', ['label' => 'Engaged time per visit', 'value' => $fmt::duration($now['avg_engaged_ms']), 'current' => $now['avg_engaged_ms'], 'previous' => $before['avg_engaged_ms']])
    @include('tracking::admin.partials.kpi', ['label' => 'Bounce rate', 'value' => $fmt::percent($now['bounce_rate']), 'current' => $now['bounce_rate'], 'previous' => $before['bounce_rate'], 'lowerIsBetter' => true])
    @include('tracking::admin.partials.kpi', ['label' => 'Clicked Sign up', 'value' => $fmt::number($now['signup_visitors']), 'current' => $now['signup_visitors'], 'previous' => $before['signup_visitors'], 'note' => $fmt::percent($now['signup_rate']) . ' of visitors'])
    @include('tracking::admin.partials.kpi', ['label' => 'Known by name', 'value' => $fmt::number($now['identified']), 'current' => $now['identified'], 'previous' => $before['identified']])
    <a href="{{ route('admin.tracking.live') }}" class="g4t-kpi is-live" style="text-decoration:none;color:inherit">
        <div class="g4t-kpi-label">Live now</div>
        <div class="g4t-kpi-value">{{ $fmt::number($liveCount) }}</div>
        <div class="g4t-kpi-foot">active in the last {{ config('tracking.live_window_minutes') }} minutes →</div>
    </a>
</div>

<div class="g4t-grid">
    <section class="g4t-card g4t-span-12">
        <div class="g4t-card-head"><h3>Visitors {{ $context->range->isHourly() ? 'by hour' : 'by day' }}</h3><span class="g4t-faint">dashed: the period before</span></div>
        <div class="g4t-card-body">
            @if($now['sessions'] === 0 && $before['sessions'] === 0)
                <div class="g4t-empty"><strong>No visits in this period yet</strong>Visits appear here a few seconds after they happen. Check Settings if tracking is switched on.</div>
            @else
                <div class="g4t-chart"><canvas id="g4t-traffic" aria-label="Visitors over time"></canvas></div>
            @endif
        </div>
    </section>
</div>

<div class="g4t-grid">
    <section class="g4t-card g4t-span-8">
        <div class="g4t-card-head"><h3>Top pages</h3><a href="{{ route('admin.tracking.pages', $filterQuery) }}">All pages →</a></div>
        <div class="g4t-card-body" style="padding:8px 0 0">
            @if(empty($pages))
                <div class="g4t-empty">No page views in this period.</div>
            @else
            <div class="g4t-table-wrap"><table>
                <thead><tr><th>Page</th><th class="n">Views</th><th class="n">Visitors</th><th class="n">Engaged</th><th class="n">Click-through</th></tr></thead>
                <tbody>
                @foreach($pages as $row)
                    <tr>
                        <td><a href="{{ route('admin.tracking.pages.show', ['page' => $row['page_id']] + $filterQuery) }}" class="g4t-strong">{{ $fmt::path($row['path'], 55) }}</a><span class="g4t-sub">{{ $row['title'] }}</span></td>
                        <td class="n">{{ $fmt::number($row['views']) }}</td>
                        <td class="n">{{ $fmt::number($row['visitors']) }}</td>
                        <td class="n">{{ $fmt::duration($row['avg_engaged_ms']) }}</td>
                        <td class="n">{{ $fmt::percent($row['ctr']) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            @endif
        </div>
    </section>
    <section class="g4t-card g4t-span-4">
        <div class="g4t-card-head"><h3>How they found you</h3><a href="{{ route('admin.tracking.sources', $filterQuery) }}">Sources →</a></div>
        <div class="g4t-card-body">
            @include('tracking::admin.partials.bars', ['items' => array_map(fn ($r) => ['name' => $fmt::channel($r['channel']), 'value' => $r['visitors'], 'extra' => $r['signups'] ? $r['signups'] . ' sign-up' . ($r['signups'] === 1 ? '' : 's') : null], $channels)])
        </div>
    </section>
</div>

<div class="g4t-grid">
    <section class="g4t-card g4t-span-4">
        <div class="g4t-card-head"><h3>Countries</h3><a href="{{ route('admin.tracking.locations', $filterQuery) }}">Locations →</a></div>
        <div class="g4t-card-body">
            @include('tracking::admin.partials.bars', ['items' => array_map(fn ($r) => ['prefix' => $fmt::flag($r['country_code']), 'name' => $r['name'], 'value' => $r['visitors'], 'href' => $r['country_code'] ? route('admin.tracking.locations', ['in_country' => $r['country_code']] + $filterQuery) : null], $countries)])
        </div>
    </section>
    <section class="g4t-card g4t-span-4">
        <div class="g4t-card-head"><h3>Devices</h3></div>
        <div class="g4t-card-body">
            @include('tracking::admin.partials.bars', ['items' => array_map(fn ($r) => ['name' => ucfirst($r['name']), 'value' => $r['visitors']], $devices)])
            <div class="g4t-faint" style="margin:14px 0 6px">Browsers</div>
            @include('tracking::admin.partials.bars', ['items' => array_map(fn ($r) => ['name' => $r['name'], 'value' => $r['visitors']], $browsers)])
        </div>
    </section>
    <section class="g4t-card g4t-span-4">
        <div class="g4t-card-head"><h3>Recently known by name</h3><a href="{{ route('admin.tracking.visitors', ['segment' => 'identified'] + $filterQuery) }}">Visitors →</a></div>
        <div class="g4t-card-body">
            @forelse($recentlyIdentified as $visitor)
                <div style="display:flex;justify-content:space-between;gap:10px;padding:6px 0;border-bottom:1px solid var(--line)">
                    <span style="min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><a href="{{ route('admin.tracking.visitors.show', $visitor->id) }}" class="g4t-strong">{{ $visitor->displayName() }}</a>@if($visitor->company)<span class="g4t-faint"> · {{ $visitor->company }}</span>@endif</span>
                    <span class="g4t-faint" style="white-space:nowrap">{{ \App\Tracking\Models\Identity::LABELS[$visitor->identified_via] ?? '' }}</span>
                </div>
            @empty
                <div class="g4t-empty">Nobody has filled in a form or signed up yet. Names appear here when they do.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
@section('screen-script')
    @include('tracking::admin.partials.chart-script')
    <script>
        g4tLineChart('g4t-traffic', @json(array_column($series, 'label')), [
            { label: 'Visitors', data: @json(array_column($series, 'visitors')) },
            { label: 'Previous period', data: @json(array_column($previousSeries, 'visitors')) }
        ]);
    </script>
@endsection
