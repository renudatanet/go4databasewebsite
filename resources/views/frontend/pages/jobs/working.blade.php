@extends('frontend.frontend-page-master')
{{-- /career/working-at-go4database, from FrontendController::jobs_working: a
     page about the company. Everything is from the admin: the wording and
     the What we do / How we work / quotes / offices blocks from Jobs >
     Working at Go4Database (App\Careers\WorkingPageText, with its defaults,
     and career_page_items), customer testimonials from Testimonial, and the
     teams from Job Category. Open roles stay on /career; this page links
     there. --}}
@php
    use App\Careers\Icon;
    $text = $working_text;
    $careers_name = get_static_option('career_with_us_page_' . $user_select_lang_slug . '_name') ?: __('Careers');
    $quotes = $working->items('quote');
    $offerings = $working->items('offering');
    $values = $working->items('value');
    $offices = $working->offices();
    $teams = $working->teams();
    $testimonials = $working->testimonials((int) $text->get('customers_count'));
    $story = $text->lines('story_text');
    // A media library image, or null when it has none or its file is gone
    // (then the card shows an initial or an icon, never a broken image).
    // Checked from the web root like get_attachment_image_by_id() itself,
    // and under public/ for a server started from the project root.
    $photo = function ($id, $size = 'large') {
        $img = !empty($id) ? get_attachment_image_by_id($id, $size, false) : null;
        if (!is_array($img) || empty($img['img_url'])) {
            return null;
        }
        $file = 'assets/uploads/media-uploader/' . basename((string) parse_url($img['img_url'], PHP_URL_PATH));
        return file_exists($file) || file_exists(public_path($file)) ? $img : null;
    };
    $role_count = fn (int $n) => $n === 1 ? $text->get('team_count_one') : $text->get('team_count', ['{count}' => $n]);
    $initial = fn ($name) => mb_strtoupper(mb_substr(trim((string) $name), 0, 1));
@endphp
@section('site-title')
    {{ $text->get('title') }}
@endsection
@section('page-title')
    {{ $text->get('title') }}
@endsection
@section('no-breadcrumb', '1')
@section('page-meta-data')
    <meta name="description" content="{{ $text->get('meta_description') }}">
@endsection
@section('og-meta')
    <meta property="og:url" content="{{ route('frontend.jobs.working') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $text->get('title') }}" />
    <meta property="og:description" content="{{ $text->get('meta_description') }}" />
