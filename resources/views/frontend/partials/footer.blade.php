@php
    $home_page_variant = $home_page ?? get_static_option('home_page_variant');
    $home_page19_color_con = $home_page_variant == '19' ? '' :  'footer-top';
@endphp
@if(!in_array(Route::currentRouteName(),['frontend.course.lesson','frontend.course.lesson.start']))
 <!-- Footer -->
  
    <div style="background:#f8faf9;padding:64px 40px 48px;border-top:1px solid #f1f5f9">
      <div class="footer-grid"
        style="max-width:1300px;margin:0 auto;display:grid;grid-template-columns:repeat(4, 1fr);gap:40px">
        <!-- Column 1: Product Features -->
        <div>
          <div style="font-size:17px;font-weight:800;color:#111;margin-bottom:22px">Product Features</div>
          <div style="display:flex;flex-direction:column;gap:16px">
            <a href="#features" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; B2B Prospecting
              Search</a>
            <a href="#features" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Real-Time Email
              Verification</a>
            <a href="#features" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Buyer Intent Data</a>
            <a href="#features" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Technographics
              Search</a>
            <a href="#features" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Chrome Extension</a>
            <a href="#features" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Contact Database
              Enrichment</a>
            <a href="#features" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; CRM Integrations</a>
            <a href="#features" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Developer API
              Access</a>
            <a href="#features" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Lookalike Audience
              Builder</a>
            <a href="https://www.go4database.com/career" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Career
              Opportunities</a>
          </div>
        </div>

        <!-- Column 2: Popular Datasets -->
        <div>
          <div style="font-size:17px;font-weight:800;color:#111;margin-bottom:22px">Popular Datasets</div>
          <div style="display:flex;flex-direction:column;gap:16px">
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; CEO &amp; Executive
              Email List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Hospital Mailing
              List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Physicians &amp;
              Doctors List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; VP Sales &amp;
              Marketing List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; CTO &amp; IT Decision
              Makers</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Real Estate Agents
              List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; HR &amp; Recruiter
              Email List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Software Engineering
              Leads</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Construction
              Companies</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Finance &amp; CFO
              Email List</a>
          </div>
        </div>

        <!-- Column 3: Healthcare Industry Lists -->
        <div>
          <div style="font-size:17px;font-weight:800;color:#111;margin-bottom:22px">Healthcare Industry Lists</div>
          <div style="display:flex;flex-direction:column;gap:16px">
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Hospitals Mailing
              List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Medical Industry
              Mailing List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Physicians Mailing
              List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Cardiologists Mailing
              List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Healthcare Companies
              List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Dentists Mailing
              List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Nurse Practitioners
              List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Chiropractors Email
              List</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Pharmacies &amp;
              Pharmacists</a>
            <a href="#search-section" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Veterinary Medicine
              List</a>
          </div>
        </div>

        <!-- Column 4: Compliances -->
        <div>
          <div style="font-size:17px;font-weight:800;color:#111;margin-bottom:22px">Compliances</div>
          <div style="display:flex;flex-direction:column;gap:16px">
            <a href="https://www.go4database.com/privacy-policy" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Privacy Policy</a>
            <a href="https://www.go4database.com/terms-of-services" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Terms of Services</a>
            <a href="https://www.go4database.com/gdpr-ccpa" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; GDPR - CCPA</a>
            <a href="https://www.go4database.com/iso" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; ISO &amp; SO2</a>
            <a href="https://www.go4database.com/reseller" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Reseller</a>
            <a href="https://www.go4database.com/vendor-code-of-conduct" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Vendor Code of
              Conduct</a>
            <a href="https://www.go4database.com/database-affiliate" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Database
              Affiliate</a>
            <a href="https://www.go4database.com/pricing" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Pricing</a>
            <a href="https://paypal.com/" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Customized
              Payment</a>
            <a href="https://www.go4database.com/contact" class="g4d-footlink"
              style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; Contact</a>
          </div>
        </div>
      </div>
    </div>
 <!-- Copyright Bar -->
    <div style="background:#6fd943;padding:22px 40px">
      <div
        style="max-width:1300px;margin:0 auto;display:flex;align-items:center;justify-content:space-around;flex-wrap:wrap;gap:12px">
        <div style="color:#0b132a;font-size:14.5px;font-weight:700;">{!! get_footer_copyright_text() !!}
        </div>
        <div style="display:flex;align-items:center;gap:16px">
          <!-- Twitter / X -->
             @foreach($all_social_item as $data)
          <a href="{{$data->url}}" target="_blank" rel="noopener noreferrer"
            aria-label="Go4Database Twitter Profile"
            style="width:32px;height:32px;border-radius:50%;background:rgba(11,19,42,0.12);display:flex;align-items:center;justify-content:center;transition:background 0.2s">
             
              {!! fa_class_to_svg($data->icon) !!}
              </a>
             @endforeach
         
        </div>
      </div>
    </div>
