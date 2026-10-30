@extends('frontend.frontend-page-master')
{{-- The career board: /career, a category's page (/career/{category}) and
     the search (/career-search), from FrontendController::career_board.
     Roles and categories come from Jobs and Job Category in the admin, the
     wording from Jobs > Job Page Settings (App\Careers\CareerText, with its
     defaults), the page name and SEO from General Settings > Page Settings. --}}
@php
    use App\Careers\Icon;
    $career_text = new \App\Careers\CareerText($user_select_lang_slug);
    $page_name = get_static_option('career_with_us_page_' . $user_select_lang_slug . '_name') ?: __('Careers');
    $is_search = $search_term !== null;
    $category_text = $job_category && trim((string) $job_category->meta_description) !== '' && trim($job_category->meta_description) !== trim($job_category->title)
        ? $job_category->meta_description
        : ($job_category ? $career_text->get('category_text', ['{category}' => $job_category->title]) : null);
    $perks = $job_category || $is_search ? [] : $career_text->pairs('perks');
    $shown_from = $all_jobs->firstItem();
@endphp
@section('site-title')
    @if($job_category){{ $career_text->get('category_heading', ['{category}' => $job_category->title]) }}@elseif($is_search){{ $career_text->get('results_title', ['{query}' => $search_term]) }}@else{{ $page_name }}@endif
@endsection
@section('page-title')
    {{ $page_name }}
