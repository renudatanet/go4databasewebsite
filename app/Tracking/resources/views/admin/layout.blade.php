@extends('backend.admin-master')
@section('site-title')
    {{ __('Tracking') }}@hasSection('screen-title') · @yield('screen-title')@endif
@endsection
@section('style')
    @include('tracking::admin.partials.style')
@endsection
@section('content')
@php
    $screens = [
        'admin.tracking.overview' => 'Overview',
        'admin.tracking.live' => 'Live now',
        'admin.tracking.pages' => 'Pages',
        'admin.tracking.clicks' => 'Clicks',
        'admin.tracking.visitors' => 'Visitors',
        'admin.tracking.locations' => 'Locations',
        'admin.tracking.sources' => 'Sources',
        'admin.tracking.settings' => 'Settings',
    ];
    // Moving between screens keeps the period and filters.
    $carry = request()->only(['range', 'from', 'to', 'country', 'device', 'channel', 'identified', 'hide_hosting']);
@endphp
<div class="col-12 g4t">
    <nav class="g4t-nav" aria-label="Tracking screens">
        @foreach($screens as $route => $name)
            <a href="{{ route($route, $route === 'admin.tracking.settings' || $route === 'admin.tracking.live' ? [] : $carry) }}"
               class="{{ request()->routeIs($route, $route . '.*') ? 'is-active' : '' }}"
               @if(request()->routeIs($route, $route . '.*')) aria-current="page" @endif>
                @if($route === 'admin.tracking.live')<span class="g4t-live-dot" aria-hidden="true"></span>@endif{{ $name }}
            </a>
        @endforeach
    </nav>

    @include('backend.partials.message')
    @if($errors->any())
        <div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
    @endif

    @yield('screen')

    <p class="g4t-credit">
        IP geolocation by <a href="https://db-ip.com" target="_blank" rel="noopener">DB-IP</a> (CC BY 4.0).
        Times are shown in {{ $tz->getName() }}.
    </p>
</div>
@endsection
@section('script')
    <script>
    (function () {
        // Period: show the date fields only for "Custom".
        var range = document.getElementById('g4t-range');
        if (range) {
            range.addEventListener('change', function () {
                var custom = range.value === 'custom';
                document.querySelectorAll('.g4t-custom').forEach(function (el) { el.hidden = !custom; });
                if (!custom) { range.form.submit(); }
            });
        }
    })();
    </script>
    @yield('screen-script')
@endsection
