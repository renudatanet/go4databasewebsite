@include('frontend.partials.header')
@include('frontend.partials.navbar-variant.navbar-'.get_static_option('navbar_variant'))

@if (!request()->is('author*') && !request()->is('list/*'))
   @include('frontend.partials.breadcrumb')
@endif
@yield('content')
@include('frontend.partials.footer')
