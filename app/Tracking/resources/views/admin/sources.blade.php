@extends('tracking::admin.layout')
@section('screen-title', 'Sources')
@section('screen')
@include('tracking::admin.partials.toolbar', ['context' => $context, 'keep' => ['tab' => $tab]])
@php $query = $context->toQuery(); @endphp
<nav class="g4t-tabs" aria-label="Group by">
    @foreach($tabs as $key => $name)
        <a href="{{ route('admin.tracking.sources', $query + ['tab' => $key]) }}" class="{{ $tab === $key ? 'is-active' : '' }}">{{ $name }}</a>
    @endforeach
</nav>
<section class="g4t-card">
    <div class="g4t-card-head">
        <h3>{{ $tabs[$tab] }}</h3>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}">Download CSV</a>
    </div>
    @if($tab === 'campaign')<p class="g4t-card-note" style="margin-top:4px">Visits whose link carried a utm_campaign tag.</p>@endif
    @if(empty($rows))
        <div class="g4t-empty">No visits in this period.</div>
    @else
    <div class="g4t-card-body" style="padding:8px 0 0"><div class="g4t-table-wrap"><table>
        <thead><tr>
            @if($tab === 'campaign')<th>Campaign</th><th>Source / medium</th>@elseif($tab === 'source')<th>Source</th><th>Channel</th>@else<th>Channel</th>@endif
            <th class="n">Visitors</th><th class="n">Visits</th><th class="n">Bounce rate</th><th class="n">Engaged</th><th class="n">Clicked Sign up</th><th class="n">Sign-up rate</th>
        </tr></thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                @if($tab === 'campaign')
                    <td class="g4t-strong">{{ $row['campaign'] }}</td><td class="g4t-muted">{{ $row['source'] ?: '–' }} / {{ $row['medium'] ?: '–' }}</td>
                @elseif($tab === 'source')
                    <td class="g4t-strong">{{ $row['source'] ?: $fmt::channel($row['channel']) }}</td><td class="g4t-muted">{{ $fmt::channel($row['channel']) }}</td>
                @else
                    <td class="g4t-strong"><a href="{{ route('admin.tracking.sources', ['tab' => 'source', 'channel' => $row['channel']] + $query) }}">{{ $fmt::channel($row['channel']) }}</a></td>
                @endif
                <td class="n">{{ $fmt::number($row['visitors']) }}</td>
                <td class="n">{{ $fmt::number($row['sessions']) }}</td>
                <td class="n">{{ $fmt::percent($row['bounce_rate']) }}</td>
                <td class="n">{{ $fmt::duration($row['avg_engaged_ms']) }}</td>
                <td class="n">{{ $fmt::number($row['signups']) }}</td>
                <td class="n">{{ $fmt::percent($row['signup_rate']) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table></div></div>
    @endif
</section>
<p class="g4t-faint" style="margin-top:8px">AI assistants: visits from links in ChatGPT, Perplexity, Claude, Gemini, Copilot and others. Direct: typed in, bookmarked, or from an app that hides where it came from.</p>
@endsection
