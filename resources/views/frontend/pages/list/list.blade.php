@extends('frontend.frontend-page-master')
@section('site-title')
    {{get_static_option('list_page_'.$user_select_lang_slug.'_name')}}
@endsection
@section('page-title')
    {{get_static_option('list_page_'.$user_select_lang_slug.'_name')}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{get_static_option('list_page_'.$user_select_lang_slug.'_meta_description')}}">
    <meta name="tags" content="{{get_static_option('list_page_'.$user_select_lang_slug.'_meta_tags')}}">
    {!! render_og_meta_image_by_attachment_id(get_static_option('list_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection

@section('content')
{{-- The lists index (/list), in the blog's look: it reuses the blog's
     tokens, header, search box (with its instant results, from
     FrontendController::list as JSON) and chips from blog/partials/style,
     with its own pieces in list/partials/index-style.

     Everything on it comes from the admin panel: industries (List >
     Category) and their lists (List), counted by FrontendController::list;
     every text from List > List Page (list_page_{lang}_*), each falling
     back to the default here when left empty. --}}
@php
    $text = function ($key, $default) use ($user_select_lang_slug) {
        $value = get_static_option('list_page_' . $user_select_lang_slug . '_' . $key);
        return trim((string) $value) !== '' ? $value : $default;
    };
    $lines = function ($key, $default) use ($text) {
        return collect(preg_split('/\r\n|\r|\n/', $text($key, $default)))->map('trim')->filter()->values();
    };

    $total_lists = 0;
    $featured = [];
    $pipeline = [];
    foreach ($all_list as $c) {
        $n = (int) ($digest[(string) $c->id]['count'] ?? 0);
        $total_lists += $n;
        if ($n > 0) { $featured[] = $c; } else { $pipeline[] = $c; }
    }
    usort($featured, function ($a, $b) use ($digest) {
        return ($digest[(string) $b->id]['count'] ?? 0) <=> ($digest[(string) $a->id]['count'] ?? 0);
    });
    $numbers = [
        '{lists}' => number_format($total_lists),
        '{industries}' => count($all_list),
        '{with}' => count($featured),
    ];
    $fill = function ($value) use ($numbers) { return strtr($value, $numbers); };

    $page_name = get_static_option('list_page_' . $user_select_lang_slug . '_name') ?: 'Lists';
    $signup_url = $text('cta2_url', 'https://app.go4database.com/register?utm_source=ListIndex&utm_medium=Internal&utm_campaign=free_credits');
    $signup_text = $text('cta2_button', 'Get 1200 free credits');

    // Each industry gets a drawn icon matched on its name. One picked in the
    // admin is used instead, except the picker's default warning triangle,
    // which every industry had without anyone choosing it.
    $icon_paths = [
        'health|medic|hospital|pharma|clinic|dental|nurs' => '<path d="M19 14c1.5-1.5 3-3.2 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.8 0-3 .5-4.5 2-1.5-1.5-2.7-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4 3 5.5l7 7Z"/><path d="M3.2 12H8l1.5-3 3 6 1.5-3h6.8"/>',
        'real estate|property|housing|mortgage' => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v8h4M18 9h2a2 2 0 0 1 2 2v11h-4M10 6h4M10 10h4M10 14h4M10 18h4"/>',
        'financ|bank|insur|account|invest' => '<path d="M3 22h18M6 18v-7M10 18v-7M14 18v-7M18 18v-7M12 2 20 7H4Z"/>',
        'tech|software|it |saas|computer|cyber' => '<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6" rx="1"/><path d="M15 2v2M15 20v2M2 15h2M2 9h2M20 15h2M20 9h2M9 2v2M9 20v2"/>',
        'educat|school|universit|college' => '<path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c3 2.7 9 2.7 12 0v-5"/>',
        'auto|car|vehicle' => '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9L18 10l-2.7-3.6A2 2 0 0 0 13.7 6H7.3a2 2 0 0 0-1.7 1L3.5 10.5A2 2 0 0 0 3 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M9 17h6"/>',
        'aviat|airline|aero' => '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2Z"/>',
        'construct|build|engineer' => '<path d="M2 20h20M4 20v-4a8 8 0 0 1 16 0v4"/><path d="M10 9V6a2 2 0 0 1 4 0v3"/><path d="M12 12v4"/>',
        'consumer|retail|ecommerce|shop' => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/>',
        'food|beverage|restaurant|hospitality' => '<path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><path d="M6 2v2M10 2v2M14 2v2"/>',
        'logist|transport|shipping|freight|supply' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2M15 18H9M19 18h2a1 1 0 0 0 1-1v-3.6a1 1 0 0 0-.2-.6l-3.5-4.4A1 1 0 0 0 17.5 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
        'oil|gas|energy|power|utilit' => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.4-.5-2-1-3-1.1-2.1-.2-4.1 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.2.4-2.3 1-3.2.4 1.5 1.5 2.7 2.5 2.7Z"/>',
        'telecom|communication|network|media' => '<path d="M4.9 19.1A10 10 0 0 1 4.9 4.9M7.8 16.2a6 6 0 0 1 0-8.4M16.2 7.8a6 6 0 0 1 0 8.4M19.1 4.9a10 10 0 0 1 0 14.2"/><circle cx="12" cy="12" r="2"/>',
        'consult|business|service|legal|law' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
    ];
    $icon_for = function ($category) use ($icon_paths) {
        if (!empty($category->icon) && stripos($category->icon, 'exclamation-triangle') === false) {
            return '<i class="' . e($category->icon) . '" aria-hidden="true"></i>';
        }
        $name = ' ' . strtolower($category->name) . ' ';
        foreach ($icon_paths as $words => $path) {
            if (preg_match('/' . $words . '/', $name)) {
                return '<svg viewBox="0 0 24 24" aria-hidden="true">' . $path . '</svg>';
            }
        }
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2 10 5-10 5L2 7Z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/></svg>';
    };
    $anchor = function ($category) { return 'industry-' . ($category->slug ?: \Illuminate\Support\Str::slug($category->name)); };
    $tick = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
@endphp

@include('frontend.pages.blog.partials.style')
@include('frontend.pages.list.partials.index-style')

<div class="g4b g4l">
    <section class="g4b-hero g4b-hero--center">
        <span class="g4b-glow" aria-hidden="true"></span>
        <div class="g4b-wrap">
            <nav class="g4b-crumbs" aria-label="Breadcrumb">
                <ol>
                    <li><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                    <li aria-current="page">{{ $page_name }}</li>
                </ol>
            </nav>

            <div class="g4b-hero-copy">
                <span class="g4l-badge"><span class="g4b-new-dot" aria-hidden="true"></span>{{ $text('badge', 'B2B data directory') }}</span>
                <h1 class="g4b-title">
                    {{ $text('heading', 'Every B2B') }}
                    @if($text('heading_highlight', 'email list') !== '')<span class="g4b-mark">{{ $text('heading_highlight', 'email list') }}</span>@endif
                    {{ $text('heading_after', 'we hold') }}
                </h1>
                <p class="g4b-lead">{{ $fill($text('subtitle', '{lists} verified lists across {industries} industries. Searching is free, credits are only spent when you reveal a contact.')) }}</p>
            </div>

            <div class="g4b-search-wrap">
                <form class="g4b-search" action="{{ route('frontend.b2c') }}" method="get" role="search" data-suggest-url="{{ route('frontend.b2c') }}" data-param="q" data-noun="list" data-popular-label="{{ $text('popular_label', 'Popular lists') }}">
                    <label class="g4b-sr" for="g4b-search-input">Search every list</label>
                    <svg class="g4b-search-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/></svg>
                    <input id="g4b-search-input" type="search" name="q" value="{{ $search_query }}"
                           placeholder="{{ $text('search_placeholder', 'Try hospitals, CEO, mortgage, telehealth…') }}" autocomplete="off" required
                           role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="g4b-suggest">
                    <button type="button" class="g4b-search-clear" aria-label="Clear search" hidden>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                    <kbd class="g4b-search-key" title="Press / to search">/</kbd>
                    <button type="submit" class="g4b-search-go">Search</button>
                    <div class="g4b-suggest" id="g4b-suggest" role="listbox" aria-label="Suggested lists" hidden></div>
                </form>
            </div>

            @if(count($featured))
                <nav class="g4b-topics" aria-label="Industries">
                    @foreach($featured as $c)
                        <a class="g4b-topic" href="#{{ $anchor($c) }}">{{ $c->name }} <span>{{ number_format($digest[(string) $c->id]['count'] ?? 0) }}</span></a>
                    @endforeach
                    <a class="g4b-topic g4l-topic-signup" href="{{ $signup_url }}" target="_blank" rel="noopener" data-cta="list_hero">{{ $signup_text }}</a>
                </nav>
            @endif
        </div>
    </section>

    <div class="g4b-wrap g4l-main">
        @if($search_query !== '')
            <section class="g4l-section" id="results" aria-labelledby="g4l-results-title">
                <div class="g4b-section-head">
                    <h2 id="g4l-results-title">{{ str_replace('{query}', $search_query, $text('results_title', 'Results for “{query}”')) }}</h2>
                    @if(count($results))
                        <span>
                            @if($result_total > count($results))
                                First {{ count($results) }} of {{ number_format($result_total) }} matches
                            @else
                                {{ count($results) }} {{ count($results) == 1 ? 'match' : 'matches' }}
                            @endif
                        </span>
                    @endif
                </div>
                @if(count($results))
                    <div class="g4l-results">
                        @foreach($results as $r)
                            @php $cat = $all_list->firstWhere('id', $r->categories_id); @endphp
                            <a class="g4l-result" href="{{ route('frontend.list.single', ['service_slug' => ltrim($r->slug, '/')]) }}">
                                <span class="g4l-result-title">{{ $r->title }}</span>
                                @if($cat)<span class="g4b-tag">{{ $cat->name }}</span>@endif
                                <svg class="g4l-arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @endforeach
                    </div>
                    @if($result_total > count($results))
                        <p class="g4l-note">{{ $text('narrow_hint', 'Narrow your search to see the rest, e.g. add a job title or a country.') }}</p>
                    @endif
                @else
                    <div class="g4l-empty">
                        <h3>{{ $text('empty_title', 'Nothing matches that yet') }}</h3>
                        <p>{{ $text('cta_text', 'We build to your criteria across the global B2B database: industry, job title, location, company size, revenue.') }}</p>
                        <a class="g4b-btn" href="{{ $text('cta_url', url('/contact')) }}">{{ $text('cta_button', 'Request a custom list') }}</a>
                    </div>
                @endif
            </section>
        @endif

        <section class="g4l-section" id="browse" aria-labelledby="g4l-browse-title">
            <div class="g4l-head">
                <h2 id="g4l-browse-title">{{ $text('browse_title', 'Browse by industry') }}</h2>
                <p>{{ $fill($text('browse_text', '{with} of {industries} industries have lists published, holding {lists} between them.')) }}</p>
            </div>

            @if(count($featured))
                <div class="g4l-grid">
                    @foreach($featured as $c)
                        @php
                            $d = $digest[(string) $c->id] ?? ['count' => 0, 'samples' => []];
                            $card_words = ['{count}' => number_format($d['count']), '{industry}' => \Illuminate\Support\Str::lower($c->name), '{Industry}' => $c->name];
                        @endphp
                        <article class="g4l-card" id="{{ $anchor($c) }}">
                            <header class="g4l-card-head">
                                <span class="g4l-icon">{!! $icon_for($c) !!}</span>
                                <div class="g4l-card-title">
                                    <h3>{{ $c->name }}</h3>
                                    <span>{{ strtr($text('card_count_text', '{count} verified lists'), $card_words) }}</span>
                                </div>
                            </header>
                            <ul class="g4l-samples">
                                @foreach($d['samples'] as $s)
                                    <li>
                                        <a href="{{ route('frontend.list.single', ['service_slug' => $s['slug']]) }}" title="{{ $s['title'] }}">
                                            <svg class="g4l-mail" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                                            <span>{{ $s['title'] }}</span>
                                            <svg class="g4l-go" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <a class="g4l-all" href="{{ route('frontend.b2c', ['q' => $c->name]) }}#results">
                                <span>{{ strtr($text('all_lists_text', 'Browse all {count} {industry} lists'), $card_words) }}</span>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        </article>
                    @endforeach
                </div>
            @endif

            @if(count($pipeline))
                <div class="g4l-pipeline">
                    <div class="g4l-pipeline-head">
                        <strong>{{ $text('pipeline_title', 'Next in the pipeline') }}</strong>
                        <span>{{ $text('pipeline_text', 'These industries publish with the next data release.') }}</span>
                    </div>
                    <ul class="g4l-chips">
                        @foreach($pipeline as $c)
                            <li><span class="g4l-chip-icon">{!! $icon_for($c) !!}</span>{{ $c->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>

        <section class="g4l-cta" aria-labelledby="g4l-cta-title">
            <div class="g4l-cta-copy">
                <h2 id="g4l-cta-title">{{ $text('cta_title', 'Can’t find the list you need?') }}</h2>
                <p>{{ $text('cta_text', 'We build to your criteria across the global B2B database: industry, job title, location, company size, revenue.') }}</p>
                <ul class="g4l-holds g4l-cta-notes">
                    @foreach($lines('cta_notes', "Real-time email verification\n95% accuracy guarantee\nNo card required") as $note)
                        <li>{!! $tick !!}{{ $note }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="g4l-cta-btns">
                <a class="g4b-btn" href="{{ $text('cta_url', url('/contact')) }}" data-cta="list_custom">{{ $text('cta_button', 'Request a custom list') }}</a>
                <a class="g4b-btn g4b-btn--ghost" href="{{ $signup_url }}" target="_blank" rel="noopener" data-cta="list_signup">{{ $signup_text }}</a>
            </div>
        </section>
    </div>
</div>
@endsection
