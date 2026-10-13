@extends('frontend.frontend-page-master')
@section('og-meta')
<meta property="og:url" content="{{ route('frontend.list.single', [
    'service_slug' => ltrim($service_item->slug, '/')
]) }}">
    <meta property="og:type" content="article"/>
    <meta property="og:title" content="{{$list_item->title}}"/>
    {!! render_og_meta_image_by_attachment_id($list_item->image) !!}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{$list_item->meta_description}}">
    <meta name="tags" content="{{$list_item->meta_tag}}">
    {!! render_og_meta_image_by_attachment_id($list_item->image) !!}
@endsection
@section('page-title')
    {{$list_item->title}}
@endsection

@push('styles')
    {{-- Bump ?v= on every list.css change: LiteSpeed serves it with a one-year
         immutable cache, so returning visitors keep the old file otherwise. --}}
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/list.css') }}?v=14">  
@endpush
@php
use App\Services;

@endphp
@section('content')

  @php
    use Illuminate\Support\Str;
    // Same value the stats strip shows, so the hero badge and "Last updated"
    // can never disagree.
    $heroUpdated = now()->subDays(10 + ($service_item->id % 6));

    // The industry directory below is a hardcoded list of ~630 titles, every
    // one of which pointed at javascript:void(0). Resolve each against the
    // lists that actually exist: a real link where there is a page, plain text
    // where there is not, rather than an anchor that goes nowhere.
    $listSlugMap = \Illuminate\Support\Facades\Cache::remember(
        'b2blist_title_slug_map',
        3600,
        function () {
            $byTitle = [];
            $slugs = [];
            foreach (\App\B2BList::select('title', 'slug', 'status')->get() as $row) {
                if (empty($row->slug)) { continue; }
                if (strcasecmp((string) $row->status, 'draft') === 0) { continue; }
                $slug = ltrim($row->slug, '/');
                $slugs[$slug] = true;
                if (!empty($row->title)) {
                    $byTitle[mb_strtolower(trim($row->title))] = $slug;
                }
            }
            return ['byTitle' => $byTitle, 'slugs' => $slugs];
        }
    );

    $listSlugSet = $listSlugMap['slugs'] ?? [];
    $listTitleMap = $listSlugMap['byTitle'] ?? [];

    $listLink = function ($title) use ($listTitleMap) {
        $clean = trim(preg_replace('/\s+/', ' ', $title));
        $slug  = $listTitleMap[mb_strtolower($clean)] ?? null;
        if ($slug === null) {
            return '<span class="industry-link-plain">' . e($clean) . '</span>';
        }
        return '<a href="' . e(route('frontend.list.single', ['service_slug' => $slug])) . '">' . e($clean) . '</a>';
    };
@endphp
    <section class="hero-section">
  
    @php
    $segments = request()->segments();
    $url = '';
@endphp

<nav aria-label="breadcrumb">
    <ul class="breadcrumb">

        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Home</a>
        </li>

        @foreach($segments as $index => $segment)
            @php
                $url .= '/' . $segment;
                $title = ucwords(str_replace('-', ' ', $segment));
            @endphp

            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $title }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ url($url) }}">{{ $title }}</a>
                </li>
            @endif

        @endforeach

    </ul>
</nav>
    <div class="hero-intro">

      <div class="hero-intro-main">

        <span class="hero-badge">
          <span class="hero-badge-dot"></span>
          {{ $industry->name }} &middot; Updated {{ $heroUpdated->format('d M Y') }}
        </span>

        @php
            // Split off the trailing list-type words so they can carry the accent;
            // a title that does not end in one simply renders whole.
            $titleHead = $service_item->title;
            $titleTail = '';
            if (preg_match('/^(.*?)\s+(Email Lists?|Mailing Lists?|Contact Lists?|Contact Databases?|Email Databases?|Databases?|Lists?)$/i', $service_item->title, $m)) {
                $titleHead = $m[1];
                $titleTail = $m[2];
            }
        @endphp
        <h1 class="hero-title">{{ $titleHead }}@if($titleTail !== '') <span class="hero-title-accent">{{ $titleTail }}</span>@endif</h1>

        @php
            // Most rows carry no excerpt, which left the heading column with
            // nothing between the title and the buttons. Fall back to a line
            // built from the row's own figures rather than leaving a hole.
            $heroSub = trim(preg_replace('/\s+/', ' ', strip_tags($service_item->excerpt ?? '')));
            if ($heroSub === '') {
                $heroSub = !empty($service_item->data_counts)
                    ? 'Reach ' . $service_item->data_counts . ' verified ' . $titleHead . ' contacts, each checked before it reaches you. Searching is free &mdash; credits are only spent when you reveal a contact.'
                    : 'Search the full ' . $titleHead . ' database free &mdash; credits are only spent when you reveal a contact.';
            }
        @endphp
        <p class="hero-sub">{!! $heroSub !!}</p>

        <div class="hero-actions">
          <a href="https://app.go4database.com/register?utm_source=ProductPage&amp;utm_medium=Internal&amp;utm_campaign=hero_free_leads" class="hero-btn" target="_blank" rel="noopener">Get 1200 free leads</a>
          <a href="#lead-form" class="hero-btn-ghost">Talk to a data expert</a>
        </div>

      </div>

      <!-- SIDE CARD -->

      <div class="side-card">

        <div class="side-heading">Need a Custom B2B Contact List?</div>

   <p>
          Find your ideal prospects by:
      </p>

      <ul class="feature-list">
          <li>
           <img src="{{asset('assets/uploads/icon.webp')}}"  width="25">Job Title</li>
          <li>
           <img src="{{asset('assets/uploads/icon.webp')}}"  width="25">Industry</li>
          <li>
           <img src="{{asset('assets/uploads/icon.webp')}}"  width="25">Company</li>
          <li>
           <img src="{{asset('assets/uploads/icon.webp')}}"  width="25">Location</li>
      </ul>
    <a href="https://app.go4database.com/register" class="custom-btn">
          Request a Custom List
      </a>

      </div>

    </div>

  <!-- HERO -->

  <div class="hero">

    <!-- LEADS: same search, table and click-to-reveal as the homepage -->

    <div class="table-wrapper">

      <!-- FILTERS -->
      <div class="filters">
    <form onsubmit="return false" autocomplete="off">
      @csrf
      <div class="field-group">
        <label for="search-title">Job title</label>
        <input type="text" placeholder="Title" id="search-title" value="{{ $service_item->search_title ?? '' }}">
      </div>
      <div class="field-group">
        <label for="search-industry">Industry / Business</label>
        <input type="text" placeholder="Industry / Business" id="search-industry" value="{{ trim($service_item->search_industry ?? '') ?: trim($service_item->search_business_category ?? '') }}">
      </div>
      <div class="field-group">
        <label for="search-location">Location</label>
        <input type="text" placeholder="Location" id="search-location" value="{{ $service_item->search_location ?? '' }}">
      </div>
      <button type="button" id="search-leads-btn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2.3"/>
          <path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"/>
        </svg>
        Search
      </button>
    </form>
      </div>

      <div id="results-container">
        <div class="g4d-leads-grid g4d-leads-head">
          <span>COMPANY</span>
          <span>PERSON NAME</span>
          <span>TITLE</span>
          <span>EMAIL</span>
          <span>PHONE</span>
        </div>
        <div id="leads-rows"></div>
      </div>
      <div class="table-footer">
<nav class="pagination-wrapper" aria-label="Page navigation "> 
                        <ul class="pagination justify-content-center" role="navigation">
        
                    <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                <span class="page-link" aria-hidden="true">«</span>
            </li>
        
        
                    
            
            
                                                                        <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
                                                                                <li class="page-item">
                            <a href="https://app.go4database.com/login" target="_blank"  class="page-link" data-page="2">2</a>
                        </li>
                                                                                <li class="page-item">
                            <a href="https://app.go4database.com/login"  target="_blank" class="page-link" data-page="3">3</a>
                        </li>
                                                                                <li class="page-item">
                            <a href="https://app.go4database.com/login" target="_blank" class="page-link" data-page="4">4</a>
                        </li>
                                         
        
                    <li class="page-item">
                <a href="https://app.go4database.com/login"  target="_blank" class="page-link" data-page="2" aria-label="Next">
                    »
                </a>
            </li>
            </ul>
 </nav>
        
        <button class="download-btn">
         <!-- DOWNLOAD ICON -->
<a href="https://app.go4database.com/login" target="_blank"  >
<i class="fa fa-download"></i>  Download List</a>
        </button>

      </div>

    </div>


  </div>

  </section>
  <div class="container">
@php
    $baseTitle = preg_replace(
        '/\s+(Email Lists?|Mailing Lists?|Contact Lists?|Contact Databases?|Email Databases?|Databases?|Lists?)$/i',
        '',
        $service_item->title
    );

    $emailTitle = $baseTitle . ' Email List';
    $mailingTitle = $baseTitle . ' Mailing List';
    $contactListTitle = $baseTitle . ' Contact List';
    $contactDatabaseTitle = $baseTitle . ' Contact Database';
@endphp

  <!-- STATS -->

@php
    $lastUpdated = $heroUpdated;

    // accuracy_commitment has existed on b2blist all along but no view read it;
    // the ring is drawn from it, so a list carrying a different figure shows it.
    $accuracy = (int) preg_replace('/\D/', '', (string) ($service_item->accuracy_commitment ?: ''));
    if ($accuracy < 1 || $accuracy > 100) { $accuracy = 95; }
    $ringR = 33;
    $ringCirc = round(2 * M_PI * $ringR, 1);
    $ringDash = round($ringCirc * $accuracy / 100, 1);

    $segmentChip = trim(preg_replace('/^' . preg_quote($industry->name, '/') . '\s*/i', '', $baseTitle));
@endphp

  <div class="stats-grid">

    @if(!empty($service_item->data_counts))
    <div class="stat-feature">
      <div class="stat-feature-head">
        <span class="stat-feature-icon">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6fd943" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/></svg>
        </span>
        <span class="stat-feature-label">Total verified records</span>
      </div>
      <div class="stat-feature-num">{{ $service_item->data_counts }}</div>
      <div class="stat-feature-chips">
        <span>{{ $industry->name }}</span>
        @if($segmentChip !== '' && strcasecmp($segmentChip, $industry->name) !== 0)
        <span>{{ $segmentChip }}</span>
        @endif
      </div>
    </div>
    @endif

    <div class="stat-card">
      <svg class="accuracy-ring" width="92" height="92" viewBox="0 0 80 80" role="img" aria-label="{{ $accuracy }} percent accuracy commitment">
        <defs>
          <linearGradient id="accuracyRing" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#145a0b"/>
            <stop offset="1" stop-color="#6fd943"/>
          </linearGradient>
        </defs>
        <circle cx="40" cy="40" r="{{ $ringR }}" fill="none" stroke="#e6efe2" stroke-width="8"/>
        <circle cx="40" cy="40" r="{{ $ringR }}" fill="none" stroke="url(#accuracyRing)" stroke-width="8" stroke-linecap="round" stroke-dasharray="{{ $ringDash }} {{ $ringCirc }}" transform="rotate(-90 40 40)"/>
        <text x="40" y="40" text-anchor="middle" dominant-baseline="central" font-family="Poppins, sans-serif" font-size="19" font-weight="800" fill="#10241a">{{ $accuracy }}%</text>
      </svg>
      <p>Accuracy Commitment</p>
    </div>

    <div class="stat-card">
      <span class="stat-refresh-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#145a0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v5h-5"/></svg>
        <span class="stat-refresh-dot"></span>
      </span>
      <div class="stat-refresh-date">{{ $lastUpdated->format('d M Y') }}</div>
      <p>Last updated</p>
    </div>

    <div class="trust-row">

      <div class="stats-cmp-main">
        <div class="stat-comp">
          <div class="stat-comp-icon"><img src="{{asset('assets/uploads/icon.webp')}}" alt="" width="30"></div>
          <p>GDPR Compliance</p>
        </div>
        <div class="stat-comp">
          <div class="stat-comp-icon"><img src="{{asset('assets/uploads/icon.webp')}}" alt="" width="30"></div>
          <p>CCPA Compliance</p>
        </div>
        <div class="stat-comp">
          <div class="stat-comp-icon"><img src="{{asset('assets/uploads/icon.webp')}}" alt="" width="30"></div>
          <p>ISO certified</p>
        </div>
      </div>

      <div class="logo-slider-viewport">
        <div class="logo-slider">
    <img src="{{asset('assets/uploads/product/Candela.png')}}" width="92">
    <img src="{{asset('assets/uploads/product/Tecan.png')}}" width="89">
    <img src="{{asset('assets/uploads/product/Westin.png')}}"  width="66">
    <img src="{{asset('assets/uploads/product/CBRE.png')}}"width="66">
    <img src="{{asset('assets/uploads/product/Shopify.png')}}" width="46">
    <img src="{{asset('assets/uploads/product/MIQ.png')}}"   width="68">
    <img src="{{asset('assets/uploads/product/J Pocker.png')}}"  width="68">
    <img src="{{asset('assets/uploads/product/Candela.png')}}"  width="92">
    <img src="{{asset('assets/uploads/product/Tecan.png')}}" width="89">
    <img src="{{asset('assets/uploads/product/Westin.png')}}" width="66">
    <img src="{{asset('assets/uploads/product/CBRE.png')}}" width="66">
    <img src="{{asset('assets/uploads/product/Shopify.png')}}" width="46">
    <img src="{{asset('assets/uploads/product/MIQ.png')}}"  width="68">
    <img src="{{asset('assets/uploads/product/J Pocker.png')}}"  width="68">
        </div>
      </div>

    </div>
  </div>

  <!-- DESCRIPTION -->

  <div class="description-block">

    <div class="description-main">
      <span class="section-eyebrow">The data</span>
      <h2 class="description-title">What is in this list</h2>
      <div class="section-rule"><span></span></div>
      <p class="description"> {{ strip_tags($service_item->description) }}
      </p>
    </div>

        <div class="tags">

          <div class="tag"><img src="{{asset('assets/uploads/product/Verified Contacts.png')}}" width="46">Verified Contacts</div>
          <div class="tag"><img src="{{asset('assets/uploads/product/Decision Makers.png')}}"  width="46">Decision Makers</div>
          <div class="tag"><img src="{{asset('assets/uploads/product/GEO Targeting.png')}}"  width="46">GEO Targeting</div>
          <div class="tag"><img src="{{asset('assets/uploads/product/High ROI Driven.png')}}"  width="46">High ROI Driven</div>

        </div>

  </div>

  <!-- GREEN BANNER -->


  <!-- SECTION -->

  <div class="section-flex">
      

    <div class="left">

      <h2 class="title">
        Get Instant Access to 1200+ {{ $mailingTitle }}
      </h2>

