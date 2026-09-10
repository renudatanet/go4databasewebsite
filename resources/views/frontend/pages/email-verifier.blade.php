@extends('frontend.frontend-page-master')
@section('site-title')
    Email Verifier
@endsection
@section('page-title')
    Email Verifier
@endsection
@section('page-meta-data')
    <meta name="description" content="Verify any email address in seconds. Check syntax, domain, and live mailbox status before you send, no test email is ever delivered.">
@endsection
@push('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('assets/frontend/css/email-verifier.css')}}">
@endpush

@section('content')
<div class="ev">

  {{-- ===================== HERO ===================== --}}
  <section class="ev-hero">
    <div class="ev-hero-inner">
      <span class="ev-eyebrow">
        <span class="ev-eyebrow-dot"></span> {{ $ev['hero_badge'] }}
      </span>

      <h1 class="ev-h1">{{ $ev['hero_title'] }} <em>{{ $ev['hero_title_highlight'] }}</em></h1>
      <p class="ev-hero-sub">{{ $ev['hero_subtitle'] }}</p>

      <div class="ev-tool">
        <div class="ev-tool-inner">
          <form id="ev-check-form" class="ev-tool-form" autocomplete="off" novalidate>
            <i data-lucide="mail" class="ev-tool-glyph"></i>
            <input type="email" id="ev-email-input" class="ev-tool-input" placeholder="name@company.com" required aria-label="Email address to verify">
            <button type="submit" id="ev-check-btn" class="ev-tool-btn">
              <span class="ev-btn-label">Verify email</span>
              <i data-lucide="loader-circle" class="ev-spin"></i>
            </button>
          </form>

          <div id="ev-result" class="ev-result" hidden>
            <div class="ev-result-top">
              <span id="ev-result-badge" class="ev-pill"></span>
              <span id="ev-result-email" class="ev-result-email"></span>
            </div>
            <p id="ev-result-reason" class="ev-result-reason"></p>
            <div id="ev-result-checks" class="ev-result-checks"></div>
          </div>

          <div id="ev-error" class="ev-alert" hidden></div>

          <div class="ev-tool-foot">
            @foreach(array_filter(array_map('trim', explode(',', $ev['tool_foot']))) as $foot)
              <span><i data-lucide="check"></i> {{ $foot }}</span>
            @endforeach
          </div>
        </div>
      </div>

      @if(($ev_items['trust'] ?? collect())->isNotEmpty())
      <div class="ev-strip">
        @foreach($ev_items['trust'] as $trust)
          <div class="ev-strip-item"><i data-lucide="{{ $trust->icon ?: 'check' }}"></i> {{ $trust->title }}</div>
        @endforeach
      </div>
      @endif
    </div>
  </section>

  {{-- ===================== FREE CREDITS OFFER ===================== --}}
  @if(trim($ev['offer_title']) !== '' || trim($ev['offer_btn']) !== '')
  <section class="ev-offer">
    <div class="ev-shell">
      <div class="ev-offer-card ev-rv">
        <div class="ev-offer-tx">
          <h2 class="ev-offer-title">{{ $ev['offer_title'] }}</h2>
          @if(trim($ev['offer_text']) !== '')
            <p class="ev-offer-sub">{{ $ev['offer_text'] }}</p>
          @endif
        </div>
        <div class="ev-offer-act">
          <a href="{{ $ev['offer_url'] }}" target="_blank" rel="noopener" class="ev-btn ev-btn--solid ev-btn--inline">
            <i data-lucide="sparkles"></i> {{ $ev['offer_btn'] }}
          </a>
          @if(trim($ev['offer_note']) !== '')
            <span class="ev-offer-note">{{ $ev['offer_note'] }}</span>
          @endif
        </div>
      </div>
    </div>
  </section>
  @endif

  {{-- ===================== WHAT WE CHECK ===================== --}}
  @if(($ev_items['check'] ?? collect())->isNotEmpty())
  <section class="ev-band">
    <div class="ev-shell">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">{{ $ev['checks_kicker'] }}</span>
        <h2 class="ev-h2">{{ $ev['checks_title'] }}</h2>
        <p class="ev-lead">{{ $ev['checks_lead'] }}</p>
      </div>

      <div class="ev-grid-3">
        @foreach($ev_items['check'] as $check)
          <div class="ev-card ev-rv">
            <div class="ev-card-ico"><i data-lucide="{{ $check->icon ?: 'check-circle-2' }}"></i></div>
            <h4>{{ $check->title }}</h4>
            <p>{{ $check->description }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  {{-- ===================== RESULT GLOSSARY ===================== --}}
  @if(($ev_items['glossary'] ?? collect())->isNotEmpty())
  <section class="ev-band ev-band--soft ev-band--tight">
    <div class="ev-shell">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">{{ $ev['glossary_kicker'] }}</span>
        <h2 class="ev-h2">{{ $ev['glossary_title'] }}</h2>
      </div>

      <div class="ev-outcomes">
        @foreach($ev_items['glossary'] as $outcome)
          <div class="ev-outcome ev-rv" data-s="{{ $outcome->badge_key ?: 'unknown' }}">
            <span class="ev-pill status-{{ $outcome->badge_key ?: 'unknown' }}">
              <i data-lucide="{{ $outcome->icon ?: 'help-circle' }}"></i> {{ $outcome->title }}
            </span>
            <p>{{ $outcome->description }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  {{-- ===================== WHY IT MATTERS ===================== --}}
  @if(($ev_items['step'] ?? collect())->isNotEmpty())
  <section class="ev-band">
    <div class="ev-shell">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">{{ $ev['why_kicker'] }}</span>
        <h2 class="ev-h2">{{ $ev['why_title'] }}</h2>
        <p class="ev-lead">{{ $ev['why_lead'] }}</p>
      </div>

      <div class="ev-flow">
        @foreach($ev_items['step'] as $step)
          <div class="ev-step ev-rv @if($step->is_highlight) ev-step--good @endif">
            <div class="ev-step-n">
              @if($step->is_highlight)
                <i data-lucide="check" style="width:16px;height:16px;stroke-width:3.5"></i>
              @else
                {{ $loop->iteration }}
              @endif
            </div>
            <h4>{{ $step->title }}</h4>
            <p>{{ $step->description }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  {{-- ===================== TESTIMONIALS ===================== --}}
  @if(($all_testimonial ?? collect())->isNotEmpty())
  <section class="ev-band ev-band--soft ev-band--tight">
    <div class="ev-shell">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">{{ $ev['testimonial_kicker'] }}</span>
        <h2 class="ev-h2">{{ $ev['testimonial_title'] }}</h2>
      </div>

      <div class="ev-quotes">
        @foreach($all_testimonial as $item)
          <div class="ev-quote ev-rv">
            <span class="ev-quote-mark">&rdquo;</span>
            <div class="ev-stars">
              @for($i=0;$i<5;$i++)
                <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
              @endfor
            </div>
            <p class="ev-quote-tx">{{ \Illuminate\Support\Str::limit(strip_tags($item->description), 180) }}</p>
            <div class="ev-who">
              @if(!empty($item->image))
                <img class="ev-avatar" src="{{asset('assets/uploads/'.$item->image)}}" alt="{{$item->name}}">
              @else
                <div class="ev-avatar"></div>
              @endif
              <div>
                <div class="ev-who-n">{{$item->name}}</div>
                <div class="ev-who-r">{{$item->designation}}</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  {{-- ===================== FAQ ===================== --}}
  <section class="ev-band ev-band--tight">
    <div class="ev-shell-narrow">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">{{ $ev['faq_kicker'] }}</span>
        <h2 class="ev-h2">{{ $ev['faq_title'] }}</h2>
      </div>

      <div class="ev-faq ev-rv">
        @if(($ev_faqs ?? collect())->isNotEmpty())
          @foreach($ev_faqs as $faq)
            <div class="ev-faq-item @if($faq->is_open == 'on') open @endif">
              <button type="button" class="ev-faq-q">
                <span>{{ $faq->title }}</span>
                <span class="ev-faq-ico"><i data-lucide="chevron-down"></i></span>
              </button>
              <div class="ev-faq-a"><div class="ev-faq-body">{!! $faq->description !!}</div></div>
            </div>
          @endforeach
        @else
          @foreach([
            ['Is checking a single email free?','Yes. Single checks on this page are free and need no account. Bulk list verification uses credits from a paid plan.'],
            ['Do you actually send an email to check it?','No. We open the delivery handshake with the recipient\'s mail server and stop before any message is sent. Nothing lands in their inbox.'],
            ['Why did I get "Unknown" instead of Valid or Invalid?','Some mail servers block or rate-limit this kind of live check, especially for senders they don\'t recognise. When we can\'t confirm the mailbox, we say Unknown rather than guess.'],
            ['What does "Catch-All" mean?','Some domains accept mail for any address at all, even ones that don\'t exist. When we detect that, your specific mailbox can\'t be confirmed, so we flag it rather than call it valid.'],
            ['Is my data stored?','Single checks on this page are processed in real time and are not written to a database.'],
          ] as $f)
            <div class="ev-faq-item">
              <button type="button" class="ev-faq-q">
                <span>{{$f[0]}}</span>
                <span class="ev-faq-ico"><i data-lucide="chevron-down"></i></span>
              </button>
              <div class="ev-faq-a"><div class="ev-faq-body">{{$f[1]}}</div></div>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </section>

  {{-- ===================== CLOSING CTA ===================== --}}
  <section class="ev-cta">
    <div class="ev-shell">
      <h2>{{ $ev['cta_title'] }}</h2>
      <p>{{ $ev['cta_text'] }}</p>
      <a href="{{ $ev['cta_url'] }}" target="_blank" rel="noopener" class="ev-btn ev-btn--light ev-btn--inline">
        <i data-lucide="sparkles"></i> {{ $ev['cta_btn'] }}
      </a>
      <p class="ev-cta-note">{{ $ev['cta_note'] }}</p>
    </div>
  </section>

</div>
@endsection

@push('script')
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    (function () {
      function init() {
        if (window.lucide) { lucide.createIcons(); }

        /* ---------- scroll reveal ---------- */
        var revealables = document.querySelectorAll('.ev-rv');
        if ('IntersectionObserver' in window) {
          var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry, i) {
              if (entry.isIntersecting) {
                var el = entry.target;
                var delay = Math.min(i * 60, 240);
                setTimeout(function () { el.classList.add('in'); }, delay);
                io.unobserve(el);
              }
            });
          }, { rootMargin: '0px 0px -60px 0px', threshold: 0.08 });
          revealables.forEach(function (el) { io.observe(el); });
        } else {
          revealables.forEach(function (el) { el.classList.add('in'); });
        }

        /* ---------- FAQ ---------- */
        document.querySelectorAll('.ev-faq-item').forEach(function (item) {
          item.querySelector('.ev-faq-q').addEventListener('click', function () {
            var wasOpen = item.classList.contains('open');
            document.querySelectorAll('.ev-faq-item').forEach(function (i) { i.classList.remove('open'); });
            if (!wasOpen) { item.classList.add('open'); }
          });
        });

        /* ---------- live checker ---------- */
        var STATUS = {
          valid:       { label: 'Valid',       icon: 'check-circle-2' },
          invalid:     { label: 'Invalid',     icon: 'x-circle' },
          catch_all:   { label: 'Catch-All',   icon: 'layers' },
          disposable:  { label: 'Disposable',  icon: 'trash-2' },
          role_based:  { label: 'Role-Based',  icon: 'users' },
          unknown:     { label: 'Unknown',     icon: 'help-circle' }
        };
        var CHECKS = {
          syntax: 'Syntax',
          mx_record: 'MX record',
          disposable: 'Not disposable',
          free_provider: 'Company domain',
          role_based: 'Personal inbox',
          smtp: 'Live mailbox',
          catch_all: 'Not catch-all'
        };
        /* attributes worth flagging, but not failures: being on Gmail or being a
           shared inbox is information, not an error, so these show amber not red */
        var ADVISORY = { free_provider: true, role_based: true, catch_all: true };

        var form = document.getElementById('ev-check-form');
        var input = document.getElementById('ev-email-input');
        var btn = document.getElementById('ev-check-btn');
        var resultBox = document.getElementById('ev-result');
        var errorBox = document.getElementById('ev-error');
        var badgeEl = document.getElementById('ev-result-badge');
        var emailEl = document.getElementById('ev-result-email');
        var reasonEl = document.getElementById('ev-result-reason');
        var checksEl = document.getElementById('ev-result-checks');

        function verdictFor(key, val) {
          if (val === 'unknown' || val === 'skipped') { return 'neutral'; }
          if (key === 'catch_all') { return val === 'no' ? 'pass' : 'warn'; }
          if (key === 'smtp') { return val === 'accepted' ? 'pass' : 'fail'; }
          if (ADVISORY[key]) { return val ? 'warn' : 'pass'; }
          if (key === 'disposable') { return val ? 'fail' : 'pass'; }
          return val ? 'pass' : 'fail';
        }

        form.addEventListener('submit', function (e) {
          e.preventDefault();
          var email = input.value.trim();
          if (!email) { input.focus(); return; }

          btn.disabled = true;
          btn.classList.add('loading');
          errorBox.hidden = true;
          resultBox.hidden = true;

          fetch("{{ route('frontend.email.verifier.check') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': "{{ csrf_token() }}",
              'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
          })
          .then(function (res) {
            if (!res.ok) { throw new Error('request-failed'); }
            return res.json();
          })
          .then(function (data) {
            var meta = STATUS[data.status] || { label: data.status, icon: 'help-circle' };

            resultBox.setAttribute('data-status', data.status);
            badgeEl.className = 'ev-pill status-' + data.status;
            badgeEl.innerHTML = '<i data-lucide="' + meta.icon + '"></i>' + meta.label;
            emailEl.textContent = data.email;
            reasonEl.textContent = data.reason;

            checksEl.innerHTML = '';
            Object.keys(data.checks || {}).forEach(function (key) {
              if (!CHECKS[key]) { return; }
              var verdict = verdictFor(key, data.checks[key]);
              var glyph = verdict === 'pass' ? 'check' : (verdict === 'neutral' ? 'minus' : 'x');
              var row = document.createElement('div');
              row.className = 'ev-cq ' + verdict;
              row.innerHTML = '<span class="ev-cq-mark"><i data-lucide="' + glyph + '"></i></span>' + CHECKS[key];
              checksEl.appendChild(row);
            });

            resultBox.hidden = false;
            if (window.lucide) { lucide.createIcons(); }
          })
          .catch(function () {
            errorBox.textContent = 'Something went wrong checking that address. Please try again.';
            errorBox.hidden = false;
          })
          .finally(function () {
            btn.disabled = false;
            btn.classList.remove('loading');
          });
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