@endsection
@section('career-schema')
    <script type="application/ld+json">{!! json_encode((new \App\Careers\JobSchema($job_board))->working($careers_name, $text->get('title'), $text->get('meta_description')), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endsection

@section('content')
@include('frontend.pages.blog.partials.style')
@include('frontend.pages.jobs.partials.working-style')

<div class="g4b g4w">
    {{-- Header: the homepage hero's banner (its gradient, dots, glow and
         data lines), with the quote cards overlapping its bottom edge the
         way the homepage's search card does. --}}
    <section class="g4w-hero @if($quotes->isNotEmpty()) g4w-hero--cards @endif">
        <span class="g4w-dots" aria-hidden="true"></span>
        <span class="g4w-orb g4w-orb--1" aria-hidden="true"></span>
        <span class="g4w-orb g4w-orb--2" aria-hidden="true"></span>
        <svg class="g4w-lines" viewBox="0 0 800 380" preserveAspectRatio="none" aria-hidden="true">
            <path class="g4w-line" d="M0,80 C150,60 250,200 400,160 S600,100 800,140"/>
            <path class="g4w-line" d="M0,200 C100,180 300,280 500,240 S700,180 800,220"/>
            <path class="g4w-line g4w-line--faint" d="M0,300 C200,260 350,340 550,300 S720,260 800,290"/>
            <path class="g4w-line g4w-line--faint" d="M100,0 C180,80 260,200 360,160 S520,100 650,180 S780,200 800,190"/>
        </svg>
        <div class="g4b-wrap">
            <nav class="g4w-crumbs" aria-label="Breadcrumb">
                <ol>
                    <li><a href="{{ route('frontend.jobs') }}">{{ $careers_name }}</a></li>
                    <li aria-current="page">{{ $text->get('title') }}</li>
                </ol>
            </nav>
            <h1 class="g4w-title">{{ $text->get('title') }}</h1>
            <p class="g4w-intro">{{ $text->get('intro') }}</p>
            <a class="g4b-btn" href="{{ route('frontend.jobs') }}" data-cta="working_hero">{{ $text->get('hero_button') }}</a>
        </div>
    </section>

    @if($quotes->isNotEmpty())
        <div class="g4w-people">
            <div class="g4b-wrap">
                {{-- Quotes from the team: a carousel with the middle card in
                     focus; without script, a row that scrolls sideways. The
                     first line of a quote is its headline, the rest its body. --}}
                <section class="g4w-carousel" aria-roledescription="carousel" aria-label="{{ $text->get('quotes_title') }}" data-count="{{ $quotes->count() }}">
                    <div class="g4w-track">
                        @foreach($quotes as $i => $quote)
                            @php
                                $img = $photo($quote->image, 'large');
                                $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $quote->body))));
                            @endphp
                            <figure class="g4w-qcard" data-index="{{ $i }}" aria-roledescription="slide" aria-label="{{ $i + 1 }} / {{ $quotes->count() }}">
                                <blockquote>
                                    @if(count($lines))<p class="g4w-qhead">{{ $lines[0] }}</p>@endif
                                    @foreach(array_slice($lines, 1) as $line)<p class="g4w-qbody">{{ $line }}</p>@endforeach
                                </blockquote>
                                <figcaption>
                                    <span class="g4w-qname">{{ $quote->title }}</span>
                                    @if($quote->subtitle)<span class="g4w-qrole">{{ $quote->subtitle }}</span>@endif
                                </figcaption>
                                {{-- A cut-out (transparent PNG) stands in front of the circle; an
                                     ordinary photo is shown as a round portrait inside it. --}}
                                <div class="g4w-qphoto @if($img) {{ $quote->cutout ? 'is-cutout' : 'is-round' }} @endif" aria-hidden="true">
                                    @if($img)
                                        <img src="{{ $img['img_url'] }}" alt="" loading="lazy">
                                    @else
                                        <span>{{ $initial($quote->title) }}</span>
                                    @endif
                                </div>
                            </figure>
                        @endforeach
                    </div>
                    @if($quotes->count() > 1)
                        <div class="g4w-qnav">
                            <button type="button" class="g4w-qbtn" data-step="-1" aria-label="{{ __('Previous quote') }}">{!! Icon::svg('back') !!}</button>
                            <span class="g4w-qdots">
                                @foreach($quotes as $i => $quote)<button type="button" data-go="{{ $i }}" aria-label="{{ __('Quote') }} {{ $i + 1 }}"></button>@endforeach
                            </span>
                            <button type="button" class="g4w-qbtn" data-step="1" aria-label="{{ __('Next quote') }}">{!! Icon::svg('arrow') !!}</button>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    @endif

    {{-- Who we are --}}
    @if(count($story))
        <section class="g4w-section" aria-labelledby="g4w-story">
            <div class="g4b-wrap g4w-story">
                <h2 id="g4w-story">{{ $text->get('story_title') }}</h2>
                <div>
                    @foreach($story as $paragraph)<p>{{ $paragraph }}</p>@endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- What we do --}}
    @if($offerings->isNotEmpty())
        <section class="g4w-section g4w-section--tint" aria-labelledby="g4w-offer">
            <div class="g4b-wrap">
                <header class="g4w-head g4w-head--center">
                    <h2 id="g4w-offer">{{ $text->get('offer_title') }}</h2>
                    @if($text->get('offer_text') !== '')<p>{{ $text->get('offer_text') }}</p>@endif
                </header>
                <ul class="g4w-cards">
                    @foreach($offerings as $i => $offering)
                        <li class="g4w-card">
                            <span class="g4w-icon" aria-hidden="true">{!! Icon::svg(['team', 'check', 'user', 'grow', 'globe', 'briefcase'][$i % 6]) !!}</span>
                            <h3>{{ $offering->title }}</h3>
                            @if($offering->body)<p>{{ $offering->body }}</p>@endif
                            @if($offering->link)
                                <a class="g4w-link" href="{{ url($offering->link) }}">{{ $text->get('offer_link') }} {!! Icon::svg('arrow') !!}<span class="g4b-sr"> {{ __('about') }} {{ $offering->title }}</span></a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- How we work --}}
    @if($values->isNotEmpty())
        <section class="g4w-section g4w-values" aria-labelledby="g4w-values">
            <div class="g4b-wrap">
                <header class="g4w-head g4w-head--center">
                    <h2 id="g4w-values">{{ $text->get('values_title') }}</h2>
                    @if($text->get('values_text') !== '')<p>{{ $text->get('values_text') }}</p>@endif
                </header>
                <ol class="g4w-value-list">
                    @foreach($values as $value)
                        <li>
                            <h3>{{ $value->title }}</h3>
                            @if($value->body)<p>{{ $value->body }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- What our customers say --}}
    @if($testimonials->isNotEmpty())
        <section class="g4w-section" aria-labelledby="g4w-customers">
            <div class="g4b-wrap">
                <header class="g4w-head g4w-head--center">
                    <h2 id="g4w-customers">{{ $text->get('customers_title') }}</h2>
                    @if($text->get('customers_text') !== '')<p>{{ $text->get('customers_text') }}</p>@endif
                </header>
                <ul class="g4w-reviews">
                    @foreach($testimonials as $review)
                        @php $img = $photo($review->image, 'thumb'); @endphp
                        <li>
                            <figure class="g4w-review">
                                <blockquote>{{ $review->description }}</blockquote>
                                <figcaption>
                                    @if($img)
                                        <img src="{{ $img['img_url'] }}" alt="{{ $review->name }}" width="44" height="44" loading="lazy">
                                    @else
                                        <span class="g4w-initial" aria-hidden="true">{{ $initial($review->name) }}</span>
                                    @endif
                                    <span><strong>{{ $review->name }}</strong>@if($review->designation)<small>{{ $review->designation }}</small>@endif</span>
                                </figcaption>
                            </figure>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Where we work --}}
    @if($offices->isNotEmpty())
        <section class="g4w-section g4w-section--tint" aria-labelledby="g4w-where">
            <div class="g4b-wrap">
                <header class="g4w-head g4w-head--center">
                    <h2 id="g4w-where">{{ $text->get('where_title') }}</h2>
                    @if($text->get('where_text') !== '')<p>{{ $text->get('where_text') }}</p>@endif
                </header>
                <ul class="g4w-grid g4w-grid--{{ min(3, max(1, $offices->count())) }}">
                    @foreach($offices as $office)
                        @php $img = $photo($office['item']->image); @endphp
                        <li class="g4w-office">
                            @if($img)
                                <img class="g4w-media" src="{{ $img['img_url'] }}" alt="{{ $img['img_alt'] ?? $office['item']->title }}" loading="lazy">
                            @else
                                <span class="g4w-media g4w-media--blank" aria-hidden="true">{!! Icon::svg('pin') !!}</span>
                            @endif
                            <h3>{{ $office['item']->title }}</h3>
                            @if($office['item']->subtitle)<p class="g4w-sub">{{ $office['item']->subtitle }}</p>@endif
                            @if($office['item']->body)<p>{{ $office['item']->body }}</p>@endif
                            @if($office['count'])
                                <a class="g4w-link" href="{{ route('frontend.jobs.search', ['search' => $office['filter']]) }}">{{ $text->get('office_link') }} ({{ $office['count'] }}) {!! Icon::svg('arrow') !!}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Our teams --}}
    @if($teams->isNotEmpty())
        <section class="g4w-section" aria-labelledby="g4w-teams">
            <div class="g4b-wrap">
                <header class="g4w-head g4w-head--center">
                    <h2 id="g4w-teams">{{ $text->get('teams_title') }}</h2>
                    @if($text->get('teams_text') !== '')<p>{{ $text->get('teams_text') }}</p>@endif
                </header>
                <ul class="g4w-grid g4w-grid--{{ min(3, $teams->count()) }}">
                    @foreach($teams as $i => $team)
                        @php $img = $photo($team['image']); @endphp
                        <li class="g4w-team">
                            @if($img)
                                <img class="g4w-media" src="{{ $img['img_url'] }}" alt="{{ $img['img_alt'] ?? $team['category']->title }}" loading="lazy">
                            @else
                                <span class="g4w-media g4w-media--blank" aria-hidden="true">{!! Icon::svg(['team', 'grow', 'globe', 'people', 'earn', 'briefcase'][$i % 6]) !!}</span>
                            @endif
                            <div class="g4w-team-body">
                                <h3>{{ $team['category']->title }}</h3>
                                <p class="g4w-sub">{{ $role_count($team['count']) }}</p>
                                <a class="g4w-link" href="{{ route('frontend.jobs.single', $team['category']->urlSlug()) }}">{{ $text->get('team_link') }} {!! Icon::svg('arrow') !!}</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Optional band, e.g. about remote or hybrid work --}}
    @if($text->get('remote_title') !== '')
        <section class="g4w-section g4w-section--tint g4w-band">
            <div class="g4b-wrap g4w-head g4w-head--center">
                <h2>{{ $text->get('remote_title') }}</h2>
                @if($text->get('remote_text') !== '')<p>{{ $text->get('remote_text') }}</p>@endif
                <a class="g4b-btn" href="{{ route('frontend.jobs') }}">{{ $text->get('remote_button') }}</a>
            </div>
        </section>
    @endif

    {{-- Closing banner --}}
    <section class="g4w-join">
        <div class="g4b-wrap">
            <div class="g4w-join-card">
                <div>
                    <h2>{{ $text->get('join_title') }}</h2>
                    @if($text->get('join_text') !== '')<p>{{ $text->get('join_text') }}</p>@endif
                </div>
                <a class="g4b-btn" href="{{ route('frontend.jobs') }}" data-cta="working_join">{{ $text->get('join_button') }}</a>
            </div>
        </div>
    </section>
</div>
@endsection
