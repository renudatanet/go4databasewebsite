@extends('tracking::admin.layout')
@section('screen-title', 'Locations')
@section('screen')
@include('tracking::admin.partials.toolbar', ['context' => $context, 'keep' => ['in_country' => $country, 'in_region' => $region]])
@php
    $query = $context->toQuery();
    $levelName = ['country' => 'Countries', 'region' => 'States and regions', 'city' => 'Cities'][$level];
@endphp
<div class="g4t-grid">
    <section class="g4t-card g4t-span-8">
        <div class="g4t-card-head"><h3>Visitors around the world</h3><span class="g4t-faint">click a country to open it</span></div>
        <div class="g4t-card-body">
            @if(empty($mapData))
                <div class="g4t-empty">No located visitors in this period.</div>
            @else
                <div id="g4t-map" class="g4t-map" role="img" aria-label="World map shaded by visitors"></div>
            @endif
        </div>
    </section>
    <section class="g4t-card g4t-span-4">
        <div class="g4t-card-head"><h3>Companies visiting</h3></div>
        <p class="g4t-card-note" style="margin-top:4px">Organisations whose own office network a visit came from. Home and mobile visitors show their internet provider instead, so they aren't listed.</p>
        <div class="g4t-card-body" style="padding-top:0">
            @include('tracking::admin.partials.bars', ['items' => array_map(fn ($r) => ['name' => $r['network_org'], 'value' => $r['visitors'], 'href' => route('admin.tracking.visitors', $query + ['q' => $r['network_org']]), 'extra' => $r['city'] ?: $r['country']], array_slice($networks, 0, 15)), 'empty' => 'No business networks in this period.'])
        </div>
    </section>
</div>

<section class="g4t-card">
    <div class="g4t-card-head">
        <h3>
            @if($country)
                <a href="{{ route('admin.tracking.locations', $query) }}">All countries</a> ›
                @if($region)
                    <a href="{{ route('admin.tracking.locations', $query + ['in_country' => $country]) }}">{{ $countryName }}</a> › {{ $region }}
                @else
                    {{ $countryName }}
                @endif
            @else
                {{ $levelName }}
            @endif
        </h3>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}">Download CSV</a>
    </div>
    @if(empty($rows))
        <div class="g4t-empty">No visitors here in this period.</div>
    @else
    <div class="g4t-card-body" style="padding:8px 0 0"><div class="g4t-table-wrap"><table>
        <thead><tr><th>{{ $level === 'country' ? 'Country' : ($level === 'region' ? 'State / region' : 'City') }}</th><th class="n">Visitors</th><th class="n">Visits</th><th class="n">Pages per visit</th><th class="n">Clicked Sign up</th><th class="n">Sign-up rate</th></tr></thead>
        <tbody>
        @foreach($rows as $row)
            @php
                $next = null;
                if ($level === 'country' && $row['country_code']) { $next = route('admin.tracking.locations', $query + ['in_country' => $row['country_code']]); }
                elseif ($level === 'region' && $row['region']) { $next = route('admin.tracking.locations', $query + ['in_country' => $country, 'in_region' => $row['region']]); }
            @endphp
            <tr>
                <td class="g4t-strong">@if($level === 'country')<span class="g4t-flag">{{ $fmt::flag($row['country_code']) }}</span>@endif
                    @if($next)<a href="{{ $next }}">{{ $row['name'] }}</a>@else{{ $row['name'] }}@endif</td>
                <td class="n">{{ $fmt::number($row['visitors']) }}</td>
                <td class="n">{{ $fmt::number($row['sessions']) }}</td>
                <td class="n">{{ $row['pages_per_session'] }}</td>
                <td class="n">{{ $fmt::number($row['signups']) }}</td>
                <td class="n">{{ $fmt::percent($row['signup_rate']) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table></div></div>
    @endif
</section>
<p class="g4t-faint" style="margin-top:8px">Locations come from IP addresses: countries are very reliable, states usually right, cities often right or a nearby city. Mobile networks and VPNs often show their hub city.</p>
@endsection
@section('screen-script')
@if(!empty($mapData))
    <style>
    .g4t-map svg{touch-action:none}.g4t-map image,.g4t-map text{user-select:none}
    .g4t-map.jvm-container{touch-action:none}
    .jvm-tooltip{border-radius:6px;background:#16211a;font-size:12.5px;box-shadow:0 2px 10px rgba(0,0,0,.18);padding:5px 9px;white-space:nowrap;position:absolute;display:none;color:#fff;z-index:20}
    .jvm-tooltip.active{display:block}
    .g4t-map .jvm-zoom-btn{border-radius:5px;background:#fff;border:1px solid #e2e8e3;color:#16211a;padding:2px;box-sizing:border-box;position:absolute;line-height:12px;cursor:pointer;height:20px;width:20px;left:10px;text-align:center}
    .g4t-map .jvm-zoom-btn.jvm-zoomin{top:10px}.g4t-map .jvm-zoom-btn.jvm-zoomout{top:34px}
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js" integrity="sha384-dUo4VnkPwa5iJ/udOYY1SRDya6+2CVXYRfing32kyXbeB6HePV/YmJ3EPPvYz/Sb" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js" integrity="sha384-QCoiowLYPphJpiHouJIJOUCcg6AxqnLAAyEjkZnJ7RQCpC3YY1kEX0PaOant/8kx" crossorigin="anonymous"></script>
    <script>
    (function () {
        if (!window.jsVectorMap) { return; }
        var visitors = @json($mapData);
        var openUrl = @json(route('admin.tracking.locations', $query + ['in_country' => '__CC__']));
        new jsVectorMap({
            selector: '#g4t-map',
            map: 'world',
            zoomButtons: true,
            regionStyle: { initial: { fill: '#e9eeea', stroke: '#ffffff', strokeWidth: .4 }, hover: { fillOpacity: .85, cursor: 'pointer' } },
            series: { regions: [{ attribute: 'fill', scale: ['#cfe3c2', '#2c5c14'], normalizeFunction: 'polynomial', values: visitors }] },
            onRegionTooltipShow: function (event, tooltip, code) {
                var count = visitors[code] || 0;
                tooltip.text(tooltip.text() + ': ' + count.toLocaleString() + (count === 1 ? ' visitor' : ' visitors'));
            },
            onRegionClick: function (event, code) {
                if (visitors[code]) { window.location = openUrl.replace('__CC__', code); }
            }
        });
    })();
    </script>
@endif
@endsection
