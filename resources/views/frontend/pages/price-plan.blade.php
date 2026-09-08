@extends('frontend.frontend-page-master')
@section('site-title')

    @if(get_static_option('price_plan_page_'.$user_select_lang_slug.'_meta_tags')!='')
        {{get_static_option('price_plan_page_'.$user_select_lang_slug.'_meta_tags')}}
    @else
        {{get_static_option('price_plan_page_'.$user_select_lang_slug.'_name')}}
    @endif    
     
@endsection
@section('page-title')
    {{get_static_option('price_plan_page_'.$user_select_lang_slug.'_name')}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{get_static_option('price_plan_page_'.$user_select_lang_slug.'_meta_description')}}">
    {{-- <meta name="tags" content="{{get_static_option('price_plan_page_'.$user_select_lang_slug.'_meta_tags')}}"> --}}
    {!! render_og_meta_image_by_attachment_id(get_static_option('price_plan_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection
@push('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{asset('assets/frontend/css/pricing.css')}}">
@endpush
@section('content')

 <!-- Header Section -->
  <header class="header">
    <div class="breadcrumb">
      <a href="#">Home</a> <i data-lucide="chevron-right" class="breadcrumb-icon"></i> <span>Plan</span>
    </div>
    <h2 class="main-title">Choose the <span class="highlight">Right Plan</span> for You</h2>
    <p class="subtitle">Find verified B2B contacts, faster. Cancel anytime.</p>

    <!-- Toggle Container -->
    <div class="toggle-container">
      <div class="toggle-wrapper">
        <button id="toggle-annual" class="toggle-btn active">Annual</button>
        <button id="toggle-monthly" class="toggle-btn">Monthly</button>
      </div>
    </div>
  </header>

  <!-- Pricing Cards Grid -->
  <main class="pricing-container">
    <div class="pricing-grid">
     @php $a = 1; @endphp
@foreach($all_price_plan as $key => $price_plan)
@foreach($price_plan as $index => $data)
      <!-- FREE PLAN -->
      <div class="pricing-card  @if(!empty($data->highlight))  selected @endif" id="card-free">
           @if(!empty($data->highlight))  
           <div class="popular-ribbon">
          <i data-lucide="sparkles" class="ribbon-icon"></i> BEST SELLING
        </div>
         @endif
        <div class="card-header">
          <div class="badge-row">
            <span class="plan-badge">{{$data->title}}</span>
            @if(!empty($data->highlight)) 
            <span class="status-badge"><i data-lucide="check" class="status-icon"></i> Active Plan</span>
            @endif
          </div>
          <p class="plan-desc">Perfect for getting started</p>
           @if($data->monthly_original_price != -1)
          <div class="price-container">
              
              
            <span class="price-symbol">$</span>
            
            <span class="price-amount" data-monthly="{{$data->monthly_price}}" data-annual="{{$data->annual_price}}">{{$data->annual_price}}</span>
            <del class="original-price" data-monthly="${{$data->monthly_original_price}}" data-annual="${{$data->annual_original_price}}">${{$data->annual_original_price}}</del>
         
            <span class="price-period" data-monthly="/Monthly" data-annual="/Monthly">/Monthly</span>
          </div>
           @else
            <div class="price-container lets-talk-container">
            <span class="price-amount lets-talk-text" data-monthly="Let's Talk" data-annual="Let's Talk">Let's
              Talk</span>
          </div>
          @endif
          <p class="price-sub bill-desc" data-monthly="{{$data->monthly_bill_text}}" data-annual="{{$data->annual_bill_text}}">{{$data->annual_bill_text}}</p>
        </div>
        
        <hr class="card-divider">
        <div class="card-features">
          <ul>
               @php
                                                $features = explode("\n",$data->features);
                                            @endphp
                                            @foreach($features as $item)
                                              <li class="feature-item active">
              <i data-lucide="check" class="feature-icon check"></i>{{$item}}</li>
                                            @endforeach
         
          </ul>
        </div>
<div class="card-footer">

    {{-- 1st Package - Only Selected Button --}}
    @if($a == 1)

        <a href="https://app.go4database.com/register?plan_id={{ $data->id }}&&frequency=annually"
           target="_blank"
           class="plan-button plan-link @if(!empty($data->highlight)) select-btn selected-btn @endif primary-outline-btn" data-plan-id="{{ $data->id }}"  target="_blank" >
            <i data-lucide="check" class="btn-check-icon"></i>
            {{ $data->btn_text }}
        </a>

          <p class="btn-subtext">&nbsp;</p>
    {{-- 2nd & 3rd Package - Button + Text --}}
    @elseif($a == 2 || $a == 3)

        <a href="https://app.go4database.com/register?plan_id={{ $data->id }}&frequency=annually"
class="plan-button plan-link @if(!empty($data->highlight)) select-btn selected-btn @endif primary-outline-btn"
data-plan-id="{{ $data->id }}"  target="_blank" >
            <i data-lucide="check" class="btn-check-icon"></i>
            {{ $data->btn_text }}
        </a>

        <p class="btn-subtext">No credit card required</p>

    {{-- 4th Package - Contact Number --}}
    @elseif($a == 4)

        <a href="tel:+17867852141"
           class="plan-button select-btn primary-outline-btn contact-btn"
           style="text-align:center;display:block;text-decoration:none;">
            +1 786 785 2141
        </a>

        <p class="btn-subtext">Call our sales team anytime</p>

    @endif

</div>

@php $a++; @endphp
      </div>
 @endforeach
 @endforeach
     

      <!-- MEGA PLAN -->
      

    </div>

    <!-- Upgrade Anytime Sub-Notice -->
    <div class="upgrade-notice">
      <i data-lucide="shield-check" class="shield-icon"></i>
      <span>Upgrade anytime. No credit card required to start free trials.</span>
    </div>

    <!-- Trust Stats Grid -->
    <section class="trust-stats">
      <div class="stat-item">
        <div class="stat-icon-wrapper">
          <i data-lucide="percent" class="stat-icon"></i>
        </div>
        <div class="stat-text">
          <h4>95%+</h4>
          <p>Accuracy Rate</p>
        </div>
      </div>
      <div class="stat-item">
        <div class="stat-icon-wrapper">
          <i data-lucide="calendar" class="stat-icon"></i>
        </div>
        <div class="stat-text">
          <h4>Daily</h4>
          <p>Data Updates</p>
        </div>
      </div>
      <div class="stat-item">
        <div class="stat-icon-wrapper">
          <i data-lucide="lock" class="stat-icon"></i>
        </div>
        <div class="stat-text">
          <h4>GDPR</h4>
          <p>Compliant</p>
        </div>
      </div>
      <div class="stat-item">
        <div class="stat-icon-wrapper">
          <i data-lucide="users" class="stat-icon"></i>
        </div>
        <div class="stat-text">
          <h4>300+</h4>
          <p>Happy Customers</p>
        </div>
      </div>
      <div class="stat-item">
        <div class="stat-icon-wrapper">
          <i data-lucide="star" class="stat-icon"></i>
        </div>
        <div class="stat-text">
          <h4>4.8 / 5</h4>
          <p>Customer Rating</p>
        </div>
      </div>
    </section>

    <!-- Money-back & Guarantee Trust Bar -->
    <div class="guarantee-bar">
      <div class="guarantee-text">
        <i data-lucide="check-circle-2" class="green-check"></i>
        <strong>30-Day Money Back Guarantee</strong>
        <span class="divider">•</span>
        <span>Cancel anytime, no questions asked.</span>
      </div>
      <div class="payment-methods">
        <span class="accept-label">We accept:</span>
        <div class="cards-logos">
          <!-- Visa SVG -->
          <svg class="payment-svg visa" viewBox="0 0 36 24" width="36" height="24" title="Visa">
            <rect width="36" height="24" rx="4" fill="#1434CB" />
            <path
              d="M12.4 16.2L14 7h1.6l-1.6 9.2H12.4z M17.6 10.2c-.3-1.1-1.3-1.5-2.4-1.5-2 0-3.1 1.2-3.1 2.8 0 1.8 1.6 1.9 1.6 2.8 0 .3-.3.6-1 .6-.6 0-1.1-.3-1.4-.5l-.3 1.5c.4.2 1.2.4 1.9.4 2 0 3.3-1 3.3-2.8 0-1.7-1.6-1.9-1.6-2.7 0-.3.3-.6.9-.6.5 0 .9.2 1.1.3l.3-1.5zm6.8 2.2l-1-4.8h-1.5L20 16.2h1.7l.3-1h2.1l.2 1H26l-1.6-6z M22.7 14h-.9l.5-1.8.4 1.8z M8.9 11.2L7.3 11l.2-.9h2.9L10.9 16.2H9.3l-1.9-5zm0 0"
              fill="#FFFFFF" />
            <path d="M7.3 10.1L4.8 7H3v.2c.4 1 2.1 5.3 2.7 6.8l2-7.8h1.6L7.3 10.1z" fill="#F7B600" />
          </svg>
          <!-- Mastercard SVG -->
          <svg class="payment-svg mastercard" viewBox="0 0 36 24" width="36" height="24" title="Mastercard">
            <rect width="36" height="24" rx="4" fill="#0F172A" />
            <circle cx="14" cy="12" r="7" fill="#EB001B" />
            <circle cx="22" cy="12" r="7" fill="#F79E1B" fill-opacity="0.85" />
          </svg>
          <!-- American Express SVG -->
          <svg class="payment-svg amex" viewBox="0 0 36 24" width="36" height="24" title="American Express">
            <rect width="36" height="24" rx="4" fill="#017CC3" />
            <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF"
              font-family="'Inter', sans-serif" font-weight="900" font-size="7" letter-spacing="0.5">AMEX</text>
          </svg>
          <!-- Stripe SVG -->
          <svg class="payment-svg stripe" viewBox="0 0 36 24" width="36" height="24" title="Stripe">
            <rect width="36" height="24" rx="4" fill="#635BFF" />
            <path
              d="M12.2 13.9c0-.7.6-1 1.6-1 1 0 1.9.3 2.5.7v-1.6c-.6-.3-1.4-.4-2.2-.4-2 0-3.4 1-3.4 2.8 0 2.7 3.7 2.3 3.7 3.5 0 .8-.7 1.1-1.8 1.1-1.1 0-2.2-.4-2.8-.8v1.7c.7.3 1.7.5 2.7.5 2.1 0 3.5-1 3.5-2.9 0-2.8-3.8-2.4-3.8-3.7z M19.4 11.2v1.7h1.4v1.5h-1.4v4.4c0 .5.2.7.7.7.2 0 .5 0 .6-.1v1.4c-.3.1-.8.2-1.3.2-1.3 0-1.8-.7-1.8-2V14.4h-1v-1.5h1v-1.7zm5.5 2c-.1 0-.2-.1-.2-.1s0 0 0 0c-.8 0-1.4.5-1.7 1.1v-1.3h-1.6v8.4h1.7v-4.1c0-1.3.9-2.1 1.8-2.1.2 0 .4 0 .5.1V13.2z M27.8 9.9c0-.6-.4-1-1-1s-1 .4-1 1 .4 1 1 1 1-.4 1-1z M26 11.2h1.6v8.4H26zm7.2 4.4c0-2.6-1.7-4.4-4-4.4-2.4 0-4.1 1.8-4.1 4.4 0 2.7 1.7 4.5 4.1 4.5 1 0 1.9-.3 2.5-.8v-1.5c-.6.4-1.3.6-2.1.6-1.5 0-2.7-.9-2.8-2.4h7.3c.1-.5.1-.9.1-1.4zm-6.3-1c.1-1.3 1-2.1 2.2-2.1 1.1 0 2 .8 2.1 2.1z"
              fill="#FFFFFF" />
          </svg>
        </div>
      </div>
    </div>

    <!-- FAQ Section -->
    <section class="faq-section">
      <div class="faq-header">
        <h2 class="faq-title">Frequently Asked <span class="highlight">Questions</span></h2>
        <p class="faq-subtitle">Got questions? We've got answers. If you can't find what you are looking for, contact
          our support team.</p>
      </div>

      <div class="faq-accordion">
        <!-- FAQ Item 1 -->
        <div class="faq-item">
          <button class="faq-question">
            <span>Can I change or cancel my plan at any time?</span>
            <i data-lucide="chevron-down" class="faq-arrow"></i>
          </button>
          <div class="faq-answer">
            <p>Yes, absolutely. You can upgrade, downgrade, or cancel your subscription at any point directly from your
              account settings. There are no contracts, commitments, or hidden cancellation fees.</p>
          </div>
        </div>

        <!-- FAQ Item 2 -->
        <div class="faq-item">
          <button class="faq-question">
            <span>What happens after my 7-day free trial ends?</span>
            <i data-lucide="chevron-down" class="faq-arrow"></i>
          </button>
          <div class="faq-answer">
            <p>Once your trial ends, your account will be billed monthly or annually depending on the billing cycle you
              chose during sign up. We do not require a credit card to activate the trial, so you will not be billed
              automatically unless you decide to enter your details.</p>
          </div>
        </div>

        <!-- FAQ Item 3 -->
        <div class="faq-item">
          <button class="faq-question">
            <span>How does the 30-day money-back guarantee work?</span>
            <i data-lucide="chevron-down" class="faq-arrow"></i>
          </button>
          <div class="faq-answer">
            <p>If you're not completely satisfied with our B2B Contact Finder service within the first 30 days of
              upgrading to a paid plan, simply email our customer support team. We will issue a 100% refund immediately,
              no questions asked.</p>
          </div>
        </div>

        <!-- FAQ Item 4 -->
        <div class="faq-item">
          <button class="faq-question">
            <span>Is my payment and card information secure?</span>
            <i data-lucide="chevron-down" class="faq-arrow"></i>
          </button>
          <div class="faq-answer">
            <p>Yes. All payments are processed through Stripe, one of the world's most secure and popular payment
              gateways. Stripe is fully PCI-DSS compliant, meaning your credentials are encrypted at the highest
              possible standard. We never store your card information on our servers.</p>
          </div>
        </div>

        <!-- FAQ Item 5 -->
        <div class="faq-item">
          <button class="faq-question">
            <span>What constitutes a "Verified Contact"?</span>
            <i data-lucide="chevron-down" class="faq-arrow"></i>
          </button>
          <div class="faq-answer">
            <p>A verified contact is any prospect profile where their email address has been successfully validated in
              real-time. Our verification engine conducts direct SMTP checks to ensure the email is fully active and
              deliverable (guaranteeing a 95%+ delivery success rate).</p>
          </div>
        </div>
      </div>
    </section>

  </main>

        
@push('script')
 <!-- Lucide Icons and Script -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="{{asset('assets/frontend/js/pricing.js')}}"></script>
  <script>
let frequency = "annually";

const annualBtn = document.getElementById("toggle-annual");
const monthlyBtn = document.getElementById("toggle-monthly");

function updatePlanLinks() {
    document.querySelectorAll(".plan-link").forEach(function(link) {
        let planId = link.dataset.planId;
        link.href = `https://app.go4database.com/register?plan_id=${planId}&frequency=${frequency}`;
    });
}

annualBtn.addEventListener("click", function () {
    frequency = "annually";
    updatePlanLinks();
});

monthlyBtn.addEventListener("click", function () {
    frequency = "monthly";
    updatePlanLinks();
});

// Initialize on page load
updatePlanLinks();
</script>
@endpush
@endsection
