@php
    $types = \App\Tracking\Data\NetworkType::LABELS;
    $class = ['business' => 'is-business', 'hosting' => 'is-hosting'][$type ?? ''] ?? '';
@endphp
@if(!empty($org))
    <span title="{{ $types[$type ?? 'unknown'] ?? '' }}">{{ $org }}</span>
    @if(($type ?? '') === 'business' || ($type ?? '') === 'hosting')
        <span class="g4t-pill {{ $class }}">{{ $type === 'business' ? 'Business' : 'Hosting/VPN' }}</span>
    @endif
@else
    <span class="g4t-faint">Unknown network</span>
@endif
