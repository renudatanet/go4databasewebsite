<!-- Navbar -->
@include('frontend.partials.navbar-style')
    <header class="g4d-sticky-nav" id="g4d-header">
      <nav class="g4d-bar" aria-label="Main">
        <a href="#top" class="g4d-logo">
          <img src="{{ asset('assets/frontend/images/logo.png') }}" alt="Go4Database Logo">
        </a>

        <div class="g4d-nav">

          <!-- Pricing -->
          <a href="{{ route('frontend.price.plan') }}" class="nav-link-item">Pricing</a>

          <!-- Success Stories Dropdown -->
          <div class="nav-dropdown-wrapper">
            <a href="#success-stories" class="nav-link-item">
              Success Stories
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6" />
              </svg>
            </a>
            <div class="nav-dropdown-menu">
              <a href="{{ route('frontend.case-study') }}" class="simple-dropdown-item">Success Stories</a>
              <a href="https://www.go4database.com/our-testimonial" class="simple-dropdown-item">Our Testimonial</a>
              <div class="dropdown-green-bar"></div>
            </div>
          </div>

          <!-- B2B Leads -->
          <a href="https://www.go4database.com/b2b" class="nav-link-item">B2B Leads</a>

          <!-- Solutions Dropdown -->
          <div class="nav-dropdown-wrapper">
            <a href="#features" class="nav-link-item">
              Solutions
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6" />
              </svg>
            </a>
            <div class="nav-dropdown-menu">
              <a href="{{ route('frontend.email.verifier') }}" class="simple-dropdown-item">Email Verifier</a>
              <a href="{{ route('frontend.email.finder') }}" class="simple-dropdown-item">Email Finder</a>
              <a href="{{ route('frontend.affiliate') }}" class="simple-dropdown-item">Affiliate Program</a>
              <div class="dropdown-green-bar"></div>
            </div>
          </div>

          <!-- Company Dropdown -->
          <div class="nav-dropdown-wrapper">
            <a href="#comparison" class="nav-link-item">
              Company
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6" />
              </svg>
            </a>
            <div class="nav-dropdown-menu">
              <a href="{{ route('frontend.blog') }}" class="simple-dropdown-item">Blog</a>
              <a href="{{ route('frontend.faq') }}" class="simple-dropdown-item">FAQ</a>
              <a href="{{ route('frontend.about') }}" class="simple-dropdown-item">About</a>
              <div class="dropdown-green-bar"></div>
            </div>
          </div>
        </div>

        <!-- Right Corner CTA Buttons -->
        <div class="g4d-actions">
          <a href="https://app.go4database.com/login?utm_source=HomePage&utm_medium=Internal&utm_campaign=app_login" class="nav-login-btn">Log in</a>
          <a href="https://app.go4database.com/register?utm_source=HomePage&utm_medium=Internal&utm_campaign=navbar_signup" class="nav-signup-btn">Sign up for free</a>
        </div>
      </nav>
    </header>