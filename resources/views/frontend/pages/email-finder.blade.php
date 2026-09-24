@extends('frontend.frontend-page-master')
@section('site-title')
    Email Finder
@endsection
@section('page-title')
    Email Finder
@endsection
@section('page-meta-data')
    <meta name="description" content="{{ $ef['meta_description'] }}">
    <meta name="keywords" content="{{ $ef['meta_keywords'] }}">
    <link rel="canonical" href="{{ route('frontend.email.finder') }}">
@endsection
@push('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('assets/frontend/css/email-finder.css?v=4')}}">
@endpush

@section('content')
<div class="ef">

  {{-- ===================== HERO + TOOL ===================== --}}
  <section class="ef-hero">
    <div class="ef-hero-inner">
      <span class="ef-eyebrow">
        <span class="ef-eyebrow-dot"></span> {{ $ef['hero_badge'] }}
      </span>

      <h1 class="ef-h1">{{ $ef['hero_title'] }} <em>{{ $ef['hero_title_highlight'] }}</em></h1>
      <p class="ef-hero-sub">{{ $ef['hero_subtitle'] }}</p>

      <div class="ef-tool">
        <div class="ef-tool-inner">
          <form id="ef-find-form" class="ef-tool-form" autocomplete="off" novalidate>
            <div class="ef-field">
              <label for="ef-first">{{ $ef['first_label'] }}</label>
              <input type="text" id="ef-first" placeholder="Jane" maxlength="60" required>
            </div>
            <div class="ef-field">
              <label for="ef-last">{{ $ef['last_label'] }}</label>
              <input type="text" id="ef-last" placeholder="Doe" maxlength="60">
            </div>
            <div class="ef-field ef-field--wide">
              <label for="ef-domain">{{ $ef['domain_label'] }}</label>
              <input type="text" id="ef-domain" placeholder="acme.com" maxlength="190" required>
            </div>
            <button type="submit" id="ef-find-btn" class="ef-tool-btn">
              <span class="ef-btn-label">{{ $ef['tool_btn'] }}</span>
              <i data-lucide="loader-circle" class="ef-spin"></i>
            </button>
          </form>

          {{-- Candidate patterns, shown while the server works through them --}}
          <div id="ef-working" class="ef-working" hidden>
            <p class="ef-working-label">{{ $ef['working_label'] }}</p>
            <div id="ef-working-list" class="ef-working-list"></div>
          </div>

          <div id="ef-result" class="ef-result" hidden>
            <div class="ef-result-top">
              <span id="ef-result-badge" class="ef-pill"></span>
              <span id="ef-result-confidence" class="ef-confidence"></span>
            </div>
            <div class="ef-result-mail">
              <span id="ef-result-email" class="ef-result-email"></span>
              <button type="button" id="ef-copy" class="ef-copy" title="Copy address">
                <i data-lucide="copy"></i><span>Copy</span>
              </button>
            </div>
            <p id="ef-result-reason" class="ef-result-reason"></p>
            <div id="ef-result-meta" class="ef-result-meta"></div>
          </div>

          <div id="ef-error" class="ef-alert" hidden></div>

          <div class="ef-tool-foot">
            @foreach($ef_items['trust'] as $item)
              <span><i data-lucide="{{ $item->icon ?: 'check' }}"></i> {{ $item->title }}</span>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ===================== HOW IT WORKS ===================== --}}
  <section class="ef-band">
    <div class="ef-band-inner">
      <h2 class="ef-h2 ef-rv">{{ $ef['steps_title'] }}</h2>
      <p class="ef-lede ef-rv">{{ $ef['steps_lead'] }}</p>

      <div class="ef-steps">
        @foreach($ef_items['step'] as $item)
          <div class="ef-step ef-rv">
            <span class="ef-step-n">{{ $loop->iteration }}</span>
            <h3>{{ $item->title }}</h3>
            <p>{{ $item->description }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===================== PATTERN FREQUENCY ===================== --}}
  <section class="ef-band ef-band--soft">
    <div class="ef-band-inner">
      <h2 class="ef-h2 ef-rv">{{ $ef['patterns_title'] }}</h2>
      <p class="ef-lede ef-rv">{{ $ef['patterns_lead'] }}</p>

      <div class="ef-patterns ef-rv">
        <div class="ef-pattern-row ef-pattern-row--head">
          <span>{{ $ef['patterns_col_format'] }}</span><span>{{ $ef['patterns_col_example'] }}</span><span>{{ $ef['patterns_col_common'] }}</span>
        </div>
        @foreach($ef_items['pattern'] as $item)
          <div class="ef-pattern-row">
            <span class="ef-mono">{{ $item->title }}</span><span class="ef-mono ef-dim">{{ $item->description }}</span>
            <span class="ef-bar-wrap">
              {{-- Width is clamped in the blade as well as validated in the
                   admin form, so a bad stored value can never stretch the bar
                   past its track. --}}
              <span class="ef-bar @if($item->is_highlight) ef-bar--dim @endif"
                    style="width:{{ max(0, min(100, (int) ($item->bar_width ?? 0))) }}%"></span>
              <b>{{ $item->badge_key }}</b>
            </span>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===================== PAIRS WITH VERIFIER ===================== --}}
  <section class="ef-band ef-band--tight">
    <div class="ef-band-inner">
      <div class="ef-pair ef-rv">
        <div class="ef-pair-tx">
          <h2 class="ef-h3">{{ $ef['pair_title'] }}</h2>
          <p>{{ $ef['pair_text'] }}</p>
        </div>
        <a href="{{ route('frontend.email.verifier') }}" class="ef-pair-btn">
          {{ $ef['pair_btn'] }} <i data-lucide="arrow-right"></i>
        </a>
      </div>
    </div>
  </section>

  {{-- ===================== FREE CREDITS ===================== --}}
  <section class="ef-offer">
    <div class="ef-offer-inner ef-rv">
      <h2 class="ef-offer-title">{{ $ef['offer_title'] }}</h2>
      <p class="ef-offer-sub">{{ $ef['offer_text'] }}</p>
      <a href="{{ $ef['offer_url'] }}" target="_blank" rel="noopener" class="ef-offer-btn">{{ $ef['offer_btn'] }}</a>
      <span class="ef-offer-foot">{{ $ef['offer_note'] }}</span>
    </div>
  </section>

  {{-- ===================== FAQ ===================== --}}
  <section class="ef-band ef-band--tight">
    <div class="ef-band-inner">
      <h2 class="ef-h2 ef-rv">{{ $ef['faq_title'] }}</h2>

      <div class="ef-faq ef-rv">
        @foreach($ef_faqs as $faq)
          <div class="ef-faq-item">
            <button class="ef-faq-q" type="button">
              <span>{{ $faq->title }}</span>
              <i data-lucide="plus"></i>
            </button>
            <div class="ef-faq-a"><p>{{ strip_tags($faq->description) }}</p></div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===================== CLOSING CTA ===================== --}}
  <section class="ef-cta">
    <div class="ef-cta-inner">
      <h2>{{ $ef['cta_title'] }}</h2>
      <p>{{ $ef['cta_text'] }}</p>
      <div class="ef-cta-row">
        <a href="#ef-find-form" class="ef-cta-btn">{{ $ef['cta_btn'] }}</a>
        <a href="{{ route('frontend.email.verifier') }}" class="ef-cta-btn ef-cta-btn--ghost">{{ $ef['cta_btn_ghost'] }}</a>
      </div>
    </div>
  </section>

