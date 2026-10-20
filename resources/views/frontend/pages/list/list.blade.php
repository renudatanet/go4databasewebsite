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

@push('styles')
    {{-- Bump ?v= on every list.css change: LiteSpeed serves it with a one-year
         immutable cache, so returning visitors keep the old file otherwise. --}}
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/list.css') }}?v=30">
@endpush

@section('content')

@php
    $withLists = 0;
    $totalLists = 0;
    foreach ($all_list as $c) {
        $n = (int) ($digest[(string) $c->id]['count'] ?? 0);
        $totalLists += $n;
        if ($n > 0) { $withLists++; }
    }
    $featured = [];
    $pipeline = [];
    foreach ($all_list as $c) {
        if ((int) ($digest[(string) $c->id]['count'] ?? 0) > 0) { $featured[] = $c; }
        else { $pipeline[] = $c; }
    }
@endphp

<div class="dir-hero">
  <div class="dir-wrap">

    <span class="dir-badge"><span class="dir-badge-dot"></span>B2B data directory</span>

    <div class="dir-hero-flex">
      <div class="dir-hero-main">
        <h1 class="dir-title">Every B2B <span class="dir-title-accent">email list</span> we hold</h1>

        <p class="dir-sub">
          {{ number_format($totalLists) }} verified {{ $totalLists == 1 ? 'list' : 'lists' }}
          across {{ count($all_list) }} {{ count($all_list) == 1 ? 'industry' : 'industries' }}.
          Searching is free &mdash; credits are only spent when you reveal a contact.
        </p>

        <form class="dir-search" action="{{ route('frontend.b2c') }}" method="GET" role="search">
          <label for="dir-q">Search every list</label>
          <div class="dir-search-row">
            <div class="dir-search-field">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.3" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
              <input id="dir-q" type="text" name="q" value="{{ $search_query }}" placeholder="Try hospitals, CEO, mortgage, telehealth&hellip;" autocomplete="off">
            </div>
            <button type="submit">Search</button>
          </div>
        </form>
      </div>

      <div class="dir-hero-card">
        <span class="dir-card-label">What every list holds</span>
        <ul class="dir-holds">
          <li><span class="dir-tick"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#145a0b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>Verified business email</li>
          <li><span class="dir-tick"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#145a0b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>Direct dial where held</li>
          <li><span class="dir-tick"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#145a0b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>Job title and seniority</li>
          <li><span class="dir-tick"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#145a0b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>Company firmographics</li>
          <li><span class="dir-tick"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#145a0b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>Location</li>
        </ul>
        <a class="dir-card-btn" href="https://app.go4database.com/register?utm_source=ListIndex&amp;utm_medium=Internal&amp;utm_campaign=free_credits" target="_blank" rel="noopener">Get 1200 free credits</a>
      </div>
    </div>
  </div>
</div>

<div class="dir-wrap">

  @if($search_query !== '')
  <section class="dir-section" id="results">
    <span class="dir-eyebrow">Search</span>
    <h2 class="dir-h2">Results for &ldquo;{{ $search_query }}&rdquo;</h2>
    <div class="dir-rule"><span></span></div>

    @if(count($results))
      <p class="dir-count">
        @if($result_total > count($results))
          Showing the first {{ count($results) }} of {{ number_format($result_total) }} matches &mdash; narrow your search to see fewer.
        @else
          {{ count($results) }} {{ count($results) == 1 ? 'match' : 'matches' }}
        @endif
      </p>
      <div class="dir-rows">
        @foreach($results as $r)
          @php $cat = $all_list->firstWhere('id', $r->categories_id); @endphp
          <a class="dir-row" href="{{ route('frontend.list.single', ['service_slug' => ltrim($r->slug, '/')]) }}">
            <span>{{ $r->title }}</span>
            <span class="dir-row-tag">{{ $cat->name ?? '' }}</span>
          </a>
        @endforeach
      </div>
    @else
      <div class="dir-empty">
        <h3>Nothing matches that yet</h3>
        <p>We build lists to order across the whole global B2B database &mdash; industry, job title, location, company size, revenue.</p>
        <a class="dir-empty-btn" href="{{ url('/contact') }}">Request a custom list</a>
      </div>
    @endif
  </section>
  @endif

  <section class="dir-section" id="browse">
    <span class="dir-eyebrow">Browse</span>
    <h2 class="dir-h2">By industry</h2>
    <div class="dir-rule"><span></span></div>
    <p class="dir-count">{{ $withLists }} of {{ count($all_list) }} {{ count($all_list) == 1 ? 'industry has' : 'industries have' }} lists published, holding {{ number_format($totalLists) }} between them.</p>

    @if(count($featured))
    <div class="dir-grid">
      @foreach($featured as $c)
        @php
            $d = $digest[(string) $c->id] ?? ['count' => 0, 'samples' => []];
        @endphp
        <div class="dir-card">
          <div class="dir-card-head">
            <div class="dir-card-id">
              <span class="dir-card-icon">
                @if(!empty($c->icon))
                  <i class="{{ $c->icon }}" aria-hidden="true"></i>
                @else
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7h18"/><path d="M6 12h12"/><path d="M9 17h6"/></svg>
                @endif
              </span>
              <h3>{{ $c->name }}</h3>
            </div>
            <span class="dir-card-count">{{ number_format($d['count']) }} {{ $d['count'] == 1 ? 'list' : 'lists' }}</span>
          </div>

          <div class="dir-card-rows">
            @foreach($d['samples'] as $s)
              <a href="{{ route('frontend.list.single', ['service_slug' => $s['slug']]) }}">{{ $s['title'] }}</a>
            @endforeach
          </div>

          <a class="dir-card-all" href="{{ route('frontend.b2c') }}?q={{ urlencode($c->name) }}">
            All {{ number_format($d['count']) }} {{ Str::lower($c->name) }} lists
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h13"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>
      @endforeach
    </div>
    @endif

    @if(count($pipeline))
    <div class="dir-pipeline">
      <div class="dir-pipeline-head">
        <strong>Next in the pipeline</strong>
        <span>&mdash; these industries publish with the next data release</span>
      </div>
      <div class="dir-pipeline-chips">
        @foreach($pipeline as $c)
          <span>{{ $c->name }}</span>
        @endforeach
      </div>
    </div>
    @endif
  </section>

  <section class="dir-cta">
    <div class="dir-cta-inner">
      <h2>Can&rsquo;t find the list you need?</h2>
      <p>We build to your criteria across the global B2B database &mdash; industry, job title, location, company size, revenue.</p>
      <div class="dir-cta-btns">
        <a class="dir-cta-primary" href="{{ url('/contact') }}">Request a custom list</a>
        <a class="dir-cta-ghost" href="https://app.go4database.com/register?utm_source=ListIndex&amp;utm_medium=Internal&amp;utm_campaign=cta_free_credits" target="_blank" rel="noopener">Get 1200 free credits</a>
      </div>
      <div class="dir-cta-notes">
        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6fd943" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Real-time email verification</span>
        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6fd943" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>95% accuracy guarantee</span>
        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6fd943" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>No card required</span>
      </div>
    </div>
  </section>

</div>
@endsection
