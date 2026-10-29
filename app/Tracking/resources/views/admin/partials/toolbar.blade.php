{{-- Period and filters for a report screen. $keep: other query values to carry along (tab, search...). --}}
@php
    $range = $context->range;
    $filters = $context->filters;
    $keep = $keep ?? [];
@endphp
<form class="g4t-toolbar" method="get" action="{{ url()->current() }}" id="g4t-toolbar">
    @foreach($keep as $name => $value)
        @if($value !== null && $value !== '')
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endif
    @endforeach
    <label for="g4t-range">Period
        <select name="range" id="g4t-range">
            @foreach(\App\Tracking\Reports\DateRange::PRESETS as $key => $name)
                <option value="{{ $key }}" {{ ($range->preset === $key) ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </label>
    <label for="g4t-from" class="g4t-custom" @if($range->preset !== 'custom') hidden @endif>From
        <input type="date" name="from" id="g4t-from" value="{{ $range->localStart()->format('Y-m-d') }}">
    </label>
    <label for="g4t-to" class="g4t-custom" @if($range->preset !== 'custom') hidden @endif>To
        <input type="date" name="to" id="g4t-to" value="{{ $range->localLastDay()->format('Y-m-d') }}">
    </label>
    <label for="g4t-country">Country
        <select name="country" id="g4t-country">
            <option value="">All countries</option>
            @foreach($filterOptions['countries'] as $code => $name)
                <option value="{{ $code }}" {{ ($filters->country === $code) ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </label>
    <label for="g4t-device">Device
        <select name="device" id="g4t-device">
            <option value="">All devices</option>
            @foreach($filterOptions['devices'] as $key => $name)
                <option value="{{ $key }}" {{ ($filters->device === $key) ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </label>
    <label for="g4t-channel">Came from
        <select name="channel" id="g4t-channel">
            <option value="">Anywhere</option>
            @foreach($filterOptions['channels'] as $key => $name)
                <option value="{{ $key }}" {{ ($filters->channel === $key) ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </label>
    <label class="g4t-check" for="g4t-identified"><input type="checkbox" name="identified" value="1" id="g4t-identified" {{ ($filters->identifiedOnly) ? 'checked' : '' }}> Known by name</label>
    <label class="g4t-check" for="g4t-hosting"><input type="checkbox" name="hide_hosting" value="1" id="g4t-hosting" {{ ($filters->hideHosting) ? 'checked' : '' }}> Hide hosting / VPN</label>
    <button class="g4t-btn" type="submit">Apply</button>
    @if(!$filters->isEmpty())
        <a class="g4t-btn is-quiet" href="{{ url()->current() . '?' . http_build_query(array_filter(['range' => $range->preset] + $keep)) }}">Clear filters</a>
    @endif
    <span class="g4t-period">{{ $range->label() }}</span>
</form>