@if(preg_match('/(xgenious)/',url('/')))
<div class="buy-now-wrap">
<ul class="buy-list">
    <li><a target="_blank"href="https://xgenious.com/laravel/nexelit/doc/" data-container="body" data-toggle="popover" data-placement="left" data-content="{{__('Documentation')}}"><i class="far fa-file-alt"></i></a></li>
    <li><a target="_blank"href="https://1.envato.market/OXNPP"><i class="fas fa-shopping-cart"></i></a></li>
    <li><a target="_blank"href="https://xgenious51.freshdesk.com/"><i class="fas fa-headset"></i></a></li>
</ul>
</div>
@endif
    <!-- Scroll To Top Button -->
    <a id="scroll-top-btn" href="#top" class="g4d-scrolltop hidden" aria-label="Scroll to Top"
      style="position:fixed;bottom:28px;right:28px;width:46px;height:46px;border-radius:50%;background:#6fd943;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 24px rgba(111,217,67,0.35);z-index:10">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <path d="M12 19V5M5 12l7-7 7 7" stroke="#0b132a" stroke-width="2.4" stroke-linecap="round"
          stroke-linejoin="round">
        </path>
      </svg>
    </a>

@include('frontend.partials.popup-structure')
@endif

<!-- load all script -->
<script src="{{asset('assets/frontend/js/bootstrap.bundle.min.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/dynamic-script.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/jquery.magnific-popup.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/imagesloaded.pkgd.min.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/isotope.pkgd.min.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/jquery.waypoints.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/jquery.counterup.min.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/owl.carousel.min.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/wow.min.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/jQuery.rProgressbar.min.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/jquery.mb.YTPlayer.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/jquery.nicescroll.min.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/slick.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/main.js')}}" defer></script>
@if(\Route::currentRouteName() === 'frontend.products')
<script src="{{asset('assets/frontend/js/jquery-ui.js')}}" defer></script>
@endif
<script src="{{asset('assets/frontend/js/toastr.min.js')}}" defer></script>

<x-frontend.others.advertisement-script/>
@if(request()->routeIs('homepage') || request()->routeIs('frontend.homepage.demo'))
@include('frontend.partials.popup-jspart')
@include('frontend.partials.gdpr-cookie')
@endif

@include('frontend.partials.twakto')
@include('frontend.partials.google-captcha')
@include('frontend.partials.inline-script')
@include('frontend.partials.product-ajax-js')
@yield('scripts')
@stack('script')
<script>
// $(document).ready(function () {
//     var url = window.location.pathname;
//     var query = window.location.search;

//     if (
//         url !== "/" &&
//         !url.endsWith("/") &&
//         !/\.[a-zA-Z0-9]+$/.test(url)
//     ) {
//         var newUrl = url + '/' + query;
//         window.location.replace(newUrl);
//     }
// });
</script>

</body>
</html>
