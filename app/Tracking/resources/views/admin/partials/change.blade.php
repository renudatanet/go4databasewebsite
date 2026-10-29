{{-- Change against the previous period. $lowerIsBetter flips the colours (bounce rate). --}}
@php
    $change = \App\Tracking\Reports\Metrics::change($current, $previous);
    $good = $change !== null && $change != 0 && (($change > 0) xor !empty($lowerIsBetter));
    $bad = $change !== null && $change != 0 && !$good;
@endphp
@if($change !== null)
    <span class="g4t-change {{ $good ? 'is-good' : ($bad ? 'is-bad' : '') }}" title="vs the previous period">{{ $fmt::change($change) }}</span>
@endif
