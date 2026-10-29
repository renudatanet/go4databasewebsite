@extends('tracking::admin.layout')
@section('screen-title', 'Visitor')
@section('screen')
@php
    $latest = $sessions[0]['session'] ?? null;
    $name = $visitor->displayName();
    $title = $name ?: ($visitor->is_anonymous ? 'Anonymous visitor' : 'Visitor #' . $visitor->id);
    $initial = mb_strtoupper(mb_substr($name ?: '?', 0, 1));
@endphp
<a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.tracking.visitors') }}" class="g4t-faint">← Back</a>

<section class="g4t-card" style="margin:8px 0 16px">
    <div class="g4t-profile">
        <div class="g4t-avatar" aria-hidden="true">{{ $initial }}</div>
        <div style="min-width:200px">
            <h2 style="font-size:20px;margin:0">{{ $title }}</h2>
            <div class="g4t-muted">
                @if($visitor->email){{ $visitor->email }}@endif
                @if($visitor->company) · {{ $visitor->company }}@endif
                @if($visitor->phone) · {{ $visitor->phone }}@endif
            </div>
            <div style="margin-top:6px">
                @if($visitor->identified_at)<span class="g4t-pill is-known">Known since {{ $fmt::time($visitor->identified_at, $tz, 'j M Y') }}</span>@endif
                @if($visitor->app_user_id)<span class="g4t-pill is-app">App user {{ $visitor->app_user_id }}</span>@endif
                @if($visitor->is_anonymous)<span class="g4t-pill">Counted without a cookie</span>@endif
            </div>
        </div>
        <div class="g4t-facts">
            <div class="g4t-fact"><span class="g4t-fact-label">Location</span>@if($latest)<span class="g4t-flag">{{ $fmt::flag($latest->country_code) }}</span>{{ $latest->placeName() }}@else – @endif</div>
            <div class="g4t-fact"><span class="g4t-fact-label">Network</span>@if($latest)@include('tracking::admin.partials.network', ['org' => $latest->network_org, 'type' => $latest->network_type])@else – @endif</div>
            <div class="g4t-fact"><span class="g4t-fact-label">Device</span>@if($latest){{ ucfirst($latest->device_type) }} · {{ trim($latest->browser . ' ' . $latest->browser_version) }} · {{ trim($latest->os . ' ' . $latest->os_version) }}@else – @endif</div>
            <div class="g4t-fact"><span class="g4t-fact-label">First seen</span>{{ $fmt::time($visitor->first_seen_at, $tz, 'j M Y, H:i') }}@if($visitor->first_source) · via {{ $visitor->first_source }}@endif</div>
            <div class="g4t-fact"><span class="g4t-fact-label">Visits · pages · clicks</span>{{ $fmt::number($totals['sessions']) }} · {{ $fmt::number($totals['page_views']) }} · {{ $fmt::number($totals['clicks']) }}</div>
            <div class="g4t-fact"><span class="g4t-fact-label">Engaged in total</span>{{ $fmt::duration($totals['engaged_ms']) }}</div>
        </div>
    </div>
    @if($other_devices->isNotEmpty())
        <div class="g4t-card-note" style="margin-top:0">Same email seen on other browsers:
            @foreach($other_devices as $other)
                <a href="{{ route('admin.tracking.visitors.show', $other->id) }}">visitor #{{ $other->id }}</a>@if(!$loop->last), @endif
            @endforeach
        </div>
    @endif
</section>

<h3 style="font-size:16px;margin:0 0 10px">Journey <span class="g4t-faint">newest visit first</span></h3>
@forelse($sessions as $index => $entry)
    @php $s = $entry['session']; @endphp
    <details class="g4t-visit" {{ $index < 3 ? 'open' : '' }}>
        <summary>
            <h4>{{ $fmt::time($s->started_at, $tz, 'D j M Y, H:i') }}</h4>
            <span class="g4t-muted">from {{ $s->source ?: $fmt::channel($s->channel) }}@if($s->campaign) · campaign {{ $s->campaign }}@endif</span>
            <span class="g4t-faint">{{ $s->page_views }} {{ $s->page_views === 1 ? 'page' : 'pages' }} · {{ $s->clicks }} {{ $s->clicks === 1 ? 'click' : 'clicks' }} · {{ $fmt::duration($s->engaged_ms) }} engaged</span>
            <span class="g4t-faint"><span class="g4t-flag">{{ $fmt::flag($s->country_code) }}</span>{{ $s->city ?: $s->country }} · {{ ucfirst($s->device_type) }}</span>
        </summary>
        <ol class="g4t-steps">
            @foreach($entry['steps'] as $step)
                @php $item = $step['item']; @endphp
                <li class="is-{{ $step['kind'] }}">
                    <span class="t">{{ $fmt::time($step['at'], $tz, 'H:i:s') }}</span>
                    <span class="dot" aria-hidden="true"></span>
                    <span class="e">
                        @switch($step['kind'])
                            @case('pageview')
                                Viewed <a href="{{ route('admin.tracking.pages.show', $item->page_id) }}"><b>{{ $item->page->path ?? '?' }}</b></a>
                                <small>@if($item->engaged_ms) · {{ $fmt::duration($item->engaged_ms) }}@endif @if($item->max_scroll) · scrolled {{ $item->max_scroll }}%@endif</small>
                                @break
                            @case('click')
                                Clicked <b>{{ $item->label ?: ($item->link->label ?? 'a button') }}</b>
                                @if($item->name === 'signup_click')<span class="g4t-pill is-known">Sign up</span>@elseif($item->name === 'login_click')<span class="g4t-pill is-app">Log in</span>@endif
                                @if($item->link && $item->link->href)<small> → {{ $fmt::href($item->link->href, 60) }}</small>@endif
                                @break
                            @case('custom')
                                <b style="font-family:var(--mono)">{{ $item->name }}</b>
                                @if(!empty($item->properties))<small> · {{ collect($item->properties)->map(fn ($v, $k) => $k . ': ' . (is_bool($v) ? ($v ? 'yes' : 'no') : $v))->implode(', ') }}</small>@endif
                                @break
                            @case('app')
                                In the app: <b>{{ str_replace('_', ' ', $item->name) }}</b>
                                @break
                            @case('identity')
                                <b>{{ \App\Tracking\Models\Identity::LABELS[$item->source] ?? $item->source }}</b>
                                <small>as {{ $item->name ?: $item->email }}@if($item->name && $item->email) ({{ $item->email }})@endif</small>
                                @break
                        @endswitch
                    </span>
                </li>
            @endforeach
        </ol>
    </details>
@empty
    <div class="g4t-card"><div class="g4t-empty">No visits recorded for this visitor.</div></div>
@endforelse

<section class="g4t-card" style="margin-top:20px">
    <div class="g4t-card-body" style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:12px;align-items:center">
        <div><b>Forget this visitor</b><div class="g4t-faint">Deletes every visit, click and name held about them. This can't be undone.</div></div>
        <form method="post" action="{{ route('admin.tracking.visitors.destroy', $visitor->id) }}" id="g4t-forget">
            @csrf
            @method('DELETE')
            <label class="g4t-faint" style="display:flex;gap:6px;align-items:center;margin:0 0 6px"><input type="checkbox" required id="g4t-forget-confirm"> I want to delete this visitor's data</label>
            <button type="submit" class="g4t-btn is-danger">Delete everything about this visitor</button>
        </form>
    </div>
</section>
@endsection