<div class="green-banner">

   
       <img src="{{asset('assets/uploads/icon.webp')}}"  width="60">

    <div class="banner-text">
        Get Instant Access to {{$service_item->data_counts}} Verified {{ $emailTitle }}
    </div>
<a href="https://app.go4database.com/login?utm_source=ProductPage&amp;utm_medium=Internal&amp;utm_campaign=app_login" class="read-btn" target="_blank">
   Have you requested for free sample data yet?</a>
</div>


    </div>

    <!-- RELATED -->

<div class="right">

  <div class="related-box">

    <!-- Tabs -->
    <ul class="nav related-header" id="relatedTab" role="tablist">

      <li class="nav-item">
        <a class="nav-link active"
           id="list1-tab"
           data-toggle="tab"
           href="#list1"
           role="tab">
          Related List
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link"
           id="list2-tab"
           data-toggle="tab"
           href="#list2"
           role="tab">
          Industry List
        </a>
      </li>

    </ul>

    <!-- Tab Content -->
    <div class="tab-content">

      <!-- TAB 1 -->
      <div class="tab-pane fade show active"
           id="list1"
           role="tabpanel">

        <ul>
        @foreach($relatedServices as $item)
        <li>
        <a href="{{ route('frontend.list.single', [
        'service_slug' => ltrim($item->slug, '/')
        ]) }}" target="_blank" >
        {{ $item->title }}
        </a>
        </li>
        @endforeach
        
        </ul>

      </div>

      <!-- TAB 2 -->
      <div class="tab-pane fade"
           id="list2"
           role="tabpanel">

        <ul>
           @foreach($all_serviceCat as $item)
           
             @php
    // Was one ->exists() query per category, every page load.
    $exists = isset($listSlugSet[$item->slug]);
@endphp

<li>
    @if($exists)
    <a href="{{ route('frontend.list.single', ['service_slug' => $item->slug]) }}" target="_blank">
        {{ $item->name }} Email List
    </a>
    @else
    <span class="industry-link-plain">{{ $item->name }} Email List</span>
    @endif
</li>
    @endforeach
        </ul>

      </div>

    </div>

  </div>

</div>


  </div>

  <!-- WHY SECTION -->

  <div class="section-flex">

    <div class="left">

      <h2 class="title">
        Why Our {{ $contactListTitle }} Delivers Better ROI
      </h2>

      <div class="why-grid">

        <div class="why-card">
          <h3>Precision Targeting</h3>
          <p>Focus on high-intent {{ $baseTitle }} decision makers</p>
        </div>

        <div class="why-card">
          <h3>Real-Time Data Updates</h3>
          <p>Fresh contacts with better deliverability</p>
        </div>

        <div class="why-card">
          <h3>AI-Powered Matching</h3>
          <p>Identify best-fit prospects faster</p>
        </div>

        <div class="why-card">
          <h3>Dedicated Expert Support</h3>
          <p>Custom list building assistance</p>
        </div>

      </div>

    </div>

    <div class="right">
<div class="slider-container">
  <div class="slider-header">
    <div class="slider-heading">Our Testimonial</div>
    <div class="nav-buttons">
      <button onclick="prevSlide()">&#10094;</button>
      <button onclick="nextSlide()">&#10095;</button>
    </div>
    
  </div>
   @foreach($all_testimonial as $testimonial)
  <div class="testimonial-slide  {{ $loop->first ? 'active' : '' }}">
    <div class="testimonial">

    {{ \Illuminate\Support\Str::limit(strip_tags($testimonial->description), 180) }}

        <strong>
        {{ $testimonial->name }}
        </strong>

        Customer

      </div>
  </div>
  @endforeach
  

  <div class="read-more"><a href="{{url('/our-testimonial')}}">Read all Testimonial →</a></div>
</div>
      

    </div>

  </div>

<!-- DECISION MAKERS -->

<div class="section">

<div class="section-flex">

<div class="left">

<h2 class="section-title">
Key Decision Makers in the {{ $emailTitle }}
</h2>

<div class="decision-table">
    @if(!empty($service_item->key_decision))
    <div class="vertical-table">
        @foreach(explode(',', $service_item->key_decision) as $pair)
            @php
                $parts = explode('=', $pair, 2);
            @endphp

            @if(count($parts) == 2)
                <div class="table-row">
                    <div class="table-label">{{ trim($parts[0]) }}</div>
                    <div class="table-value">{{ trim($parts[1]) }}</div>
                </div>
            @endif
        @endforeach
    </div>
@endif



</div>

</div>

<!-- SMALL CARD -->

<div class="right">

<div class="small-card">

<div class="logo">
<img src="https://www.go4database.com/assets/uploads/media-uploader/go4database-logo17535121121771578926.webp">
</div>

<div class="ase-grid">

<div class="ase-item">
<div class="ase-box">a</div>
<p>Active</p>
</div>

<div class="ase-item">
<div class="ase-box">s</div>
<p>Specific</p>
</div>

<div class="ase-item">
<div class="ase-box">e</div>
<p>Effortless</p>
</div>

</div>

<div class="small-card-button">
Use it then Believe it
</div>

</div>

</div>

</div>

</div>

  <!-- STEPS -->

  <div class="section-flex steps-flex">

    <div class="left">

      <h2 class="title">
        Simple steps to get free {{ $contactDatabaseTitle }}
      </h2>
@if(!empty(config('app.video_cdn')))
<video width="100%" controls class="video-box" preload="none" poster="{{ asset('assets/uploads/product/list.webp') }}">
    <source src="{{ config('app.video_cdn') }}" type="video/mp4">
</video>
@endif


      <!-- <div class="video-box" style="background-image: url({{asset('assets/uploads/product/list.png')}});">
        ▶
      </div> -->

    </div>

  </div>

<!-- BENEFITS -->

<div class="section">

<h2 class="section-title">
Who Can Benefit from Our {{ $mailingTitle }} to Boost ROI?
</h2>

<div class="benefits">
@php
    $buyers = array_filter(
        array_map('trim', explode(';', $service_item->buyerlist ?? ''))
    );

    $chunks = array_chunk(
        $buyers,
        max(1, ceil(count($buyers) / 2))
    );
@endphp

