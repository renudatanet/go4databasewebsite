@extends('tracking::admin.layout')
@section('screen-title', 'Settings')
@section('screen')
@php $geoReady = $geo['city']['exists'] && $geo['asn']['exists']; @endphp
<div class="g4t-grid">
    <section class="g4t-card g4t-span-8">
        <div class="g4t-card-head"><h3>Tracking settings</h3></div>
        <form method="post" action="{{ route('admin.tracking.settings.update') }}" class="g4t-card-body g4t-form">
            @csrf
            <div class="form-group">
                <label style="display:flex;gap:8px;align-items:center"><input type="hidden" name="enabled" value="0"><input type="checkbox" name="enabled" value="1" {{ $settings->enabled() ? 'checked' : '' }}> Track visits and clicks</label>
                <div class="help">Turning this off stops tracking at once, on every page. Data already collected is kept.</div>
            </div>

            <div class="form-group">
                <label for="excluded_ips">Don't track these IP addresses</label>
                <textarea name="excluded_ips" id="excluded_ips" rows="4" class="form-control" placeholder="One per line, e.g. 203.0.113.7 or 203.0.113.0/24">{{ old('excluded_ips', implode("\n", $settings->excludedIps())) }}</textarea>
                <div class="help">Your office and team. Your current address is <code>{{ $yourIp }}</code>. This browser is already excluded, because it has opened these screens.</div>
            </div>

            <div class="form-group">
                <label for="consent_mode">Cookie consent</label>
                <select name="consent_mode" id="consent_mode" class="form-control">
                    <option value="eu_consent" {{ $settings->consentMode() === 'eu_consent' ? 'selected' : '' }}>Ask first in the EU, UK and Switzerland (recommended)</option>
                    <option value="consent_everywhere" {{ $settings->consentMode() === 'consent_everywhere' ? 'selected' : '' }}>Ask first everywhere</option>
                    <option value="everyone" {{ $settings->consentMode() === 'everyone' ? 'selected' : '' }}>Track everyone with a cookie</option>
                </select>
                <div class="help">Until a visitor accepts the site's cookie banner (General Settings → GDPR), they are counted without a cookie: their visits count, but they can't be followed across days and their full IP isn't kept. If the banner is switched off, nobody in those countries can accept it.</div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="ip_retention_days">Mask full IP addresses after (days)</label>
                    <input type="number" min="0" max="3650" name="ip_retention_days" id="ip_retention_days" class="form-control" value="{{ old('ip_retention_days', $settings->ipRetentionDays()) }}">
                    <div class="help">Location is worked out on arrival, so masking loses nothing in the reports. 0 masks them at the next nightly run.</div>
                </div>
                <div class="col-md-6 form-group">
                    <label for="data_retention_days">Delete visits after (days)</label>
                    <input type="number" min="30" max="3650" name="data_retention_days" id="data_retention_days" class="form-control" value="{{ old('data_retention_days', $settings->dataRetentionDays()) }}">
                    <div class="help">395 days keeps a full year to compare with.</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="timezone">Report timezone</label>
                    <select name="timezone" id="timezone" class="form-control">
                        @foreach($timezones as $zone)
                            <option value="{{ $zone }}" {{ $settings->timezone()->getName() === $zone ? 'selected' : '' }}>{{ $zone }}</option>
                        @endforeach
                    </select>
                    <div class="help">Days in the reports start at midnight here.</div>
                </div>
                <div class="col-md-6 form-group">
                    <label for="app_host">App address</label>
                    <input type="text" name="app_host" id="app_host" class="form-control" value="{{ old('app_host', $settings->appHost()) }}">
                    <div class="help">Sign up and Log in links to this host carry the visitor id, so sign-ups join the visitor's journey.</div>
                </div>
            </div>

            <button type="submit" class="g4t-btn">Save settings</button>
        </form>
    </section>

    <div class="g4t-span-4" style="display:flex;flex-direction:column;gap:16px;min-width:0">
        <section class="g4t-card">
            <div class="g4t-card-head"><h3>Location files</h3></div>
            <div class="g4t-card-body">
                @foreach(['city' => 'City (country, state, city)', 'asn' => 'Network (company or provider)'] as $kind => $name)
                    <div class="g4t-status {{ $geo[$kind]['exists'] ? 'is-ok' : '' }}" style="margin-bottom:6px">
                        <span>{{ $name }}<br><span class="g4t-faint">@if($geo[$kind]['exists']){{ number_format($geo[$kind]['size'] / 1048576, 1) }} MB · {{ $geo[$kind]['modified'] }}@else Not downloaded yet @endif</span></span>
                    </div>
                @endforeach
                @if(!$geoReady)
                    <div class="alert alert-warning" style="margin:10px 0">Without these files visits are still counted, but with no location or network.</div>
                @endif
                <form method="post" action="{{ route('admin.tracking.settings.geo') }}" style="margin-top:10px" id="g4t-geo-form">
                    @csrf
                    <button type="submit" class="g4t-btn is-quiet" id="g4t-geo-button">{{ $geoReady ? 'Update now' : 'Download now' }}</button>
                    <div class="help g4t-faint" style="margin-top:6px">About 65 MB from DB-IP; takes up to a minute. Updated automatically each month.</div>
                </form>
            </div>
        </section>

        <section class="g4t-card">
            <div class="g4t-card-head"><h3>Connection to the app</h3></div>
            <div class="g4t-card-body">
                <div class="g4t-faint" style="margin-bottom:4px">The app sends sign-ups and logins to</div>
                <code class="g4t-code">{{ $appEventUrl }}</code>
                <div class="g4t-faint" style="margin:10px 0 4px">Shared secret</div>
                @if($settings->appSecret())
                    <code class="g4t-code" id="g4t-secret">{{ $settings->appSecret() }}</code>
                    <button type="button" class="g4t-btn is-quiet" style="margin-top:8px" id="g4t-copy-secret">Copy secret</button>
                @else
                    <div class="g4t-muted">Not made yet. The app can't connect until it has one.</div>
                @endif
                <form method="post" action="{{ route('admin.tracking.settings.secret') }}" style="margin-top:8px">
                    @csrf
                    <button type="submit" class="g4t-btn is-quiet">{{ $settings->appSecret() ? 'Make a new secret' : 'Make the secret' }}</button>
                </form>
                <div class="help g4t-faint" style="margin-top:6px">The message format is in app/Tracking/README.md.</div>
            </div>
        </section>

        <section class="g4t-card">
            <div class="g4t-card-head"><h3>Stored data</h3></div>
            <div class="g4t-card-body">
                @foreach($counts as $name => $count)
                    <div style="display:flex;justify-content:space-between;padding:3px 0"><span class="g4t-muted">{{ $name }}</span><span class="num">{{ $fmt::number($count) }}</span></div>
                @endforeach
                <div class="g4t-faint" style="margin-top:8px">Last clean-up: {{ $settings->lastPruneAt() ? $fmt::time($settings->lastPruneAt(), $tz, 'j M Y, H:i') : 'not run yet' }}. It runs nightly once the server's scheduler is set up.</div>
            </div>
        </section>
    </div>
</div>
@endsection
@section('screen-script')
<script>
(function () {
    var copy = document.getElementById('g4t-copy-secret');
    if (copy) {
        copy.addEventListener('click', function () {
            var text = document.getElementById('g4t-secret').textContent;
            navigator.clipboard.writeText(text).then(function () { copy.textContent = 'Copied'; }, function () {
                var range = document.createRange(); range.selectNodeContents(document.getElementById('g4t-secret'));
                var selection = window.getSelection(); selection.removeAllRanges(); selection.addRange(range);
            });
        });
    }
    var geoForm = document.getElementById('g4t-geo-form');
    if (geoForm) {
        geoForm.addEventListener('submit', function () {
            var button = document.getElementById('g4t-geo-button');
            button.disabled = true; button.textContent = 'Downloading…';
        });
    }
})();
</script>
@endsection
