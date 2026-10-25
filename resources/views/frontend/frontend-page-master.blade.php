@include('frontend.partials.header')
@include('frontend.partials.navbar-variant.navbar-'.get_static_option('navbar_variant'))

{{-- The FAQ page is matched by route name rather than path, because its slug
     is admin-configurable and differs between environments. --}}
@if (!request()->is('author*') && !request()->is('list/*') && !request()->routeIs('frontend.b2c') && !request()->is('email-verifier') && !request()->is('email-finder') && !request()->is('affiliate') && !request()->routeIs('frontend.faq'))
   {{-- A page that draws its own header (the blog listings) sets this section. --}}
   @sectionMissing('no-breadcrumb')
      @include('frontend.partials.breadcrumb')
   @endif
@endif
@yield('content')
@include('frontend.partials.footer')