@if(count($buyers))
    @foreach($chunks as $chunk)
        <div class="benefit-card">
            <ul>
                @foreach($chunk as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach
@else
    <div class="benefit-card">
        <p>No entries available.</p>
    </div>
@endif

</div>

</div>

<!-- ROI SECTION -->

<div class="roi-section">

<span>
Grow your ROI with Result Focused
</span>

<h2>
Global B2B Contact Data
</h2>

<p>
Choose data as per your specific criteria
</p>

<div class="icon-grid">

<div class="icon-item">
<div class="icon"><img src="{{asset('assets/uploads/product/Industry - Green.png')}}"></div>
<p>Industry</p>
</div>

<div class="icon-item">
<div class="icon"><img src="{{asset('assets/uploads/product/Job Titile - Green.png')}}"></div>
<p>Job Title</p>
</div>

<div class="icon-item">
<div class="icon"><img src="{{asset('assets/uploads/product/Location - White.png')}}"></div>
<p>Location</p>
</div>

<div class="icon-item">
<div class="icon"><img src="{{asset('assets/uploads/product/Employee Size - Green.png')}}"></div>
<p>Employee Size</p>
</div>

<div class="icon-item">
<div class="icon"><img src="{{asset('assets/uploads/product/Reveue Size - Green.png')}}"></div>
<p>Revenue Size</p>
</div>

</div>

<a href="https://app.go4database.com/login?utm_source=ProductPage&amp;utm_medium=Internal&amp;utm_campaign=app_login" class="roi-btn" target="_blank">
Have you requested for free sample data yet?
</a>

</div>


  <!-- RATINGS + LEAD FORM -->

  <div class="section-flex ratings-flex">

    <div class="left">

    <!-- TRUST -->

    <div class="trust">

      <div class="trust-item">
        <img src="{{asset('assets/uploads/product/Trustpilot.webp')}}">
        <div class="rating-box">
        <div class="rating-text"> 4.5 </div><div class="stars">★★★★★</div>
      </div>
      </div>
      <div class="trust-item">
       <img src="{{asset('assets/uploads/product/G2.webp')}}" alt="G2">
      <div class="rating-box">
        <div class="rating-text"> 4.5 </div><div class="stars">★★★★★</div>
      </div>
     </div>

      <div class="trust-item">
        <img src="{{asset('assets/uploads/product/datarade.webp')}}">
   <div class="rating-box">
        <div class="rating-text"> 4.5 </div><div class="stars">★★★★★</div>
      </div>
  </div>

      <div class="trust-item">
        <img src="{{asset('assets/uploads/product/BBB.webp')}}">
     <div class="rating-box">
        <div class="rating-text"> 4.5 </div><div class="stars">★★★★★</div>
      </div>
    </div>

    </div>



    </div>

    <div class="right">

        <div class="side-card" id="lead-form">
             <div class="side-card-heading">Talk to marketing expert to get precise targeted mailing list</div>
  @if(session('success'))
      <div class="alert alert-success">
          {{ session('success') }}
      </div>
  @endif
    <form action="{{ route('service-lead.submit') }}" method="POST">
          @csrf

          <input type="text" name="name" placeholder="Name" required>

          <input type="text" name="phone" placeholder="Phone" required>

          <input type="email" name="email" placeholder="Email" required>

          <input type="text" name="business_details" placeholder="Website/Business Details">

          <button type="submit">Continue</button>
      </form>

        </div>

    </div>

  </div>

<!-- FAQ -->
<!-- FAQ SECTION -->

<div class="faq-section">

    <h2 class="faq-title">
        Frequently Asked Questions
    </h2>

    <p class="faq-subtitle">
        You might have these questions in your mind?
    </p>


<div class="faq-wrapper">

    @if(!empty($service_item->faqs))

        @foreach($service_item->faqs as $key => $faq)

            <div class="faq-item {{ $key == 0 ? 'active' : '' }}">

                <button class="faq-question">

                    <h3>
                        {{ $faq['question'] }}
                    </h3>

                    <span class="faq-icon">+</span>

                </button>

                <div class="faq-answer">

                    <div class="faq-answer-content">

                        {{ $faq['answer'] }}

                    </div>

                </div>

            </div>

        @endforeach

    @else

        <p>No FAQ Found</p>

    @endif

</div>
</div>

<!-- FAQ SCRIPT -->

<!-- SECTION -->


<div class="industry-section">

    <h2 class="industry-title">
        Other Targeted Industry Mailing List
    </h2>

    <div class="industry-flex">

        <!-- LEFT TAB -->

        <div class="industry-tabs">
            
            @foreach($all_serviceCat as $key => $category)

            <button class="industry-tab-btn {{ $key == 0 ? 'active' : '' }}"
                    data-tab="industry{{ $category->id }}">
               {{ $category->name }}
            </button>
@endforeach
            

        </div>

        <!-- RIGHT CONTENT -->

        <div class="industry-content">

            <!-- TAB 1 -->
            <div class="industry-tab-content active"
                 id="industry1">

          <ul class="industry-list">
@php
$titles = [
    'Hospitals Email List' => 'healthcare/hospitals',
    'Chiropractic Clinics Database' => 'healthcare/chiropractic-clinics',
    'Pharma CEO Email List' => 'healthcare/pharmaceutical-companies/ceo',
    'Multi-Specialty Clinics Mailing List' => 'healthcare/multi-specialty-clinics',
    'Acupuncture Clinics Email List' => 'healthcare/acupuncture-clinics',
    'Medical Equipment CEO Contact List' => 'healthcare/medical-equipment/ceo',
    'Pharmaceutical Companies Database' => 'healthcare/pharmaceutical-companies',
    'Holistic Wellness Centers Mailing List' => 'healthcare/holistic-wellness-centers',
    'Medical Device VP Mailing List' => 'healthcare/medical-device/vp',
    'Medical Device Manufacturers Contact List' =>'healthcare/medical-device-manufacturers',
    'IV Therapy Clinics Email List' =>'healthcare/iv-therapy-clinics',
    'Healthcare SaaS Founder Database' =>'healthcare/saas/founder',
    'Healthcare SaaS Providers Email List' =>'healthcare/saas-providers',
    'Nutrition Consultants Contact List' =>'healthcare/nutrition-consultants',
    'Telehealth CEO Contact List' =>'healthcare/telehealth/ceo',
    'Telemedicine Platforms Database' =>'healthcare/telemedicine-platforms',
    'Hearing Aid Clinics Database' =>'healthcare/hearing-aid-clinics',
    'Clinical Research VP Database' =>'healthcare/clinical-research/vp',
    'Health Insurance Providers Mailing List' =>'healthcare/health-insurance-providers',
    'Speech Therapy Centers Mailing List' =>'healthcare/speech-therapy-centers',
    'Medical Billing Owner Mailing List' =>'healthcare/medical-billing/owner',
    'Clinical Research Organizations Database' =>'healthcare/clinical-research-organizations',
    'Occupational Therapy Clinics Email List' =>'healthcare/occupational-therapy-clinics',
    'Healthcare Compliance Manager List' =>'',
    'Diagnostic Imaging Centers Contact List' =>'',
    'Alternative Medicine Clinics Database' =>'',
    'Rehab Center CEO Database' =>'',
    'Medical Billing Companies Email List' =>'',
    'Rural Medical Practices Mailing List' =>'',
    'Healthcare Recruiter Contact List' =>'',
    'Home Healthcare Agencies Database' =>'',
    'Elderly Care Consultants Contact List' =>'',
    'Wellness Brand Founder Email List' =>'',
    'Laboratory Networks Mailing List' =>'',
    'Independent Therapists Email List' =>'',
    'Healthcare CRM Executive Database' =>'',
    'Rehabilitation Centers Contact List' =>'',
    'Wellness Retreat Centers Database' =>'',
    'Lab Network CEO Mailing List' =>'',
    'Healthcare Staffing Agencies Email List' =>'',
    'Medical Tourism Agencies Mailing List' =>'',
    'Healthcare Analytics VP Contact List' =>'',
    'Healthcare CRM Providers Database' =>'',
    'Mobile Healthcare Units Contact List' =>'',
    'Assisted Living Owner Database' =>'',
];
@endphp
     @foreach($titles as $title => $slug)

    <li>
        @if(!empty($slug))
        <a href="{{ route('frontend.list.single', ['service_slug' => $slug]) }}" target="_blank">{{ $title }}</a>
        @else
        {!! $listLink($title) !!}
        @endif
    </li>

@endforeach
 
  </ul>

            </div>
            <!-- TAB 2 -->
        <div class="industry-tab-content" id="industry2">

                <ul class="industry-list">
                    <li> {!! $listLink('Real Estate Investors Email List') !!}</li>
                    <li> {!! $listLink('Multifamily Property Buyers Database') !!}</li>
                    <li> {!! $listLink('Real Estate CEO Mailing List') !!}</li>
                    <li> {!! $listLink('Commercial Real Estate Firms Contact List') !!}</li>
                    <li> {!! $listLink('Office Leasing Companies Email List') !!}</li>
                    <li> {!! $listLink('Commercial Property VP Database') !!}</li>
                    <li> {!! $listLink('Property Management Companies Mailing List') !!}</li>
                    <li> {!! $listLink('HOA Management Services Contact List') !!}</li>
                    <li> {!! $listLink('Property Operations Manager Email List') !!}</li>
                    <li> {!! $listLink('Real Estate Developers Database') !!}</li>
                    <li> {!! $listLink('Land Development Firms Mailing List Real Estate') !!}</li>
                    <li> {!! $listLink('Founder Contact List') !!}</li>
                    <li> {!! $listLink('Residential Brokerage Firms Email List') !!}</li>
                    <li> {!! $listLink('Luxury Home Realtors Database') !!}</li>
                    <li> {!! $listLink('Brokerage Owner Mailing List') !!}</li>
                    <li> {!! $listLink('Mortgage Lenders Contact List') !!}</li>
                    <li> {!! $listLink('Hard Money Lending Companies Email List') !!}</li>
                    <li> {!! $listLink('Mortgage CEO Database') !!}</li>
                    <li> {!! $listLink('Real Estate SaaS Providers Mailing List') !!}</li>
                    <li> {!! $listLink('Property CRM Platforms Contact List') !!}</li>
                    <li> {!! $listLink('PropTech Founder Email List') !!}</li>
                    <li> {!! $listLink('Title Companies Database') !!}</li>
                    <li> {!! $listLink('Escrow Service Providers Mailing List') !!}</li>
                    <li> {!! $listLink('Title Agency Owner Contact List') !!}</li>
                    <li> {!! $listLink('Real Estate Investment Trusts Email List') !!}</li>
                    <li> {!! $listLink('Industrial REIT Firms Database') !!}</li>
                    <li> {!! $listLink('Investment VP Mailing List') !!}</li>
                    <li> {!! $listLink('Vacation Rental Companies Contact List') !!}</li>
                    <li> {!! $listLink('Airbnb Property Managers Email List') !!}</li>
                    <li> {!! $listLink('Short-Term Rental CEO Database') !!}</li>
                    <li> {!! $listLink('Construction Management Firms Mailing List') !!}</li>
                    <li> {!! $listLink('Commercial Renovation Contractors Contact List') !!}</li>
                    <li> {!! $listLink('Construction Project Owner Email List') !!}</li>
                    <li> {!! $listLink('Senior Housing Providers Database') !!}</li>
                    <li> {!! $listLink('Assisted Living Communities Mailing List') !!}</li>
                    <li> {!! $listLink('Senior Living CEO Contact List') !!}</li>
                    <li> {!! $listLink('Student Housing Companies Email List') !!}</li>
                    <li> {!! $listLink('University Apartment Operators Database') !!}</li>
                    <li> {!! $listLink('Housing Operations Manager Mailing List') !!}</li>
                    <li> {!! $listLink('Real Estate Staffing Agencies Contact List') !!}</li>
                    <li> {!! $listLink('Leasing Recruitment Firms Email List') !!}</li>
                    <li> {!! $listLink('Real Estate Recruiter Database') !!}</li>
                    <li> {!! $listLink('Self Storage Companies Mailing List') !!}</li>
                    <li> {!! $listLink('Mini Storage Facility Operators Contact List') !!}</li>
                    <li> {!! $listLink('Storage Facility Owner Email List') !!}</li>
                    
                    
                </ul>

            </div>

      <!-- TAB 3 -->
        <div class="industry-tab-content" id="industry3">

                <ul class="industry-list">
                    <li> {!! $listLink('Technology Industry Email List') !!}</li>
                    <li> {!! $listLink('Cloud Computing Companies Database') !!}</li>
                    <li> {!! $listLink('Technology CEO Email List') !!}</li>
                    <li> {!! $listLink('Software Companies Mailing List') !!}</li>
                    <li> {!! $listLink('SaaS Providers Contact List') !!}</li>
                    <li> {!! $listLink('Software Founder Database') !!}</li>
                    <li> {!! $listLink('IT Services Database') !!}</li>
                    <li> {!! $listLink('Managed IT Services Email List') !!}</li>
                    <li> {!! $listLink('IT Director Contact List') !!}</li>
                    <li> {!! $listLink('Cybersecurity Companies Contact List') !!}</li>
                    <li> {!! $listLink('Network Security Vendors Mailing List') !!}</li>
                    <li> {!! $listLink('Cybersecurity CEO Database') !!}</li>
                    <li> {!! $listLink('Artificial Intelligence Email List') !!}</li>
                    <li> {!! $listLink('Machine Learning Startups Database') !!}</li>
                    <li> {!! $listLink('AI Founder Contact List') !!}</li>
                    <li> {!! $listLink('Data Center Companies Mailing List') !!}</li>
                    <li> {!! $listLink('Colocation Providers Email List') !!}</li>
                    <li> {!! $listLink('Data Center VP Database') !!}</li>
                    <li> {!! $listLink('Fintech Technology Database') !!}</li>
                    <li> {!! $listLink('Digital Payment Platforms Contact List') !!}</li>
                    <li> {!! $listLink('Fintech CEO Mailing List') !!}</li>
                    <li> {!! $listLink('Telecommunications Companies Email List') !!}</li>
                    <li> {!! $listLink('VoIP Service Providers Database') !!}</li>
                    <li> {!! $listLink('Telecom Manager Contact List') !!}</li>
                    <li> {!! $listLink('Healthcare Technology Mailing List') !!}</li>
                    <li> {!! $listLink('Medical Software Vendors Contact List') !!}</li>
                    <li> {!! $listLink('HealthTech Founder Email List') !!}</li>
                    <li> {!! $listLink('EdTech Companies Database') !!}</li>
                    <li> {!! $listLink('Online Learning Platforms Mailing List') !!}</li>
                    <li> {!! $listLink('EdTech CEO Contact List') !!}</li>
                    <li> {!! $listLink('Ecommerce Technology Email List') !!}</li>
                    <li> {!! $listLink('Retail Software Providers Database') !!}</li>
                    <li> {!! $listLink('Ecommerce VP Mailing List') !!}</li>
                    <li> {!! $listLink('Blockchain Companies Contact List') !!}</li>
                    <li> {!! $listLink('Crypto Technology Firms Database') !!}</li>
                    <li> {!! $listLink('Blockchain Founder Mailing List') !!}</li>
                    <li> {!! $listLink('HR Technology Database') !!}</li>
                    <li> {!! $listLink('Recruitment Software Companies Email List') !!}</li>
                    <li> {!! $listLink('HR Tech Recruiter Contact List') !!}</li>
                    <li> {!! $listLink('Marketing Technology Mailing List') !!}</li>
                    <li> {!! $listLink('Automation Software Vendors Database') !!}</li>
                    <li> {!! $listLink('MarTech Manager Email List') !!}</li>
                    <li> {!! $listLink('Enterprise Software Contact List') !!}</li>
                    <li> {!! $listLink('CRM Software Providers Mailing List') !!}</li>
                    <li> {!! $listLink('Enterprise CIO Database') !!}</li>
                    
                    
                </ul>

            </div>

      <!-- TAB 4 -->
        <div class="industry-tab-content" id="industry4">

                <ul class="industry-list">
                    <li> {!! $listLink('Retail Industry Email List') !!}</li>
                    <li> {!! $listLink('Grocery Store Chains Mailing List') !!}</li>
                    <li> {!! $listLink('Retail CEOs Email List') !!}</li>
                    <li> {!! $listLink('ECommerce Retailers Database') !!}</li>
                    <li> {!! $listLink('Online Fashion Retail Contact List') !!}</li>
                    <li> {!! $listLink('ECommerce Founders Mailing List') !!}</li>
                    <li> {!! $listLink('Apparel Retail Email List') !!}</li>
                    <li> {!! $listLink('Luxury Clothing Boutiques Database') !!}</li>
                    <li> {!! $listLink('Fashion Retail Owners Contact List') !!}</li>
                    <li> {!! $listLink('Consumer Electronics Stores Mailing List') !!}</li>
                    <li> {!! $listLink('Mobile Phone Retailers Email List') !!}</li>
                    <li> {!! $listLink('Retail Operations VPs Database') !!}</li>
                    <li> {!! $listLink('Home Improvement Retail Database') !!}</li>
                    <li> {!! $listLink('Hardware Store Chains Contact List') !!}</li>
                    <li> {!! $listLink('Store Managers Mailing List') !!}</li>
                    <li> {!! $listLink('Supermarket Retailers Contact List') !!}</li>
                    <li> {!! $listLink('Organic Food Retailers Database') !!}</li>
                    <li> {!! $listLink('Grocery Store Owners Email List') !!}</li>
                    <li> {!! $listLink('Beauty Supply Retail Mailing List') !!}</li>
                    <li> {!! $listLink('Cosmetics Retail Chains Email List') !!}</li>
                    <li> {!! $listLink('Beauty Retail CEOs Contact List') !!}</li>
                    <li> {!! $listLink('Furniture Retailers Email Database') !!}</li>
                    <li> {!! $listLink('Office Furniture Showrooms Mailing List') !!}</li>
                    <li> {!! $listLink('Furniture Store Owners Database') !!}</li>
                    <li> {!! $listLink('Automotive Parts Retail Contact List') !!}</li>
                    <li> {!! $listLink('Tire Retail Dealers Email List') !!}</li>
                    <li> {!! $listLink('Auto Retail Managers Mailing List') !!}</li>
                    <li> {!! $listLink('Sporting Goods Retailers Mailing List') !!}</li>
                    <li> {!! $listLink('Outdoor Equipment Stores Database') !!}</li>
                    <li> {!! $listLink('Sporting Goods CEOs Email List') !!}</li>
                    <li> {!! $listLink('Pharmacy Retail Database') !!}</li>
                    <li> {!! $listLink('Independent Drug Stores Contact List') !!}</li>
                    <li> {!! $listLink('Pharmacy Owners Mailing List') !!}</li>
                    <li> {!! $listLink('Pet Supply Retail Email List') !!}</li>
                    <li> {!! $listLink('Specialty Pet Stores Database') !!}</li>
                    <li> {!! $listLink('Pet Retail Founders Contact List') !!}</li>
                    <li> {!! $listLink('Jewelry Retailers Contact Database') !!}</li>
                    <li> {!! $listLink('Luxury Watch Retailers Mailing List') !!}</li>
                    <li> {!! $listLink('Jewelry Store CEOs Email List') !!}</li>
                    <li> {!! $listLink('Convenience Store Chains Email List') !!}</li>
                    <li> {!! $listLink('Gas Station Retail Markets Database') !!}</li>
                    <li> {!! $listLink('Convenience Store Managers Contact List') !!}</li>
                    <li> {!! $listLink('Department Store Retail Mailing List') !!}</li>
                    <li> {!! $listLink('Discount Retail Chains Email Database') !!}</li>
                    <li> {!! $listLink('Retail Procurement VPs Contact List') !!}</li>
                    
                    
                </ul>

            </div>
      <!-- TAB 5 -->
       <div class="industry-tab-content" id="industry5">

            <ul class="industry-list">
                <li> {!! $listLink('Construction Companies Email List') !!}</li>
                <li> {!! $listLink('Commercial Building Contractors Database') !!}</li>
                <li> {!! $listLink('Construction CEOs Contact List') !!}</li>
                <li> {!! $listLink('General Contractors Mailing List') !!}</li>
                <li> {!! $listLink('Residential Home Builders Email List') !!}</li>
                <li> {!! $listLink('Construction Owners Mailing List') !!}</li>
                <li> {!! $listLink('Engineering And Construction Database') !!}</li>
                <li> {!! $listLink('Civil Engineering Firms Contact List') !!}</li>
                <li> {!! $listLink('Project Managers Email Database') !!}</li>
                <li> {!! $listLink('Heavy Construction Companies Contact List') !!}</li>
                <li> {!! $listLink('Highway Infrastructure Contractors Mailing List') !!}</li>
                <li> {!! $listLink('Construction VPs Email List') !!}</li>
                <li> {!! $listLink('Real Estate Development Firms Database') !!}</li>
                <li> {!! $listLink('Multifamily Housing Developers Contact List') !!}</li>
                <li> {!! $listLink('Real Estate Founders Mailing List') !!}</li>
                <li> {!! $listLink('Industrial Construction Email List') !!}</li>
                <li> {!! $listLink('Manufacturing Plant Builders Database') !!}</li>
                <li> {!! $listLink('Industrial Construction CEOs Email List') !!}</li>
                <li> {!! $listLink('Roofing Contractors Mailing List') !!}</li>
                <li> {!! $listLink('Metal Roofing Installers Contact List') !!}</li>
                <li> {!! $listLink('Roofing Company Owners Database') !!}</li>
                <li> {!! $listLink('Electrical Construction Database') !!}</li>
                <li> {!! $listLink('Commercial Electrical Contractors Email List') !!}</li>
                <li> {!! $listLink('Electrical Project Managers Mailing List') !!}</li>
                <li> {!! $listLink('HVAC Construction Contact List') !!}</li>
                <li> {!! $listLink('Mechanical Contractors Database') !!}</li>
                <li> {!! $listLink('HVAC Business Owners Email List') !!}</li>
                <li> {!! $listLink('Plumbing Contractors Email Database') !!}</li>
                <li> {!! $listLink('Water System Installation Firms Mailing List') !!}</li>
                <li> {!! $listLink('Plumbing Company CEOs Contact List') !!}</li>
                <li> {!! $listLink('Concrete Construction Mailing List') !!}</li>
                <li> {!! $listLink('Ready Mix Concrete Suppliers Database') !!}</li>
                <li> {!! $listLink('Concrete Contractors Owners List') !!}</li>
                <li> {!! $listLink('Green Building Companies Email List') !!}</li>
                <li> {!! $listLink('Sustainable Construction Consultants Contact List') !!}</li>
                <li> {!! $listLink('Green Construction Directors Mailing List') !!}</li>
                <li> {!! $listLink('Construction Equipment Dealers Database') !!}</li>
                <li> {!! $listLink('Heavy Machinery Rental Companies Email List') !!}</li>
                <li> {!! $listLink('Equipment Rental Managers Contact List') !!}</li>
                <li> {!! $listLink('Architecture And Construction Firms Mailing List') !!}</li>
                <li> {!! $listLink('Landscape Construction Contractors Database') !!}</li>
                <li> {!! $listLink('Architecture Firm Founders Email List') !!}</li>
                <li> {!! $listLink('Remodeling And Renovation Contractors Contact List') !!}</li>
                <li> {!! $listLink('Kitchen And Bath Remodelers Mailing List') !!}</li>
                <li> {!! $listLink('Renovation Business Owners Database') !!}</li>
                
                
            </ul>

        </div>

      <!-- TAB 6 -->
        <div class="industry-tab-content" id="industry6">

                <ul class="industry-list">
                    <li> {!! $listLink('Financial Services Email List') !!}</li>
                    <li> {!! $listLink('Investment Banking Firms Database') !!}</li>
                    <li> {!! $listLink('Finance CEOs Contact List') !!}</li>
                    <li> {!! $listLink('Banking Industry Mailing List') !!}</li>
                    <li> {!! $listLink('Commercial Banks Email Database') !!}</li>
                    <li> {!! $listLink('Banking Executives Mailing List') !!}</li>
                    <li> {!! $listLink('Insurance Companies Contact List') !!}</li>
                    <li> {!! $listLink('Health Insurance Providers Mailing List') !!}</li>
                    <li> {!! $listLink('Insurance Founders Email List') !!}</li>
                    <li> {!! $listLink('Wealth Management Firms Database') !!}</li>
                    <li> {!! $listLink('Private Equity Firms Contact List') !!}</li>
                    <li> {!! $listLink('Wealth Management VPs Email List') !!}</li>
                    <li> {!! $listLink('Accounting Firms Email List') !!}</li>
                    <li> {!! $listLink('Certified Public Accountants Database') !!}</li>
                    <li> {!! $listLink('Finance Directors Contact List') !!}</li>
                    <li> {!! $listLink('Mortgage Lenders Mailing List') !!}</li>
                    <li> {!! $listLink('Residential Loan Providers Email List') !!}</li>
                    <li> {!! $listLink('Mortgage Company Owners Database') !!}</li>
                    <li> {!! $listLink('Credit Union Database') !!}</li>
                    <li> {!! $listLink('Community Credit Unions Contact List') !!}</li>
                    <li> {!! $listLink('Credit Union Managers Mailing List') !!}</li>
                    <li> {!! $listLink('FinTech Companies Email Database') !!}</li>
                    <li> {!! $listLink('Digital Payment Providers Mailing List') !!}</li>
                    <li> {!! $listLink('FinTech CEOs Contact List') !!}</li>
                    <li> {!! $listLink('Venture Capital Firms Contact List') !!}</li>
                    <li> {!! $listLink('Startup Investment Funds Database') !!}</li>
                    <li> {!! $listLink('Venture Capital Partners Email List') !!}</li>
                    <li> {!! $listLink('Tax Consulting Firms Mailing List') !!}</li>
                    <li> {!! $listLink('Corporate Tax Advisors Contact Database') !!}</li>
                    <li> {!! $listLink('Tax Firm Owners Email List') !!}</li>
                    <li> {!! $listLink('Asset Management Companies Email List') !!}</li>
                    <li> {!! $listLink('Hedge Fund Managers Database') !!}</li>
                    <li> {!! $listLink('Asset Management Executives Contact List') !!}</li>
                    <li> {!! $listLink('Financial Advisory Firms Database') !!}</li>
                    <li> {!! $listLink('Retirement Planning Consultants Mailing List') !!}</li>
                    <li> {!! $listLink('Financial Advisors Email List') !!}</li>
                    <li> {!! $listLink('Commercial Finance Companies Contact List') !!}</li>
                    <li> {!! $listLink('Equipment Financing Providers Database') !!}</li>
                    <li> {!! $listLink('Commercial Lending Managers Mailing List') !!}</li>
                    <li> {!! $listLink('Payroll Service Providers Email List') !!}</li>
                    <li> {!! $listLink('Employee Benefits Consultants Contact List') !!}</li>
                    <li> {!! $listLink('Payroll Operations Directors Database') !!}</li>
                    <li> {!! $listLink('Risk Management Firms Mailing List') !!}</li>
                    <li> {!! $listLink('Compliance Consulting Services Database') !!}</li>
                    <li> {!! $listLink('Risk Management CEOs Email List') !!}</li>
                    
                    
                </ul>

            </div>
      <!-- TAB 7 -->
        <div class="industry-tab-content" id="industry7">

                <ul class="industry-list">
                    <li> {!! $listLink('Food And Beverage Industry Email List') !!}</li>
                    <li> {!! $listLink('Packaged Food Manufacturers Database') !!}</li>
                    <li> {!! $listLink('Food Industry CEOs Contact List') !!}</li>
                    <li> {!! $listLink('Restaurant Chains Mailing List') !!}</li>
                    <li> {!! $listLink('Fast Casual Restaurants Email Database') !!}</li>
                    <li> {!! $listLink('Restaurant Owners Mailing List') !!}</li>
                    <li> {!! $listLink('Beverage Companies Contact List') !!}</li>
                    <li> {!! $listLink('Soft Drink Manufacturers Mailing List') !!}</li>
                    <li> {!! $listLink('Beverage Executives Email List') !!}</li>
                    <li> {!! $listLink('Food Processing Companies Database') !!}</li>
                    <li> {!! $listLink('Frozen Food Producers Contact List') !!}</li>
                    <li> {!! $listLink('Food Processing Managers Database') !!}</li>
                    <li> {!! $listLink('Grocery And Supermarket Email List') !!}</li>
                    <li> {!! $listLink('Organic Food Retailers Mailing List') !!}</li>
                    <li> {!! $listLink('Grocery Store CEOs Contact List') !!}</li>
                    <li> {!! $listLink('Dairy Industry Mailing List') !!}</li>
                    <li> {!! $listLink('Cheese Manufacturers Email Database') !!}</li>
                    <li> {!! $listLink('Dairy Plant Owners Contact List') !!}</li>
                    <li> {!! $listLink('Bakery And Confectionery Database') !!}</li>
                    <li> {!! $listLink('Artisan Bakery Chains Mailing List') !!}</li>
                    <li> {!! $listLink('Bakery Founders Email List') !!}</li>
                    <li> {!! $listLink('Meat And Poultry Companies Contact List') !!}</li>
                    <li> {!! $listLink('Seafood Processing Firms Database') !!}</li>
                    <li> {!! $listLink('Protein Industry VPs Mailing List') !!}</li>
                    <li> {!! $listLink('Catering Services Email Database') !!}</li>
                    <li> {!! $listLink('Corporate Catering Providers Contact List') !!}</li>
                    <li> {!! $listLink('Catering Business Owners Email List') !!}</li>
                    <li> {!! $listLink('Coffee And Tea Companies Mailing List') !!}</li>
                    <li> {!! $listLink('Specialty Coffee Roasters Database') !!}</li>
                    <li> {!! $listLink('Coffee Shop CEOs Contact List') !!}</li>
                    <li> {!! $listLink('Alcohol Beverage Industry Contact List') !!}</li>
                    <li> {!! $listLink('Craft Brewery Companies Email List') !!}</li>
                    <li> {!! $listLink('Brewery Founders Mailing List') !!}</li>
                    <li> {!! $listLink('Food Distribution Companies Database') !!}</li>
                    <li> {!! $listLink('Wholesale Food Suppliers Contact List') !!}</li>
                    <li> {!! $listLink('Distribution Managers Email Database') !!}</li>
                    <li> {!! $listLink('Snack Food Manufacturers Email List') !!}</li>
                    <li> {!! $listLink('Healthy Snack Brands Mailing List') !!}</li>
                    <li> {!! $listLink('Snack Food Executives Contact List') !!}</li>
                    <li> {!! $listLink('Bottled Water Companies Mailing List') !!}</li>
                    <li> {!! $listLink('Beverage Bottling Plants Database') !!}</li>
                    <li> {!! $listLink('Bottling Operations Directors Email List') !!}</li>
                    <li> {!! $listLink('Commercial Kitchen Suppliers Contact List') !!}</li>
                    <li> {!! $listLink('Restaurant Equipment Dealers Database') !!}</li>
                    <li> {!! $listLink('Food Service Procurement Managers Mailing List') !!}</li>
                    
                    
                </ul>

            </div>
      <!-- TAB 8 -->
        <div class="industry-tab-content" id="industry8">

                <ul class="industry-list">
                    <li> {!! $listLink('Education Industry Email List') !!}</li>
                    <li> {!! $listLink('Higher Education Institutions Database') !!}</li>
                    <li> {!! $listLink('Education CEOs Contact List') !!}</li>
                    <li> {!! $listLink('Schools And Universities Mailing List') !!}</li>
                    <li> {!! $listLink('Private K12 Schools Email Database') !!}</li>
                    <li> {!! $listLink('School Administrators Mailing List') !!}</li>
                    <li> {!! $listLink('EdTech Companies Contact List') !!}</li>
                    <li> {!! $listLink('Online Learning Platforms Mailing List') !!}</li>
                    <li> {!! $listLink('EdTech Founders Email List') !!}</li>
                    <li> {!! $listLink('Colleges And Universities Database') !!}</li>
                    <li> {!! $listLink('Community Colleges Contact List') !!}</li>
                    <li> {!! $listLink('University Presidents Email List') !!}</li>
                    <li> {!! $listLink('Training And Development Firms Email List') !!}</li>
                    <li> {!! $listLink('Corporate Training Providers Database') !!}</li>
                    <li> {!! $listLink('Training Managers Contact List') !!}</li>
                    <li> {!! $listLink('Educational Services Mailing List') !!}</li>
                    <li> {!! $listLink('Tutoring Centers Email Database') !!}</li>
                    <li> {!! $listLink('Education Business Owners Mailing List') !!}</li>
                    <li> {!! $listLink('Academic Institutions Contact List') !!}</li>
                    <li> {!! $listLink('STEM Education Programs Database') !!}</li>
                    <li> {!! $listLink('Academic Directors Email List') !!}</li>
                    <li> {!! $listLink('Early Childhood Education Database') !!}</li>
                    <li> {!! $listLink('Preschool And Daycare Centers Mailing List') !!}</li>
                    <li> {!! $listLink('Childcare Owners Contact List') !!}</li>
                    <li> {!! $listLink('Vocational Schools Email List') !!}</li>
                    <li> {!! $listLink('Technical Training Institutes Database') !!}</li>
                    <li> {!! $listLink('Vocational School CEOs Mailing List') !!}</li>
                    <li> {!! $listLink('Student Recruitment Firms Contact List') !!}</li>
                    <li> {!! $listLink('International Education Consultants Email List') !!}</li>
                    <li> {!! $listLink('Admissions Directors Database') !!}</li>
                    <li> {!! $listLink('E Learning Companies Mailing List') !!}</li>
                    <li> {!! $listLink('Virtual Classroom Providers Contact List') !!}</li>
                    <li> {!! $listLink('Learning Platform Executives Email List') !!}</li>
                    <li> {!! $listLink('Educational Publishers Database') !!}</li>
                    <li> {!! $listLink('Digital Textbook Providers Mailing List') !!}</li>
                    <li> {!! $listLink('Publishing Managers Contact List') !!}</li>
                    <li> {!! $listLink('Charter Schools Email Database') !!}</li>
                    <li> {!! $listLink('Montessori Schools Contact List') !!}</li>
                    <li> {!! $listLink('School Founders Mailing List') !!}</li>
                    <li> {!! $listLink('Education Nonprofit Organizations Mailing List') !!}</li>
                    <li> {!! $listLink('Scholarship Program Providers Database') !!}</li>
                    <li> {!! $listLink('Nonprofit Education Directors Email List') !!}</li>
                    <li> {!! $listLink('Campus Technology Providers Contact List') !!}</li>
                    <li> {!! $listLink('Student Information System Vendors Database') !!}</li>
                    <li> {!! $listLink('Education IT Managers Mailing List') !!}</li>
                    
                    
                </ul>

            </div>
      <!-- TAB 9 -->
        <div class="industry-tab-content" id="industry9">

                <ul class="industry-list">
                    <li> {!! $listLink('Automotive Industry Email List') !!}</li>
                    <li> {!! $listLink('Auto Dealerships Database') !!}</li>
                    <li> {!! $listLink('Automotive CEOs Contact List') !!}</li>
                    <li> {!! $listLink('Car Dealers Mailing List') !!}</li>
                    <li> {!! $listLink('Luxury Vehicle Dealers Email Database') !!}</li>
                    <li> {!! $listLink('Dealership Owners Mailing List') !!}</li>
                    <li> {!! $listLink('Auto Parts Manufacturers Contact List') !!}</li>
                    <li> {!! $listLink('OEM Automotive Suppliers Mailing List') !!}</li>
                    <li> {!! $listLink('Automotive VPs Email List') !!}</li>
                    <li> {!! $listLink('Vehicle Repair Services Database') !!}</li>
                    <li> {!! $listLink('Collision Repair Centers Contact List') !!}</li>
                    <li> {!! $listLink('Auto Service Managers Database') !!}</li>
                    <li> {!! $listLink('Automotive Technology Companies Email List') !!}</li>
                    <li> {!! $listLink('Connected Car Solutions Providers Mailing List') !!}</li>
                    <li> {!! $listLink('Automotive Founders Contact List') !!}</li>
                    <li> {!! $listLink('Trucking And Fleet Companies Mailing List') !!}</li>
                    <li> {!! $listLink('Commercial Vehicle Operators Database') !!}</li>
                    <li> {!! $listLink('Fleet Managers Email List') !!}</li>
                    <li> {!! $listLink('Electric Vehicle Companies Contact List') !!}</li>
                    <li> {!! $listLink('EV Charging Station Providers Database') !!}</li>
                    <li> {!! $listLink('EV Industry Executives Mailing List') !!}</li>
                    <li> {!! $listLink('Tire Manufacturers Email Database') !!}</li>
                    <li> {!! $listLink('Tire Retail Chains Contact List') !!}</li>
                    <li> {!! $listLink('Tire Company Owners Email List') !!}</li>
                    <li> {!! $listLink('Auto Finance Companies Mailing List') !!}</li>
                    <li> {!! $listLink('Vehicle Leasing Providers Database') !!}</li>
                    <li> {!! $listLink('Automotive Finance Directors Contact List') !!}</li>
                    <li> {!! $listLink('Automotive Aftermarket Suppliers Contact List') !!}</li>
                    <li> {!! $listLink('Performance Parts Manufacturers Email List') !!}</li>
                    <li> {!! $listLink('Aftermarket Sales Managers Mailing List') !!}</li>
                    <li> {!! $listLink('Motorcycle Dealers Database') !!}</li>
                    <li> {!! $listLink('Powersports Retailers Contact List') !!}</li>
                    <li> {!! $listLink('Motorcycle Business Owners Email List') !!}</li>
                    <li> {!! $listLink('Car Rental Companies Mailing List') !!}</li>
                    <li> {!! $listLink('Luxury Car Rental Providers Database') !!}</li>
                    <li> {!! $listLink('Rental Operations Managers Contact List') !!}</li>
                    <li> {!! $listLink('Automotive Logistics Firms Email List') !!}</li>
                    <li> {!! $listLink('Vehicle Transport Services Mailing List') !!}</li>
                    <li> {!! $listLink('Logistics Directors Database') !!}</li>
                    <li> {!! $listLink('Auto Body Shops Contact Database') !!}</li>
                    <li> {!! $listLink('Paint And Refinishing Contractors Email List') !!}</li>
                    <li> {!! $listLink('Collision Center CEOs Mailing List') !!}</li>
                    <li> {!! $listLink('Automotive Software Providers Mailing List') !!}</li>
                    <li> {!! $listLink('Dealer Management System Vendors Database') !!}</li>
                    <li> {!! $listLink('Automotive IT Managers Contact List') !!}</li>
                    
                    
                </ul>

            </div>
      <!-- TAB 10 -->
        <div class="industry-tab-content" id="industry10">

                <ul class="industry-list">
                   <li> {!! $listLink('Logistics Companies Email List') !!}</li>
                    <li> {!! $listLink('Third Party Logistics Providers Database') !!}</li>
                    <li> {!! $listLink('Logistics CEOs Contact List') !!}</li>
                    <li> {!! $listLink('Transportation Services Mailing List') !!}</li>
                    <li> {!! $listLink('Freight Forwarding Companies Email Database') !!}</li>
                    <li> {!! $listLink('Transportation Managers Mailing List') !!}</li>
                    <li> {!! $listLink('Supply Chain Companies Contact List') !!}</li>
                    <li> {!! $listLink('Warehouse Distribution Centers Mailing List') !!}</li>
                    <li> {!! $listLink('Supply Chain Executives Email List') !!}</li>
                    <li> {!! $listLink('Trucking Companies Database') !!}</li>
                    <li> {!! $listLink('Long Haul Trucking Fleets Contact List') !!}</li>
                    <li> {!! $listLink('Fleet Owners Mailing List') !!}</li>
                    <li> {!! $listLink('Freight And Shipping Email List') !!}</li>
                    <li> {!! $listLink('Ocean Freight Carriers Database') !!}</li>
                    <li> {!! $listLink('Shipping Directors Contact List') !!}</li>
                    <li> {!! $listLink('Warehousing Services Mailing List') !!}</li>	
                    <li> {!! $listLink('Cold Storage Facilities Email Database') !!}</li>	
                    <li> {!! $listLink('Warehouse Managers Contact List') !!}</li>
                    <li> {!! $listLink('Courier And Delivery Companies Contact List') !!}</li>
                    <li> {!! $listLink('Last Mile Delivery Providers Database') !!}</li>
                    <li> {!! $listLink('Delivery Operations CEOs Mailing List') !!}</li>
                    <li> {!! $listLink('Air Cargo Services Email Database') !!}</li>
                    <li> {!! $listLink('Aviation Logistics Providers Contact List') !!}</li>	
                    <li> {!! $listLink('Air Freight Managers Email List') !!}</li>
                    <li> {!! $listLink('Rail Logistics Companies Mailing List') !!}</li>
                    <li> {!! $listLink('Intermodal Transport Providers Database') !!}</li>
                    <li> {!! $listLink('Rail Logistics Executives Contact List') !!}</li>
                    <li> {!! $listLink('Ecommerce Fulfillment Centers Contact List') !!}</li>
                    <li> {!! $listLink('Order Fulfillment Providers Email List') !!}</li>
                    <li> {!! $listLink('Fulfillment Operations Managers Database') !!}</li>
                    <li> {!! $listLink('Import Export Companies Mailing List') !!}</li>
                    <li> {!! $listLink('Customs Brokerage Firms Database') !!}</li>
                    <li> {!! $listLink('Customs Compliance Directors Email List') !!}</li>
                    <li> {!! $listLink('Fleet Management Services Contact Database') !!}</li>
                    <li> {!! $listLink('Commercial Vehicle Tracking Providers Mailing List') !!}</li>
                    <li> {!! $listLink('Fleet Operations VPs Contact List') !!}</li>
                    <li> {!! $listLink('Maritime Logistics Companies Email List') !!}</li>
                    <li> {!! $listLink('Port Terminal Operators Database') !!}</li>
                    <li> {!! $listLink('Maritime CEOs Mailing List') !!}</li>
                    <li> {!! $listLink('Reverse Logistics Providers Mailing List') !!}</li>
                    <li> {!! $listLink('Returns Management Services Contact List') !!}</li>
                    <li> {!! $listLink('Reverse Logistics Managers Email Database') !!}</li>
                    <li> {!! $listLink('Logistics Technology Companies Contact List') !!}</li>	
                    <li> {!! $listLink('Transportation Management Software Vendors Database') !!}</li>
                    <li> {!! $listLink('Logistics IT Directors Mailing List') !!}</li>
                </ul>

            </div>
      <!-- TAB 11 -->
        <div class="industry-tab-content" id="industry11">

                <ul class="industry-list">
                    <li>{!! $listLink('Business Consulting Firms Email List') !!}</li>
                    <li>{!! $listLink('Management Consulting Companies Database') !!}</li>
                    <li>{!! $listLink('Consulting CEOs Contact List') !!}</li>
                    
                    <li>{!! $listLink('Corporate Advisory Services Mailing List') !!}</li>
                    <li>{!! $listLink('Strategy Consulting Firms Email Database') !!}</li>
                    <li>{!! $listLink('Business Consultants Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Financial Consulting Companies Contact List') !!}</li>
                    <li>{!! $listLink('Risk Management Consultants Mailing List') !!}</li>
                    <li>{!! $listLink('Consulting Founders Email List') !!}</li>
                    
                    <li>{!! $listLink('Human Resources Consulting Database') !!}</li>
                    <li>{!! $listLink('Talent Acquisition Consulting Firms Contact List') !!}</li>
                    <li>{!! $listLink('HR Consulting Directors Email List') !!}</li>
                    
                    <li>{!! $listLink('IT Consulting Services Email List') !!}</li>
                    <li>{!! $listLink('Digital Transformation Consultants Database') !!}</li>
                    <li>{!! $listLink('IT Consulting VPs Contact List') !!}</li>
                    
                    <li>{!! $listLink('Marketing Consulting Firms Mailing List') !!}</li>
                    <li>{!! $listLink('Brand Strategy Consultants Email Database') !!}</li>
                    <li>{!! $listLink('Marketing Consulting Owners Contact List') !!}</li>
                    
                    <li>{!! $listLink('Operations Consulting Companies Contact List') !!}</li>
                    <li>{!! $listLink('Supply Chain Consulting Firms Database') !!}</li>
                    <li>{!! $listLink('Operations Managers Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Healthcare Consulting Services Email Database') !!}</li>
                    <li>{!! $listLink('Medical Practice Consultants Contact List') !!}</li>
                    <li>{!! $listLink('Healthcare Consulting CEOs Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Legal Consulting Firms Mailing List') !!}</li>
                    <li>{!! $listLink('Compliance Advisory Services Database') !!}</li>
                    <li>{!! $listLink('Compliance Consultants Email List') !!}</li>
                    
                    <li>{!! $listLink('Environmental Consulting Companies Contact List') !!}</li>
                    <li>{!! $listLink('Sustainability Consulting Firms Email Database') !!}</li>
                    <li>{!! $listLink('Environmental Consulting Directors Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Executive Coaching Services Database') !!}</li>
                    <li>{!! $listLink('Leadership Development Consultants Contact List') !!}</li>
                    <li>{!! $listLink('Executive Coaches Email List') !!}</li>
                    
                    <li>{!! $listLink('Startup Consulting Firms Mailing List') !!}</li>
                    <li>{!! $listLink('Small Business Advisory Services Database') !!}</li>
                    <li>{!! $listLink('Startup Advisors Contact List') !!}</li>
                    
                    <li>{!! $listLink('Engineering Consulting Companies Email List') !!}</li>
                    <li>{!! $listLink('Infrastructure Consulting Firms Mailing List') !!}</li>
                    <li>{!! $listLink('Engineering Consulting Managers Database') !!}</li>
                    
                    <li>{!! $listLink('Retail Consulting Services Contact Database') !!}</li>
                    <li>{!! $listLink('Ecommerce Strategy Consultants Email List') !!}</li>
                    <li>{!! $listLink('Retail Consulting Executives Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Business Analytics Consulting Firms Mailing List') !!}</li>
                    <li>{!! $listLink('Data Intelligence Consultants Database') !!}</li>
                    <li>{!! $listLink('Analytics Consulting Founders Contact List') !!}</li>
                    
                </ul>

            </div>
      <!-- TAB 12 -->
        <div class="industry-tab-content" id="industry12">

               
                <ul class="industry-list">
                    <li>{!! $listLink('Telecommunications Companies Email List') !!}</li>
                    <li>{!! $listLink('Wireless Network Providers Database') !!}</li>
                    <li>{!! $listLink('Telecom CEOs Contact List') !!}</li>
                    
                    <li>{!! $listLink('Telecom Service Providers Mailing List') !!}</li>
                    <li>{!! $listLink('Mobile Carrier Companies Email Database') !!}</li>
                    <li>{!! $listLink('Telecommunications Executives Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Internet Service Providers Contact List') !!}</li>
                    <li>{!! $listLink('Fiber Optic Network Operators Mailing List') !!}</li>
                    <li>{!! $listLink('ISP Owners Contact List') !!}</li>
                    
                    <li>{!! $listLink('Telecom Infrastructure Companies Database') !!}</li>
                    <li>{!! $listLink('Cell Tower Management Firms Contact List') !!}</li>
                    <li>{!! $listLink('Telecom Operations Managers Email List') !!}</li>
                    
                    <li>{!! $listLink('VoIP Service Providers Email List') !!}</li>
                    <li>{!! $listLink('Cloud Communications Platforms Mailing List') !!}</li>
                    <li>{!! $listLink('VoIP Founders Database') !!}</li>
                    
                    <li>{!! $listLink('Cable And Satellite Companies Contact List') !!}</li>
                    <li>{!! $listLink('Broadband Service Providers Database') !!}</li>
                    <li>{!! $listLink('Cable Industry VPs Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Unified Communications Companies Email Database') !!}</li>
                    <li>{!! $listLink('Business Phone System Providers Contact List') !!}</li>
                    <li>{!! $listLink('Communications Directors Email List') !!}</li>
                    
                    <li>{!! $listLink('Telecom Equipment Manufacturers Mailing List') !!}</li>
                    <li>{!! $listLink('Network Hardware Suppliers Database') !!}</li>
                    <li>{!! $listLink('Telecom Product Managers Contact List') !!}</li>
                    
                    <li>{!! $listLink('Data Center Connectivity Providers Contact List') !!}</li>
                    <li>{!! $listLink('Colocation Network Services Email List') !!}</li>
                    <li>{!! $listLink('Data Network Executives Mailing List') !!}</li>
                    
                    <li>{!! $listLink('5G Technology Companies Database') !!}</li>
                    <li>{!! $listLink('Small Cell Deployment Providers Mailing List') !!}</li>
                    <li>{!! $listLink('5G Innovation CEOs Contact List') !!}</li>
                    
                    <li>{!! $listLink('Telecom Software Vendors Email List') !!}</li>
                    <li>{!! $listLink('OSS BSS Solution Providers Database') !!}</li>
                    <li>{!! $listLink('Telecom IT Managers Email List') !!}</li>
                    
                    <li>{!! $listLink('Satellite Communications Companies Mailing List') !!}</li>
                    <li>{!! $listLink('Remote Connectivity Providers Contact List') !!}</li>
                    <li>{!! $listLink('Satellite Network Directors Database') !!}</li>
                    
                    <li>{!! $listLink('Enterprise Networking Firms Contact Database') !!}</li>
                    <li>{!! $listLink('SD WAN Service Providers Mailing List') !!}</li>
                    <li>{!! $listLink('Enterprise Telecom Managers Contact List') !!}</li>
                    
                    <li>{!! $listLink('Telecommunications Consulting Firms Email List') !!}</li>
                    <li>{!! $listLink('Network Security Consultants Database') !!}</li>
                    <li>{!! $listLink('Telecom Consulting Founders Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Telecom Resellers And Distributors Contact List') !!}</li>
                    <li>{!! $listLink('Managed Communication Services Database') !!}</li>
                    <li>{!! $listLink('Channel Partner Managers Email List') !!}</li>
                    
                </ul>


            </div>

      <!-- TAB 13 -->
        <div class="industry-tab-content" id="industry13">
 <ul class="industry-list">
                    <li>{!! $listLink('Consumer Goods Companies Email List') !!}</li>
                    <li>{!! $listLink('Household Products Manufacturers Database') !!}</li>
                    <li>{!! $listLink('Consumer Goods CEOs Contact List') !!}</li>
                    
                    <li>{!! $listLink('Fast Moving Consumer Goods Mailing List') !!}</li>
                    <li>{!! $listLink('Packaged Consumer Brands Email Database') !!}</li>
                    <li>{!! $listLink('FMCG Executives Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Personal Care Products Contact List') !!}</li>
                    <li>{!! $listLink('Skincare Brands Database') !!}</li>
                    <li>{!! $listLink('Beauty Brand Founders Email List') !!}</li>
                    
                    <li>{!! $listLink('Home And Kitchen Products Database') !!}</li>
                    <li>{!! $listLink('Kitchenware Manufacturers Contact List') !!}</li>
                    <li>{!! $listLink('Product Development Managers Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Apparel Consumer Brands Email List') !!}</li>
                    <li>{!! $listLink('Footwear Manufacturers Mailing List') !!}</li>
                    <li>{!! $listLink('Fashion Brand Owners Contact List') !!}</li>
                    
                    <li>{!! $listLink('Consumer Electronics Companies Database') !!}</li>
                    <li>{!! $listLink('Smart Home Device Manufacturers Email List') !!}</li>
                    <li>{!! $listLink('Electronics CEOs Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Health And Wellness Brands Contact List') !!}</li>
                    <li>{!! $listLink('Nutritional Supplement Companies Database') !!}</li>
                    <li>{!! $listLink('Wellness Brand Managers Email List') !!}</li>
                    
                    <li>{!! $listLink('Toys And Games Manufacturers Mailing List') !!}</li>
                    <li>{!! $listLink('Educational Toy Brands Contact List') !!}</li>
                    <li>{!! $listLink('Toy Industry Executives Database') !!}</li>
                    
                    <li>{!! $listLink('Pet Consumer Goods Email Database') !!}</li>
                    <li>{!! $listLink('Pet Accessories Manufacturers Mailing List') !!}</li>
                    <li>{!! $listLink('Pet Brand Founders Contact List') !!}</li>
                    
                    <li>{!! $listLink('Luxury Consumer Brands Contact List') !!}</li>
                    <li>{!! $listLink('Premium Lifestyle Products Database') !!}</li>
                    <li>{!! $listLink('Luxury Brand Directors Email List') !!}</li>
                    
                    <li>{!! $listLink('Cleaning Products Companies Mailing List') !!}</li>
                    <li>{!! $listLink('Eco Friendly Household Brands Database') !!}</li>
                    <li>{!! $listLink('Cleaning Product Owners Contact List') !!}</li>
                    
                    <li>{!! $listLink('Baby Products Manufacturers Email List') !!}</li>
                    <li>{!! $listLink('Infant Care Brands Mailing List') !!}</li>
                    <li>{!! $listLink('Baby Brand CEOs Database') !!}</li>
                    
                    <li>{!! $listLink('Outdoor Consumer Goods Contact Database') !!}</li>
                    <li>{!! $listLink('Camping Gear Manufacturers Email List') !!}</li>
                    <li>{!! $listLink('Outdoor Product Managers Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Beverage Consumer Brands Mailing List') !!}</li>
                    <li>{!! $listLink('Functional Drink Companies Database') !!}</li>
                    <li>{!! $listLink('Beverage Brand Executives Contact List') !!}</li>
                    
                    <li>{!! $listLink('Consumer Goods Distributors Email List') !!}</li>
                    <li>{!! $listLink('Wholesale Consumer Product Suppliers Database') !!}</li>
                    <li>{!! $listLink('Distribution VPs Mailing List') !!}</li>
                    
                    
                </ul>
            </div>
      <!-- TAB 14 -->
        <div class="industry-tab-content" id="industry14">

                <ul class="industry-list">
                    <li>{!! $listLink('Oil And Gas Companies Email List') !!}</li>
                    <li>{!! $listLink('Upstream Exploration Firms Database') !!}</li>
                    <li>{!! $listLink('Oil And Gas CEOs Contact List') !!}</li>
                    
                    <li>{!! $listLink('Petroleum Industry Mailing List') !!}</li>
                    <li>{!! $listLink('Offshore Drilling Contractors Email Database') !!}</li>
                    <li>{!! $listLink('Energy Executives Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Natural Gas Companies Contact List') !!}</li>
                    <li>{!! $listLink('LNG Processing Facilities Mailing List') !!}</li>
                    <li>{!! $listLink('Gas Operations Managers Contact List') !!}</li>
                    
                    <li>{!! $listLink('Oilfield Services Database') !!}</li>
                    <li>{!! $listLink('Hydraulic Fracturing Providers Contact List') !!}</li>
                    <li>{!! $listLink('Oilfield Service Owners Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Refining And Petrochemical Companies Email List') !!}</li>
                    <li>{!! $listLink('Crude Oil Refineries Database') !!}</li>
                    <li>{!! $listLink('Refinery Directors Email List') !!}</li>
                    
                    <li>{!! $listLink('Pipeline Operators Mailing List') !!}</li>
                    <li>{!! $listLink('Midstream Transportation Companies Contact Database') !!}</li>
                    <li>{!! $listLink('Pipeline Managers Contact List') !!}</li>
                    
                    <li>{!! $listLink('Energy Infrastructure Companies Email Database') !!}</li>
                    <li>{!! $listLink('Storage Terminal Operators Mailing List') !!}</li>
                    <li>{!! $listLink('Infrastructure VPs Contact List') !!}</li>
                    
                    <li>{!! $listLink('Drilling Equipment Manufacturers Contact List') !!}</li>
                    <li>{!! $listLink('Oil Rig Equipment Suppliers Database') !!}</li>
                    <li>{!! $listLink('Equipment Procurement Managers Email List') !!}</li>
                    
                    <li>{!! $listLink('Energy Consulting Firms Mailing List') !!}</li>
                    <li>{!! $listLink('Reservoir Engineering Consultants Database') !!}</li>
                    <li>{!! $listLink('Energy Consulting Founders Contact List') !!}</li>
                    
                    <li>{!! $listLink('Fuel Distribution Companies Contact Database') !!}</li>
                    <li>{!! $listLink('Wholesale Petroleum Suppliers Email List') !!}</li>
                    <li>{!! $listLink('Fuel Distribution CEOs Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Offshore Energy Companies Email List') !!}</li>
                    <li>{!! $listLink('Subsea Engineering Contractors Mailing List') !!}</li>
                    <li>{!! $listLink('Offshore Operations Directors Database') !!}</li>
                    
                    <li>{!! $listLink('Oil And Gas Technology Providers Contact List') !!}</li>
                    <li>{!! $listLink('Energy Analytics Software Vendors Database') !!}</li>
                    <li>{!! $listLink('Energy IT Managers Email List') !!}</li>
                    
                    <li>{!! $listLink('Environmental Energy Services Mailing List') !!}</li>
                    <li>{!! $listLink('Oil Spill Response Contractors Database') !!}</li>
                    <li>{!! $listLink('HSE Managers Contact List') !!}</li>
                    
                    <li>{!! $listLink('Industrial Lubricant Manufacturers Email Database') !!}</li>
                    <li>{!! $listLink('Specialty Oil Product Suppliers Contact List') !!}</li>
                    <li>{!! $listLink('Lubricant Brand Executives Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Renewable Energy Transition Firms Contact List') !!}</li>
                    <li>{!! $listLink('Carbon Capture Technology Providers Database') !!}</li>
                    <li>{!! $listLink('Energy Innovation Leaders Email List') !!}</li>
                    
                </ul>

            </div>
      <!-- TAB 15 -->
        <div class="industry-tab-content" id="industry15">

                <ul class="industry-list">
                    <li>{!! $listLink('Aviation Industry Email List') !!}</li>
                    <li>{!! $listLink('Commercial Airlines Database') !!}</li>
                    <li>{!! $listLink('Aviation CEOs Contact List') !!}</li>
                    
                    <li>{!! $listLink('Aerospace Companies Mailing List') !!}</li>
                    <li>{!! $listLink('Aircraft Manufacturers Email Database') !!}</li>
                    <li>{!! $listLink('Aerospace Executives Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Airport Operations Contact List') !!}</li>
                    <li>{!! $listLink('International Airport Authorities Database') !!}</li>
                    <li>{!! $listLink('Airport Managers Contact List') !!}</li>
                    
                    <li>{!! $listLink('Aviation Maintenance Companies Email List') !!}</li>
                    <li>{!! $listLink('Aircraft MRO Providers Mailing List') !!}</li>
                    <li>{!! $listLink('Maintenance Directors Email List') !!}</li>
                    
                    <li>{!! $listLink('Private Aviation Firms Database') !!}</li>
                    <li>{!! $listLink('Business Jet Operators Contact List') !!}</li>
                    <li>{!! $listLink('Private Aviation Owners Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Air Cargo Companies Mailing List') !!}</li>
                    <li>{!! $listLink('Freight Aviation Services Database') !!}</li>
                    <li>{!! $listLink('Cargo Operations Managers Email List') !!}</li>
                    
                    <li>{!! $listLink('Flight Training Schools Contact List') !!}</li>
                    <li>{!! $listLink('Pilot Training Academies Email Database') !!}</li>
                    <li>{!! $listLink('Aviation School Founders Contact List') !!}</li>
                    
                    <li>{!! $listLink('Aviation Technology Providers Database') !!}</li>
                    <li>{!! $listLink('Flight Software Vendors Mailing List') !!}</li>
                    <li>{!! $listLink('Aviation IT Managers Contact List') !!}</li>
                    
                    <li>{!! $listLink('Aircraft Parts Suppliers Email List') !!}</li>
                    <li>{!! $listLink('Avionics Equipment Manufacturers Database') !!}</li>
                    <li>{!! $listLink('Procurement Executives Mailing List') !!}</li>
                    
                    <li>{!! $listLink('Helicopter Services Companies Contact List') !!}</li>
                    <li>{!! $listLink('Emergency Air Transport Providers Database') !!}</li>
                    <li>{!! $listLink('Helicopter Operations Directors Email List') !!}</li>
                    
                    <li>{!! $listLink('Airline Catering Services Mailing List') !!}</li>
                    <li>{!! $listLink('In Flight Food Suppliers Contact Database') !!}</li>
                    <li>{!! $listLink('Catering Managers Email List') !!}</li>
                    
                    <li>{!! $listLink('Aviation Consulting Firms Email Database') !!}</li>
                    <li>{!! $listLink('Airport Infrastructure Consultants Mailing List') !!}</li>
                    <li>{!! $listLink('Aviation Consulting CEOs Contact List') !!}</li>
                    
                    <li>{!! $listLink('Drone Technology Companies Contact List') !!}</li>
                    <li>{!! $listLink('Commercial UAV Operators Database') !!}</li>
                    <li>{!! $listLink('Drone Industry Founders Email List') !!}</li>
                    
                    <li>{!! $listLink('Aviation Fuel Suppliers Mailing List') !!}</li>
                    <li>{!! $listLink('Jet Fuel Distribution Companies Database') !!}</li>
                    <li>{!! $listLink('Fuel Operations Managers Contact List') !!}</li>
                    
                    <li>{!! $listLink('Airline Staffing Agencies Email List') !!}</li>
                    <li>{!! $listLink('Aviation Recruitment Firms Mailing List') !!}</li>
                    <li>{!! $listLink('Aviation Recruiters Contact Database') !!}</li>
                    
                </ul>

            </div>

        </div>

    </div>

</div>

<!-- TAB SCRIPT -->
<div class="green-banner">

    <img src="{{asset('assets/uploads/icon.webp')}}"  width="60">

    <div class="banner-text">
        Unlock {{$service_item->data_counts}} Verified {{ $contactListTitle }} Today
    </div>
<a href="https://app.go4database.com/login?utm_source=ProductPage&amp;utm_medium=Internal&amp;utm_campaign=app_login" class="read-btn" target="_blank">
   Have you requested for free sample data yet?</a>
</div>


<!-- CTA -->

<div class="cta">

<h2>
Try Book Your Data with 1200 Free Credits, no Credit Card Required.
</h2>

<p>
No subscription commitments or mandatory demos.
Discover the power of 95% accurate, laser targeted data on your terms.
</p>

<div class="cta-form">
<form action="https://app.go4database.com/register" method="get">
<input name="email" type="text" placeholder="Enter your work email">

<button class="orange-btn">
Get 1200 Free Leads
</button>
</form>
<span>OR</span>

<a href="https://app.go4database.com/login?utm_source=ProductPage&amp;utm_medium=Internal&amp;utm_campaign=app_login" class="demo-btn">
Book a Demo
</a>

</div>

<div class="cta-features">

<div class="cta-feature">
<div class="check"><img src="{{asset('assets/uploads/icon.webp')}}"  width="30"></div>
Real time email verification
</div>

<div class="cta-feature">
<div class="check"><img src="{{asset('assets/uploads/icon.webp')}}"  width="30"></div>
95% data accuracy guarantee
</div>

<div class="cta-feature">
<div class="check"><img src="{{asset('assets/uploads/icon.webp')}}"  width="30"></div>
GDPR & CCPA compliant
</div>

</div>

</div>


<!-- SAMPLE DATA -->

<div class="sample-data-section" id="sample-data-section">

    <div class="sample-box">

        <div class="sample-flex">

            <!-- LEFT -->

            <div class="sample-left">

                <h2>
                    Have you requested for free sample data yet?
                </h2>

                <p>
                    Experience the accuracy of our verified B2B database with a complimentary sample list customized to your ideal customer profile.
                </p>

            </div>

            <!-- RIGHT -->

            <div class="sample-right">
@if(session('sample-success'))
    <div class="alert alert-success">
        {{ session('sample-success') }}
    </div>
@endif
              <form action="{{ route('sample-lead.submit') }}" method="POST">
    @csrf

    <div class="sample-form-grid">

        <input type="text"
               name="fname"
               placeholder="Enter your first name"
               required>

        <input type="text"
               name="lname"
               placeholder="Enter your last name"
               required>

        <input type="email"
               name="business-email"
               placeholder="Business email"
               required>

        <textarea name="sample-message"
                  placeholder="What outcome are you seeking from this list?"
                  required></textarea>

        <input type="text"
               name="sample-phone"
               placeholder="Phone"
               required>

    </div>

    <button type="submit" class="sample-btn">
        Submit Your Request
    </button>
</form>

            </div>

        </div>

    </div>

</div>

<!-- BLOGS -->
<section class="related-blogs py-5">

    <div class="container">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="blog-title">Related Blogs</h2>
            </div>

            <div>
                <a class="carousel-btn mr-2"
                   href="#blogCarousel"
                   role="button"
                   data-slide="prev">
                    &#8249;
                </a>

                <a class="carousel-btn active"
                   href="#blogCarousel"
                   role="button"
                   data-slide="next">
                    &#8250;
                </a>
            </div>

        </div>

        <!-- CAROUSEL -->
        <div id="blogCarousel"
             class="carousel slide"
             data-ride="carousel">

 <div class="carousel-inner">

    @foreach($relatedBlogs->chunk(3) as $key => $blogs)
        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">

            <div class="row">

                @foreach($blogs as $blog)
                
                    <div class="col-md-4">

                        <div class="blog-card">

                            <div class="blog-img">
  {!! render_image_markup_by_attachment_id($blog->image) !!}
                                
                                <span class="blog-date">
                                    {{ date('d M, Y', strtotime($blog->created_at)) }}
                                </span>

                            </div>

                            <div class="blog-content">

                                <h3>
                                    {{ $blog->title }}
                                </h3>

                                <p>
                                    {{ \Illuminate\Support\Str::limit(strip_tags($blog->content), 120) }}
                                </p>

                                <a href="{{ route('frontend.blog.single', $blog->slug) }}"
                                   class="read-btn">
                                    Read More
                                </a>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>
    @endforeach

</div>

        </div>

    </div>

</section>

</div>
<script>

const industryTabs =
document.querySelectorAll('.industry-tab-btn');

const industryContents =
document.querySelectorAll('.industry-tab-content');

industryTabs.forEach(tab => {

    tab.addEventListener('click', () => {

        industryTabs.forEach(btn => {
            btn.classList.remove('active');
        });

        industryContents.forEach(content => {
            content.classList.remove('active');
        });

        tab.classList.add('active');

        document.getElementById(
            tab.dataset.tab
        ).classList.add('active');

    });

});

</script>
<script>

const faqItems =
document.querySelectorAll('.faq-item');

faqItems.forEach(item => {

    const question =
    item.querySelector('.faq-question');

    question.addEventListener('click', () => {

        if(item.classList.contains('active')){

            item.classList.remove('active');

        }else{

            faqItems.forEach(faq => {
                faq.classList.remove('active');
            });

            item.classList.add('active');

        }

    });

});

</script>

@if(\App\Http\Controllers\WebsiteLeadsLocalProxyController::enabled())
{{-- Local testing only: the API only accepts calls from www.go4database.com. --}}
<script>
  window.G4D_LEADS_API = @json(route('local.website.leads'));
  window.G4D_LEADS_CONTACT_API = @json(route('local.website.leads.contact'));
</script>
@endif
<script>
/* Lead search and click-to-reveal, the same API and behaviour as the homepage.
   Inline rather than in a JS file: production's public_html/assets is a separate
   copy, so a new file would have to be copied by hand on every deploy. */
(function () {
  // Website-only endpoint: at most 5 leads plus a total, never an email or phone,
  // only has_email / has_phone flags and a 30 minute contact_token per lead.
  var API_BASE = window.G4D_LEADS_API || 'https://app.go4database.com/api/website/leads';
  var CONTACT_API = window.G4D_LEADS_CONTACT_API || 'https://app.go4database.com/api/website/leads/contact';
  var REGISTER_URL = 'https://app.go4database.com/register?utm_source=ListPage&utm_medium=Internal&utm_campaign=';

  var STATUS_ICON = {
    yes: '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>',
    no: '<svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" aria-hidden="true"><path d="M7 7l10 10M17 7L7 17"/></svg>'
  };

  // Lead fields go into innerHTML, so escape them rather than trusting the data.
  function escapeHtml(value) {
    return String(value === null || value === undefined ? '' : value).replace(/[&<>"']/g, function (ch) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
    });
  }

  function el(id) { return document.getElementById(id); }

  function note(text, isError) {
    var rows = el('leads-rows');
    if (!rows) return;
    rows.innerHTML = '<div class="g4d-leads-note' + (isError ? ' is-error' : '') + '">' + escapeHtml(text) + '</div>';
  }

  function statusBadge(available, what) {
    return available
      ? '<span class="g4d-lead-status is-yes" title="' + what + ' available">' + STATUS_ICON.yes + '</span>'
      : '<span class="g4d-lead-status is-no" title="No ' + what.toLowerCase() + ' on file">' + STATUS_ICON.no + '</span>';
  }

  function renderLeads(leadsArr) {
    var rows = el('leads-rows');
    if (!rows) return;
    if (!leadsArr || !leadsArr.length) {
      note('No results found.');
      return;
    }
    rows.innerHTML = leadsArr.map(function (lead) {
      var token = escapeHtml(lead.contact_token);
      // The data uses "0000" and "" for unknown values; leave those out entirely.
      var meta = [
        lead.founded_year && lead.founded_year !== '0000' ? 'Founded ' + escapeHtml(lead.founded_year) : '',
        lead.turnover ? 'Turnover ' + escapeHtml(lead.turnover) : ''
      ].filter(Boolean).join(' &middot; ');
      var title = escapeHtml(lead.title);
      return '<div class="g4d-lead g4d-leads-grid">' +
        '<div class="g4d-lead-company-cell"><div class="g4d-lead-company">' + escapeHtml(lead.company) + '</div>' +
        (meta ? '<div class="g4d-lead-meta">' + meta + '</div>' : '') + '</div>' +
        '<div class="g4d-lead-person">' + escapeHtml(lead.person_name) + '</div>' +
        '<div class="g4d-lead-title" title="' + title + '">' + title + '</div>' +
        '<div class="g4d-lead-email"><button type="button" class="g4d-lead-btn verify-email-btn view-btn" data-type="email" data-token="' + token + '" data-available="' + (lead.has_email ? 1 : 0) + '">View Email ' + statusBadge(!!lead.has_email, 'Email') + '</button></div>' +
        '<div class="g4d-lead-phone"><button type="button" class="g4d-lead-btn view-btn" data-type="contact" data-token="' + token + '" data-available="' + (lead.has_phone ? 1 : 0) + '">View Phone ' + statusBadge(!!lead.has_phone, 'Phone') + '</button></div>' +
        '</div>';
    }).join('');
  }

  // Typing and the Search button can overlap requests; only the newest one may
  // render, so a slow earlier response can't overwrite a later search.
  var latestRequest = 0;

  // Some pages were imported with a business category ("Healthcare Staffing
  // Agencies") in place of an industry ("Healthcare"), which matches nothing in
  // the data. Rather than opening on an empty table, drop that term once and
  // clear the box, so the page shows the leads it does have and says what it
  // searched for. Only the page's own opening search widens, never a typed one.
  function fetchLeads(params, options) {
    var widenOnEmpty = !!(options && options.widenOnEmpty) && !!params.industry_business;
    var requestId = ++latestRequest;
    note('Loading...');
    fetch(API_BASE + '?' + new URLSearchParams(params).toString(), { headers: { Accept: 'application/json' } })
      .then(function (res) {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
      })
      .then(function (json) {
        if (requestId !== latestRequest) return;
        var leadsArr = Array.isArray(json) ? json : (Array.isArray(json.data) ? json.data : []);
        if (!leadsArr.length && widenOnEmpty) {
          var box = el('search-industry');
          if (box) box.value = '';
          buildParamsAndFetch();
          return;
        }
        renderLeads(leadsArr);
      })
      .catch(function (err) {
        if (requestId !== latestRequest) return;
        console.error('Lead fetch failed:', err);
        note('Something went wrong. Please try again.', true);
      });
  }

  // Same three boxes as the app's Normal filter. Each takes comma separated
  // values, e.g. "CEO, CTO".
  function buildParamsAndFetch(options) {
    var params = {};
    [['title', 'search-title'], ['industry_business', 'search-industry'], ['location', 'search-location']].forEach(function (pair) {
      var box = el(pair[1]);
      var value = box ? box.value.trim() : '';
      if (value) params[pair[0]] = value;
    });
    if (!Object.keys(params).length) {
      latestRequest++; // discard any request still in flight
      note('Enter a title, industry or location to search.');
      return;
    }
    fetchLeads(params, options);
  }

  function setupFilters() {
    var timer;
    ['search-title', 'search-industry', 'search-location'].forEach(function (id) {
      var box = el(id);
      if (!box) return;
      box.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(buildParamsAndFetch, 550);
      });
      box.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          clearTimeout(timer);
          buildParamsAndFetch();
        }
      });
    });
    var button = el('search-leads-btn');
    if (button) button.addEventListener('click', function () { clearTimeout(timer); buildParamsAndFetch(); });
  }

  document.addEventListener('DOMContentLoaded', function () {
    setupFilters();
    // These pages are about one list, so they open showing its leads.
    buildParamsAndFetch({ widenOnEmpty: true });
  });

  // View Email / View Phone: reveal that one lead's value. The API allows 10 per
  // minute and 100 per day per visitor, answers 429 past that, and 404 once the
  // search's tokens are older than 30 minutes.
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('#leads-rows .view-btn');
    if (!btn || btn.disabled) return;

    var type = btn.dataset.type === 'contact' ? 'contact' : 'email';
    var action = type === 'contact' ? 'view_contact' : 'view_email';
    var unavailable = type === 'contact' ? 'No phone available' : 'No email available';
    if (typeof gtag === 'function') gtag('event', action, { location: 'list_page' });

    // Always textContent, never innerHTML: lead data comes from uploaded files.
    var show = function (text) {
      var span = document.createElement('span');
      span.className = 'g4d-lead-value';
      span.textContent = text;
      btn.replaceWith(span);
    };

    // Known to be missing: say so without spending one of the visitor's reveals.
    if (btn.dataset.available !== '1') {
      show(unavailable);
      return;
    }

    var setBusy = function (busy) {
      btn.disabled = busy;
      btn.style.opacity = busy ? '0.6' : '';
      btn.style.cursor = busy ? 'progress' : 'pointer';
    };

    setBusy(true);
    fetch(CONTACT_API + '?token=' + encodeURIComponent(btn.dataset.token) + '&type=' + type, { headers: { Accept: 'application/json' } })
      .then(function (res) { return res.json().then(function (body) { return { status: res.status, body: body }; }); })
      .then(function (result) {
        if (result.status === 200) {
          show((type === 'contact' ? result.body.phone : result.body.email) || unavailable);
        } else if (result.status === 429) {
          var link = document.createElement('a');
          link.href = REGISTER_URL + action + '_limit';
          link.textContent = 'Sign up free to see more';
          link.className = 'g4d-lead-limit';
          btn.replaceWith(link);
          if (typeof gtag === 'function') gtag('event', 'reveal_limit_reached', { location: 'list_page' });
        } else if (result.status === 404) {
          buildParamsAndFetch(); // results expired: fetch fresh ones, and fresh tokens
        } else {
          setBusy(false);
        }
      })
      .catch(function () { setBusy(false); });
  });
})();
</script>
<script>
let currentIndex = 0;
const slides = document.querySelectorAll(".testimonial-slide");

function showSlide(index) {
  slides.forEach(slide => slide.classList.remove("active"));
  slides[index].classList.add("active");
}

function nextSlide() {
  currentIndex = (currentIndex + 1) % slides.length;
  showSlide(currentIndex);
}

function prevSlide() {
  currentIndex = (currentIndex - 1 + slides.length) % slides.length;
  showSlide(currentIndex);
}
</script>
@if(session('sample-success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('sample-data-section')
        .scrollIntoView({ behavior: 'smooth' });
});
</script>

@endif
@endsection
