@include('frontend.partials.homesupportbar')
@include('frontend.partials.navbar-new') 
{{-- home.css goes in the page itself rather than as a <link>: waiting on that
     request held the first paint back by a round trip and more on PageSpeed's
     phone test. The file in assets/ is still the one to edit. --}}
@php
    // Every text, link and list below is editable in Page Manage > Home >
    // Homepage content (App\HomePage\HomePageText); each default is the page
    // as it was built.
    $home = new \App\HomePage\HomePageText($user_select_lang_slug ?? get_user_lang());
@endphp
@push('styles')
    @php
        $home_css = inline_stylesheet('assets/frontend/css/home.css');
    @endphp
    @if($home_css)
    <style>{!! $home_css !!}</style>
    @else
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/home.css?v=128') }}">
    @endif
@endpush
 <!-- ═══════════════════════════════════════════════════════
         ANIMATED HERO SECTION — Go4Database
    ═══════════════════════════════════════════════════════ -->
<div class="g4d-hero">

      <!-- ── Top Hero Banner Container ── -->
      <div class="g4d-hero-top" style="position:relative;height:390px;width:100%;">

        <!-- Grid dots texture -->
        <div class="g4d-hero-dots"></div>

        <!-- Glowing orbs -->
        <div class="horb horb1"></div>
        <div class="horb horb2"></div>

        <!-- Animated SVG data lines -->
        <svg class="hlines-svg" viewBox="0 0 800 380" preserveAspectRatio="none">
          <path class="hline-path" d="M0,80  C150,60 250,200 400,160 S600,100 800,140" />
          <path class="hline-path" d="M0,200 C100,180 300,280 500,240 S700,180 800,220"
            style="stroke:rgba(22,163,74,0.25)" />
          <path class="hline-path2" d="M0,300 C200,260 350,340 550,300 S720,260 800,290" />
          <path class="hline-path2" d="M100,0  C180,80  260,200 360,160 S520,100 650,180 S780,200 800,190" />
          <!-- Animated nodes -->
          <circle r="4" fill="#16a34a" opacity="0.9" style="filter:drop-shadow(0 0 6px #16a34a)">
            <animateMotion dur="4s" repeatCount="indefinite" path="M0,80 C150,60 250,200 400,160 S600,100 800,140" />
          </circle>
          <circle r="3" fill="#059669" opacity="0.7" style="filter:drop-shadow(0 0 5px #059669)">
            <animateMotion dur="6s" repeatCount="indefinite" begin="1.5s"
              path="M0,200 C100,180 300,280 500,240 S700,180 800,220" />
          </circle>
          <circle r="3.5" fill="#15803d" opacity="0.8" style="filter:drop-shadow(0 0 5px #15803d)">
            <animateMotion dur="5s" repeatCount="indefinite" begin="0.8s"
              path="M100,0 C180,80 260,200 360,160 S520,100 650,180 S780,200 800,190" />
          </circle>
        </svg>

        <!-- Left text fade overlay -->
        <div class="g4d-hero-fade"></div>

        <!-- ── Floating Executive Lead Cards (Live Verified B2B Contacts) ── -->
        @if($home->on('hero_cards'))
        @php
            // Position, avatar colour and badge of the four cards, as built.
            $hero_card_looks = [
                ['hc4', '#16a34a', 'rgba(22, 163, 74, 0.35)', false],
                ['hc1', '#0d9488', 'rgba(13, 148, 136, 0.35)', true],
                ['hc2', '#0284c7', 'rgba(2, 132, 199, 0.35)', true],
                ['hc3', '#0f766e', 'rgba(15, 118, 110, 0.35)', true],
            ];
        @endphp
        @foreach(array_slice($home->pairs('hero_cards'), 0, 4) as $i => [$card_name, $card_role])
          @php
              [$card_class, $card_color, $card_glow, $card_full] = $hero_card_looks[$i];
              $card_initials = collect(preg_split('/\s+/', trim($card_name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
          @endphp
        <div class="hcard {{ $card_class }}"
          style="background: rgba(255, 255, 255, 0.25); border: 2px solid rgba(255, 255, 255, 0.75); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-radius: 999px; padding: 9px 20px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.3);">
          <div class="hcard-avatar"
            style="background: {{ $card_color }}; color: #ffffff; width: 34px; height: 34px; border-radius: 50%; font-weight: 800; font-size: 13px; box-shadow: 0 4px 10px {{ $card_glow }};">
            {{ $card_initials }}</div>
          <div>
            <div style="display: flex; align-items: center; gap: 6px;">
              <span
                style="font-size: 13.5px; font-weight: 800; color: #ffffff; text-shadow: 0 1px 4px rgba(0,0,0,0.15);">{{ $card_name }}</span>
              <span
                style="background: rgba(255, 255, 255, 0.22); color: #ffffff; border: 1.5px solid rgba(255, 255, 255, 0.7); border-radius: 99px; padding: 2px {{ $card_full ? '8px' : '7px' }}; font-size: 10px; font-weight: 800;">✓@if($card_full)
                Verified @endif</span>
            </div>
            <div style="font-size: 11px; color: #f1f5f9; margin-top: 2px; font-weight: 600;">{{ $card_role }}@if($card_full) <span
                style="margin-left:4px;">✉️ 📞</span>@endif</div>
          </div>
        </div>
        @endforeach
        @endif
      </div> <!-- /.g4d-hero-top -->

      <!-- ── Main Content ── -->
      <div class="g4d-hero-content">
        <div class="g4d-hero-left">
<h1 class="g4d-hero-hl"
            style="font-size: 40px; font-weight: 800; line-height: 1.28; letter-spacing: -1.2px; color: #ffffff; margin: 0; text-shadow: 0 2px 8px rgba(0,0,0,0.18);">
            {{ $home->get('hero_title') }} <br>
            @php
                $hero_words = $home->lines('hero_words');
                // The longest word is painted first (see home-01.js), so it is the largest paint.
                $hero_first = collect($hero_words)->sortByDesc(fn ($w) => mb_strlen($w))->first() ?? '';
            @endphp
            <span id="hero-rotating-word" data-words="{{ json_encode(array_values($hero_words), JSON_UNESCAPED_UNICODE) }}"
              style="color:#0b132a;font-weight:900;display:inline-block;margin-top:6px;transition:opacity 0.3s ease,transform 0.3s ease;text-shadow:0 0 2px #fff, 0 0 10px #ffffff, 0 0 24px #ffffff, 0 0 40px rgba(255,255,255,0.9), 0 4px 16px rgba(255,255,255,0.8);filter:drop-shadow(0 0 8px rgba(255,255,255,0.9));">{{ $hero_first }}</span>
          </h1>


          <!-- CTA Buttons -->
          <div class="hero-cta-group" style="display:flex;align-items:center;gap:14px;margin-top:44px;">
            <a href="{{ $home->url('hero_button_url') }}" class="hero-btn-primary"
              style="background:#ffffff;color:#0b132a;box-shadow:0 12px 30px rgba(0,0,0,0.2);border:none;font-weight:800;padding:12px 26px;text-decoration:none;">
              {{ $home->get('hero_button') }}
              <svg class="hero-btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0b132a"
                stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
            </a>
            <a href="{{ $home->url('hero_button2_url') }}" class="hero-btn-secondary"
              style="background:rgba(255,255,255,0.18);color:#ffffff;border:1.5px solid rgba(255,255,255,0.5);backdrop-filter:blur(10px);font-weight:700;box-shadow:0 4px 14px rgba(0,0,0,0.06);padding:12px 24px;">
              {{ $home->get('hero_button2') }}
            </a>
          </div>

          <!-- Simple Micro-trust Text -->
          <div style="display:flex;align-items:center;gap:14px;margin-top:14px;padding-left:2px;flex-wrap:wrap;">
            <span
              style="font-size:12.5px;font-weight:700;color:#ffffff;display:inline-flex;align-items:center;gap:5px;text-shadow:0 1px 4px rgba(0,0,0,0.15);">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12" />
              </svg>
              {{ $home->get('hero_note') }}
            </span>
          </div>

        </div>
      </div>

    </div><!-- /.g4d-hero -->

    <!-- Search & Live Demo Card (Highlighted Star Feature Container) -->
    <div id="search-section"
      style="max-width:1200px;margin:-50px auto 28px;padding:0 40px;position:relative;z-index:20">
      <div
        style="background:#ffffff;border-radius:20px;box-shadow:0 28px 64px -12px rgba(15, 23, 42, 0.28), 0 12px 28px -6px rgba(15, 23, 42, 0.16), 0 4px 12px rgba(0, 0, 0, 0.08);border:4px solid #ffffff">
        <!-- Row 1: Category Tabs -->
<div class="tabscroll-wrap" style="padding:0 26px;border-bottom:1px solid #f1f5f9">
          <div class="g4d-tabscroll"
            style="display:flex;align-items:center;gap:28px;overflow-x:auto;scrollbar-width:none">
            @foreach($home->pairs('search_tabs') as $tab_i => [$tab_name, $tab_titles])
            <div class="category-tab{{ $tab_i === 0 ? ' active' : '' }}" data-titles="{{ $tab_titles }}" onclick="switchSearchTab(this, null)">{{ $tab_name }}</div>
            @endforeach
          </div>
        </div>

        <!-- Row 2: Search Input Controls -->
        <div class="search-controls-row"
          style="display:flex;align-items:center;gap:12px;padding:18px 26px;flex-wrap:wrap">
          <input id="filter-title" placeholder="{{ $home->get('search_title_placeholder') }}"
            style="flex:1;min-width:120px;background:#fff;border:1.5px solid #d9e1ea;border-radius:10px;padding:11px 16px;font-size:14px;color:#0f172a;font-family:inherit;outline:none">
          <input id="filter-industry" placeholder="{{ $home->get('search_industry_placeholder') }}"
            style="flex:1.35;min-width:160px;background:#fff;border:1.5px solid #d9e1ea;border-radius:10px;padding:11px 16px;font-size:14px;color:#0f172a;font-family:inherit;outline:none">
          <input id="filter-location" placeholder="{{ $home->get('search_location_placeholder') }}"
            style="flex:1;min-width:120px;background:#fff;border:1.5px solid #d9e1ea;border-radius:10px;padding:11px 16px;font-size:14px;color:#0f172a;font-family:inherit;outline:none">

          <a id="download-leads-btn" href="{{ $home->url('search_button_url') }}" target="_blank" rel="noopener"
            style="cursor:pointer;background:#3b8e15;color:#fff;font-size:14px;font-weight:700;padding:11px 24px;border-radius:99px;display:flex;align-items:center;gap:8px;text-decoration:none;box-shadow:0 8px 20px rgba(59,142,21,0.3);flex-shrink:0">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 3v11m0 0l-4.2-4.2M12 14l4.2-4.2M4 19h16" stroke="currentColor" stroke-width="2.3"
                stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ $home->get('search_button') }}
          </a>
        </div>

        <!-- Results Table -->
        {{-- Styled here rather than in home.css: production's public_html/assets is a
             separate copy, so every stylesheet change would need copying by hand. --}}
        <style>
          #results-container { margin: 16px 28px 28px; border: 1px solid rgba(0,0,0,0.08); border-radius: 14px; overflow: hidden; background: #fff; }
          .g4d-leads-summary { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 16px; padding: 12px 20px; border-bottom: 1px solid #e2e8f0; font-size: 13.5px; color: #475569; }
          .g4d-leads-summary strong { color: #0f172a; font-weight: 700; }
          .g4d-leads-summary a { color: #3b8e15; font-weight: 700; text-decoration: none; white-space: nowrap; }
          .g4d-leads-summary a:hover { text-decoration: underline; }
          .g4d-leads-grid { display: grid; grid-template-columns: minmax(0,1.5fr) minmax(0,1.15fr) minmax(0,1.45fr) minmax(150px,1.1fr) 140px; column-gap: 16px; align-items: center; }
          .g4d-leads-head { padding: 13px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 12px; font-weight: 800; letter-spacing: 0.4px; color: #1e293b; }
          .g4d-lead { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; color: #333; }
          .g4d-lead:last-child { border-bottom: 0; }
          .g4d-lead-company { font-weight: 700; color: #1e293b; font-size: 14px; line-height: 1.35; }
          .g4d-lead-meta { margin-top: 3px; font-size: 11.5px; color: #64748b; }
          .g4d-lead-person { font-weight: 700; color: #1e293b; font-size: 14px; line-height: 1.35; }
          .g4d-lead-title { color: #475569; font-weight: 500; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
          .g4d-lead-btn { display: inline-flex; align-items: center; gap: 8px; border: 1px solid #cbd5e1; background: #fff; border-radius: 8px; padding: 7px 12px; font-family: inherit; font-size: 12.5px; font-weight: 600; color: #334155; cursor: pointer; white-space: nowrap; box-shadow: 0 1px 2px rgba(0,0,0,0.04); transition: border-color .15s, box-shadow .15s; }
          .g4d-lead-btn:hover { border-color: #3b8e15; box-shadow: 0 2px 8px rgba(59,142,21,0.15); }
          .g4d-lead-btn:focus-visible { outline: 2px solid #3b8e15; outline-offset: 2px; }
          .g4d-lead-status { width: 16px; height: 16px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
          .g4d-lead-status.is-yes { background: #dcfce7; color: #15803d; }
          .g4d-lead-status.is-no { background: #fee2e2; color: #dc2626; }
          .g4d-lead-value { font-size: 13px; font-weight: 600; color: #1e293b; overflow-wrap: anywhere; }
          .g4d-lead-limit { font-size: 13px; font-weight: 700; color: #3b8e15; }
          /* home.css pairs the buttons on one row below 768px; with only Download left,
             put it beside Location instead of alone at half width. */
          @media (max-width: 768px) {
            .search-controls-row #filter-location { flex: 1 1 calc(50% - 6px) !important; }
            .search-controls-row #download-leads-btn { flex: 1 1 calc(50% - 6px) !important; margin-top: 0 !important; }
          }
          @media (max-width: 900px) {
            .g4d-leads-head { display: none; }
            .g4d-lead.g4d-leads-grid { grid-template-columns: 1fr 1fr; column-gap: 10px; row-gap: 2px; padding: 14px 16px; }
            .g4d-lead-company-cell, .g4d-lead-person, .g4d-lead-title { grid-column: 1 / -1; }
            .g4d-lead-person { margin-top: 8px; font-size: 13.5px; }
            .g4d-lead-title { font-size: 13px; -webkit-line-clamp: 3; }
            .g4d-lead-email, .g4d-lead-phone { margin-top: 10px; }
            .g4d-lead-btn { width: 100%; justify-content: center; }
            .g4d-leads-summary { padding: 10px 16px; font-size: 13px; }
          }
        </style>
        <div id="results-container" class="hidden">
          <div id="leads-summary" class="g4d-leads-summary hidden"></div>
          <div class="g4d-leads-grid g4d-leads-head">
            <span>COMPANY</span>
            <span>PERSON NAME</span>
            <span>TITLE</span>
            <span>EMAIL</span>
            <span>PHONE</span>
          </div>
          <div id="leads-rows"></div>
        </div>
      </div>
    </div>

     <!-- Statistics Section (On White Page Background) -->
@if($home->on('stats'))
<div style="padding:20px 40px 0px;background:#ffffff;position:relative;z-index:10;">
      <div style="max-width:1200px;margin:0 auto;text-align:center;">
        <div
          style="font-size:11px;font-weight:800;color:#334155;letter-spacing:2px;margin-bottom:16px;text-transform:uppercase">
          {{ $home->get('stats_title') }}</div>
        <div
          style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:18px;text-align:center;">
          @foreach($home->pairs('stats') as [$stat_number, $stat_label])

          <div class="stat-card"
            style="padding:20px 20px;border-radius:16px;box-shadow:0 4px 14px rgba(0,0,0,0.03);cursor:default;">
            <div
              style="font-size:32px;font-weight:800;color:#0f172a;margin-bottom:4px;letter-spacing:-0.8px;line-height:1;">
              {{ $stat_number }}</div>
            <div style="font-size:11px;font-weight:800;color:#334155;text-transform:uppercase;letter-spacing:0.8px;">
              {{ $stat_label }}</div>
          </div>
          @endforeach

        </div>
      </div>
    </div>
@endif

    <!-- Trusted By Logos Marquee -->
    @if($home->on('logos'))
    <div style="margin-top:0px;padding:36px 0 24px;background:#ffffff;position:relative;overflow:hidden;width:100%;">
      <div style="position:relative;width:100%;text-align:center">
        <div
          style="font-size:11.5px;font-weight:800;color:#475569;letter-spacing:2.5px;margin-bottom:42px;text-transform:uppercase">
          {{ $home->get('logos_title') }}</div>
        <div class="g4d-marquee"
          style="overflow:hidden;-webkit-mask-image:linear-gradient(90deg,transparent,black 12%,black 88%,transparent);mask-image:linear-gradient(90deg,transparent,black 12%,black 88%,transparent);width:100%;">
          <div class="g4d-marquee-track" style="display:flex;align-items:center;gap:90px;width:max-content">
@php $own_logos = $home->logos('logos'); @endphp
@if(count($own_logos))
            {{-- The admin's logos, four times over for the seamless loop. --}}
            @for($set = 0; $set < 4; $set++)
              @foreach($own_logos as [$logo_name, $logo_image])
                @if($logo_image)
            <img class="marquee-logo" src="{{ $logo_image }}" alt="{{ $logo_name }}" @if($set > 0) aria-hidden="true" @endif />
                @else
            <span class="marquee-text-logo" @if($set > 0) aria-hidden="true" @endif
              style="font-family:'Inter',sans-serif;font-size:17px;font-weight:700;color:#111;white-space:nowrap;">{{ $logo_name }}</span>
                @endif
              @endforeach
            @endfor
@else
<!-- Set 1 -->
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:17px;font-weight:700;color:#111;display:flex;align-items:center;gap:5px;white-space:nowrap;"><span
                style="color:#e53e3e;font-size:13px;">●</span>TECAN<span style="color:#e53e3e;">.</span></span>
            <span class="marquee-text-logo"
              style="font-family:'Palatino Linotype',Georgia,serif;font-size:15px;font-weight:600;letter-spacing:3px;color:#1a1a2e;text-transform:uppercase;white-space:nowrap;">Westin</span>
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:18px;font-weight:900;color:#003087;letter-spacing:1px;white-space:nowrap;">CBRE</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Google_2015_logo.svg') }}"
              alt="Google" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Amazon_logo.svg') }}"
              alt="Amazon" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Dropbox_logo_2017.svg') }}"
              alt="Dropbox" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Shopify_logo_2018.svg') }}"
              alt="Shopify" />
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:15px;font-weight:600;letter-spacing:2.5px;color:#222;text-transform:uppercase;white-space:nowrap;">CANDELA</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Salesforce.com_logo.svg') }}"
              alt="Salesforce" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Zendesk_logo.svg') }}"
              alt="Zendesk" />
            <span class="marquee-text-logo"
              style="font-family:'Didot','Palatino Linotype',Georgia,serif;font-size:16px;font-weight:700;color:#111;letter-spacing:1.5px;white-space:nowrap;">J.POCKER</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/HubSpot_Logo.svg') }}"
              alt="HubSpot" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Microsoft_logo.svg') }}"
              alt="Microsoft" />
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:18px;font-weight:800;color:#0a0a23;display:flex;align-items:center;gap:2px;white-space:nowrap;"><span
                style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border:2.5px solid #0a0a23;border-radius:50%;font-size:13px;font-weight:900;">Q</span>MiQ</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Linkedin_icon.svg') }}"
              alt="LinkedIn" style="height:22px;" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Slack_icon_2019.svg') }}"
              alt="Slack" style="height:28px;" />

            <!-- Set 2 (duplicate for seamless loop) -->
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:17px;font-weight:700;color:#111;display:flex;align-items:center;gap:5px;white-space:nowrap;"><span
                style="color:#e53e3e;font-size:13px;">●</span>TECAN<span style="color:#e53e3e;">.</span></span>
            <span class="marquee-text-logo"
              style="font-family:'Palatino Linotype',Georgia,serif;font-size:15px;font-weight:600;letter-spacing:3px;color:#1a1a2e;text-transform:uppercase;white-space:nowrap;">Westin</span>
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:18px;font-weight:900;color:#003087;letter-spacing:1px;white-space:nowrap;">CBRE</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Google_2015_logo.svg') }}"
              alt="Google" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Amazon_logo.svg') }}"
              alt="Amazon" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Dropbox_logo_2017.svg') }}"
              alt="Dropbox" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Shopify_logo_2018.svg') }}"
              alt="Shopify" />
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:15px;font-weight:600;letter-spacing:2.5px;color:#222;text-transform:uppercase;white-space:nowrap;">CANDELA</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Salesforce.com_logo.svg') }}"
              alt="Salesforce" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Zendesk_logo.svg') }}"
              alt="Zendesk" />
            <span class="marquee-text-logo"
              style="font-family:'Didot','Palatino Linotype',Georgia,serif;font-size:16px;font-weight:700;color:#111;letter-spacing:1.5px;white-space:nowrap;">J.POCKER</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/HubSpot_Logo.svg') }}"
              alt="HubSpot" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Microsoft_logo.svg') }}"
              alt="Microsoft" />
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:18px;font-weight:800;color:#0a0a23;display:flex;align-items:center;gap:2px;white-space:nowrap;"><span
                style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border:2.5px solid #0a0a23;border-radius:50%;font-size:13px;font-weight:900;">Q</span>MiQ</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Linkedin_icon.svg') }}"
              alt="LinkedIn" style="height:22px;" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Slack_icon_2019.svg') }}"
              alt="Slack" style="height:28px;" />

            <!-- Set 3 -->
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:17px;font-weight:700;color:#111;display:flex;align-items:center;gap:5px;white-space:nowrap;"><span
                style="color:#e53e3e;font-size:13px;">●</span>TECAN<span style="color:#e53e3e;">.</span></span>
            <span class="marquee-text-logo"
              style="font-family:'Palatino Linotype',Georgia,serif;font-size:15px;font-weight:600;letter-spacing:3px;color:#1a1a2e;text-transform:uppercase;white-space:nowrap;">Westin</span>
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:18px;font-weight:900;color:#003087;letter-spacing:1px;white-space:nowrap;">CBRE</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Google_2015_logo.svg') }}"
              alt="Google" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Amazon_logo.svg') }}"
              alt="Amazon" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Dropbox_logo_2017.svg') }}"
              alt="Dropbox" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Shopify_logo_2018.svg') }}"
              alt="Shopify" />
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:15px;font-weight:600;letter-spacing:2.5px;color:#222;text-transform:uppercase;white-space:nowrap;">CANDELA</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Salesforce.com_logo.svg') }}"
              alt="Salesforce" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Zendesk_logo.svg') }}"
              alt="Zendesk" />
            <span class="marquee-text-logo"
              style="font-family:'Didot','Palatino Linotype',Georgia,serif;font-size:16px;font-weight:700;color:#111;letter-spacing:1.5px;white-space:nowrap;">J.POCKER</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/HubSpot_Logo.svg') }}"
              alt="HubSpot" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Microsoft_logo.svg') }}"
              alt="Microsoft" />
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:18px;font-weight:800;color:#0a0a23;display:flex;align-items:center;gap:2px;white-space:nowrap;"><span
                style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border:2.5px solid #0a0a23;border-radius:50%;font-size:13px;font-weight:900;">Q</span>MiQ</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Linkedin_icon.svg') }}"
              alt="LinkedIn" style="height:22px;" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Slack_icon_2019.svg') }}"
              alt="Slack" style="height:28px;" />

            <!-- Set 4 -->
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:17px;font-weight:700;color:#111;display:flex;align-items:center;gap:5px;white-space:nowrap;"><span
                style="color:#e53e3e;font-size:13px;">●</span>TECAN<span style="color:#e53e3e;">.</span></span>
            <span class="marquee-text-logo"
              style="font-family:'Palatino Linotype',Georgia,serif;font-size:15px;font-weight:600;letter-spacing:3px;color:#1a1a2e;text-transform:uppercase;white-space:nowrap;">Westin</span>
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:18px;font-weight:900;color:#003087;letter-spacing:1px;white-space:nowrap;">CBRE</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Google_2015_logo.svg') }}"
              alt="Google" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Amazon_logo.svg') }}"
              alt="Amazon" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Dropbox_logo_2017.svg') }}"
              alt="Dropbox" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Shopify_logo_2018.svg') }}"
              alt="Shopify" />
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:15px;font-weight:600;letter-spacing:2.5px;color:#222;text-transform:uppercase;white-space:nowrap;">CANDELA</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Salesforce.com_logo.svg') }}"
              alt="Salesforce" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Zendesk_logo.svg') }}"
              alt="Zendesk" />
            <span class="marquee-text-logo"
              style="font-family:'Didot','Palatino Linotype',Georgia,serif;font-size:16px;font-weight:700;color:#111;letter-spacing:1.5px;white-space:nowrap;">J.POCKER</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/HubSpot_Logo.svg') }}"
              alt="HubSpot" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Microsoft_logo.svg') }}"
              alt="Microsoft" />
            <span class="marquee-text-logo"
              style="font-family:'Inter',sans-serif;font-size:18px;font-weight:800;color:#0a0a23;display:flex;align-items:center;gap:2px;white-space:nowrap;"><span
                style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border:2.5px solid #0a0a23;border-radius:50%;font-size:13px;font-weight:900;">Q</span>MiQ</span>
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Linkedin_icon.svg') }}"
              alt="LinkedIn" style="height:22px;" />
            <img class="marquee-logo" src="{{ asset('assets/frontend/images/logos/Slack_icon_2019.svg') }}"
              alt="Slack" style="height:28px;" />
@endif
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- 1200 Free Credits Promo Section (Animated Light Green Glass Card) -->
    @if($home->on('promo'))
    <div class="promo-wrapper" style="margin-top:40px;padding:40px 40px 16px;background:#ffffff;">
      <div class="promo-inner-card"
        style="max-width:1200px;margin:0 auto;background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 45%, #ffffff 100%);border:1px solid #e2e8f0;border-radius:28px;padding:48px 56px;position:relative;overflow:hidden;box-shadow:0 20px 50px -16px rgba(34, 197, 94, 0.18);">

        <!-- Ambient decorative radial lighting -->
        <div
          style="position:absolute;top:-80px;right:-80px;width:360px;height:360px;background:radial-gradient(circle, rgba(74, 222, 128, 0.3) 0%, transparent 70%);pointer-events:none;">
        </div>
        <div
          style="position:absolute;bottom:-60px;left:-60px;width:280px;height:280px;background:radial-gradient(circle, rgba(187, 247, 208, 0.4) 0%, transparent 70%);pointer-events:none;">
        </div>

        <div class="promo-grid-container"
          style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:44px;align-items:center;position:relative;z-index:2;">

          <!-- Left: Offer Details & CTA -->
          <div>
            <!-- Animated Badge -->
<div class="offer-badge">
              <span class="offer-dot"></span>
              {{ $home->get('promo_badge') }}
            </div>

            <h2
              style="font-size:38px;font-weight:800;color:#0f172a;letter-spacing:-1.4px;line-height:1.15;margin-bottom:16px;">
              {!! $home->marked('promo_title', 'color:#15803d;') !!}
            </h2>

            <p style="font-size:16px;color:#475569;line-height:1.65;margin-bottom:28px;font-weight:500;">
              {{ $home->get('promo_text') }}
            </p>

            <div class="promo-check-grid"
              style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:32px;">
              @foreach($home->lines('promo_points') as $promo_point)
              <div style="display:flex;align-items:center;gap:10px;font-size:14px;font-weight:700;color:#0f172a;">
                <span
                  style="width:22px;height:22px;border-radius:50%;background:#dcfce7;color:#15803d;border:1px solid #86efac;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:900;">✓</span>
                {{ $promo_point }}
              </div>
              @endforeach
            </div>

            <div>
              <a href="{{ $home->url('promo_button_url') }}" class="hero-btn-primary"
                style="background:#6fd943;color:#0b132a;border:none;padding:15px 34px;font-size:15px;box-shadow:0 10px 28px rgba(111,217,67,0.4);font-weight:800;">
                {{ $home->get('promo_button') }}
                <svg class="hero-btn-arrow" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M5 12h14"></path>
                  <path d="m12 5 7 7-7 7"></path>
                </svg>
              </a>
            </div>
          </div>

          <!-- Right: Seamless Floating Portrait -->
          <div
            style="position:relative;display:flex;justify-content:center;align-items:flex-end;background:transparent;overflow:hidden;"
            class="promo-img-container">
            @php $promo_image = $home->image('promo_image'); @endphp
            @if($promo_image)
            <img loading="lazy" decoding="async" src="{{ $promo_image['img_url'] }}" alt="{{ $promo_image['img_alt'] ?? '' }}" style="width:100%;max-width:440px;height:auto;display:block;object-fit:contain;mix-blend-mode:multiply;">
            @else
            <img loading="lazy" decoding="async" src="{{ asset('assets/frontend/images/free-credits-promo.webp') }}"
              srcset="{{ asset('assets/frontend/images/free-credits-promo-440.webp') }} 440w, {{ asset('assets/frontend/images/free-credits-promo.webp') }} 880w"
              sizes="440px"
              alt="1200 Free Credits Offer" width="880" height="880" loading="lazy" style="width:100%;max-width:440px;height:auto;display:block;object-fit:contain;
                mix-blend-mode:multiply;
                -webkit-mask-image: radial-gradient(ellipse 88% 92% at 50% 55%, black 40%, transparent 75%);
                mask-image: radial-gradient(ellipse 88% 92% at 50% 55%, black 40%, transparent 75%);
                filter: contrast(1.02);">
            @endif
          </div>

        </div>

      </div>
    </div>

    @endif
    <!-- AI Verified Leads Section -->
    @if($home->on('ai'))
    <section id="ai-verified-leads" style="padding:80px 40px;background:#ffffff;border-top:1px solid #f1f5f9;">
      <div style="max-width:1240px;margin:0 auto;">

        <!-- Section Header -->
        <div class="leads-header" style="text-align:center;max-width:900px;margin:0 auto 48px;position:relative;">


          <!-- Bold, Balanced & Gradient Headline -->
          <h2 class="leads-h2"
            style="font-size:50px;font-weight:900;letter-spacing:-2px;color:#0f172a;line-height:1.15;margin:0 0 16px 0;font-family:'Plus Jakarta Sans','Inter',sans-serif;">
            {!! $home->marked('ai_title', 'background:linear-gradient(135deg, #15803d 0%, #16a34a 50%, #6fd943 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:inline-block;') !!}
          </h2>

          <!-- Clean Descriptive Subtitle -->
          <p style="font-size:17.5px;color:#64748b;line-height:1.65;font-weight:500;margin:0 auto;max-width:640px;">
            {{ $home->get('ai_text') }}
          </p>

        </div>


        <!-- 3-Card Tab Selector Strip (Matching Pastel Palette) -->
        <div class="ai-leads-grid">

          @php
              // Card colours, pink, lavender and blue, as built (a fourth card starts over).
              $ai_looks = [
                  ['#fce7f3', 'rgba(236,72,153,0.25)', 'rgba(236,72,153,0.08)', '#db2777'],
                  ['#ede9fe', 'rgba(139,92,246,0.25)', 'rgba(139,92,246,0.08)', '#7c3aed'],
                  ['#e0f2fe', 'rgba(14,165,233,0.25)', 'rgba(14,165,233,0.08)', '#0284c7'],
              ];
          @endphp
          @foreach($home->pairs('ai_cards') as $i => [$ai_label, $ai_word])
          @php [$ai_bg, $ai_border, $ai_shadow, $ai_label_color] = $ai_looks[$i % 3]; @endphp
          <div class="ai-lead-card"
            style="background:{{ $ai_bg }};border:1.5px solid {{ $ai_border }};box-shadow:0 6px 20px {{ $ai_shadow }};">
            <div class="card-icon-box" style="border:2px solid #0b132a;background:#fff;">
              <div class="dot" style="background:#0b132a;"></div>
              <span class="letter" style="color:#6fd943;">{{ mb_strtolower(mb_substr($ai_word, 0, 1)) }}</span>
            </div>
            <div class="card-text">
              <div class="card-label" style="color:{{ $ai_label_color }};font-weight:700;">{{ $ai_label }}</div>
              <div class="card-title">{{ $ai_word }}</div>
            </div>
          </div>
          @endforeach

        </div>
      </div>
    </section>
    @endif

    <!-- 21st.dev Feature Section with Hover Effects (by Manu Arora @manuarora700) -->
    @if($home->on('features'))
    <div id="features" class="g4d-section-texture"
      style="padding:72px 40px;background:#f8faf9;border-top:1px solid #f1f5f9">
      <div style="max-width:1240px;margin:0 auto">

        <!-- Header -->
        <div style="text-align:center;max-width:720px;margin:0 auto 52px">
          <h2 style="font-size:44px;font-weight:800;letter-spacing:-1.5px;color:#0f172a;line-height:1.12">
            {{ $home->get('features_title') }}</h2>
          <div style="font-size:17px;color:#64748b;margin-top:16px;line-height:1.7;font-weight:500">{{ $home->get('features_text') }}</div>
        </div>

        <!-- 8-Card Grid with Hover Indicator Bars & Border Matrix (Manu Arora Pattern) -->
        <div class="manua-feature-grid">
          @php
              // The eight icons, in order (a ninth card starts over).
              $feature_icons = [
                <<<'SVG'
            <div class="manua-feature-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
            </div>
SVG,
                <<<'SVG'
            <div class="manua-feature-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2.2" />
                <circle cx="12" cy="12" r="3" fill="currentColor" />
                <path d="M12 3v3M12 18v3M3 12h3M18 12h3" stroke="currentColor" stroke-width="2.2"
                  stroke-linecap="round" />
              </svg>
            </div>
SVG,
                <<<'SVG'
            <div class="manua-feature-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2.2"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
SVG,
                <<<'SVG'
            <div class="manua-feature-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M23 6l-9.5 9.5-5-5L1 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                  stroke-linejoin="round" />
                <path d="M17 6h6v6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
            </div>
SVG,
                <<<'SVG'
            <div class="manua-feature-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="12" rx="2" stroke="currentColor" stroke-width="2.2" />
                <path d="M2 20h20M9 16v4M15 16v4" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
              </svg>
            </div>
SVG,
                <<<'SVG'
            <div class="manua-feature-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M23 4v6h-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                  stroke-linejoin="round" />
                <path d="M1 20v-6h6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                  stroke-linejoin="round" />
                <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15" stroke="currentColor"
                  stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
SVG,
                <<<'SVG'
            <div class="manua-feature-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                  stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
            </div>
SVG,
                <<<'SVG'
            <div class="manua-feature-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path
                  d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"
                  stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
SVG
              ];
          @endphp
          @foreach($home->pairs('features') as $i => [$feature_title, $feature_text])

          <div class="manua-feature-item">
            <div class="manua-feature-bar"></div>
            {!! $feature_icons[$i % 8] !!}
            <div class="manua-feature-title">{{ $feature_title }}</div>
            <div class="manua-feature-desc">{{ $feature_text }}</div>
          </div>
          @endforeach

        </div>

      </div>
    </div>

    @endif

    <!-- 10. Direct Comparison Section (With Go4Database vs Others) -->
    @if($home->on('compare'))
    <div id="comparison" style="padding:72px 40px;background:#ffffff">
      <div style="max-width:1200px;margin:0 auto">
        <div style="text-align:center;max-width:700px;margin:0 auto 52px">
          <h2 style="font-size:40px;font-weight:800;letter-spacing:-1.5px;color:#0f172a;line-height:1.12">{{ $home->get('compare_title') }}</h2>
          <div style="font-size:17px;color:#64748b;margin-top:14px;line-height:1.7;font-weight:500">{{ $home->get('compare_text') }}</div>
        </div>

        <div class="comparison-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:28px">
          <div
            style="background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 45%, #bbf7d0 100%);border:1.5px solid rgba(34, 197, 94, 0.35);border-radius:20px;padding:36px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
              <h3 style="font-size:22px;font-weight:800;color:#1f7a2e">{{ $home->get('compare_us_title') }}</h3>
              <span
                style="background:#1f7a2e;color:#fff;font-size:11px;font-weight:800;padding:4px 10px;border-radius:999px">{{ $home->get('compare_us_badge') }}</span>
            </div>

            <div style="display:flex;flex-direction:column;gap:18px">
              @foreach($home->lines('compare_us') as $point)
              <div style="display:flex;gap:12px;font-size:15.5px;color:#111;font-weight:600">
                <span style="color:#1f7a2e;font-size:18px;font-weight:800">✓</span>
                <span>{{ $point }}</span>
              </div>
              @endforeach
            </div>
          </div>

          <div style="background:#fafafa;border:1px solid rgba(0,0,0,0.1);border-radius:20px;padding:36px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
              <h3 style="font-size:22px;font-weight:800;color:#777">{{ $home->get('compare_them_title') }}</h3>
              <span
                style="background:#e5e7eb;color:#666;font-size:11px;font-weight:700;padding:4px 10px;border-radius:999px">{{ $home->get('compare_them_badge') }}</span>
            </div>

            <div style="display:flex;flex-direction:column;gap:18px">
              @foreach($home->lines('compare_them') as $point)
              <div style="display:flex;gap:12px;font-size:15.5px;color:#666">
                <span style="color:#dc2626;font-size:18px;font-weight:800">✕</span>
                <span>{{ $point }}</span>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    @endif

    <!-- Integrations Matrix Map -->
    @if($home->on('integrations'))
    <div id="integrations" class="g4d-section-texture" style="padding:72px 40px 48px;background:#f8faf9">
      <div style="max-width:1200px;margin:0 auto">
        <div style="text-align:center;max-width:720px;margin:0 auto 52px">
          <div
            style="display:inline-flex;align-items:center;gap:6px;background:#e8f7ea;color:#1f7a2e;font-size:12.5px;font-weight:700;padding:8px 16px;border-radius:999px;margin-bottom:18px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
              <path d="M12 2l9 4.9v10.2L12 22l-9-4.9V6.9L12 2z" stroke="#1f7a2e" stroke-width="2.2"
                stroke-linejoin="round" />
            </svg>
            {{ $home->get('integ_badge') }}
          </div>
          <h2 style="font-size:44px;font-weight:800;letter-spacing:-1.5px;color:#0f172a;line-height:1.15">{{ $home->get('integ_title') }}</h2>
          <div style="font-size:18px;color:#64748b;line-height:1.7;margin-top:14px;font-weight:500">{{ $home->get('integ_text') }}</div>
        </div>

        <!-- 21st.dev CSS Card Stacking Container -->
        <div class="stacking-cards-container">

          @php
              // The three cards' colours, icons and built-in screenshots, as built.
              $integ_cards = [
                  1 => ['linear-gradient(135deg, #fff1f2 0%, #ffe4e6 50%, #fecdd3 100%);border:1.5px solid rgba(244, 63, 94, 0.25)', '#ffe4e6', '#e11d48', '⚡',
                        'assets/frontend/images/b2b-marketplace-table.webp', 'Go4Database B2B leads table with company, contact name, title and verified email', 1140, 877],
                  2 => ['linear-gradient(135deg, #faf5ff 0%, #f3e8ff 50%, #e9d5ff 100%);border:1.5px solid rgba(168, 85, 247, 0.25)', '#f3e8ff', '#7c3aed', '🔍',
                        'assets/frontend/images/advanced-filters-form.webp', 'Go4Database advanced filters for company, industry, technology, turnover, title and seniority', 1140, 821],
                  3 => ['linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #bae6fd 100%);border:1.5px solid rgba(56, 189, 248, 0.3)', '#e0f2fe', '#0284c7', '✨',
                        'assets/frontend/images/ai-lead-finder.webp', 'AI Suggestions & Lead Finder Platform Screenshot', 1024, 631],
              ];
          @endphp
          @foreach($integ_cards as $n => [$card_bg, $badge_bg, $badge_color, $note_icon, $shot, $shot_alt, $shot_w, $shot_h])
          @php $own_shot = $home->image("card{$n}_image"); @endphp
          <div class="clay-card-stack"
            style="background:{{ $card_bg }}">
            <div>
              <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;margin-top:-6px;flex-wrap:wrap">
                <img loading="lazy" decoding="async" src="{{ asset('assets/frontend/images/logo.png') }}" alt="Go4Database Logo"
                  style="height:28px;width:auto;display:block;transform:translateY(-1px)">
                <div
                  style="display:inline-block;background:{{ $badge_bg }};color:{{ $badge_color }};font-size:11.5px;font-weight:800;letter-spacing:1px;padding:6px 14px;border-radius:999px">
                  {{ $home->get("card{$n}_label") }}</div>
              </div>
              <h3
                style="font-size:34px;font-weight:800;letter-spacing:-1px;color:#111;line-height:1.2;margin-bottom:16px">
                {{ $home->get("card{$n}_title") }}</h3>
              <div style="font-size:16px;color:#555;line-height:1.65;margin-bottom:24px">{{ $home->get("card{$n}_text") }}</div>

              <div
                style="background:#fff;border:1px solid rgba(0,0,0,0.06);border-radius:12px;padding:14px 18px;display:flex;align-items:center;gap:12px;margin-bottom:28px">
                <span style="font-size:18px">{{ $note_icon }}</span>
                <span style="font-size:14px;font-weight:600;color:#222">{{ $home->get("card{$n}_note") }}</span>
              </div>

              <div class="card-btn-group" style="display:flex;align-items:center;gap:14px">
                <a href="{{ $home->url("card{$n}_button_url") }}"
                  style="background:#6fd943;color:#0b132a;font-size:14.5px;font-weight:800;padding:12px 24px;border-radius:99px;box-shadow:0 6px 16px rgba(111,217,67,0.35);text-decoration:none;">{{ $home->get("card{$n}_button") }}</a>
                <a href="{{ $home->url("card{$n}_button2_url") }}"
                  style="background:#fff;border:1px solid rgba(0,0,0,0.15);color:#333;font-size:14.5px;font-weight:600;padding:12px 24px;border-radius:99px">{{ $home->get("card{$n}_button2") }}</a>
              </div>
            </div>

            <div
              style="background:#fff;border-radius:18px;padding:10px;box-shadow:0 20px 50px -15px rgba(0,0,0,0.12);border:1px solid rgba(0,0,0,0.08);overflow:hidden">
              @if($own_shot)
              <img loading="lazy" decoding="async" src="{{ $own_shot['img_url'] }}" alt="{{ $own_shot['img_alt'] ?? '' }}"
                style="width:100%;height:auto;border-radius:12px;display:block;object-fit:cover">
              @else
              <img loading="lazy" decoding="async" src="{{ asset($shot) }}" alt="{{ $shot_alt }}"
                width="{{ $shot_w }}" height="{{ $shot_h }}"
                style="width:100%;height:auto;border-radius:12px;display:block;object-fit:cover">
              @endif
            </div>
          </div>
          @endforeach

        </div>
      </div>
    </div>

    @endif
@php
    $testimonials_for_js = $all_testimonial->take(3)->values();
    $first_testimonial = $testimonials_for_js->first();
@endphp

<!-- 21st.dev Simple Animated Testimonials Component (by Bankkroll) -->
@if($home->on('reviews'))
<div id="case-studies"
  style="padding:48px 40px 60px;background:#f8fafc;border-top:1px solid #f1f5f9;overflow:hidden">
  <div style="max-width:1200px;margin:0 auto">

    <!-- Header -->
    <div style="text-align:center;max-width:720px;margin:0 auto 52px">

      <div style="font-size:44px;font-weight:800;letter-spacing:-1.5px;color:#0f172a;line-height:1.12">{{ $home->get('reviews_title') }}</div>
      <div style="font-size:17px;color:#64748b;margin-top:14px;line-height:1.7;font-weight:500">{{ $home->get('reviews_text') }}</div>
    </div>

    @if($testimonials_for_js->isNotEmpty())
    <!-- Animated Split Component Grid -->
    <div class="testimonial-split-grid"
      style="display:grid;grid-template-columns:1fr 1.25fr;gap:64px;align-items:center;background:#fff;border:1px solid #e2e8f0;border-radius:28px;padding:56px 60px;box-shadow:0 25px 60px -20px rgba(0,0,0,0.06)">

      <!-- Left: 3D Stacked Image/Avatar Showcase -->
      <div style="position:relative">
        <div id="anim-avatar-container"
          style="position:relative;width:100%;height:340px;display:flex;align-items:center;justify-content:center">
          @foreach($testimonials_for_js as $data)
          @php
              // Alternate the card styling to match the original 3-card design
              // (green-tinted, dark/neutral-tinted, green-tinted...).
              $is_alt = $loop->index === 1;
              $card_bg = $is_alt ? 'linear-gradient(135deg,#ffffff,#f8fafc)' : 'linear-gradient(135deg,#ffffff,#f0fdf4)';
              $card_border = $is_alt ? '#cbd5e1' : '#6fd943';
              $card_shadow = $is_alt ? '0 14px 35px rgba(0,0,0,0.08)' : '0 14px 35px rgba(111,217,67,0.2)';
              $avatar_bg = $is_alt ? '#0b132a' : '#6fd943';
              $avatar_color = $is_alt ? '#6fd943' : '#0b132a';
              $avatar_shadow = $is_alt ? '0 8px 20px rgba(0,0,0,0.15)' : '0 8px 20px rgba(111,217,67,0.3)';
              $initials = strtoupper(substr($data->name, 0, 1)) . strtoupper(substr(strrchr($data->name, ' ') ?: $data->name, -1));
          @endphp
          <!-- Animated Avatar Card {{ $loop->index }} -->
          <div class="anim-avatar-card" id="anim-card-{{ $loop->index }}"
            style="background:{{ $card_bg }};color:#0b132a;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:32px;text-align:center;border:1.5px solid {{ $card_border }};box-shadow:{{ $card_shadow }}">
            <div
              style="width:90px;height:90px;border-radius:50%;background:{{ $avatar_bg }};color:{{ $avatar_color }};font-size:32px;font-weight:900;display:flex;align-items:center;justify-content:center;margin-bottom:20px;box-shadow:{{ $avatar_shadow }}">
              {{ $initials }}
            </div>
            <div style="font-size:22px;font-weight:800;color:#0b132a">{{ $data->name }}</div>
            <div style="font-size:14px;color:#475569;margin-top:4px">{{ $data->designation }}</div>
            <div
              style="margin-top:18px;background:#e8f7ea;padding:6px 16px;border-radius:999px;font-size:12.5px;font-weight:800;color:#1f7a2e;border:1px solid #bbf7d0">
              {{ $data->metric ?? 'Verified Customer' }}
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <!-- Right: Quote Content & Navigation Buttons -->
      <div>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
          <span id="anim-rating-text" style="color:#10b981;font-size:18px">{{ str_repeat('★', $first_testimonial->rating ?? 5) }}{{ str_repeat('☆', 5 - ($first_testimonial->rating ?? 5)) }}</span>
          <span id="anim-metric-badge"
            style="background:#e8f7ea;color:#1f7a2e;font-size:12.5px;font-weight:800;padding:4px 14px;border-radius:999px;border:1px solid rgba(31,122,46,0.18)">{{ $first_testimonial->metric ?? '' }}</span>
        </div>

        <!-- Quote Text with Fade Transition -->
        <div id="anim-quote-text"
          style="font-size:22px;font-weight:600;color:#0f172a;line-height:1.55;letter-spacing:-0.4px;min-height:120px;margin-bottom:32px;transition:opacity 0.35s ease, transform 0.35s ease">
          "{{ $first_testimonial->description ?? '' }}"
        </div>

        <!-- Author & Controls Row -->
        <div
          style="display:flex;align-items:center;justify-content:space-between;gap:20px;padding-top:24px;border-top:1px solid #f1f5f9">
          <div>
            <div id="anim-author-name" style="font-size:19px;font-weight:800;color:#0f172a">{{ $first_testimonial->name ?? '' }}</div>
            <div id="anim-author-role" style="font-size:14px;color:#64748b;margin-top:2px">{{ $first_testimonial->designation ?? '' }}</div>
          </div>

          <!-- Navigation Arrow Buttons (Bankkroll pattern) -->
          <div style="display:flex;align-items:center;gap:12px">
            <button class="anim-nav-btn" id="anim-prev-btn" aria-label="Previous Testimonial">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
            </button>
            <button class="anim-nav-btn" id="anim-next-btn" aria-label="Next Testimonial">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
            </button>
          </div>
        </div>

      </div>

    </div>

    {{-- Feed the real testimonial data to home-01.js so the JS carousel matches these cards --}}
    @php
        $testimonials_json = $testimonials_for_js->map(function ($t) {
            return [
                'quote'  => $t->description ?? '',
                'name'   => $t->name ?? '',
                'role'   => $t->designation ?? '',
                'metric' => $t->metric ?? 'Verified Customer',
                'rating' => str_repeat('★', $t->rating ?? 5) . str_repeat('☆', 5 - ($t->rating ?? 5)),
            ];
        });
    @endphp
    <script>
      window.__TESTIMONIALS__ = @json($testimonials_json);
    </script>
    @endif

  </div>
</div>

@endif

    <!-- ── Favorite List: Most Active Users Mailing List Section ── -->
    @if($home->on('lists'))
    <section id="favorite-lists"
      style="background:#f8fafc;padding:80px 24px;position:relative;overflow:hidden;border-top:1px solid #e2e8f0">
      <!-- Background subtle decorative green ambient light -->
      <div
        style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:800px;height:350px;background:radial-gradient(circle, rgba(34,197,94,0.07) 0%, transparent 70%);pointer-events:none">
      </div>
<div class="fav-section-wrapper">
        <div style="text-align:center">

          <h2 class="fav-title">{{ $home->get('lists_title') }}</h2>
          <p class="fav-subtitle">{{ $home->get('lists_text') }}</p>
        </div>

        <!-- Modern Pill Category Tabs -->
        <div class="fav-tabs-pill-wrap">
          <div class="fav-tabs-nav" id="favTabs">
            <div  class="fav-tab-btn active" onclick="filterFavCards('all', this)">{{ $home->get('lists_all_tab') }}</div>
             @foreach($all_work_category as $data)
            <div class="fav-tab-btn" onclick="filterFavCards('{{Str::slug($data->name)}}', this)">{{$data->name}}</div>
            @endforeach
            </div>
        </div>

        <!-- 3 Text Cards Grid -->
        <div class="fav-cards-grid" id="favCardsGrid">
 @php
    $cardStyles = [
        [
            'background' => 'linear-gradient(135deg,#fff0f3,#fce4ec)',
            'border' => '#f8bbd0',
            'shadow' => 'rgba(244,114,182,0.12)',
        ],
        [
            'background' => 'linear-gradient(135deg,#f3e8ff,#ede9fe)',
            'border' => '#d8b4fe',
            'shadow' => 'rgba(168,85,247,0.12)',
        ],
        [
            'background' => 'linear-gradient(135deg,#e0f2fe,#e0f7fa)',
            'border' => '#bae6fd',
            'shadow' => 'rgba(14,165,233,0.12)',
        ],
    ];
@endphp

@foreach($all_work as $data)

    @php
        $style = $cardStyles[$loop->index % 3];
    @endphp

    <a href="{{route('frontend.work.single',$data->slug)}}"
       class="fav-banner-card"
       data-category="{{ Str::slug(trim(get_work_category_by_id($data->id, 'slug'))) }}"
       style="
           text-decoration:none;
           display:flex;
           flex-direction:column;
           background:{{ $style['background'] }};
           border:1.5px solid {{ $style['border'] }};
           border-radius:24px;
           padding:32px 28px;
           gap:14px;
           transition:all 0.3s ease;
           box-shadow:0 8px 24px {{ $style['shadow'] }};
       ">

        <div style="display:flex;align-items:center;justify-content:space-between;">

            <span style="
                background:#fff;
                color:#db2777;
                font-size:11.5px;
                font-weight:800;
                padding:5px 13px;
                border-radius:99px;
                letter-spacing:0.3px;
                box-shadow:0 2px 6px rgba(219,39,119,0.1);
            ">
                {{ get_work_category_by_id($data->id, 'string') }}
            </span>

            <span style="
                width:38px;
                height:38px;
                background:#6fd943;
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                box-shadow:0 4px 10px rgba(111,217,67,0.3);
            ">
                <svg width="18" height="18" viewBox="0 0 24 24"
                     fill="none"
                     stroke="#0b132a"
                     stroke-width="2.5"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </span>

        </div>

        <h3 style="
            font-size:24px;
            font-weight:900;
            color:#0b132a;
            line-height:1.2;
        ">
            {{ $data->title }}
</h3>

        <div style="
            font-size:13.5px;
            color:#64748b;
            line-height:1.65;
            font-weight:500;
        ">
            {{ \Illuminate\Support\Str::words(strip_tags($data->description), 20, '...') }}
        </div>

        <div style="
            display:flex;
            align-items:center;
            gap:6px;
            color:#1f7a2e;
            font-size:13px;
            font-weight:800;
            margin-top:4px;
        ">
            Explore Lists

            <svg width="14" height="14" viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2.5">
                <path d="M5 12h14M12 5l7 7-7 7" />
            </svg>
        </div>

    </a>

@endforeach
        
        </div>

        <!-- Center CTA Button -->
        <div style="display:flex;justify-content:center;margin-top:44px">
          <a href="{{ route('frontend.work') }}" class="fav-cta-btn">
            <span>{{ $home->get('lists_button') }}</span>
            <span class="fav-cta-icon-box">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7" />
              </svg>
            </span>
          </a>
        </div>
      </div>
</section>
    @endif
@if(!empty(filter_static_option_value('home_page_latest_news_section_status',$static_field_data)))

    <!-- ── Professional B2B Knowledgebase & Blog Section ── -->
    <div id="blog" style="padding:80px 40px;background:#ffffff;border-top:1px solid #e2e8f0;position:relative">
<div style="max-width:1240px;margin:0 auto">

        <!-- Header Row -->
        <div
          style="display:flex;align-items:flex-end;justify-content:space-between;gap:30px;margin-bottom:44px;flex-wrap:wrap">
          <div>
            <div
              style="display:inline-flex;align-items:center;gap:6px;background:#e8f7ea;color:#15803d;font-size:12px;font-weight:800;padding:6px 14px;border-radius:99px;margin-bottom:14px;border:1px solid rgba(34,197,94,0.25)">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
              </svg>
              {{filter_static_option_value('home_page_01_'.$user_select_lang_slug.'_latest_news_title',$static_field_data)}}
</div>
            <h2 style="font-size:40px;font-weight:800;letter-spacing:-1.3px;color:#0f172a;line-height:1.15">
             {{filter_static_option_value('home_page_01_'.$user_select_lang_slug.'_latest_news_description',$static_field_data)}} 
            </h2>
          </div>
        </div>

        <!-- 3 Cards Grid -->
        <div class="blog-grid" style="display:grid;grid-template-columns:repeat(3, 1fr);gap:32px;align-items:stretch">
  @foreach($all_blog as $data )
          <!-- Card 1 -->
          <div class="blog-card-hover blog-item" data-category="{{ get_blog_category_name_by_id($data->blog_categories_id) }}" onclick="openBlogArticle({{ $data->id }})">
              <div style="position:relative;height:210px;overflow:hidden;background:#f8fafc">
              {!! render_image_markup_by_attachment_id($data->image, null, 'large', 'loading="lazy" decoding="async"') !!}
              <div
                style="position:absolute;top:14px;left:14px;background:rgba(15,23,42,0.85);backdrop-filter:blur(8px);color:#6fd943;border-radius:99px;padding:4px 12px;font-size:11px;font-weight:800;letter-spacing:0.8px;text-transform:uppercase">
               {!! get_blog_category_by_id($data->blog_categories_id,'link') !!}
              </div>
              <div
                style="position:absolute;bottom:12px;right:12px;background:#6fd943;color:#0b132a;border-radius:8px;padding:6px 12px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.2)">
                <div style="font-size:15px;font-weight:900;line-height:1">{{date_format($data->created_at,'d')}}</div>
                <div style="font-size:11px;font-weight:800;line-height:1;margin-top:2px">{{date_format($data->created_at,'M')}}</div>
              </div>
            </div>
            <div
              style="padding:24px;display:flex;flex-direction:column;justify-content:space-between;height:calc(100% - 210px);box-sizing:border-box">
              <div>
                <div style="font-size:12.5px;color:#64748b;font-weight:600;margin-bottom:8px">{{date_format($data->created_at,'d M Y')}} • 6 min read
                </div>
                <div style="font-size:19px;font-weight:800;color:#0f172a;line-height:1.35;margin-bottom:10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  {{$data->title}}
                </div>
                <div style="font-size:14px;color:#475569;line-height:1.6;margin-bottom:20px">
                 {{ \Illuminate\Support\Str::words(html_entity_decode(strip_tags($data->content), ENT_QUOTES | ENT_HTML5), 30, '...') }}
                </div>
              </div>
              <div
                style="display:flex;align-items:center;justify-content:space-between;padding-top:16px;border-top:1px solid #f1f5f9">
                <div style="display:flex;align-items:center;gap:8px">
                 @php
    $nameParts = preg_split('/\s+/', trim($data->authorData->name));

    $author_initials = strtoupper(
        substr($nameParts[0], 0, 1) .
        (count($nameParts) > 1 ? substr($nameParts[1], 0, 1) : '')
    );
@endphp
                   <div
                    style="width:28px;height:28px;border-radius:50%;background:#16a34a;color:#fff;font-size:11px;font-weight:800;display:flex;align-items:center;justify-content:center">
                    {{ $author_initials }}</div>
                  <span style="font-size:12.5px;font-weight:700;color:#334155">{{ $data->authorData->name }}</span>
                </div>
                <span
                  style="color:#15803d;font-size:13.5px;font-weight:800;display:inline-flex;align-items:center;gap:4px">
                  <a href="{{route('frontend.blog.single',$data->slug)}}" style="color:inherit;text-decoration:underline">Read Article &rarr;</a>
                </span>
              </div>
            </div>
          </div>
@endforeach
         

        </div>

      </div>
    </div>
    </div>
    </div>
@php
    $blogArticlesData = $all_blog->mapWithKeys(function ($blog) {

        $imageDetails = get_attachment_image_by_id($blog->image, 'full');

        return [
            $blog->id => [
                'cat' => get_blog_category_name_by_id($blog->blog_categories_id) ?? '',
                'title' => $blog->title ?? '',

                'meta' => $blog->created_at->format('M d, Y')
                    . ' • '
                    . ($blog->read_time ?? 6)
                    . ' min read'
                    . ' • By '
                    . ($blog->author->name ?? 'Admin'),

                'img' => $imageDetails['img_url'] ?? '',

                // The text itself is fetched when the pop-up opens
                // (frontend.home.article); the post page is the fallback.
                'url' => route('frontend.blog.single', $blog->slug),
            ]
        ];
    })->toArray();
@endphp

<script>
    const blogArticlesData = @json($blogArticlesData);
    window.G4D_ARTICLE_URL = @json(url('/home-article'));
</script>

@endif
    <!-- ── Interactive Blog Article Modal Reader ── -->
    <div id="blog-reader-modal"
      style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.75);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);z-index:9999;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
      <div
        style="background:#ffffff;border-radius:24px;max-width:840px;width:100%;max-height:90vh;overflow-y:auto;position:relative;box-shadow:0 25px 60px -15px rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.8);">

        <!-- Modal Top Bar with Close Button -->
        <div
          style="position:sticky;top:0;background:rgba(255,255,255,0.95);backdrop-filter:blur(10px);padding:18px 28px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;z-index:10">
          <div id="modal-blog-cat"
            style="background:#e8f7ea;color:#15803d;font-weight:800;font-size:11px;padding:4px 12px;border-radius:999px;letter-spacing:0.6px;text-transform:uppercase">
            PLAYBOOK</div>
          <button onclick="closeBlogArticle()"
            style="border:none;background:#f1f5f9;color:#475569;width:34px;height:34px;border-radius:50%;font-size:18px;font-weight:800;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s">&times;</button>
        </div>

        <!-- Article Banner Image & Header -->
        <div style="padding:28px 36px 0">
          <img id="modal-blog-img" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="Article Banner"
            style="width:100%;height:300px;object-fit:cover;border-radius:16px;margin-bottom:24px">

          <div id="modal-blog-meta" style="font-size:13px;color:#64748b;font-weight:600;margin-bottom:8px">Mar 23, 2026
            • 6 min read</div>
          <h2 id="modal-blog-title"
            style="font-size:30px;font-weight:800;color:#0f172a;line-height:1.25;margin:0 0 20px 0;letter-spacing:-0.8px">
            Female Multi-Channel Mailing Lists: Strategic B2B Reach
          </h2>
        </div>

        <!-- Article Body Content -->
        <div id="modal-blog-body" style="padding:0 36px 36px;font-size:16px;color:#334155;line-height:1.75">
          <!-- Populated dynamically by JS -->
        </div>

      </div>
    </div>

    <!-- 21st.dev FAQs-Two Component Pattern (by Méschac Irung) -->
    @if($home->on('faq'))
    <div id="faq" class="g4d-section-texture" style="padding:72px 40px;background:#f8faf9;border-top:1px solid #f1f5f9">
      <div style="max-width:920px;margin:0 auto">

        <!-- Header -->
        <div style="text-align:center;margin-bottom:56px">
          <div
            style="display:inline-flex;align-items:center;gap:6px;background:#e8f7ea;color:#1f7a2e;font-size:12.5px;font-weight:700;padding:8px 16px;border-radius:999px;margin-bottom:16px;border:1px solid rgba(31,122,46,0.2)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
              <path
                d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                stroke="#1f7a2e" stroke-width="2" />
              <path
                d="M9.09 9C9.3251 8.33167 9.78915 7.76811 10.4 7.40913C11.0108 7.05016 11.7289 6.91894 12.4272 7.03871C13.1255 7.15849 13.7588 7.52152 14.2151 8.06353C14.6713 8.60553 14.9211 9.29152 14.92 10C14.92 12 11.92 13 11.92 13"
                stroke="#1f7a2e" stroke-width="2" stroke-linecap="round" />
              <circle cx="12" cy="17" r="1" fill="#1f7a2e" />
            </svg>
            {{ $home->get('faq_badge') }}
          </div>
          <div style="font-size:44px;font-weight:800;letter-spacing:-1.5px;color:#0f172a;line-height:1.12">{{ $home->get('faq_title') }}</div>
          <div
            style="font-size:17px;color:#64748b;margin-top:14px;max-width:640px;margin-left:auto;margin-right:auto;line-height:1.6">
            {{ $home->get('faq_text') }}</div>
        </div>

        <!-- Accordion Items -->
        <div style="display:flex;flex-direction:column;gap:18px">

          @foreach($home->pairs('faqs') as $i => [$faq_q, $faq_a])
          <div class="faq-item{{ $i === 0 ? ' open' : '' }}">
            <div class="faq-question">
              <span>{{ $faq_q }}</span>
              <div class="faq-icon-box">
                <svg class="faq-icon" width="14" height="14" viewBox="0 0 24 24" fill="none"
                  style="transition:transform 0.25s">
                  <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </div>
            </div>
            <div class="faq-answer">
              {{ $faq_a }}
            </div>
          </div>
          @endforeach

        </div>

        <!-- Support / Still Have Questions Card (Tailark / 21st.dev faqs-two Signature Callout) -->
        <div
          style="margin-top:52px;background:#fff;border:1px solid #e2e8f0;border-radius:20px;padding:36px 40px;display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;box-shadow:0 10px 30px rgba(0,0,0,0.03)">
          <div style="display:flex;align-items:center;gap:20px">
            <div style="display:flex;margin-right:4px">
              <span
                style="width:40px;height:40px;border-radius:50%;background:#1f7a2e;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.1)">G4D</span>
              <span
                style="width:40px;height:40px;border-radius:50%;background:#7dd957;color:#111;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;border:2px solid #fff;margin-left:-12px;box-shadow:0 2px 6px rgba(0,0,0,0.1)">24/7</span>
            </div>
            <div>
              <div style="font-size:18px;font-weight:800;color:#0f172a">{{ $home->get('faq_help_title') }}</div>
              <div style="font-size:14.5px;color:#64748b;margin-top:2px">{{ $home->get('faq_help_text') }}</div>
            </div>
          </div>
          <a href="{{ $home->url('faq_help_url') }}"
            style="background:#6fd943;color:#0b132a;font-size:14px;font-weight:800;padding:12px 24px;border-radius:99px;white-space:nowrap;box-shadow:0 6px 18px rgba(111,217,67,0.35);transition:all 0.15s">{{ $home->get('faq_help_button') }}</a>
        </div>

      </div>
    </div>

    @endif

    <!-- Final CTA Banner (UpLead Pattern) -->
    @if($home->on('cta'))
    <div class="g4d-section-texture"
      style="padding:72px 40px;background:linear-gradient(135deg,#f0fdf4,#dcfce7 60%,#e6f7e9);color:#0b132a;text-align:center;border-top:1px solid #d1fae5;border-bottom:1px solid #d1fae5">
      <div style="max-width:800px;margin:0 auto">
        <h2
          style="font-size:46px;font-weight:900;letter-spacing:-1.5px;line-height:1.1;margin-bottom:18px;color:#0b132a">
          {{ $home->get('cta_title') }}</h2>
        <div style="font-size:18px;color:#475569;margin-bottom:36px;font-weight:500;line-height:1.7">{{ $home->get('cta_text') }}</div>
        <div style="display:flex;align-items:center;justify-content:center;gap:16px;flex-wrap:wrap">
          <a href="{{ $home->url('cta_button_url') }}"
            style="background:#6fd943;color:#0b132a;font-size:16px;font-weight:800;padding:16px 36px;border-radius:99px;box-shadow:0 10px 24px rgba(111,217,67,0.35);transition:all 0.2s">{{ $home->get('cta_button') }}</a>
          <a href="{{ $home->url('cta_button2_url') }}"
            style="background:#ffffff;border:1.5px solid #6fd943;color:#0b132a;font-size:16px;font-weight:700;padding:16px 36px;border-radius:99px;box-shadow:0 4px 12px rgba(0,0,0,0.05);transition:all 0.2s">{{ $home->get('cta_button2') }}</a>
        </div>
      </div>
    </div>

    @endif

    <!-- Contact & Subscription Form -->
    @if($home->on('contact'))
    <div id="contact" style="max-width:1440px;margin:0 auto;padding:60px 40px 40px">
      <div class="contact-cta-grid"
        style="display:grid;grid-template-columns:1.1fr 1fr;border-radius:20px;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 20px 50px -15px rgba(111,217,67,0.15)">
        <div class="contact-cta-left"
          style="background:linear-gradient(135deg,#f0fdf4,#dcfce7 60%,#e6f7e9);padding:64px 56px;display:flex;flex-direction:column;justify-content:space-between;gap:60px;border-right:1px solid #d1fae5">
          <div style="font-size:30px;font-weight:800;letter-spacing:-0.8px;color:#0b132a;line-height:1.25">{{ $home->get('contact_title') }}</div>
          <div>
            <div style="font-size:11.5px;font-weight:800;color:#1f7a2e;letter-spacing:1.5px;margin-bottom:18px">
              {{ $home->get('contact_partners_title') }}</div>
            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:24px 28px;">
@php $own_partners = $home->logos('contact_partners'); @endphp
@if(count($own_partners))
              @foreach($own_partners as [$logo_name, $logo_image])
                @if($logo_image)
              <img loading="lazy" decoding="async" class="trusted-logo" src="{{ $logo_image }}" alt="{{ $logo_name }}" style="height: 22px;" />
                @else
              <span class="trusted-text-logo" style="font-family:'Inter',sans-serif;font-size:16px;font-weight:700;color:#111;">{{ $logo_name }}</span>
                @endif
              @endforeach
@else
<span class="trusted-text-logo"
                style="font-family:'Inter',sans-serif;font-size:16px;font-weight:700;color:#111;display:flex;align-items:center;gap:4px;"><span
                  style="color:#e53e3e;font-size:11px;">●</span>TECAN<span style="color:#e53e3e;">.</span></span>
              <span class="trusted-text-logo"
                style="font-family:'Palatino Linotype',Georgia,serif;font-size:14px;font-weight:600;letter-spacing:2px;color:#1a1a2e;text-transform:uppercase;">Westin</span>
              <span class="trusted-text-logo"
                style="font-family:'Inter',sans-serif;font-size:16px;font-weight:900;color:#003087;letter-spacing:0.5px;">CBRE</span>
              <img loading="lazy" decoding="async" class="trusted-logo" src="{{ asset('assets/frontend/images/logos/Shopify_logo_2018.svg') }}"
                alt="Shopify" style="height: 22px;" />
              <span class="trusted-text-logo"
                style="font-family:'Inter',sans-serif;font-size:14px;font-weight:600;letter-spacing:2px;color:#222;text-transform:uppercase;">CANDELA</span>
              <span class="trusted-text-logo"
                style="font-family:'Didot','Palatino Linotype',Georgia,serif;font-size:15px;font-weight:700;color:#111;letter-spacing:1px;">J.POCKER</span>
              <span class="trusted-text-logo"
                style="font-family:'Inter',sans-serif;font-size:16px;font-weight:800;color:#0a0a23;display:flex;align-items:center;gap:2px;"><span
                  style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border:2px solid #0a0a23;border-radius:50%;font-size:11px;font-weight:900;">Q</span>MiQ</span>
              <span class="trusted-text-logo"
                style="font-family:'Inter',sans-serif;font-size:15px;font-weight:800;color:#0a0a23;letter-spacing:-0.3px;display:inline-flex;align-items:center;gap:4px;"><svg
                  width="16" height="16" viewBox="0 0 24 24" fill="#0a0a23" xmlns="http://www.w3.org/2000/svg"
                  style="flex-shrink:0">
                  <path
                    d="M22.282 9.821a5.985 5.985 0 0 0-.516-4.91 6.046 6.046 0 0 0-6.51-2.9A6.065 6.065 0 0 0 4.981 4.18a5.985 5.985 0 0 0-3.998 2.9 6.046 6.046 0 0 0 .743 7.097 5.98 5.98 0 0 0 .51 4.911 6.051 6.051 0 0 0 6.515 2.9A5.985 5.985 0 0 0 13.26 24a6.056 6.056 0 0 0 5.772-4.206 5.99 5.99 0 0 0 3.997-2.9 6.056 6.056 0 0 0-.747-7.073zM13.26 22.43a4.476 4.476 0 0 1-2.876-1.04l.141-.081 4.779-2.758a.795.795 0 0 0 .392-.681v-6.737l2.02 1.168a.071.071 0 0 1 .038.052v5.583a4.504 4.504 0 0 1-4.494 4.494zM3.6 18.304a4.47 4.47 0 0 1-.535-3.014l.142.085 4.783 2.759a.771.771 0 0 0 .78 0l5.843-3.369v2.332a.08.08 0 0 1-.033.062L9.74 19.95a4.5 4.5 0 0 1-6.14-1.646zM2.34 7.896a4.485 4.485 0 0 1 2.366-1.973V11.6a.766.766 0 0 0 .388.676l5.815 3.355-2.02 1.168a.076.076 0 0 1-.071 0l-4.83-2.786A4.504 4.504 0 0 1 2.34 7.872zm16.597 3.855l-5.843-3.374L15.115 7.2a.076.076 0 0 1 .071 0l4.83 2.786a4.494 4.494 0 0 1-.676 8.105v-5.678a.79.79 0 0 0-.403-.662zm2.01-3.023l-.141-.085-4.774-2.782a.776.776 0 0 0-.785 0L9.409 9.23V6.897a.066.066 0 0 1 .028-.061l4.83-2.787a4.5 4.5 0 0 1 6.68 4.66zm-12.64 4.135l-2.02-1.164a.08.08 0 0 1-.038-.057V6.075a4.5 4.5 0 0 1 7.375-3.453l-.142.08L8.704 5.46a.795.795 0 0 0-.393.681zm1.097-2.365l2.602-1.5 2.607 1.5v2.999l-2.597 1.5-2.607-1.5z" />
                </svg>OpenAI</span>
              <span class="trusted-text-logo"
                style="font-family:'Georgia',serif;font-size:17px;font-weight:800;color:#c05621;letter-spacing:-0.2px;display:inline-flex;align-items:center;">Claude</span>
              <span class="trusted-text-logo"
                style="font-family:'Inter',sans-serif;font-size:15px;font-weight:900;color:#1e293b;letter-spacing:-0.5px;display:inline-flex;align-items:center;gap:3px;"><span
                  style="color:#6fd943;font-weight:900;">11</span>elevenlabs</span>
@endif
            </div>
          </div>
        </div>

        <div class="contact-cta-right" style="background:#fff;padding:56px 56px 48px">
          <div style="font-size:28px;font-weight:800;color:#111;letter-spacing:-0.6px;margin-bottom:32px">{{ $home->get('contact_form_title') }}</div>
          {{-- Kept as a form lead (Admin > List > Form Leads); home-01.js sends it and shows the answer below. --}}
          <form id="contact-form" action="{{ route('home-contact.submit') }}" method="POST" data-success="{{ $home->get('contact_success') }}" style="display:flex;flex-direction:column;gap:22px">
            @csrf
            <input type="text" name="website_url" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-10000px;width:1px;height:1px;opacity:0">
            <label style="display:flex;flex-direction:column;gap:8px">
              <span style="font-size:13.5px;font-weight:700;color:#333">{{ $home->get('contact_email_label') }}</span>
              <input type="email" name="email" autocomplete="email" placeholder="name@company.com" required
                style="border:1px solid rgba(0,0,0,0.12);border-radius:9px;padding:13px 16px;font-size:14.5px;color:#333;font-family:inherit">
            </label>
            <label style="display:flex;flex-direction:column;gap:8px">
              <span style="font-size:13.5px;font-weight:700;color:#333">{{ $home->get('contact_name_label') }}</span>
              <input type="text" name="name" autocomplete="name" placeholder="Your Name" required
                style="border:1px solid rgba(0,0,0,0.12);border-radius:9px;padding:13px 16px;font-size:14.5px;color:#333;font-family:inherit">
            </label>
            <label style="display:flex;flex-direction:column;gap:8px">
              <span style="font-size:13.5px;font-weight:700;color:#333">{{ $home->get('contact_phone_label') }}</span>
              <input type="tel" name="phone" autocomplete="tel" placeholder="Your Phone"
                style="border:1px solid rgba(0,0,0,0.12);border-radius:9px;padding:13px 16px;font-size:14.5px;color:#333;font-family:inherit">
            </label>
            <button id="contact-submit-btn" type="submit"
              style="border:none;cursor:pointer;background:#6fd943;color:#0b132a;font-size:15px;font-weight:800;padding:15px;border-radius:99px;box-shadow:0 10px 22px rgba(111,217,67,0.35);transition:all 0.15s">{{ $home->get('contact_button') }}</button>
            <p class="g4d-contact-note" role="status" @if(!session('home_contact_success')) hidden @endif style="margin:0;font-size:14px;font-weight:600;color:#15803d">{{ session('home_contact_success') }}</p>
          </form>
        </div>
      </div>
    </div>
    @endif
  @if(\App\Http\Controllers\WebsiteLeadsLocalProxyController::enabled())
    {{-- Local testing: the app API only accepts www.go4database.com, so search through this site --}}
    <script>
      window.G4D_LEADS_API = @json(route('local.website.leads'));
      window.G4D_LEADS_CONTACT_API = @json(route('local.website.leads.contact'));
    </script>
  @endif
  {{-- Bump ?v= on every home-01.js change: LiteSpeed serves JS with a one-year immutable cache --}}
  <script src="{{ asset('assets/frontend/js/home-01.js') }}?v=10" defer></script>
@include('frontend.partials.contact-section')
