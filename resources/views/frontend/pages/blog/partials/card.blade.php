{{-- One post in a blog listing. $post is a Blog with authorData and category
     loaded; $variant is 'featured' for the lead story, otherwise 'card'. --}}
@php
    $variant = $variant ?? 'card';
    $url = route('frontend.blog.single', $post->slug);
    // The 740px copy (Regenerate Media Image makes it); the helper falls back
    // to the original when it is missing. 350px grid- copies blur on 2x screens.
    $img = get_attachment_image_by_id($post->image, 'large');
    $topic = $post->category;
    $author = $post->authorData;
    $avatar = $author ? get_attachment_image_by_id($author->image, 'thumb') : [];
    // A media record can outlive its file; then the initial shows, not a
    // broken picture. Same relative path the image helper itself checks.
    if (!empty($avatar['img_url']) && !file_exists('assets/uploads/media-uploader/' . basename(parse_url($avatar['img_url'], PHP_URL_PATH)))) {
        $avatar = [];
    }
    $minutes = max(1, (int) ceil(str_word_count(strip_tags((string) $post->content)) / 225));
    $date = $post->updated_at ?? $post->created_at;
    $read_more = get_static_option('blog_page_' . $user_select_lang_slug . '_read_more_btn_text');
@endphp
<article class="g4b-card g4b-card--{{ $variant }}{{ !empty($across) ? ' is-across' : '' }}">
    <a class="g4b-card-media" href="{{ $url }}" tabindex="-1" aria-hidden="true">
        @if(!empty($img['img_url']))
            {{-- The link is hidden from screen readers (the title link names the
                 post), so the alt text is for image search. width/height only
                 give the browser the shape before CSS sizes it. --}}
            <img src="{{ $img['img_url'] }}" alt="{{ $img['img_alt'] ?: $post->title }}" width="740" height="416"
                 @if($variant === 'featured') fetchpriority="high" @else loading="lazy" @endif decoding="async">
        @endif
    </a>
    <div class="g4b-card-body">
        {{-- On a topic's own page every card would repeat the page's topic. --}}
        @if($topic && empty($hide_topic))
            <a class="g4b-tag" href="{{ route('frontend.blog.category', ['any' => $topic->slug ?: \Illuminate\Support\Str::slug($topic->name)]) }}">{{ $topic->name }}</a>
        @endif

        @if($variant === 'featured')
            <h2 class="g4b-card-title"><a href="{{ $url }}">{{ $post->title }}</a></h2>
        @else
            <h3 class="g4b-card-title"><a href="{{ $url }}">{{ $post->title }}</a></h3>
        @endif

        @if(!empty($post->excerpt))
            <p class="g4b-card-excerpt">{{ $post->excerpt }}</p>
        @endif

        <div class="g4b-card-meta">
            @if($author)
                <span class="g4b-author">
                    @if(!empty($avatar['img_url']))
                        <img class="g4b-avatar" src="{{ $avatar['img_url'] }}" alt="" width="28" height="28" loading="lazy">
                    @else
                        <span class="g4b-avatar g4b-avatar--initial" aria-hidden="true">{{ mb_substr($author->name, 0, 1) }}</span>
                    @endif
                    @if(!empty($author->slug))
                        <a href="{{ route('frontend.author.single', $author->slug) }}">{{ $author->name }}</a>
                    @else
                        <span>{{ $author->name }}</span>
                    @endif
                </span>
            @endif
            {{-- Kept together so the line breaks between author and facts, never mid-way. --}}
            <span class="g4b-facts">
                @if($date)
                    <span>Updated <time datetime="{{ \Carbon\Carbon::parse($date)->toDateString() }}">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</time></span>
                @endif
                <span>{{ $minutes }} min read</span>
            </span>
        </div>

        @if($variant === 'featured')
            {{-- The visible text is the admin's read-more wording; the hidden
                 title makes the link say where it goes, for search engines and
                 screen readers ("Read More" alone fails Lighthouse's link-text check). --}}
            <a class="g4b-btn" href="{{ $url }}" tabindex="-1">{{ $read_more ?: 'Read article' }}<span class="g4b-sr">: {{ $post->title }}</span></a>
        @endif
    </div>
</article>
