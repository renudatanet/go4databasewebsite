{{-- The blog listing shared by the index, category and search pages.

     Everything on it comes from the admin panel: the page name and
     description from Page Settings, posts per page and the read-more text
     from Blog > Blog Page Settings, and the posts, topics and authors from
     their own CRUD screens. The data around the posts is built by
     FrontendController::blog_listing_data().

     Passed in: $mode ('index', 'topic', 'search' or 'author'), $heading,
     $lead, and optionally $crumb, $active_topic (a category id) and
     $search_term. --}}
@php
    $mode = $mode ?? 'index';
    // Older callers of blog-category (blog_author_page) don't pass these.
    $blog_topics = $blog_topics ?? collect();
    $blog_total_posts = $blog_total_posts ?? $all_blogs->total();
    $blog_total_authors = $blog_total_authors ?? 0;
    $blog_most_read = $blog_most_read ?? collect();
    $blog_faqs = $blog_faqs ?? collect();
    $posts = $all_blogs->getCollection();
    $blog_name = get_static_option('blog_page_' . $user_select_lang_slug . '_name') ?: 'Blog';
    // The lead story and the most-read list only on the plain first page.
    $is_front = $mode === 'index' && $all_blogs->currentPage() === 1 && $posts->count() > 0;
    $lead_post = $is_front ? $posts->first() : null;
    $grid_posts = $is_front ? $posts->slice(1) : $posts;
    // A card left alone on the last row runs the full width instead of
    // leaving two empty columns beside it (the first page's grid opens with
    // two wide cards, then rows of three).
    $grid_count = $grid_posts->count();
    $lone_last = $is_front ? ($grid_count === 1 || ($grid_count > 2 && ($grid_count - 2) % 3 === 1)) : $grid_count % 3 === 1;
    // The section wording from Blog > Blog Page Settings, per language; each
    // falls back to the default here when left empty.
    $text = function ($key, $default) use ($user_select_lang_slug) {
        $value = get_static_option('blog_page_' . $user_select_lang_slug . '_' . $key);
        return trim((string) $value) !== '' ? $value : $default;
    };
    $topic_url = function ($topic) {
        return route('frontend.blog.category', ['any' => $topic->slug ?: \Illuminate\Support\Str::slug($topic->name)]);
    };
@endphp

@include('frontend.pages.blog.partials.style')

{{-- Structured data: what this page is and the posts on it. The site-wide
     block (partials/og-meta) already has the Organization, the WebSite and
     this page's breadcrumb, so this points at those by @id instead of
     repeating them. Search results are left out; they are noindex. --}}
