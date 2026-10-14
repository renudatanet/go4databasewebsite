@php
    $home_page_variant = $home_page ?? get_static_option('home_page_variant');
    $home_page19_color_con = $home_page_variant == '19' ? '' :  'footer-top';
@endphp
@if(!in_array(Route::currentRouteName(),['frontend.course.lesson','frontend.course.lesson.start']))
 <!-- Footer -->
  
  @php
      // Each column is a menu chosen in Appearance Settings > Footer Settings.
      // A column with no menu set, or an empty menu, is simply left out.
      $footerCols = [];
      foreach (['one', 'two', 'three', 'four'] as $slot) {
          $menuId = get_static_option('footer_column_' . $slot . '_menu_id');
          if (empty($menuId)) { continue; }
          $menuRow = \App\Menu::find($menuId);
          if (is_null($menuRow)) { continue; }
          $items = json_decode($menuRow->content, true);
          if (!is_array($items)) { continue; }
          $links = [];
          foreach ($items as $it) {
              if (!is_array($it) || empty($it)) { continue; }
              $label = trim((string) ($it['menutitle'] ?? '')) ?: trim((string) ($it['pname'] ?? ''));
              if ($label === '') { continue; }
              $href = (string) ($it['purl'] ?? '');
              $href = $href !== '' ? str_replace('@url', rtrim(url('/'), '/'), $href) : '#';
              $links[] = ['label' => $label, 'href' => $href, 'target' => (string) ($it['antarget'] ?? '')];
          }
          if (empty($links)) { continue; }
          $footerCols[] = [
              'title' => get_static_option('footer_column_' . $slot . '_title'),
              'links' => $links,
          ];
      }
      // Falls back to the site's own meta description rather than inventing a
      // tagline; set a shorter one in Footer Settings to override it.
      $footerAbout = get_static_option('footer_about_text');
      if (empty($footerAbout)) {
          $footerAbout = get_static_option('site_' . $user_select_lang_slug . '_meta_description');
      }
      $footerAbout = \Illuminate\Support\Str::limit(strip_tags((string) $footerAbout), 150);
  @endphp

  {{-- Inlined on purpose, as the chat widget is: production's public_html/assets
       is a real directory rather than a link to @core/public/assets, so a separate
       stylesheet would need copying by hand on every deploy. --}}
  <style>
    .g4f{background:#fbfcfa;border-top:1px solid #eaf1e6;padding:54px 0 40px;}
    .g4f-inner{max-width:1240px;margin:0 auto;padding:0 32px;display:grid;
      grid-template-columns:1.35fr repeat(4,1fr);gap:40px 32px;}
    .g4f-brand{min-width:0;}
    .g4f-logo{display:inline-block;max-width:190px;}
    .g4f-logo img{max-width:190px;height:auto;}
    .g4f-logo span{font-size:19px;font-weight:800;color:#10241a;}
    .g4f-about{margin:16px 0 0;font-size:14px;line-height:1.7;color:#4c5a52;max-width:290px;}
    .g4f-social{margin-top:20px;display:flex;flex-wrap:wrap;gap:9px;}
    .g4f-social a{width:38px;height:38px;border-radius:10px;border:1px solid #e3e8e0;background:#fff;
      color:#4c5a52;display:flex;align-items:center;justify-content:center;
      transition:background .15s,border-color .15s,color .15s;}
    .g4f-social a:hover{background:#145a0b;border-color:#145a0b;color:#fff;}
    .g4f-social svg{width:16px;height:16px;fill:currentColor;}
    .g4f-col{min-width:0;}
    .g4f-head{font-size:13px;font-weight:800;letter-spacing:.4px;text-transform:uppercase;
      color:#10241a;margin:0;}
    .g4f-head-rule{display:block;margin-top:8px;width:24px;height:2px;border-radius:2px;
      background:linear-gradient(90deg,#145a0b,#6fd943);}
    .g4f-col ul{margin:16px 0 0;padding:0;list-style:none;display:flex;flex-direction:column;gap:10px;}
    .g4f-col a{font-size:13.5px;line-height:1.5;color:#4c5a52;text-decoration:none;transition:color .15s;}
    .g4f-col a:hover{color:#145a0b;}
    @media (max-width:1080px){
      .g4f-inner{grid-template-columns:repeat(3,minmax(0,1fr));}
      .g4f-brand{grid-column:1/-1;}
    }
    @media (max-width:720px){ .g4f-inner{grid-template-columns:repeat(2,minmax(0,1fr));gap:32px 24px;} }
    @media (max-width:460px){ .g4f-inner{grid-template-columns:1fr;} }
  </style>

  <footer class="g4f">
    <div class="g4f-inner">

      <div class="g4f-brand">
        <a class="g4f-logo" href="{{ url('/') }}">
          @if(!empty(get_static_option('site_logo')))
            {!! render_image_markup_by_attachment_id(get_static_option('site_logo'), '', 'full') !!}
          @else
            <span>{{ get_static_option('site_' . $user_select_lang_slug . '_title') }}</span>
          @endif
        </a>

        @if(!empty($footerAbout))
          <p class="g4f-about">{{ $footerAbout }}</p>
        @endif

        @if(!empty($all_social_item) && count($all_social_item))
        <div class="g4f-social">
          @foreach($all_social_item as $data)
            <a href="{{ $data->url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $data->title ?? 'Social profile' }}">
              {!! fa_class_to_svg($data->icon) !!}
            </a>
          @endforeach
        </div>
        @endif
      </div>

      @foreach($footerCols as $col)
        <nav class="g4f-col" aria-label="{{ $col['title'] }}">
          <h2 class="g4f-head">{{ $col['title'] }}</h2>
          <span class="g4f-head-rule"></span>
          <ul>
            @foreach($col['links'] as $link)
              <li><a href="{{ $link['href'] }}" @if($link['target'] !== '') target="{{ $link['target'] }}" rel="noopener" @endif>{{ $link['label'] }}</a></li>
            @endforeach
          </ul>
        </nav>
      @endforeach

    </div>
  </footer>

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
<script src="{{asset('assets/frontend/js/owl.carousel.min.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/main.js')}}" defer></script>
@if(\Route::currentRouteName() === 'frontend.products')
<script src="{{asset('assets/frontend/js/jquery-ui.js')}}" defer></script>
<script src="{{asset('assets/frontend/js/toastr.min.js')}}" defer></script>
@endif

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

{{-- Chat widget. Lives here rather than in home-01 so it reaches every
     page: both frontend-master and frontend-page-master include this
     footer. It renders nothing at all unless it is switched on in the
     admin panel. --}}
@include('frontend.partials.chat-widget')

</body>
</html>
