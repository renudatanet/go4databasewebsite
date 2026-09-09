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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('assets/frontend/css/email-verifier.css')}}">
@endpush

@section('content')
<div class="ev-wrap">

  <!-- Hero + live checker -->
  <div class="ev-hero">
    <span class="ev-badge">
      <i data-lucide="zap"></i> Real-time, single email checker
    </span>
    <h1 class="ev-title">Verify Any Email in <span class="highlight">Seconds</span></h1>
    <p class="ev-subtitle">Paste an address below and we'll check its syntax, domain, and live mailbox status, no test email is ever actually sent.</p>

    <div class="ev-tool-card">
      <form id="ev-check-form" class="ev-tool-form" autocomplete="off">
        <i data-lucide="mail" class="ev-tool-icon"></i>
        <input type="email" id="ev-email-input" class="ev-tool-input" placeholder="name@company.com" required>
        <button type="submit" id="ev-check-btn" class="ev-tool-btn">
          <span class="ev-btn-label">Verify Email</span>
          <i data-lucide="loader-2" class="ev-btn-spinner"></i>
        </button>
      </form>
      <div id="ev-result" class="ev-result" hidden>
        <div class="ev-result-top">
          <span id="ev-result-badge" class="ev-result-badge"></span>
          <span id="ev-result-email" class="ev-result-email"></span>
        </div>
        <p id="ev-result-reason" class="ev-result-reason"></p>
        <div id="ev-result-checks" class="ev-result-checks"></div>
      </div>
      <div id="ev-error" class="ev-tool-error" hidden></div>
    </div>

    <p class="ev-hero-note">Free to use &middot; No signup required for single checks</p>
  </div>

  <div class="ev-trust-row">
    <div class="ev-trust-item"><i data-lucide="mail-x"></i> No email is ever delivered</div>
    <div class="ev-trust-item"><i data-lucide="shield-check"></i> GDPR-aware</div>
    <div class="ev-trust-item"><i data-lucide="lock"></i> SSL Secured</div>
    <div class="ev-trust-item"><i data-lucide="server"></i> Live mail server check</div>
  </div>

  <!-- How it works: checks -->
  <section class="ev-section ev-checklist-section">
    <div class="ev-section-head">
      <div class="ev-section-eyebrow">How Our Email Checker Works</div>
      <h2 class="ev-section-title">What We Check</h2>
      <p class="ev-section-sub">Every address goes through the same layered scan before we give you a verdict.</p>
    </div>
    <div class="ev-checklist-grid">
      @foreach([
        'Syntax validation',
        'MX record lookup',
        'Domain health',
        'Disposable email detection',
        'Role account detection',
        'Free provider detection',
        'Catch-all detection',
        'Live SMTP mailbox check',
        'Hard bounce prediction',
      ] as $check)
        <div class="ev-check-item"><i data-lucide="check-circle-2"></i> {{ $check }}</div>
      @endforeach
    </div>
  </section>

  <!-- How it works: outcomes -->
  <section class="ev-section" style="padding-top:0;">
    <div class="ev-section-head">
      <div class="ev-section-eyebrow">Reading Your Result</div>
      <h2 class="ev-section-title">What Each Status Means</h2>
    </div>
    <div class="ev-outcome-grid">
      <div class="ev-outcome-card">
        <span class="ev-status-pill status-valid">Valid</span>
        <p>The mailbox exists and is safe to send to.</p>
      </div>
      <div class="ev-outcome-card">
        <span class="ev-status-pill status-invalid">Invalid</span>
        <p>The address doesn't exist or the domain can't receive mail.</p>
      </div>
      <div class="ev-outcome-card">
        <span class="ev-status-pill status-catch_all">Catch-All</span>
        <p>The domain accepts mail for any address, we can't fully confirm this one mailbox.</p>
      </div>
      <div class="ev-outcome-card">
        <span class="ev-status-pill status-disposable">Disposable</span>
        <p>A known temporary/throwaway email provider.</p>
      </div>
      <div class="ev-outcome-card">
        <span class="ev-status-pill status-role_based">Role-Based</span>
        <p>A shared inbox like info@ or support@, not a named person.</p>
      </div>
      <div class="ev-outcome-card">
        <span class="ev-status-pill status-unknown">Unknown</span>
        <p>Syntax and domain look fine, but the live mailbox couldn't be confirmed right now.</p>
      </div>
    </div>
  </section>

  <!-- Why you need it -->
  <section class="ev-section">
    <div class="ev-section-head">
      <div class="ev-section-eyebrow">Why It Matters</div>
      <h2 class="ev-section-title">Why You Need Email Verification</h2>
      <p class="ev-section-sub">A single bad send can hurt your sender reputation for months. Here's what's really at stake.</p>
    </div>
    <div class="ev-why-grid">
      <div class="ev-why-card">
        <div class="ev-why-step">1</div>
        <h4>Invalid emails bounce</h4>
        <p>Every bad address you send to comes back as a hard bounce, wasted send, wasted opportunity.</p>
        <i data-lucide="arrow-right" class="ev-why-arrow"></i>
      </div>
      <div class="ev-why-card">
        <div class="ev-why-step">2</div>
        <h4>Bounces trigger spam filters</h4>
        <p>Mailbox providers watch your bounce rate closely, high bounces flag you as a likely spammer.</p>
        <i data-lucide="arrow-right" class="ev-why-arrow"></i>
      </div>
      <div class="ev-why-card">
        <div class="ev-why-step">3</div>
        <h4>Reputation blocks delivery</h4>
        <p>Once your sender reputation drops, even your good emails stop reaching the inbox.</p>
        <i data-lucide="arrow-right" class="ev-why-arrow"></i>
      </div>
      <div class="ev-why-card">
        <div class="ev-why-step">4</div>
        <h4>Clean lists fix all three</h4>
        <p>Verified lists mean better delivery, higher open rates, and a lower cost per real lead.</p>
      </div>
    </div>
  </section>

  <!-- Pricing -->
  <section class="ev-section">
    <div class="ev-section-head">
      <div class="ev-section-eyebrow">Pricing</div>
      <h2 class="ev-section-title">Pay Only For What You Verify</h2>
      <p class="ev-section-sub">No subscriptions. Buy credits, use them whenever you need to clean a list.</p>
    </div>
    <div class="ev-pricing-grid">

      <div class="ev-price-card">
        <div class="ev-price-name">Free Trial</div>
        <div class="ev-price-amount">$0</div>
        <div class="ev-price-contacts">1,000 contacts</div>
        <ul class="ev-price-features">
          <li><i data-lucide="check"></i> Domain filtration</li>
          <li><i data-lucide="check"></i> Real-time verification</li>
          <li><i data-lucide="check"></i> Unlimited downloads</li>
          <li><i data-lucide="check"></i> 1 user / organization</li>
        </ul>
        <a href="https://app.go4database.com/register" target="_blank" class="ev-price-btn">Start Free</a>
      </div>

      <div class="ev-price-card popular">
        <div class="ev-price-ribbon">MOST POPULAR</div>
        <div class="ev-price-name">Nano</div>
        <div class="ev-price-amount">$49</div>
        <div class="ev-price-contacts">100,000 contacts</div>
        <ul class="ev-price-features">
          <li><i data-lucide="check"></i> Domain filtration</li>
          <li><i data-lucide="check"></i> Real-time verification</li>
          <li><i data-lucide="check"></i> Unlimited downloads</li>
          <li><i data-lucide="check"></i> 1 user / organization</li>
        </ul>
        <a href="https://app.go4database.com/register?plan=nano" target="_blank" class="ev-price-btn solid">Get Started</a>
      </div>

      <div class="ev-price-card">
        <div class="ev-price-name">Micro</div>
        <div class="ev-price-amount">$200</div>
        <div class="ev-price-contacts">1,000,000 contacts</div>
        <ul class="ev-price-features">
          <li><i data-lucide="check"></i> Domain filtration</li>
          <li><i data-lucide="check"></i> Real-time verification</li>
          <li><i data-lucide="check"></i> Unlimited downloads</li>
          <li><i data-lucide="check"></i> 1 user / organization</li>
        </ul>
        <a href="https://app.go4database.com/register?plan=micro" target="_blank" class="ev-price-btn">Get Started</a>
      </div>

    </div>
    <p class="ev-pricing-note">Need more than 1M contacts? <a href="{{route('frontend.contact')}}" style="color:var(--primary-green-dark);font-weight:600;">Contact sales</a> for a custom plan.</p>
  </section>

  @if(($all_testimonial ?? collect())->isNotEmpty())
  <!-- Testimonials -->
  <section class="ev-section" style="padding-top:0;">
    <div class="ev-section-head">
      <div class="ev-section-eyebrow">Social Proof</div>
      <h2 class="ev-section-title">What Our Users Say</h2>
    </div>
    <div class="ev-testimonial-grid">
      @foreach($all_testimonial as $item)
        <div class="ev-testimonial-card">
          <div class="ev-testimonial-stars">
            @for($i=0;$i<5;$i++)
              <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            @endfor
          </div>
          <p class="ev-testimonial-text">{{ \Illuminate\Support\Str::limit(strip_tags($item->description), 180) }}</p>
          <div class="ev-testimonial-person">
            @if(!empty($item->image))
              <img class="ev-testimonial-avatar" src="{{asset('assets/uploads/'.$item->image)}}" alt="{{$item->name}}">
            @else
              <div class="ev-testimonial-avatar"></div>
            @endif
            <div>
              <div class="ev-testimonial-name">{{$item->name}}</div>
              <div class="ev-testimonial-role">{{$item->designation}}</div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>
  @endif

  <!-- FAQ -->
  <section class="ev-section" style="padding-top:0;max-width:800px;">
    <div class="ev-section-head">
      <div class="ev-section-eyebrow">Questions</div>
      <h2 class="ev-section-title">Frequently Asked Questions</h2>
    </div>
    <div class="ev-faq-accordion">
      <div class="ev-faq-item">
        <button type="button" class="ev-faq-question"><span>Is checking a single email free?</span><i data-lucide="chevron-down"></i></button>
        <div class="ev-faq-answer"><p>Yes. Single email checks on this page are free and don't require signing up. Bulk list verification uses credits from a paid plan.</p></div>
      </div>
      <div class="ev-faq-item">
        <button type="button" class="ev-faq-question"><span>Do you actually send an email to check it?</span><i data-lucide="chevron-down"></i></button>
        <div class="ev-faq-answer"><p>No. We connect to the recipient's mail server and start the delivery handshake, then stop before the message is actually sent. No email ever lands in the inbox.</p></div>
      </div>
      <div class="ev-faq-item">
        <button type="button" class="ev-faq-question"><span>Why did I get "Unknown" instead of Valid or Invalid?</span><i data-lucide="chevron-down"></i></button>
        <div class="ev-faq-answer"><p>Some mail servers block or rate-limit this kind of live check. When that happens we can't fully confirm the mailbox, so we report Unknown rather than guessing.</p></div>
      </div>
      <div class="ev-faq-item">
        <button type="button" class="ev-faq-question"><span>What does "Catch-All" mean?</span><i data-lucide="chevron-down"></i></button>
        <div class="ev-faq-answer"><p>Some domains accept mail sent to any address at all, even ones that don't really exist. When we detect that, we can't confirm your specific mailbox is real, so we flag it as Catch-All.</p></div>
      </div>
      <div class="ev-faq-item">
        <button type="button" class="ev-faq-question"><span>Is my data stored?</span><i data-lucide="chevron-down"></i></button>
        <div class="ev-faq-answer"><p>Single checks on this page are processed in real time and are not saved to a database.</p></div>
      </div>
    </div>
  </section>

  <!-- Final CTA -->
  <div class="ev-cta-banner">
    <h2>Ready to clean your whole list?</h2>
    <p>Verify thousands of addresses at once, no credit card, no commitment.</p>
    <a href="https://app.go4database.com/register" target="_blank" class="ev-btn-primary">
      <i data-lucide="sparkles"></i> Try 100 Free Credits
    </a>
  </div>

