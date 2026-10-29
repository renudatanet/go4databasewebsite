@extends('tracking::admin.layout')
@section('screen-title', 'Clicks')
@section('screen')
@include('tracking::admin.partials.toolbar', ['context' => $context, 'keep' => ['tab' => $tab, 'q' => $search]])
@php
    $query = $context->toQuery();
    $tabs = ['' => 'All clicks'] + \App\Tracking\Ingestion\LinkClassifier::KIND_LABELS + ['events' => 'Site events'];
@endphp
<nav class="g4t-tabs" aria-label="Kinds of click">
    @foreach($tabs as $key => $name)
        <a href="{{ route('admin.tracking.clicks', $query + array_filter(['tab' => $key])) }}" class="{{ (string) $tab === (string) $key ? 'is-active' : '' }}">{{ $name }}</a>
    @endforeach
</nav>

@if($tab === 'events')
<section class="g4t-card">
    <div class="g4t-card-head"><h3>Site events</h3></div>
    <p class="g4t-card-note" style="margin-top:4px">Events the site reports (View Email, searches, Ask AI…) and steps reported by the app.</p>
    @if(empty($events))
        <div class="g4t-empty">No events in this period.</div>
    @else
    <div class="g4t-table-wrap"><table>
        <thead><tr><th>Event</th><th>From</th><th class="n">Times</th><th class="n">Visitors</th></tr></thead>
        <tbody>
        @foreach($events as $event)
            <tr>
                <td class="g4t-strong" style="font-family:var(--mono)">{{ $event['name'] }}</td>
                <td>@if($event['type'] === 'app')<span class="g4t-pill is-app">The app</span>@else<span class="g4t-pill">Website</span>@endif</td>
                <td class="n">{{ $fmt::number($event['count']) }}</td>
                <td class="n">{{ $fmt::number($event['visitors']) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    @endif
</section>
@else
<section class="g4t-card">
    <div class="g4t-card-head">
        <h3>{{ $fmt::number($paginator->total()) }} links and buttons clicked</h3>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'page' => null]) }}">Download CSV</a>
    </div>
    <form method="get" class="g4t-card-body" style="display:flex;gap:10px">
        @foreach($query + array_filter(['tab' => $tab]) as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach
        <input type="search" name="q" value="{{ $search }}" placeholder="Search link text or address" aria-label="Search links" class="form-control" style="max-width:320px;height:34px">
        <button class="g4t-btn is-quiet" type="submit">Search</button>
    </form>
    @if(empty($rows))
        <div class="g4t-empty"><strong>No clicks match</strong>Try a longer period or another tab.</div>
    @else
    <div class="g4t-table-wrap"><table>
        <thead><tr><th>Link</th><th>Goes to</th><th>Kind</th><th class="n">Clicks</th><th class="n">Visitors</th><th class="n">On pages</th></tr></thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td class="g4t-strong">{{ $row['label'] ?: '(no text)' }}
                    @if($row['name'] === 'signup_click')<span class="g4t-pill is-known">Sign up</span>@elseif($row['name'] === 'login_click')<span class="g4t-pill is-app">Log in</span>@endif
                </td>
                <td class="g4t-path" title="{{ $row['href'] }}">{{ $row['href'] ? $fmt::href($row['href'], 64) : 'Button' }}</td>
                <td><span class="g4t-pill">{{ \App\Tracking\Ingestion\LinkClassifier::KIND_LABELS[$row['kind']] ?? $row['kind'] }}</span></td>
                <td class="n">{{ $fmt::number($row['clicks']) }}</td>
                <td class="n">{{ $fmt::number($row['clickers']) }}</td>
                <td class="n">{{ $fmt::number($row['pages']) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    {{ $paginator->links('pagination::bootstrap-4') }}
    @endif
</section>
<p class="g4t-faint" style="margin-top:8px">For click-through rates of the links on one page, open that page from Pages.</p>
@endif
@endsection
