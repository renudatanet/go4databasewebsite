<!-- Navbar -->
    <header class="g4d-sticky-nav">
      <nav
        style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;max-width:1440px;margin:0 auto;position:relative;z-index:100;overflow:visible">
        <a href="#top" style="display:flex;align-items:center;flex-shrink:0">
          <img src="{{ asset('assets/frontend/images/logo.png') }}" alt="Go4Database Logo" style="height:34px;width:auto;display:block">
        </a>

        <!-- Middle Centered Menu Links -->
        <div class="g4d-nav"
          style="display:flex;align-items:center;gap:28px;position:absolute;left:50%;transform:translateX(-50%);overflow:visible">

          <!-- Pricings -->
          <a href="{{ route('frontend.price.plan') }}" class="nav-link-item">Pricings</a>

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
              <a href="https://www.go4database.com/email-verifier" class="simple-dropdown-item">Email Verifier</a>
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
              <a href="{{ route('frontend.faq') }}" class="simple-dropdown-item">Faq</a>
              <a href="{{ route('frontend.about') }}" class="simple-dropdown-item">About</a>
              <div class="dropdown-green-bar"></div>
            </div>
          </div>
        </div>

        <!-- Right Corner CTA Buttons -->
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;margin-left:auto;">
          <a href="https://app.go4database.com/login" class="nav-login-btn">Log in</a>
          <a href="https://app.go4database.com/register" class="nav-signup-btn">Sign up for free</a>
        </div>
      </nav>
    </header>