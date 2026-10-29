<div class="g4t-kpi {{ $class ?? '' }}">
    <div class="g4t-kpi-label">{{ $label }}</div>
    <div class="g4t-kpi-value">{{ $value }}</div>
    <div class="g4t-kpi-foot">
        @if(isset($current))
            @include('tracking::admin.partials.change', ['current' => $current, 'previous' => $previous ?? null, 'lowerIsBetter' => $lowerIsBetter ?? false])
        @endif
        @if(!empty($note))<span>{{ $note }}</span>@endif
    </div>
</div>
