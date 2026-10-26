{{-- A blog post, for blog-single.blade.php.

     The article and everything about it come from the post (Blog in the
     admin, or the AI Blog Agent): title, excerpt, content, image, FAQs,
     topic and author. The data around it is built by
     FrontendController::blog_post_extras(). The wording is from Blog >
     Blog Single Page Settings, per language; each text falls back to the
     default given here when left empty. --}}
@php
    $text = function ($key, $default) use ($user_select_lang_slug) {
        $value = get_static_option('blog_single_page_' . $user_select_lang_slug . '_' . $key);
        return trim((string) $value) !== '' ? $value : $default;
    };
    $blog_name = get_static_option('blog_page_' . $user_select_lang_slug . '_name') ?: 'Blog';
    $topic = $blog_post->category;
    $topic_url = $topic ? route('frontend.blog.category', ['any' => $topic->slug ?: \Illuminate\Support\Str::slug($topic->name)]) : null;
    $author = $blog_post->authorData;
    $avatar = $author ? get_attachment_image_by_id($author->image, 'thumb') : [];
    $author_url = ($author && !empty($author->slug)) ? route('frontend.author.single', $author->slug) : null;
    $author_bio = $author ? trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $author->content)))) : '';
    $hero = get_attachment_image_by_id($blog_post->image, 'large');
    // The lead image keeps its own proportions: most are 2.7:1 banners with
    // the title written on them, which a fixed crop cut off. Its size in the
    // media library ("851 x 315 pixels") reserves the space before it loads.
    $hero_height = 416;
    if (!empty($hero) && preg_match('/(\d+)\s*x\s*(\d+)/', (string) optional(\App\MediaUpload::find($blog_post->image))->dimensions, $size) && $size[1] > 0 && $size[2] > 0) {
        $hero_height = (int) round(740 * $size[2] / $size[1]);
    }
    $updated = $blog_post->updated_at ?? $blog_post->created_at;
    $url = route('frontend.blog.single', $blog_post->slug);
    $toc_items = $toc['toc'] ?? [];
    $toc_title = $text('toc_title', 'On this page');
    // Images in the article load as they come into view; the lead image
    // above them is the one the page waits for. Empty paragraphs the editor
    // leaves between real ones are dropped, they doubled the spacing.
    $body = preg_replace('/<img(?![^>]*\sloading=)/i', '<img loading="lazy" decoding="async"', (string) ($toc['content'] ?? $blog_post->content));
    $body = preg_replace('/<p\b[^>]*>(?:\s|&nbsp;|&#160;|\x{00A0}|<br\s*\/?>)*<\/p>/iu', '', $body) ?? $body;
    $faqs = collect($blog_post->faqs ?? [])->filter(function ($faq) {
        return is_array($faq) && trim((string) ($faq['question'] ?? '')) !== '' && trim((string) ($faq['answer'] ?? '')) !== '';
    })->values();
    $share_links = [
        'LinkedIn' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($url),
        'X' => 'https://twitter.com/intent/tweet?url=' . rawurlencode($url) . '&text=' . rawurlencode($blog_post->title),
        'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($url),
    ];
    $industry_lists = $post_lists['industry'] ?? collect();
    $role_lists = $post_lists['roles'] ?? collect();
    $list_url = function ($list) { return route('frontend.list.single', ['service_slug' => ltrim($list->slug, '/')]); };
    // Brand Logos in the admin; until any are added, the four shown before.
    $logos = ($post_brands ?? collect())->map(function ($brand) {
        $img = get_attachment_image_by_id($brand->image, 'thumb');
        return ['src' => $img['img_url'] ?? '', 'alt' => $brand->title, 'url' => $brand->url];
    })->filter(function ($logo) { return $logo['src'] !== ''; })->values();
    if ($logos->isEmpty()) {
        $logos = collect(['zoho' => 'Zoho', 'hostinger' => 'Hostinger', 'google' => 'Google', 'cloudfare' => 'Cloudflare'])->map(function ($name, $file) {
            return ['src' => asset('assets/uploads/media-uploader/' . $file . '.webp'), 'alt' => $name, 'url' => null];
        })->values();
    }
@endphp

@include('frontend.pages.blog.partials.style')
@include('frontend.pages.blog.partials.post-style')

