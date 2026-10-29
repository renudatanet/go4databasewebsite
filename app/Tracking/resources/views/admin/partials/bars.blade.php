{{-- A ranked list with share bars. $items: list of [name, value, href?, extra?]; $total: what 100% is. --}}
@php $max = max(1, $total ?? collect($items)->max('value') ?? 1); @endphp
@if(empty($items))
    <div class="g4t-empty">{{ $empty ?? 'Nothing in this period yet.' }}</div>
@else
    <ul class="g4t-bars">
        @foreach($items as $item)
            <li style="--share: {{ round(($item['value'] / $max) * 100, 1) }}%">
                <span class="g4t-bar-name">
                    @if(!empty($item['prefix'])){{ $item['prefix'] }} @endif
                    @if(!empty($item['href']))<a href="{{ $item['href'] }}">{{ $item['name'] }}</a>@else{{ $item['name'] }}@endif
                </span>
                <span class="g4t-bar-value">{{ $fmt::number($item['value']) }}@if(!empty($item['extra']))<span class="g4t-faint"> · {{ $item['extra'] }}</span>@endif</span>
            </li>
        @endforeach
    </ul>
@endif