@if(in_array($mode, ['index', 'topic']) && $posts->count())
    @php
        $page_url = url()->current();
        $site_url = rtrim(url('/'), '/');
        $structured = [
            '@context' => 'https://schema.org',
            '@graph' => [
                array_filter([
                    '@type' => 'CollectionPage',
                    '@id' => $page_url . '#webpage',
                    'url' => $page_url,
                    'name' => $heading,
                    'description' => $lead ?: null,
                    'isPartOf' => ['@id' => $site_url . '#website'],
                    'publisher' => ['@id' => $site_url . '#organization'],
                    'mainEntity' => ['@id' => $page_url . '#articles'],
                ]),
                [
                    '@type' => 'ItemList',
                    '@id' => $page_url . '#articles',
                    'numberOfItems' => $all_blogs->total(),
                    'itemListElement' => $posts->values()->map(function ($post, $i) use ($all_blogs) {
                        return ['@type' => 'ListItem', 'position' => $all_blogs->firstItem() + $i, 'url' => route('frontend.blog.single', $post->slug), 'name' => $post->title];
                    })->all(),
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($structured, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endif

<div class="g4b">
    @php
        // The index's heading, from Blog Page Settings: "Page Heading (H1)"
        // then its green "Heading Highlighted Words". Left empty they read
        // "Explore insights on" + "B2B Growth"; a heading of your own has no
        // green words unless they are set too. Topic and search pages keep
        // their own heading.
        $custom_heading = trim((string) get_static_option('blog_page_' . $user_select_lang_slug . '_heading'));
        $hero_heading = $custom_heading !== '' ? $custom_heading : 'Explore insights on';
        $hero_highlight = $text('heading_highlight', $custom_heading !== '' ? '' : 'B2B Growth');
    @endphp
    <section class="g4b-hero g4b-hero--center">
        <span class="g4b-glow" aria-hidden="true"></span>
        <div class="g4b-wrap">
            <nav class="g4b-crumbs" aria-label="Breadcrumb">
                <ol>
                    <li><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                    @if($mode === 'index' && empty($crumb))
                        <li aria-current="page">{{ $blog_name }}</li>
                    @else
                        <li><a href="{{ route('frontend.blog') }}">{{ $blog_name }}</a></li>
                        <li aria-current="page">{{ $crumb }}</li>
                    @endif
                </ol>
            </nav>

            <div class="g4b-hero-copy">
                @if($lead_post)
                    {{-- The newest post, so the header always has something new in it. --}}
                    <a class="g4b-new" href="{{ route('frontend.blog.single', $lead_post->slug) }}">
                        <span class="g4b-new-tag"><span class="g4b-new-dot" aria-hidden="true"></span>{{ $text('new_label', 'New') }}</span>
                        <span class="g4b-new-title">{{ \Illuminate\Support\Str::limit($lead_post->title, 70) }}</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endif
                @if($mode === 'index')
                    <h1 class="g4b-title">{{ $hero_heading }}@if($hero_highlight !== '') <span class="g4b-mark">{{ $hero_highlight }}</span>@endif</h1>
                @else
                    <h1 class="g4b-title">{{ $heading }}</h1>
                @endif
                @if(!empty($lead))
                    <p class="g4b-lead">{{ $lead }}</p>
                @endif
            </div>

            <div class="g4b-search-wrap">
                <form class="g4b-search" action="{{ route('frontend.blog.search') }}" method="get" role="search" data-suggest-url="{{ route('frontend.blog.search') }}" data-popular-label="{{ $text('most_read_title', 'Most read') }}">
                    <label class="g4b-sr" for="g4b-search-input">Search articles</label>
                    <svg class="g4b-search-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/></svg>
                    <input id="g4b-search-input" type="search" name="search" value="{{ $search_term ?? '' }}"
                           placeholder="{{ $text('search_placeholder', 'Search articles and guides') }}" autocomplete="off" required
                           role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="g4b-suggest">
                    <button type="button" class="g4b-search-clear" aria-label="Clear search" hidden>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                    <kbd class="g4b-search-key" title="Press / to search">/</kbd>
                    <button type="submit" class="g4b-search-go">Search</button>
                    <div class="g4b-suggest" id="g4b-suggest" role="listbox" aria-label="Suggested articles" hidden></div>
                </form>
                <p class="g4b-search-hint">{{ $text('search_hint', 'Searches the titles and summaries of every article. Results appear as you type.') }}</p>
            </div>

            @if($blog_topics->count())
                <nav class="g4b-topics" aria-label="Topics">
                    <a class="g4b-topic {{ $mode === 'index' ? 'is-active' : '' }}" href="{{ route('frontend.blog') }}" @if($mode === 'index') aria-current="page" @endif>
                        {{ $text('all_label', 'All articles') }}
                    </a>
                    @foreach($blog_topics as $topic)
                        @php $active = isset($active_topic) && (int) $active_topic === (int) $topic->id; @endphp
                        <a class="g4b-topic {{ $active ? 'is-active' : '' }}" href="{{ $topic_url($topic) }}" @if($active) aria-current="page" @endif>
                            {{ $topic->name }} <span>{{ $topic->post_count }}</span>
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>
    </section>

    <section class="g4b-main">
        <div class="g4b-wrap">
            @if($posts->isEmpty())
                <div class="g4b-empty">
                    @if($mode === 'search')
                        <h2>No articles match “{{ $search_term }}”</h2>
                        <p>Try a shorter search, or pick a topic above.</p>
                    @else
                        <h2>No articles here yet</h2>
                        <p>New articles in this topic will appear here.</p>
                    @endif
                    <a class="g4b-btn" href="{{ route('frontend.blog') }}">See all articles</a>
                </div>
            @else
                @if($lead_post)
                    <div class="g4b-top {{ $blog_most_read->count() ? '' : 'is-solo' }}">
                        @include('frontend.pages.blog.partials.card', ['post' => $lead_post, 'variant' => 'featured'])

                        @if($blog_most_read->count())
                            <aside class="g4b-popular" aria-labelledby="g4b-popular-title">
                                <h2 id="g4b-popular-title">{{ $text('most_read_title', 'Most read') }}</h2>
                                <ol>
                                    @foreach($blog_most_read as $popular)
                                        @php $thumb = get_attachment_image_by_id($popular->image, 'thumb'); @endphp
                                        <li>
                                            <a href="{{ route('frontend.blog.single', $popular->slug) }}">
                                                <span class="g4b-rank" aria-hidden="true">{{ $loop->iteration }}</span>
                                                <span class="g4b-popular-text">
                                                    <span class="g4b-popular-title">{{ $popular->title }}</span>
                                                    <span class="g4b-popular-views">{{ number_format((int) $popular->total_visitors) }} views</span>
                                                </span>
                                                @if(!empty($thumb['img_url']))
                                                    <img src="{{ $thumb['img_url'] }}" alt="" width="64" height="64" loading="lazy">
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            </aside>
                        @endif
                    </div>
                @endif

                <div class="g4b-results">
                @if($grid_posts->count())
                    <div class="g4b-section-head">
                        <h2>{{ $is_front ? $text('latest_title', 'Latest articles') : ($list_heading ?? 'Articles') }}</h2>
                        @if($all_blogs->lastPage() > 1)
                            <span class="g4b-count" data-first="{{ $all_blogs->firstItem() }}" data-last="{{ $all_blogs->lastItem() }}" data-total="{{ $all_blogs->total() }}">{{ $all_blogs->firstItem() }}–{{ $all_blogs->lastItem() }} of {{ number_format($all_blogs->total()) }} articles</span>
                        @endif
                    </div>
                    <div class="g4b-grid {{ $is_front ? 'g4b-grid--front' : '' }}">
                        @foreach($grid_posts as $post)
                            @include('frontend.pages.blog.partials.card', ['post' => $post, 'variant' => 'card', 'across' => $loop->last && $lone_last, 'hide_topic' => $mode === 'topic'])
                        @endforeach
                    </div>
                @endif

                {{-- The only way on to older posts: no numbered page links. The
                     script adds the next batch here without leaving the page
                     or changing the address; as a plain link it still lets
                     search engines (and anyone without script) reach every
                     post. Those ?page=N pages are marked noindex. --}}
                @if($all_blogs->hasMorePages())
                    @php $left = $all_blogs->total() - $all_blogs->lastItem(); @endphp
                    <div class="g4b-more">
                        <a class="g4b-more-btn" href="{{ $all_blogs->nextPageUrl() }}" rel="next">
                            Show more articles <span class="g4b-more-left">{{ number_format($left) }} more</span>
                        </a>
                    </div>
                @endif
                </div>
            @endif
        </div>
    </section>

    @if($mode === 'index' && $blog_faqs->count())
        <section class="g4b-faq" aria-labelledby="g4b-faq-title">
            <div class="g4b-wrap">
                <div class="g4b-faq-grid">
                    <div class="g4b-faq-intro">
                        <h2 id="g4b-faq-title">{{ $text('faq_title', 'Frequently asked questions') }}</h2>
                        @if(!empty($blog_faq_category))
                            <p>From our help centre: {{ $blog_faq_category->name }}.</p>
                        @endif
                        <a class="g4b-btn g4b-btn--ghost" href="{{ route('frontend.faq') }}">See all FAQs</a>
                    </div>
                    <div class="g4b-faq-list">
                        @foreach($blog_faqs as $faq)
                            <details class="g4b-faq-item" @if(!empty($faq->is_open)) open @endif>
                                <summary>
                                    <span>{{ $faq->title }}</span>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                </summary>
                                <div class="g4b-faq-answer">{!! $faq->description !!}</div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    @include('frontend.pages.blog.partials.newsletter', [
        'news_title' => $text('newsletter_title', 'Get new articles by email'),
        'news_text' => $text('newsletter_text', $blog_topics->count() ? 'New posts on ' . $blog_topics->take(3)->pluck('name')->join(', ', ' and ') . ', sent to your inbox.' : ''),
    ])
</div>
