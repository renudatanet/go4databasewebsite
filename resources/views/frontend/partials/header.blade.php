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
   
{{-- Fonts, in one request that does not block the first paint: the stylesheet is
     preloaded and only becomes a stylesheet once it has arrived, so text shows in
     the metric-matched Nunito-fallback face first and swaps in (display=swap).
     Nunito is the site font (--body-font / --heading-font; the hero headline is
     Nunito 800, so its file is preloaded too). The homepage also sets Inter for
     body copy and Poppins 900 for one icon letter; font files download only
     where a stylesheet uses the family, so other pages pay for the CSS alone.
     Two things used to sit here and cost ~1.4s of blocked rendering: a request
     for five unused families (~36 faces), and three @import lines inside
     home.css that could only start after that file had arrived. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="font" type="font/woff2" href="https://fonts.gstatic.com/s/nunito/v32/XRXV3I6Li01BKofINeaB.woff2" crossorigin>
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Inter:wght@400..900&family=Poppins:wght@900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Inter:wght@400..900&family=Poppins:wght@900&display=swap"></noscript>
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

    {{-- The theme sets overflow-x:hidden on body. That makes body a scroll
         container, which silently disables position:sticky everywhere on the
         site: the sticky header never sticks, and the homepage's stacking
         cards never stack. `clip` hides horizontal overflow exactly the same
         way without creating that container.

         Inline rather than in a stylesheet on purpose. It has to load after
         the theme CSS to win, and keeping it here means there is no separate
         asset to copy into the live assets folder and fall out of step. --}}
    <style>html,body{overflow-x:clip;}body{overflow-y:visible;}</style>

    {{-- The search popup (frontend.partials.search-popup, included below on
         every page) is only hidden by a rule inside style.css --
         .search-popup-inner-wrapper{position:fixed;...;visibility:hidden;opacity:0}.
         style.css is deferred on purpose (Round 1) so it never blocks first
         paint, but that means for however long it takes to arrive, this
         element sits in the page as a normal, visible, ~163px block instead
         -- and every page on the site jumps up by that much the moment
         style.css finally loads and hides it. Measured on PageSpeeds own
         throttled mobile test: a single 0.17 CLS jump, timed exactly to
         style.css's arrival. Repeating the same three properties here,
         inline, makes it hidden from first paint instead -- style.css still
         sets the same values later, so nothing changes when it arrives. --}}
    <style>.search-popup-inner-wrapper{position:fixed;visibility:hidden;opacity:0;}</style>

    @stack('styles')
</head>

<body class="{{request()->path()}} home_variant_{{$home_page_variant}} nexelit_version_{{getenv('XGENIOUS_NEXELIT_VERSION')}} {{filter_static_option_value('item_license_status',$global_static_field_data)}} apps_key_{{filter_static_option_value('site_script_unique_key',$global_static_field_data)}} ">
@include('frontend.partials.preloader')
@include('frontend.partials.search-popup')


