@include('frontend.partials.header')
@include('frontend.partials.navbar-variant.navbar-'.get_static_option('navbar_variant'))

{{-- The FAQ page is matched by route name rather than path, because its slug
     is admin-configurable and differs between environments. --}}
@if (!request()->is('author*') && !request()->is('list/*') && !request()->is('email-verifier') && !request()->routeIs('frontend.faq'))
   @include('frontend.partials.breadcrumb')
@endif
@yield('content')
@include('frontend.partials.footer')
