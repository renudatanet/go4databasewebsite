@extends('frontend.frontend-page-master')
@php
    // Resolved before the title sections so both can use it.
    $af_lang = !empty(session()->get('lang'))
        ? session()->get('lang')
        : optional(\App\Language::where('default', 1)->first())->slug;
    $af_page_title = \App\Http\Controllers\AffiliateSettingsController::text_value($af_lang, 'page_title');
@endphp
@section('site-title')
    {{ $af_page_title }}
@endsection
@section('page-title')
    {{ $af_page_title }}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{ $af['meta_description'] }}">
    <meta name="keywords" content="{{ $af['meta_keywords'] }}">
    <link rel="canonical" href="{{ route('frontend.affiliate') }}">
@endsection
@push('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('assets/frontend/css/affiliate.css?v=1')}}">
@endpush

@php
    use App\Http\Controllers\AffiliateSettingsController as AF;

    // "To be confirmed" rather than an invented figure: these are public
    // promises, so an unset term says so plainly on the page.
    $tbc = $af['tbc_label'] ?: __('To be confirmed');

    // :months resolved once here, so every label and the sums agree.
    $months_txt = fn($s) => str_replace(':months', $af_months, $s);
    $rate_txt       = $af_rate !== null ? rtrim(rtrim(number_format($af_rate, 2, '.', ''), '0'), '.') . '%' : $tbc;
    $term_txt       = AF::term('commission_term') ?: $tbc;
    $cookie_txt     = AF::term('cookie_days') ?: $tbc;
    $payout_txt     = AF::term('payout_method') ?: $tbc;
    $minimum_txt    = AF::term('payout_minimum') ?: $tbc;
    // Where an Apply button can actually send someone. An external link
    // always works; the on-page anchor only exists while the apply section
    // is switched on. With neither, there is nowhere to go, so the buttons
    // are not rendered at all rather than left pointing at a missing anchor.
    $apply_external = Str::startsWith(AF::term('apply_url'), 'http');
    $apply_url      = AF::term('apply_url') ?: '#af-apply';
    $apply_href     = $apply_external ? $apply_url : (AF::show('apply') ? '#af-apply' : null);
    // Same for the "see what you'd earn" link, which scrolls to the calculator.
    $calc_href      = AF::show('calculator') ? '#af-calc' : null;
    $max_referrals  = (int) (AF::term('max_referrals') ?: 50);

    // Where the calculator starts. Both are clamped to what the slider and
    // the plan buttons can actually represent, so a stale setting cannot
    // leave the calculator showing a value the controls cannot reach.
    $default_refs   = (int) (AF::term('default_referrals') ?: 10);
    $default_refs   = max(1, min(max(5, $max_referrals), $default_refs));
    $default_plan   = (int) AF::term('default_plan');
    if (!in_array($default_plan, $af_plans, true)) {
        $default_plan = $af_plans[(int) floor(count($af_plans) / 2)] ?? ($af_plans[0] ?? 0);
    }

    // What the partner's audience saves. Separate from the commission: one is
    // what the partner earns, the other is what their referral pays less.
    $discount       = trim((string) AF::term('referral_discount'));
    $discount_title = str_replace(':discount', $discount, $af['discount_title']);
@endphp

@section('content')
<div class="af">

  {{-- ===================== HERO ===================== --}}
  <section class="af-hero">
    <div class="af-hero-inner">
      <span class="af-eyebrow">
        <span class="af-eyebrow-dot"></span> {{ $af['hero_badge'] }}
      </span>

      <h1 class="af-h1">{{ $af['hero_title'] }} <em>{{ $af['hero_title_highlight'] }}</em></h1>
      <p class="af-hero-sub">{{ $af['hero_subtitle'] }}</p>

      <div class="af-hero-row">
        @if($apply_href)
          <a href="{{ $apply_href }}" class="af-btn" @if($apply_external) target="_blank" rel="noopener" @endif>{{ $af['hero_btn'] }}</a>
        @endif
        @if($calc_href)
          <a href="{{ $calc_href }}" class="af-btn af-btn--ghost">{{ $af['hero_btn_alt'] }}</a>
        @endif
      </div>

      <div class="af-trust">
        @foreach($af_items['trust'] as $item)
          <span><i data-lucide="{{ $item->icon ?: 'check' }}"></i> {{ $item->title }}</span>
        @endforeach
      </div>
    </div>
  </section>

  @if(AF::show('discount'))
  {{-- ===================== AUDIENCE DISCOUNT =====================
       Only rendered once a discount has actually been set, so the page
       never advertises a saving that does not exist. --}}
  @if($discount !== '')
  <section class="af-discount">
    <div class="af-inner">
      <div class="af-discount-card af-rv">
        <div class="af-discount-badge">{{ $discount }}</div>
        <div class="af-discount-tx">
          <h2 class="af-h3">{{ $discount_title }}</h2>
          <p>{{ $af['discount_text'] }}</p>
        </div>
      </div>
    </div>
  </section>
  @endif

  @endif
  @if(AF::show('calculator'))
  {{-- ===================== EARNINGS CALCULATOR ===================== --}}
  <section class="af-band" id="af-calc">
    <div class="af-inner">
      <div class="af-calc-head">
        <div>
          <h2 class="af-h2 af-rv">{{ $af['calc_title'] }}</h2>
          <p class="af-lede af-rv">{{ $months_txt($af['calc_lead']) }}</p>
        </div>
        @if($af_rate === null)
          <span class="af-note">
            <i data-lucide="circle-alert"></i> {{ __('Commission rate not set yet') }}
          </span>
        @endif
      </div>

      <div class="af-calc af-rv @if($af_rate === null) af-calc--unset @endif"
           id="af-calc-box"
           data-rate="{{ $af_rate !== null ? $af_rate : '' }}"
           data-months="{{ $af_months }}">
        <div class="af-calc-inner">

          <div class="af-calc-controls">
            <div>
              <div class="af-field-head">
                <label class="af-field-label" for="af-refs">{{ $af['calc_refs_label'] }}</label>
                <span class="af-field-value" id="af-refs-out">{{ $default_refs }}</span>
              </div>
              <input type="range" id="af-refs" class="af-range" min="1" max="{{ max(5, $max_referrals) }}" value="{{ $default_refs }}" step="1">
              <div class="af-range-ends"><span>1</span><span>{{ max(5, $max_referrals) }}</span></div>
            </div>

            <div>
              <div class="af-field-label" style="margin-bottom:12px;">{{ $af['calc_plan_label'] }}</div>
              <div class="af-plans" id="af-plans" role="group" aria-label="{{ $af['calc_plan_label'] }}">
                @foreach($af_plans as $i => $plan)
                  <button type="button" class="af-plan" data-plan="{{ $plan }}"
                          aria-pressed="{{ $plan == $default_plan ? 'true' : 'false' }}">
                    ${{ $plan }}<small>/mo</small>
                  </button>
                @endforeach
              </div>
            </div>

            <div class="af-calc-foot">
              <i data-lucide="info"></i>
              @if($af_rate !== null)
                <span>{!! __('Assumes a :rate recurring commission and that referred customers stay subscribed.', ['rate' => '<b>' . e($rate_txt) . '</b>']) !!}</span>
              @else
                <span>{{ $af['calc_unset_note'] }}</span>
              @endif
            </div>
          </div>

          <div class="af-calc-out">
            <div>
              <div class="af-out-label">{{ $months_txt($af['calc_monthly_label']) }}</div>
              <div class="af-out-value" id="af-out-monthly">{{ $af_rate !== null ? '—' : $tbc }}</div>
            </div>
            <div class="af-out-rule"></div>
            <div>
              <div class="af-out-label">{{ $months_txt($af['calc_year_label']) }}</div>
              <div class="af-out-value" id="af-out-year">{{ $af_rate !== null ? '—' : $tbc }}</div>
            </div>
            @if($apply_href)
              <a href="{{ $apply_href }}" class="af-btn" style="margin-top:4px;" @if($apply_external) target="_blank" rel="noopener" @endif>{{ $af['calc_btn'] }}</a>
            @endif
          </div>

        </div>
      </div>
    </div>
  </section>

  @endif
  @if(AF::show('terms'))
  {{-- ===================== TERMS ===================== --}}
  <section class="af-band af-band--soft">
    <div class="af-inner">
      <h2 class="af-h2 af-rv">{{ $af['terms_title'] }}</h2>
      <p class="af-lede af-rv">{{ $af['terms_lead'] }}</p>

      <div class="af-terms af-rv">
        <div class="af-term">
          <div class="af-term-k">{{ $af['terms_rate_label'] }}</div>
          <div class="af-term-v @if($af_rate === null) af-term-v--unset @endif">{{ $rate_txt }}</div>
          <div class="af-term-n">{{ $af['terms_rate_note'] }}</div>
        </div>
        <div class="af-term">
          <div class="af-term-k">{{ $af['terms_term_label'] }}</div>
          <div class="af-term-v @if(!AF::term('commission_term')) af-term-v--unset @endif">{{ $term_txt }}</div>
          <div class="af-term-n">{{ $af['terms_term_note'] }}</div>
        </div>
        <div class="af-term">
          <div class="af-term-k">{{ $af['terms_cookie_label'] }}</div>
          <div class="af-term-v @if(!AF::term('cookie_days')) af-term-v--unset @endif">{{ $cookie_txt }}</div>
          <div class="af-term-n">{{ $af['terms_cookie_note'] }}</div>
        </div>
        <div class="af-term">
          <div class="af-term-k">{{ $af['terms_payout_label'] }}</div>
          <div class="af-term-v @if(!AF::term('payout_method')) af-term-v--unset @endif">{{ $payout_txt }}</div>
          <div class="af-term-n">{{ $af['terms_payout_note'] }}</div>
        </div>
        <div class="af-term">
          <div class="af-term-k">{{ $af['terms_minimum_label'] }}</div>
          <div class="af-term-v @if(!AF::term('payout_minimum')) af-term-v--unset @endif">{{ $minimum_txt }}</div>
          <div class="af-term-n">{{ $af['terms_minimum_note'] }}</div>
        </div>
      </div>
    </div>
  </section>

  @endif
  @if(AF::show('steps'))
  {{-- ===================== HOW IT WORKS ===================== --}}
  <section class="af-band">
    <div class="af-inner">
      <h2 class="af-h2 af-rv">{{ $af['steps_title'] }}</h2>
      <div class="af-steps">
        @foreach($af_items['step'] as $item)
          <div class="af-step af-rv">
            <span class="af-step-n">{{ $loop->iteration }}</span>
            <h3>{{ $item->title }}</h3>
            <p>{{ $item->description }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  @endif
  @if(AF::show('compare'))
  {{-- ===================== AFFILIATE VS RESELLER ===================== --}}
  <section class="af-band af-band--soft">
    <div class="af-inner">
      <h2 class="af-h2 af-rv">{{ $af['compare_title'] }}</h2>
      <p class="af-lede af-rv">{{ $af['compare_lead'] }}</p>

      <div class="af-cmp af-rv">
        <div class="af-cmp-row af-cmp-row--head">
          <span></span>
          <span class="af-cmp-aff">{{ $af['compare_col_aff'] }}</span>
          <span>{{ $af['compare_col_res'] }}</span>
        </div>
        @foreach($af_items['compare'] as $item)
          <div class="af-cmp-row">
            <div class="af-cmp-k">{{ $item->title }}</div>
            <div class="af-cmp-v af-cmp-v--aff">{{ $item->description }}</div>
            <div class="af-cmp-v af-cmp-v--res">{{ $item->alt_text }}</div>
          </div>
        @endforeach
        <div class="af-cmp-foot">
          <span class="af-cmp-foot-tx">{{ $af['compare_cta_text'] }}</span>
          <a href="{{ $af['compare_cta_url'] }}" class="af-btn af-btn--ghost">
            {{ $af['compare_cta_btn'] }} <i data-lucide="arrow-right"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  @endif
  @if(AF::show('who'))
  {{-- ===================== WHO IT SUITS ===================== --}}
  <section class="af-band">
    <div class="af-inner">
      <h2 class="af-h2 af-rv">{{ $af['who_title'] }}</h2>
      <p class="af-lede af-rv">{{ $af['who_lead'] }}</p>
      <div class="af-who">
        @foreach($af_items['who'] as $item)
          <div class="af-who-card af-rv">
            <div class="af-who-badge">
              @if($item->icon && strlen($item->icon) > 2)
                <i data-lucide="{{ $item->icon }}"></i>
              @else
                {{ $item->icon ?: Str::substr($item->title, 0, 1) }}
              @endif
            </div>
            <h3>{{ $item->title }}</h3>
            <p>{{ $item->description }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  @endif
  @if(AF::show('gets'))
  {{-- ===================== WHAT PARTNERS GET ===================== --}}
  <section class="af-band af-band--dark">
    <div class="af-inner">
      <h2 class="af-h2 af-h2--inv af-rv">{{ $af['gets_title'] }}</h2>
      <p class="af-lede af-lede--inv af-rv">{{ $af['gets_lead'] }}</p>
      <div class="af-gets">
        @foreach($af_items['get'] as $item)
          <div class="af-get af-rv">
            <i data-lucide="check"></i>
            <div>
              <div class="af-get-t">{{ $item->title }}</div>
              <div class="af-get-b">{{ $item->description }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  @endif
  @if(AF::show('testimonials'))
  {{-- ===================== TESTIMONIALS ===================== --}}
  @if($all_testimonial->count())
  <section class="af-band">
    <div class="af-inner">
      <h2 class="af-h2 af-rv">{{ $af['testimonial_title'] }}</h2>
      <div class="af-quotes">
        @foreach($all_testimonial as $t)
          <div class="af-quote af-rv">
            <p class="af-quote-tx">{{ Str::limit(strip_tags($t->description), 240) }}</p>
            <div class="af-quote-by">
              @php $img = get_attachment_image_by_id($t->image, null, true); @endphp
              @if(!empty($img['img_url']))
                <img src="{{ $img['img_url'] }}" alt="{{ $t->name }}" class="af-quote-av" width="38" height="38" loading="lazy">
              @else
                <span class="af-quote-av">{{ Str::substr($t->name, 0, 1) }}</span>
              @endif
              <div>
                <div class="af-quote-n">{{ $t->name }}</div>
                <div class="af-quote-r">{{ $t->designation }}</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  @endif
  @if(AF::show('faq'))
  {{-- ===================== FAQ ===================== --}}
  <section class="af-band af-band--soft">
    <div class="af-inner">
      <h2 class="af-h2 af-rv">{{ $af['faq_title'] }}</h2>
      <div class="af-faq af-rv">
        @foreach($af_faqs as $faq)
          <div class="af-faq-item">
            <button class="af-faq-q" type="button">
              <span>{{ $faq->title }}</span>
              <i data-lucide="plus"></i>
            </button>
            <div class="af-faq-a"><p>{{ strip_tags($faq->description) }}</p></div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  @endif
  @if(AF::show('apply'))
  {{-- ===================== APPLY =====================
       With an external Apply link set, this is a plain banner pointing at it.
       Without one, it is a real form that saves the application and shows up
       under Affiliate Applications in the admin panel, so the button is never
       a dead end. --}}
  <section class="af-cta" id="af-apply">
    @if($apply_external)
      <div class="af-cta-card">
        <h2>{{ $af['cta_title'] }}</h2>
        <p class="af-cta-tx">{{ $af['cta_text'] }}</p>
        <a href="{{ $apply_url }}" class="af-btn af-btn--brand" style="margin-top:26px;" target="_blank" rel="noopener">{{ $af['cta_btn'] }}</a>
        <span class="af-cta-note">{{ $af['cta_note'] }}</span>
      </div>
    @else
      <div class="af-apply-card">
        @if(session('af_applied'))
          <div class="af-applied" role="status">
            <i data-lucide="circle-check"></i>
            <span>{{ session('af_applied') }}</span>
          </div>
        @else
          <h2>{{ $af['apply_title'] }}</h2>
          <p class="af-cta-tx">{{ $af['apply_lead'] }}</p>

          @if($errors->any())
            <div class="af-errors" role="alert">
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('frontend.affiliate.apply') }}" class="af-form">
            @csrf
            <div class="af-form-row">
              <div class="af-form-field">
                <label for="af-name">{{ $af['apply_name'] }}</label>
                <input type="text" id="af-name" name="name" maxlength="120" required value="{{ old('name') }}">
              </div>
              <div class="af-form-field">
                <label for="af-email">{{ $af['apply_email'] }}</label>
                <input type="email" id="af-email" name="email" maxlength="190" required value="{{ old('email') }}">
              </div>
            </div>
            <div class="af-form-field">
              <label for="af-site">{{ $af['apply_site'] }}</label>
              <input type="text" id="af-site" name="site" maxlength="255" placeholder="https://" value="{{ old('site') }}">
            </div>
            <div class="af-form-field">
              <label for="af-promo">{{ $af['apply_promo'] }}</label>
              <textarea id="af-promo" name="promotion" rows="4" maxlength="2000">{{ old('promotion') }}</textarea>
            </div>

            {{-- Honeypot. Hidden from people, irresistible to bots. --}}
            <div class="af-hp" aria-hidden="true">
              <label for="af-company-website">Company website</label>
              <input type="text" id="af-company-website" name="company_website" tabindex="-1" autocomplete="off">
            </div>

            <button type="submit" class="af-btn af-btn--brand af-form-submit">{{ $af['apply_btn'] }}</button>
            <span class="af-cta-note">{{ $af['cta_note'] }}</span>
          </form>
        @endif
      </div>
    @endif
  </section>

  @endif
</div>

{{-- Structured data. The FAQ answers are the same list the page renders, so
     the two can never drift apart, and the FAQ block is left out entirely
     when the FAQ section is switched off: FAQ markup for questions a visitor
     cannot see is exactly what Google penalises. --}}
@php
    $af_graph = [
        [
            '@type' => 'WebPage',
            'name' => $af_page_title,
            'url' => route('frontend.affiliate'),
            'description' => $af['meta_description'],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Go4Database',
                'url' => url('/'),
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $af_page_title, 'item' => route('frontend.affiliate')],
            ],
        ],
    ];

    if (AF::show('faq') && $af_faqs->count()) {
        $af_graph[] = [
            '@type' => 'FAQPage',
            'mainEntity' => $af_faqs->map(function ($faq) {
                return [
                    '@type' => 'Question',
                    'name' => $faq->title,
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($faq->description)],
                ];
            })->values()->all(),
        ];
    }