</div>
@endsection

@push('script')
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.lucide) { lucide.createIcons(); }

      // FAQ accordion
      document.querySelectorAll('.ev-faq-item').forEach(function (item) {
        item.querySelector('.ev-faq-question').addEventListener('click', function () {
          var isOpen = item.classList.contains('active');
          document.querySelectorAll('.ev-faq-item').forEach(function (i) { i.classList.remove('active'); });
          if (!isOpen) { item.classList.add('active'); }
        });
      });

      // Live email checker
      var statusLabels = {
        valid: 'Valid',
        invalid: 'Invalid',
        catch_all: 'Catch-All',
        disposable: 'Disposable',
        role_based: 'Role-Based',
        unknown: 'Unknown'
      };
      var checkLabels = {
        syntax: 'Syntax',
        mx_record: 'MX Record',
        disposable: 'Disposable',
        free_provider: 'Free Provider',
        role_based: 'Role-Based',
        smtp: 'Live Mailbox',
        catch_all: 'Catch-All'
      };

      var form = document.getElementById('ev-check-form');
      var input = document.getElementById('ev-email-input');
      var btn = document.getElementById('ev-check-btn');
      var resultBox = document.getElementById('ev-result');
      var errorBox = document.getElementById('ev-error');
      var badgeEl = document.getElementById('ev-result-badge');
      var emailEl = document.getElementById('ev-result-email');
      var reasonEl = document.getElementById('ev-result-reason');
      var checksEl = document.getElementById('ev-result-checks');

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var email = input.value.trim();
        if (!email) { return; }

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
          badgeEl.textContent = statusLabels[data.status] || data.status;
          badgeEl.className = 'ev-result-badge status-' + data.status;
          emailEl.textContent = data.email;
          reasonEl.textContent = data.reason;

          checksEl.innerHTML = '';
          Object.keys(data.checks || {}).forEach(function (key) {
            var val = data.checks[key];
            var passed = (val === true || val === 'accepted' || val === 'no');
            var neutral = (val === 'unknown' || val === 'skipped');
            var span = document.createElement('span');
            span.className = 'ev-check-pill ' + (neutral ? 'neutral' : (passed ? 'pass' : 'fail'));
            span.textContent = checkLabels[key] || key;
            checksEl.appendChild(span);
          });

          resultBox.hidden = false;
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
    });
  </script>
@endpush
