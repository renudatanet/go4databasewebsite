@php
    $global_static_field_data = $global_static_field_data ?? [];
@endphp
@php
    $home_page_variant = $home_page ?? filter_static_option_value('home_page_variant',$global_static_field_data);
@endphp
        <!DOCTYPE html>
<html lang="{{ $user_select_lang_slug ?? 'en' }}"  dir="{{get_user_lang_direction()}}">
 
<head>

@php
    $canonical = url()->current(); // removes query string automatically
@endphp

{{-- Canonical (always clean) --}}
<link rel="canonical" href="{{ $canonical }}" />

{{-- Noindex ANY URL that has query parameters --}}
@if(request()->getQueryString())
    <meta name="robots" content="noindex, follow">
@endif


@if(!empty(filter_static_option_value('site_google_analytics',$global_static_field_data)))
    {!! get_static_option('site_google_analytics') !!}
@endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {!! render_favicon_by_id(filter_static_option_value('site_favicon',$global_static_field_data)) !!}
    <!-- {!! load_google_fonts() !!} -->
   
<link rel="preload" as="font" type="font/woff2" 
      href="https://fonts.gstatic.com/s/nunito/v32/XRXV3I6Li01BKofINeaB.woff2" 
      crossorigin>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,400;1,500;1,700;1,900&family=Outfit:wght@200;300;400;500;600;700;800;900&family=Source+Serif+Pro:wght@200;300;400;600;700;900&family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
      <!-- GOOD — 1 request for all fonts -->
<link rel="preload" href="{{ asset('assets/frontend/webfonts/fa-brands-400.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="{{ asset('assets/frontend/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    
<!-- Preload key CSS (icons, layout, main styles) -->
<link rel="preload" href="{{ asset('assets/frontend/css/bootstrap.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('assets/frontend/css/style.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">

<!-- Other styles loaded normally but not render-blocking -->
<link rel="stylesheet" href="{{ asset('assets/frontend/css/fontawesome.min.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/flaticon.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/nexicon.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/owl.carousel.min.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/animate.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/magnific-popup.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/style-two.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/helpers.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/responsive.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/jquery.ihavecookies.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/dynamic-style.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/toastr.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/slick.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="{{ asset('assets/frontend/css/jquery.mb.YTPlayer.min.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" media="print" onload="this.media='all'">

<!-- Fallback for no-JS browsers -->
<noscript>
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/style.css?v=123') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/nexicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/style-two.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/helpers.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/jquery.ihavecookies.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/dynamic-style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/jquery.mb.YTPlayer.min.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</noscript>
@if(file_exists('assets/frontend/css/home-'.$home_page_variant.'.css') && empty(get_static_option('home_page_page_builder_status')))
        <link rel="stylesheet" href="{{asset('assets/frontend/css/home-'.$home_page_variant.'.css')}}">
    @endif
    
    @include('frontend.partials.css-variable')
    @include('frontend.partials.navbar-css')
    @yield('style')
    @if(!empty(filter_static_option_value('site_rtl_enabled',$global_static_field_data)) || get_user_lang_direction() == 'rtl')
        <link rel="stylesheet" href="{{asset('assets/frontend/css/rtl.css')}}">
        <link rel="stylesheet" href="{{asset('assets/frontend/css/new_rtl.css')}}">
    @endif
    @include('frontend.partials.og-meta')
    <script src="{{asset('assets/frontend/js/jquery-3.4.1.min.js')}}" defer></script>
    <script src="{{asset('assets/frontend/js/jquery-migrate-3.1.0.min.js')}}" defer></script>

    <script>var siteurl = "{{url('/')}}"</script>

    {!! filter_static_option_value('site_third_party_tracking_code',$global_static_field_data) !!}

    {{-- Mobile navigation. Loaded here rather than per page because both the
         home layout and the inner-page layout include this partial, and the
         desktop mega-menu has no small-screen handling of its own outside
         home.css. The script reads the existing navbar, so the navbar file
         itself is never touched. --}}
    <link rel="stylesheet" href="{{asset('assets/frontend/css/mobile-nav.css?v=1')}}">
    <script src="{{asset('assets/frontend/js/mobile-nav.js?v=1')}}" defer></script>

    @stack('styles')
</head>

<body class="{{request()->path()}} home_variant_{{$home_page_variant}} nexelit_version_{{getenv('XGENIOUS_NEXELIT_VERSION')}} {{filter_static_option_value('item_license_status',$global_static_field_data)}} apps_key_{{filter_static_option_value('site_script_unique_key',$global_static_field_data)}} ">
@include('frontend.partials.preloader')
@include('frontend.partials.search-popup')