@endphp
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => $af_graph,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@push('script')
{{-- Same icon source the Email Verifier and Email Finder pages use. --}}
<script src="https://unpkg.com/lucide@1.48.0/dist/umd/lucide.min.js"
        integrity="sha384-Hh7C333mXel+qppGoFs4qAOXp7h67eur4XsQVF2bvHAM3MQ4DX4cQI7oRCzb98J4"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
(function () {
  'use strict';

  // Gate the reveal animation behind a class the script itself adds, so that
  // if this script never runs the content is not left parked at opacity:0.
  document.documentElement.classList.add('af-js');

  var box = document.getElementById('af-calc-box');
  var refs = document.getElementById('af-refs');
  var refsOut = document.getElementById('af-refs-out');
  var plansWrap = document.getElementById('af-plans');
  var outMonthly = document.getElementById('af-out-monthly');
  var outYear = document.getElementById('af-out-year');

  // Empty when no rate has been set in the admin panel. The figures then stay
  // as they were rendered ("To be confirmed") rather than being computed from
  // a number nobody has agreed to.
  var rate = box ? parseFloat(box.getAttribute('data-rate')) : NaN;
  var hasRate = !isNaN(rate) && rate > 0;
  var months = box ? parseInt(box.getAttribute('data-months'), 10) : 12;
  if (!months || months < 1) months = 12;

  function money(n) {
    return '$' + Math.round(n).toLocaleString('en-US');
  }

  function selectedPlan() {
    if (!plansWrap) return 0;
    var on = plansWrap.querySelector('.af-plan[aria-pressed="true"]');
    return on ? parseInt(on.getAttribute('data-plan'), 10) || 0 : 0;
  }

  function recalc() {
    if (!refs || !refsOut) return;
    var n = parseInt(refs.value, 10) || 0;
    refsOut.textContent = n;
    if (!hasRate || !outMonthly || !outYear) return;

    var plan = selectedPlan();
    var r = rate / 100;

    // Recurring: by month M you are paid for everyone referred in months
    // 1 through M. The running total accumulates 1x + 2x + ... + Mx,
    // which is M(M+1)/2 cohorts.
    outMonthly.textContent = money(n * months * plan * r);
    outYear.textContent = money(n * plan * r * (months * (months + 1) / 2));
  }

  if (refs) {
    refs.addEventListener('input', recalc);
  }

  if (plansWrap) {
    plansWrap.addEventListener('click', function (e) {
      var btn = e.target.closest('.af-plan');
      if (!btn) return;
      plansWrap.querySelectorAll('.af-plan').forEach(function (b) {
        b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
      });
      recalc();
    });
  }

  recalc();

  // FAQ accordion
  document.querySelectorAll('.af-faq-q').forEach(function (q) {
    q.addEventListener('click', function () {
      var item = q.parentElement;
      var open = item.classList.contains('open');
      document.querySelectorAll('.af-faq-item').forEach(function (i) { i.classList.remove('open'); });
      if (!open) item.classList.add('open');
    });
  });

  // Reveal on scroll
  var rv = document.querySelectorAll('.af-rv');
  if ('IntersectionObserver' in window && rv.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -60px 0px' });
    rv.forEach(function (el) { io.observe(el); });
  } else {
    rv.forEach(function (el) { el.classList.add('in'); });
  }

  if (window.lucide && typeof window.lucide.createIcons === 'function') {
    window.lucide.createIcons();
  }
})();
</script>
@endpush