<div class="g4b g4p">
    <header class="g4b-hero g4p-hero">
        <div class="g4b-wrap">
            <nav class="g4b-crumbs" aria-label="Breadcrumb">
                <ol>
                    <li><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('frontend.blog') }}">{{ $blog_name }}</a></li>
                    @if($topic)
                        <li><a href="{{ $topic_url }}">{{ $topic->name }}</a></li>
                    @endif
                </ol>
            </nav>

            <h1 class="g4p-title">{{ $blog_post->title }}</h1>
            @if(!empty($blog_post->excerpt))
                <p class="g4p-lead">{{ $blog_post->excerpt }}</p>
            @endif

            <div class="g4p-meta">
                @if($author)
                    <span class="g4p-byline">
                        @if(!empty($avatar['img_url']))
                            <img class="g4p-byline-avatar" src="{{ $avatar['img_url'] }}" alt="" width="44" height="44">
                        @else
                            <span class="g4p-byline-avatar g4b-avatar--initial" aria-hidden="true">{{ mb_substr($author->name, 0, 1) }}</span>
                        @endif
                        <span class="g4p-byline-text">
                            @if($author_url)
                                <a href="{{ $author_url }}" rel="author">{{ $author->name }}</a>
                            @else
                                <span>{{ $author->name }}</span>
                            @endif
                            @if(!empty($author->position))
                                <span class="g4p-byline-role">{{ $author->position }}</span>
                            @endif
                        </span>
                    </span>
                @endif
                <span class="g4b-facts">
                    @if($updated)
                        <span>Updated <time datetime="{{ \Carbon\Carbon::parse($updated)->toDateString() }}">{{ \Carbon\Carbon::parse($updated)->format('d M Y') }}</time></span>
                    @endif
                    <span>{{ $post_minutes }} min read</span>
                </span>
            </div>
        </div>
    </header>

    <div class="g4b-wrap g4p-layout">
        <article class="g4p-article">
            @if(!empty($hero['img_url']))
                <figure class="g4p-figure">
                    <img src="{{ $hero['img_url'] }}" alt="{{ $hero['img_alt'] ?: $blog_post->title }}" width="740" height="{{ $hero_height }}" fetchpriority="high" decoding="async">
                    @if(!empty($blog_post->video_url))
                        {{-- main.js opens .videos-play links in its video popup. --}}
                        <a class="g4p-play videos-play mfp-iframe" href="{{ $blog_post->video_url }}" aria-label="Play the video">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l11-6.5z"/></svg>
                        </a>
                    @endif
                </figure>
            @endif

            @if(count($toc_items))
                {{-- On small screens the contents sit here, folded, instead of in the sidebar. --}}
                <details class="g4p-toc-mobile">
                    <summary>{{ $toc_title }} <span>{{ count($toc_items) }} sections</span></summary>
                    <ol>
                        @foreach($toc_items as $item)
                            <li><a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a></li>
                        @endforeach
                    </ol>
                </details>
            @endif

            <div class="g4p-body">{!! $body !!}</div>

            @if($faqs->count() || !empty($blog_post->faq_content))
                <section class="g4p-faq" aria-labelledby="g4p-faq-title">
                    <h2 id="g4p-faq-title">{{ $text('faq_title', 'Frequently asked questions') }}</h2>
                    @foreach($faqs as $faq)
                        <details class="g4b-faq-item" @if($loop->first) open @endif>
                            <summary>
                                <span>{{ $faq['question'] }}</span>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                            </summary>
                            <div class="g4b-faq-answer">{{ $faq['answer'] }}</div>
                        </details>
                    @endforeach
                    @if(!empty($blog_post->faq_content))
                        <div class="g4p-body g4p-faq-extra">{!! $blog_post->faq_content !!}</div>
                    @endif
                </section>
            @endif

            <div class="g4p-share">
                <span class="g4p-share-label">{{ get_static_option('blog_single_page_' . $user_select_lang_slug . '_share_title') ?: 'Share this article' }}</span>
                <ul>
                    @foreach($share_links as $network => $href)
                        <li>
                            <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" data-share="{{ strtolower($network) }}" aria-label="Share on {{ $network }} (opens in a new tab)">
                                @if($network === 'LinkedIn')
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3 9h4v12H3zM9 9h3.8v1.7h.05c.53-1 1.83-2 3.77-2 4 0 4.4 2.6 4.4 6V21h-4v-5.3c0-1.27-.02-2.9-1.77-2.9-1.78 0-2.05 1.4-2.05 2.8V21H9Z"/></svg>
                                @elseif($network === 'X')
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.75 3h3.07l-6.7 7.66L22 21h-6.17l-4.83-6.32L5.46 21H2.39l7.17-8.2L2 3h6.33l4.37 5.77L17.75 3Zm-1.08 16.2h1.7L7.4 4.72H5.57L16.67 19.2Z"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 9V6.5c0-.8.2-1.3 1.3-1.3H17V2h-2.8C11.7 2 10 3.6 10 6.3V9H8v3.5h2V22h3.5v-9.5H16l.5-3.5H14Z"/></svg>
                                @endif
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <button type="button" class="g4p-copy" data-url="{{ $url }}" aria-label="Copy the link to this article">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.07 0l3-3a5 5 0 0 0-7.07-7.07l-1.5 1.5M14 11a5 5 0 0 0-7.07 0l-3 3a5 5 0 0 0 7.07 7.07l1.5-1.5"/></svg>
                            <span class="g4p-copy-done" aria-live="polite"></span>
                        </button>
                    </li>
                </ul>
            </div>

            @if($author)
                <section class="g4p-author" aria-labelledby="g4p-author-title">
                    @if(!empty($avatar['img_url']))
                        <img class="g4p-author-avatar" src="{{ $avatar['img_url'] }}" alt="" width="72" height="72" loading="lazy">
                    @else
                        <span class="g4p-author-avatar g4b-avatar--initial" aria-hidden="true">{{ mb_substr($author->name, 0, 1) }}</span>
                    @endif
                    <div>
                        <h2 id="g4p-author-title">{{ $text('author_title', 'About the author') }}</h2>
                        <p class="g4p-author-name">
                            @if($author_url)<a href="{{ $author_url }}" rel="author">{{ $author->name }}</a>@else{{ $author->name }}@endif
                            @if(!empty($author->position))<span>{{ $author->position }}</span>@endif
                        </p>
                        @if($author_bio !== '')
                            <p class="g4p-author-bio">{{ \Illuminate\Support\Str::limit($author_bio, 320) }}</p>
                        @endif
                        @if($author_url)
                            <a class="g4p-text-link" href="{{ $author_url }}">More articles by {{ \Illuminate\Support\Str::before($author->name, ' ') ?: $author->name }}</a>
                        @endif
                    </div>
                </section>
            @endif
        </article>

        <aside class="g4p-aside" aria-label="Contents and sign-up">
            <div class="g4p-sticky">
                @if(count($toc_items))
                    <nav class="g4p-toc" aria-label="{{ $toc_title }}">
                        <p class="g4p-aside-title">{{ $toc_title }} <span class="g4p-toc-pct" aria-hidden="true"></span></p>
                        <ol>
                            @foreach($toc_items as $item)
                                <li><a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a></li>
                            @endforeach
                        </ol>
                    </nav>
                @endif
                <div class="g4p-cta">
                    <p class="g4p-cta-title">{{ $text('cta_title', 'Get 1200 free credits') }}</p>
                    <p>{{ $text('cta_text', 'Find verified business emails and phone numbers for your next campaign. No credit card required.') }}</p>
                    <a class="g4b-btn" href="{{ $text('cta_url', 'https://app.go4database.com/register?utm_source=blog&utm_medium=internal&utm_campaign=blog_post_cta') }}" data-cta="blog_post">{{ $text('cta_button', 'Sign up free') }}</a>
                </div>
            </div>
        </aside>
    </div>

    @if($post_related->count())
        <section class="g4p-related" aria-labelledby="g4p-related-title">
            <div class="g4b-wrap">
                <div class="g4b-section-head">
                    <h2 id="g4p-related-title">{{ get_static_option('blog_single_page_' . $user_select_lang_slug . '_related_post_title') ?: 'Related articles' }}</h2>
                    <a class="g4p-text-link" href="{{ route('frontend.blog') }}">All articles</a>
                </div>
                <div class="g4b-grid">
                    @foreach($post_related as $related)
                        @include('frontend.pages.blog.partials.card', ['post' => $related, 'variant' => 'card', 'across' => $loop->last && $post_related->count() === 1])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="g4p-more" aria-label="More from Go4Database">
        <div class="g4b-wrap g4p-more-grid">
            @if($industry_lists->count() || $role_lists->count())
                <div class="g4p-panel g4p-lists">
                    <h2>{{ $text('lists_title', 'Mailing lists for this topic') }}</h2>
                    <div class="g4p-tabs" role="tablist" aria-label="{{ $text('lists_title', 'Mailing lists for this topic') }}">
                        <button type="button" role="tab" id="g4p-tab-industry" aria-controls="g4p-panel-industry" aria-selected="true">{{ $text('lists_tab_industry', 'Verified mailing lists') }}</button>
                        <button type="button" role="tab" id="g4p-tab-roles" aria-controls="g4p-panel-roles" aria-selected="false" tabindex="-1">{{ $text('lists_tab_roles', 'Decision makers') }}</button>
                    </div>
                    <ul id="g4p-panel-industry" role="tabpanel" aria-labelledby="g4p-tab-industry">
                        @foreach($industry_lists as $list)
                            <li><a href="{{ $list_url($list) }}">{{ $list->title }}</a></li>
                        @endforeach
                    </ul>
                    <ul id="g4p-panel-roles" role="tabpanel" aria-labelledby="g4p-tab-roles">
                        @foreach($role_lists as $list)
                            <li><a href="{{ $list_url($list) }}">{{ $list->title }}</a></li>
                        @endforeach
                    </ul>
                    <a class="g4p-text-link" href="{{ route('frontend.b2c') }}">Browse all lists</a>
                </div>
            @endif

            <div class="g4p-panel g4p-about">
                <h2>{{ $text('about_title', 'About Go4Database') }}</h2>
                <p>{{ $text('about_text', 'Go4Database is a mass & global Business Contacts Search Platform. Having its mission to help almost all small companies, freelancers and startups to find their right audience for business generation at very affordable price.') }}</p>
                <ul class="g4p-stats">
                    <li>{{ $text('about_stat_one', '400 Mn records') }}</li>
                    <li>{{ $text('about_stat_two', '845 category lists') }}</li>
                </ul>
                <p class="g4p-logos-title">{{ $text('partners_title', 'Brands and partners') }}</p>
                <ul class="g4p-logos">
                    @foreach($logos as $logo)
                        <li>
                            @if(!empty($logo['url']))
                                <a href="{{ $logo['url'] }}" target="_blank" rel="noopener noreferrer"><img src="{{ $logo['src'] }}" alt="{{ $logo['alt'] }}" width="120" height="40" loading="lazy"></a>
                            @else
                                <img src="{{ $logo['src'] }}" alt="{{ $logo['alt'] }}" width="120" height="40" loading="lazy">
                            @endif
                        </li>
                    @endforeach
                </ul>
                <a class="g4p-text-link" href="{{ url('/contact') }}">Contact us</a>
            </div>

            @if($post_case_studies->count())
                <div class="g4p-panel g4p-cases">
                    <h2>{{ $text('case_title', 'Case studies') }}</h2>
                    <ul>
                        @foreach($post_case_studies as $case)
                            <li>
                                <a href="{{ route('frontend.case-study.single', $case->slug) }}">{{ $case->title }}</a>
                                @if(!empty($case->excerpt))
                                    <p>{{ \Illuminate\Support\Str::limit(trim(strip_tags($case->excerpt)), 120) }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <a class="g4p-text-link" href="{{ route('frontend.case-study') }}">All case studies</a>
                </div>
            @endif
        </div>
    </section>

    @include('frontend.pages.blog.partials.newsletter', [
        'news_title' => get_static_option('blog_page_' . $user_select_lang_slug . '_newsletter_title') ?: 'Get new articles by email',
        'news_text' => get_static_option('blog_page_' . $user_select_lang_slug . '_newsletter_text') ?: ($topic ? 'New posts on ' . $topic->name . ' and more, sent to your inbox.' : ''),
    ])
</div>
