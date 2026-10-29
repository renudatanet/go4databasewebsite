@extends('tracking::admin.layout')
@section('screen-title', 'Visitors')
@section('screen')
@include('tracking::admin.partials.toolbar', ['context' => $context, 'keep' => ['q' => $search, 'segment' => $segment]])
@php $query = $context->toQuery(); @endphp
<nav class="g4t-tabs" aria-label="Visitor groups">
    @foreach($segments as $key => $name)
        <a href="{{ route('admin.tracking.visitors', $query + array_filter(['segment' => $key, 'q' => $search])) }}" class="{{ $segment === $key ? 'is-active' : '' }}">{{ $name }}</a>
    @endforeach
</nav>
<section class="g4t-card">
    <div class="g4t-card-head">
        <h3>{{ $fmt::number($paginator->total()) }} visitors</h3>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'page' => null]) }}">Download CSV</a>
    </div>
    <form method="get" class="g4t-card-body" style="display:flex;gap:10px">
        @foreach($query + array_filter(['segment' => $segment]) as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach
        <input type="search" name="q" value="{{ $search }}" placeholder="Name, email, company, network or city" aria-label="Search visitors" class="form-control" style="max-width:340px;height:34px">
        <button class="g4t-btn is-quiet" type="submit">Search</button>
    </form>
    @if(empty($rows))
        <div class="g4t-empty"><strong>No visitors match</strong>Try a longer period or another group.</div>
    @else
    <div class="g4t-table-wrap"><table>
        <thead><tr><th>Visitor</th><th>Where</th><th>Network</th><th>Device</th><th>First came from</th><th class="n">Visits</th><th class="n">Pages</th><th class="n">Last seen</th></tr></thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td>
                    <a class="g4t-strong" href="{{ route('admin.tracking.visitors.show', $row['visitor_id']) }}">{{ $row['name'] ?: ($row['email'] ?: ($row['is_anonymous'] ? 'Anonymous visitor' : 'Visitor #' . $row['visitor_id'])) }}</a>
                    @if($row['name'] || $row['email'])<span class="g4t-pill is-known">Known</span>@endif
                    @if($row['total_sessions'] > 1)<span class="g4t-pill">Returning</span>@endif
                    @if($row['company'] || ($row['name'] && $row['email']))<span class="g4t-sub">{{ $row['company'] ?: $row['email'] }}</span>@endif
                </td>
                <td style="white-space:nowrap"><span class="g4t-flag">{{ $fmt::flag($row['country_code']) }}</span>{{ implode(', ', array_filter(array_unique([$row['city'], $row['region']]))) ?: ($row['country'] ?: 'Unknown') }}</td>
                <td>@include('tracking::admin.partials.network', ['org' => $row['network_org'], 'type' => $row['network_type']])</td>
                <td class="g4t-muted" style="white-space:nowrap">{{ ucfirst((string) $row['device_type']) }}<span class="g4t-sub">{{ trim($row['browser'] . ' · ' . $row['os'], ' ·') }}</span></td>
                <td class="g4t-muted">{{ $row['first_source'] ?: '–' }}</td>
                <td class="n">{{ $fmt::number($row['sessions']) }}</td>
                <td class="n">{{ $fmt::number($row['page_views']) }}</td>
                <td class="n">{{ $fmt::time($row['last_seen'], $tz) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    {{ $paginator->links('pagination::bootstrap-4') }}
    @endif
</section>
<p class="g4t-faint" style="margin-top:8px">A visitor is one browser. Names come from forms, the newsletter and the app; they are never guessed from an IP address. "Anonymous" visitors are from the EU, UK or Switzerland and haven't accepted cookies, so they are counted for one day without a cookie.</p>
@endsection