</div>

{{-- Structured data: tells search engines this page is a working tool, and
     feeds the FAQ answers into the rich result that can show under the
     listing. Built from the same $faqs array the page renders, so the two
     can never drift apart. --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebApplication',
            'name' => 'Go4Database Email Finder',
            'url' => route('frontend.email.finder'),
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Any',
            'description' => $ef['meta_description'],
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD',
            ],
            'provider' => [
                '@type' => 'Organization',
                'name' => 'Go4Database',
                'url' => url('/'),
            ],
        ],
        [
            '@type' => 'FAQPage',
            'mainEntity' => $ef_faqs->map(function ($faq) {
                return [
                    '@type' => 'Question',
                    'name' => $faq->title,
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($faq->description)],
                ];
            })->values()->all(),
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Email Finder', 'item' => route('frontend.email.finder')],
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@push('script')
  <script src="https://unpkg.com/lucide@1.48.0/dist/umd/lucide.min.js"
        integrity="sha384-Hh7C333mXel+qppGoFs4qAOXp7h67eur4XsQVF2bvHAM3MQ4DX4cQI7oRCzb98J4"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    (function () {
      function init() {
        if (window.lucide) { lucide.createIcons(); }

        /* ---------- scroll reveal ----------
           Opt in to the hidden-then-reveal styling only now that the script
           is definitely running, so a JS failure leaves the page readable
           rather than blank. */
        var root = document.querySelector('.ef');
        var revealables = document.querySelectorAll('.ef-rv');
        if (root && revealables.length) { root.classList.add('ef-js'); }
        if ('IntersectionObserver' in window) {
          var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry, i) {
              if (entry.isIntersecting) {
                var el = entry.target;
                setTimeout(function () { el.classList.add('in'); }, Math.min(i * 60, 240));
                io.unobserve(el);
              }
            });
          }, { rootMargin: '0px 0px -60px 0px', threshold: 0.08 });
          revealables.forEach(function (el) { io.observe(el); });
        } else {
          revealables.forEach(function (el) { el.classList.add('in'); });
        }

        /* ---------- FAQ ---------- */
        document.querySelectorAll('.ef-faq-item').forEach(function (item) {
          item.querySelector('.ef-faq-q').addEventListener('click', function () {
            var wasOpen = item.classList.contains('open');
            document.querySelectorAll('.ef-faq-item').forEach(function (i) { i.classList.remove('open'); });
            if (!wasOpen) { item.classList.add('open'); }
          });
        });

        /* ---------- finder ---------- */
        var STATUS = {
          found:          { label: 'Confirmed',   tone: 'ok',   icon: 'check-circle-2' },
          best_guess:     { label: 'Best guess',  tone: 'warn', icon: 'help-circle' },
          catch_all:      { label: 'Catch-all',   tone: 'warn', icon: 'layers' },
          no_mail_server: { label: 'No mail server', tone: 'bad', icon: 'x-circle' },
          not_found:      { label: 'Not found',   tone: 'bad',  icon: 'x-circle' }
        };

        var form = document.getElementById('ef-find-form');
        var btn = document.getElementById('ef-find-btn');
        var working = document.getElementById('ef-working');
        var workingList = document.getElementById('ef-working-list');
        var box = document.getElementById('ef-result');
        var errorBox = document.getElementById('ef-error');
        var copyBtn = document.getElementById('ef-copy');

        function esc(s) {
          return String(s === null || s === undefined ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
          });
        }

        function showError(msg) {
          box.hidden = true;
          working.hidden = true;
          errorBox.textContent = msg;
          errorBox.hidden = false;
        }

        function render(data) {
          errorBox.hidden = true;
          working.hidden = true;

          var meta = STATUS[data.status] || STATUS.not_found;

          if (!data.email) {
            showError(data.reason || 'We could not find an address for that person.');
            return;
          }

          var badge = document.getElementById('ef-result-badge');
          badge.className = 'ef-pill ef-pill--' + meta.tone;
          badge.innerHTML = '<i data-lucide="' + meta.icon + '"></i>' + esc(meta.label);

          document.getElementById('ef-result-confidence').textContent =
            data.confidence ? data.confidence + ' confidence' : '';
          document.getElementById('ef-result-email').textContent = data.email;
          document.getElementById('ef-result-reason').textContent = data.reason || '';

          var bits = [];
          if (data.pattern) { bits.push('Pattern: <b>' + esc(data.pattern) + '@</b>'); }
          if (data.checked) { bits.push('Addresses checked: <b>' + esc(data.checked) + '</b>'); }
          if (data.domain) { bits.push('Domain: <b>' + esc(data.domain) + '</b>'); }
          document.getElementById('ef-result-meta').innerHTML = bits.join('<span class="ef-dot"></span>');

          box.hidden = false;
          if (window.lucide) { lucide.createIcons(); }
        }

        function showWorking(first, last, domain) {
          // A visible, honest preview of what the server is about to try.
          var f = (first || '').toLowerCase().replace(/[^a-z0-9._-]/g, '');
          var l = (last || '').toLowerCase().replace(/[^a-z0-9._-]/g, '');
          var d = (domain || '').toLowerCase().replace(/^https?:\/\//, '').replace(/^www\./, '').split('/')[0];
          if (!f || !d) { return; }

          var locals = l ? [f + '.' + l, f, f.charAt(0) + l, f + l] : [f];
          workingList.innerHTML = locals.map(function (local) {
            return '<span class="ef-chip"><i data-lucide="loader-circle" class="ef-chip-spin"></i>' + esc(local + '@' + d) + '</span>';
          }).join('');
          working.hidden = false;
          if (window.lucide) { lucide.createIcons(); }
        }

        form.addEventListener('submit', function (e) {
          e.preventDefault();

          var first = document.getElementById('ef-first').value.trim();
          var last = document.getElementById('ef-last').value.trim();
          var domain = document.getElementById('ef-domain').value.trim();

          if (!first || !domain) {
            showError('Enter a first name and a company domain.');
            return;
          }

          btn.classList.add('is-busy');
          btn.disabled = true;
          box.hidden = true;
          errorBox.hidden = true;
          showWorking(first, last, domain);

          fetch("{{ route('frontend.email.finder.search') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': "{{ csrf_token() }}",
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ first_name: first, last_name: last, domain: domain })
          })
            .then(function (res) {
              if (res.status === 429) { throw new Error('rate'); }
              if (!res.ok) { throw new Error('http'); }
              return res.json();
            })
            .then(render)
            .catch(function (err) {
              showError(err && err.message === 'rate'
                ? 'That is a lot of lookups in a short time. Give it a minute and try again.'
                : 'Something went wrong running that search. Please try again.');
            })
            .finally(function () {
              btn.classList.remove('is-busy');
              btn.disabled = false;
            });
        });

        copyBtn.addEventListener('click', function () {
          var value = document.getElementById('ef-result-email').textContent;
          if (!value) { return; }
          var done = function () {
            var label = copyBtn.querySelector('span');
            var original = label.textContent;
            label.textContent = 'Copied';
            setTimeout(function () { label.textContent = original; }, 1600);
          };
          if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(value).then(done).catch(function () {});
          }
        });
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
      } else {
        init();
      }
    })();
  </script>
@endpush
