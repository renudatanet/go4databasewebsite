{{-- The newsletter band at the foot of the blog listings and posts. Posts to
     frontend.subscribe.newsletter, so addresses land in Newsletter Manage; the
     script for it is in partials/style. Passed in: $news_title, $news_text. --}}
<section class="g4b-news" aria-labelledby="g4b-news-title">
    <div class="g4b-wrap">
        <div class="g4b-news-card">
            <div class="g4b-news-copy">
                <h2 id="g4b-news-title">{{ $news_title }}</h2>
                @if(!empty($news_text))
                    <p>{{ $news_text }}</p>
                @endif
            </div>
            <form class="g4b-news-form" action="{{ route('frontend.subscribe.newsletter') }}" method="post" novalidate>
                @csrf
                <label class="g4b-sr" for="g4b-news-email">Work email</label>
                <input id="g4b-news-email" type="email" name="email" placeholder="you@company.com" autocomplete="email" required>
                <button type="submit">Subscribe</button>
                <p class="g4b-news-msg" role="status" aria-live="polite"></p>
            </form>
        </div>
    </div>
</section>
