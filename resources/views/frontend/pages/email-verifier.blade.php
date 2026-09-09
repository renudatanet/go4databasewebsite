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
        <span class="ev-eyebrow-dot"></span> Live checker, results in seconds
      </span>

      <h1 class="ev-h1">Know if an email is real<br>before you <em>hit send</em></h1>
      <p class="ev-hero-sub">We check the syntax, the domain, and the live mailbox itself, then tell you plainly whether it's safe to send. No test email is ever delivered.</p>

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
            <span><i data-lucide="check"></i> Free to use</span>
            <span><i data-lucide="check"></i> No signup needed</span>
            <span><i data-lucide="check"></i> Nothing is stored</span>
          </div>
        </div>
      </div>

      <div class="ev-strip">
        <div class="ev-strip-item"><i data-lucide="mail-x"></i> No email ever delivered</div>
        <div class="ev-strip-item"><i data-lucide="server"></i> Live mail server check</div>
        <div class="ev-strip-item"><i data-lucide="shield-check"></i> GDPR-aware</div>
        <div class="ev-strip-item"><i data-lucide="lock"></i> SSL secured</div>
      </div>
    </div>
  </section>

  {{-- ===================== WHAT WE CHECK ===================== --}}
  <section class="ev-band">
    <div class="ev-shell">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">How it works</span>
        <h2 class="ev-h2">Nine checks on every address</h2>
        <p class="ev-lead">Each address runs through the same layered scan, from a simple format check all the way to a live conversation with the receiving mail server.</p>
      </div>

      <div class="ev-grid-3">
        @foreach([
          ['spell-check','Syntax validation','Catches typos and malformed addresses before they ever cost you a send.'],
          ['server','MX record lookup','Confirms the domain actually has mail servers configured to receive email.'],
          ['globe','Domain health','Verifies the domain resolves properly and is set up to accept mail.'],
          ['trash-2','Disposable detection','Flags throwaway inboxes built to disappear within minutes of signup.'],
          ['users','Role account detection','Identifies shared inboxes like info@ and support@ that skew engagement.'],
          ['at-sign','Free provider detection','Tells you when an address is personal rather than a company domain.'],
          ['layers','Catch-all detection','Spots domains that accept everything, so you know when a result is unconfirmed.'],
          ['plug-zap','Live mailbox check','Connects to the real mail server and asks whether that mailbox exists.'],
          ['shield-check','Bounce prediction','Combines every signal above into one clear verdict you can act on.'],
        ] as $c)
          <div class="ev-card ev-rv">
            <div class="ev-card-ico"><i data-lucide="{{$c[0]}}"></i></div>
            <h4>{{$c[1]}}</h4>
            <p>{{$c[2]}}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===================== RESULT GLOSSARY ===================== --}}
  <section class="ev-band ev-band--soft ev-band--tight">
    <div class="ev-shell">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">Reading your result</span>
        <h2 class="ev-h2">What each status means</h2>
      </div>

      <div class="ev-outcomes">
        @foreach([
          ['valid','Valid','check-circle-2','The mailbox exists and is safe to send to.'],
          ['invalid','Invalid','x-circle',"The address doesn't exist, or the domain can't receive mail at all."],
          ['catch_all','Catch-All','layers','The domain accepts mail for any address, so this one mailbox stays unconfirmed.'],
          ['disposable','Disposable','trash-2','A known temporary or throwaway email provider.'],
          ['role_based','Role-Based','users','A shared inbox like info@ or support@ rather than a named person.'],
          ['unknown','Unknown','help-circle','Format and domain look fine, but the mailbox itself could not be confirmed right now.'],
        ] as $o)
          <div class="ev-outcome ev-rv" data-s="{{$o[0]}}">
            <span class="ev-pill status-{{$o[0]}}"><i data-lucide="{{$o[2]}}"></i> {{$o[1]}}</span>
            <p>{{$o[3]}}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===================== WHY IT MATTERS ===================== --}}
  <section class="ev-band">
    <div class="ev-shell">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">Why it matters</span>
        <h2 class="ev-h2">One bad list can cost you months</h2>
        <p class="ev-lead">Sender reputation is slow to build and fast to lose. Here's the chain reaction a dirty list sets off.</p>
      </div>

      <div class="ev-flow">
        <div class="ev-step ev-rv">
          <div class="ev-step-n">1</div>
          <h4>Invalid emails bounce</h4>
          <p>Every bad address comes straight back as a hard bounce, a wasted send and a wasted opportunity.</p>
        </div>
        <div class="ev-step ev-rv">
          <div class="ev-step-n">2</div>
          <h4>Bounces trip the filters</h4>
          <p>Mailbox providers watch your bounce rate closely. A high one marks you as a likely spammer.</p>
        </div>
        <div class="ev-step ev-rv">
          <div class="ev-step-n">3</div>
          <h4>Reputation blocks delivery</h4>
          <p>Once your sender reputation drops, even your genuinely good emails stop reaching the inbox.</p>
        </div>
        <div class="ev-step ev-step--good ev-rv">
          <div class="ev-step-n"><i data-lucide="check" style="width:16px;height:16px;stroke-width:3.5"></i></div>
          <h4>Clean lists fix all three</h4>
          <p>Verified lists mean better delivery, higher open rates and a lower cost per real lead.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ===================== PRICING ===================== --}}
  <section class="ev-band ev-band--soft">
    <div class="ev-shell">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">Pricing</span>
        <h2 class="ev-h2">Pay only for what you verify</h2>
        <p class="ev-lead">No subscription. Buy credits once and use them whenever a list needs cleaning.</p>
      </div>

      <div class="ev-plans">
        <div class="ev-plan ev-rv">
          <div class="ev-plan-name">Free Trial</div>
          <div class="ev-plan-price"><span class="ev-plan-cur">$</span><span class="ev-plan-num">0</span></div>
          <div class="ev-plan-vol">1,000 contacts</div>
          <div class="ev-plan-sep"></div>
          <ul class="ev-plan-feats">
            <li><i data-lucide="check"></i> Domain filtration</li>
            <li><i data-lucide="check"></i> Real-time verification</li>
            <li><i data-lucide="check"></i> Unlimited downloads</li>
            <li><i data-lucide="check"></i> 1 user / organization</li>
          </ul>
          <a href="https://app.go4database.com/register" target="_blank" rel="noopener" class="ev-btn ev-btn--ghost">Start free</a>
        </div>

        <div class="ev-plan ev-plan--hero ev-rv">
          <span class="ev-plan-tag">Most popular</span>
          <div class="ev-plan-name">Nano</div>
          <div class="ev-plan-price"><span class="ev-plan-cur">$</span><span class="ev-plan-num">49</span></div>
          <div class="ev-plan-vol">100,000 contacts</div>
          <div class="ev-plan-sep"></div>
          <ul class="ev-plan-feats">
            <li><i data-lucide="check"></i> Domain filtration</li>
            <li><i data-lucide="check"></i> Real-time verification</li>
            <li><i data-lucide="check"></i> Unlimited downloads</li>
            <li><i data-lucide="check"></i> 1 user / organization</li>
          </ul>
          <a href="https://app.go4database.com/register?plan=nano" target="_blank" rel="noopener" class="ev-btn ev-btn--solid">Get started <i data-lucide="arrow-right"></i></a>
        </div>

        <div class="ev-plan ev-rv">
          <div class="ev-plan-name">Micro</div>
          <div class="ev-plan-price"><span class="ev-plan-cur">$</span><span class="ev-plan-num">200</span></div>
          <div class="ev-plan-vol">1,000,000 contacts</div>
          <div class="ev-plan-sep"></div>
          <ul class="ev-plan-feats">
            <li><i data-lucide="check"></i> Domain filtration</li>
            <li><i data-lucide="check"></i> Real-time verification</li>
            <li><i data-lucide="check"></i> Unlimited downloads</li>
            <li><i data-lucide="check"></i> 1 user / organization</li>
          </ul>
          <a href="https://app.go4database.com/register?plan=micro" target="_blank" rel="noopener" class="ev-btn ev-btn--ghost">Get started</a>
        </div>
      </div>

      <p class="ev-plan-note ev-rv">Need more than a million contacts? <a href="{{route('frontend.contact')}}">Talk to sales</a> about a custom volume.</p>
    </div>
  </section>

  {{-- ===================== TESTIMONIALS ===================== --}}
  @if(($all_testimonial ?? collect())->isNotEmpty())
  <section class="ev-band ev-band--tight">
    <div class="ev-shell">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">Customers</span>
        <h2 class="ev-h2">What our users say</h2>
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
  <section class="ev-band ev-band--soft ev-band--tight">
    <div class="ev-shell-narrow">
      <div class="ev-head ev-head--center ev-rv">
        <span class="ev-kicker">Questions</span>
        <h2 class="ev-h2">Frequently asked</h2>
      </div>

      <div class="ev-faq ev-rv">
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
            <div class="ev-faq-a"><p>{{$f[1]}}</p></div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===================== CLOSING CTA ===================== --}}
  <section class="ev-cta">
    <div class="ev-shell">
      <h2>Ready to clean your whole list?</h2>
      <p>Verify thousands of addresses at once and send with confidence.</p>
      <a href="https://app.go4database.com/register" target="_blank" rel="noopener" class="ev-btn ev-btn--light ev-btn--inline">
        <i data-lucide="sparkles"></i> Try 100 free credits
      </a>
      <p class="ev-cta-note">No credit card required</p>
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