@endsection
@section('no-breadcrumb', '1')
@section('page-meta-data')
    @if($job_category)
        <meta name="description" content="{{ $category_text }}">
        @if($job_category->meta_tags)<meta name="tags" content="{{ $job_category->meta_tags }}">@endif
    @else
        <meta name="description" content="{{ get_static_option('career_with_us_page_'.$user_select_lang_slug.'_meta_description') }}">
        <meta name="tags" content="{{ get_static_option('career_with_us_page_'.$user_select_lang_slug.'_meta_tags') }}">
    @endif
    {!! render_og_meta_image_by_attachment_id(get_static_option('career_with_us_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection
@section('career-schema')
    <script type="application/ld+json">{!! json_encode((new \App\Careers\JobSchema($job_board))->board($page_name, $all_jobs->getCollection(), $job_category), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endsection

@section('content')
@include('frontend.pages.blog.partials.style')
@include('frontend.pages.jobs.partials.style')

<div class="g4b g4c">
    <section class="g4b-hero g4b-hero--center">
        <span class="g4b-glow" aria-hidden="true"></span>
        <div class="g4b-wrap">
            <nav class="g4b-crumbs" aria-label="Breadcrumb">
                <ol>
                    <li><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                    @if($job_category || $is_search)
                        <li><a href="{{ route('frontend.jobs') }}">{{ $page_name }}</a></li>
                        <li aria-current="page">{{ $job_category ? $job_category->title : __('Search') }}</li>
                    @else
                        <li aria-current="page">{{ $page_name }}</li>
                    @endif
                </ol>
            </nav>

            <div class="g4b-hero-copy">
                <span class="g4c-badge"><span class="g4b-new-dot" aria-hidden="true"></span>{{ $career_text->get('badge') }}</span>
                @if($job_category)
                    <h1 class="g4b-title">{{ $career_text->get('category_heading', ['{category}' => $job_category->title]) }}</h1>
                    <p class="g4b-lead">{{ $category_text }}</p>
                @elseif($is_search)
                    <h1 class="g4b-title">{{ $career_text->get('results_title', ['{query}' => $search_term]) }}</h1>
                @else
                    <h1 class="g4b-title">
                        {{ $career_text->get('heading') }}
                        @if($career_text->get('heading_highlight') !== '')<span class="g4b-mark">{{ $career_text->get('heading_highlight') }}</span>@endif
                    </h1>
                    <p class="g4b-lead">{{ $career_text->get('subtitle', ['{roles}' => $open_total]) }}</p>
                @endif
            </div>

            <div class="g4b-search-wrap">
                <form class="g4b-search" action="{{ route('frontend.jobs.search') }}" method="get" role="search"
                      data-suggest-url="{{ route('frontend.jobs.search') }}" data-param="search" data-noun="role" data-popular-label="{{ $career_text->get('roles_title') }}">
                    <label class="g4b-sr" for="g4b-search-input">{{ __('Search roles') }}</label>
                    <svg class="g4b-search-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/></svg>
                    <input id="g4b-search-input" type="search" name="search" value="{{ $search_term }}" maxlength="100"
                           placeholder="{{ $career_text->get('search_placeholder') }}" autocomplete="off" required
                           role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="g4b-suggest">
                    <button type="button" class="g4b-search-clear" aria-label="{{ __('Clear search') }}" hidden>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                    <kbd class="g4b-search-key" title="{{ __('Press / to search') }}">/</kbd>
                    <button type="submit" class="g4b-search-go">{{ __('Search') }}</button>
                    <div class="g4b-suggest" id="g4b-suggest" role="listbox" aria-label="{{ __('Matching roles') }}" hidden></div>
                </form>
            </div>

            @if(count($all_job_category))
                <nav class="g4b-topics" aria-label="{{ __('Teams') }}">
                    <a class="g4b-topic @if(!$job_category && !$is_search) is-active @endif" href="{{ route('frontend.jobs') }}" @if(!$job_category && !$is_search) aria-current="page" @endif>{{ $career_text->get('all_label') }} <span>{{ $open_total }}</span></a>
                    @foreach($all_job_category as $category)
                        @php $current = $job_category && $job_category->id === $category->id; @endphp
                        <a class="g4b-topic @if($current) is-active @endif" href="{{ route('frontend.jobs.single', $category->urlSlug()) }}" @if($current) aria-current="page" @endif>{{ $category->title }} <span>{{ $category->open_count }}</span></a>
                    @endforeach
                </nav>
            @endif
        </div>
    </section>

    <div class="g4b-wrap g4c-main">
        @if(count($perks))
            <section class="g4c-section" aria-labelledby="g4c-perks-title">
                <div class="g4c-head"><h2 id="g4c-perks-title">{{ $career_text->get('perks_title') }}</h2></div>
                <ul class="g4c-perks">
                    @foreach($perks as $i => [$perk_title, $perk_text])
                        <li class="g4c-perk">
                            <span class="g4c-perk-icon">{!! Icon::svg(Icon::PERKS[$i % count(Icon::PERKS)]) !!}</span>
                            <h3>{{ $perk_title }}</h3>
                            @if($perk_text !== '')<p>{{ $perk_text }}</p>@endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="g4c-section" id="roles" aria-labelledby="g4c-roles-title">
            <div class="g4c-head">
                <h2 id="g4c-roles-title">{{ $is_search ? __('Results') : $career_text->get('roles_title') }}</h2>
                @if($all_jobs->total())
                    <span class="g4c-count">{{ $all_jobs->total() }} {{ $all_jobs->total() === 1 ? __('open role') : __('open roles') }}</span>
                @endif
            </div>

            @if($all_jobs->total())
                <ul class="g4c-roles">
                    {{-- Not $job: a child view's variables reach the layout, and og-meta
                         gives any page holding a $job that role's title. --}}
                    @foreach($all_jobs as $listed_job)
                        @include('frontend.pages.jobs.partials.role-card', ['job' => $listed_job])
                    @endforeach
                </ul>
                @if($all_jobs->hasMorePages())
                    @php $left = $all_jobs->total() - $all_jobs->lastItem(); @endphp
                    <div class="g4c-more">
                        <a class="g4c-more-btn" href="{{ $all_jobs->nextPageUrl() }}" rel="next">
                            {{ __('Show') }} {{ $left }} {{ $left === 1 ? __('more role') : __('more roles') }}
                        </a>
                    </div>
                @endif
            @else
                <div class="g4c-empty">
                    <h3>{{ $career_text->get('empty_title') }}</h3>
                    <p>{{ $career_text->get('empty_text') }}</p>
                    @if($job_category || $is_search)
                        <a class="g4b-btn g4b-btn--ghost" href="{{ route('frontend.jobs') }}">{{ __('See all open roles') }}</a>
                    @endif
                </div>
            @endif
        </section>

        @include('frontend.pages.jobs.partials.cta')
    </div>
</div>
@endsection
