@extends('frontend.frontend-page-master')
@section('site-title')
    {{ get_static_option('faq_page_'.$user_select_lang_slug.'_name') ?: __('Frequently Asked Questions') }}
@endsection
@section('page-title')
    {{ get_static_option('faq_page_'.$user_select_lang_slug.'_name') ?: __('Frequently Asked Questions') }}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{get_static_option('faq_page_'.$user_select_lang_slug.'_meta_description')}}">
    <meta name="tags" content="{{get_static_option('faq_page_'.$user_select_lang_slug.'_meta_tags')}}">
    {!! render_og_meta_image_by_attachment_id(get_static_option('faq_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection
@push('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('assets/frontend/css/faq.css?v=2')}}">
@endpush

@section('content')
@php
    /* One list to render from, so categorised and uncategorised questions
       take the same path through the page instead of two near-identical loops. */
    $fq_groups = collect();

    foreach (($all_categories ?? collect()) as $fq_cat) {
        $fq_groups->push([
            'key'  => 'c' . $fq_cat->id,
            'name' => $fq_cat->name,
            'faqs' => $fq_cat->faqs,
        ]);
    }

    if (($uncategorized_faqs ?? collect())->isNotEmpty()) {
        $fq_groups->push([
            'key'  => 'general',
            'name' => __('General'),
            'faqs' => $uncategorized_faqs,
        ]);
    }

    $fq_total = $fq_groups->sum(fn ($g) => $g['faqs']->count());
@endphp

<div class="fq">

  {{-- ===================== HERO + SEARCH ===================== --}}
  <section class="fq-hero">
    <div class="fq-shell">
      <span class="fq-kicker">{{ __('Help centre') }}</span>
      <h1 class="fq-h1">{{ __('Frequently asked') }} <span>{{ __('questions') }}</span></h1>
      <p class="fq-lede">{{ __('Answers about your account, our data, orders, billing and privacy. Search below, or pick a topic.') }}</p>

      @if($fq_total)
      <div class="fq-search">
        <i data-lucide="search" class="fq-search-icon"></i>
        <input type="search" id="fq-search" class="fq-search-input" autocomplete="off"
               placeholder="{{ __('Search all questions...') }}"
               aria-label="{{ __('Search the questions') }}">
        <button type="button" id="fq-clear" class="fq-search-clear" hidden aria-label="{{ __('Clear search') }}">
          <i data-lucide="x"></i>
        </button>
      </div>

      <p class="fq-meta">
        <span id="fq-meta-default">{{ $fq_total }} {{ $fq_total == 1 ? __('question') : __('questions') }} {{ __('across') }} {{ $fq_groups->count() }} {{ $fq_groups->count() == 1 ? __('topic') : __('topics') }}</span>
        <span id="fq-meta-search" hidden></span>
      </p>
      @endif
    </div>
  </section>

  {{-- ===================== TOPICS + ANSWERS ===================== --}}
  <section class="fq-body">
    <div class="fq-shell fq-grid">

      @if($fq_groups->count() > 1)
      <aside class="fq-rail" aria-label="{{ __('Topics') }}">
        <div class="fq-rail-inner">
          <span class="fq-rail-label">{{ __('Topics') }}</span>
          <nav class="fq-rail-nav">
            @foreach($fq_groups as $g)
              <a href="#fq-{{ $g['key'] }}" class="fq-rail-link" data-target="fq-{{ $g['key'] }}">
                <span>{{ $g['name'] }}</span>
                <em>{{ $g['faqs']->count() }}</em>
              </a>
            @endforeach
          </nav>
          <div class="fq-rail-help">
            <p>{{ __('Still stuck?') }}</p>
            <a href="{{ route('frontend.contact') }}" class="fq-rail-cta">
              {{ __('Talk to our team') }} <i data-lucide="arrow-right"></i>
            </a>
          </div>
        </div>
      </aside>
      @endif

      <div class="fq-main">
        @forelse($fq_groups as $g)
          <section class="fq-group" id="fq-{{ $g['key'] }}">
            <header class="fq-group-head">
              <h2 class="fq-group-title">{{ $g['name'] }}</h2>
              <span class="fq-group-count">{{ $g['faqs']->count() }}</span>
            </header>

            <div class="fq-list">
              @foreach($g['faqs'] as $data)
                <article class="fq-item @if($data->is_open == 'on') is-open @endif"
                         itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                  <h3 class="fq-q-wrap">
                    <button type="button" class="fq-q" aria-expanded="{{ $data->is_open == 'on' ? 'true' : 'false' }}">
                      <span class="fq-q-tx" itemprop="name">{{ $data->title }}</span>
                      <span class="fq-q-mark" aria-hidden="true"></span>
                    </button>
                  </h3>
                  <div class="fq-a" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="fq-a-inner" itemprop="text">{!! $data->description !!}</div>
                  </div>
                </article>
              @endforeach
            </div>
          </section>
        @empty
          <p class="fq-none">{{ __('No questions have been published yet.') }}</p>
        @endforelse

        <div class="fq-empty" id="fq-empty" hidden>
          <i data-lucide="search-x"></i>
          <h3>{{ __('Nothing matched that') }}</h3>
          <p>{{ __('Try a shorter word, or ask us directly and we will answer it.') }}</p>
          <a href="{{ route('frontend.contact') }}" class="fq-btn">{{ __('Ask your question') }}</a>
        </div>
      </div>
    </div>
  </section>

  {{-- ===================== CLOSING HELP ===================== --}}
  <section class="fq-cta">
    <div class="fq-shell">
      <div class="fq-cta-card">
        <div>
          <h2>{{ __("Can't find what you're after?") }}</h2>
          <p>{{ __('Tell us what you need and a real person will get back to you.') }}</p>
        </div>
        <a href="{{ route('frontend.contact') }}" class="fq-btn fq-btn--solid">
          <i data-lucide="message-circle"></i> {{ __('Contact us') }}
        </a>
      </div>
    </div>
  </section>

</div>
@endsection

@push('script')
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="{{asset('assets/frontend/js/faq.js?v=2')}}"></script>
@endpush
