<!-- Navbar -->
@include('frontend.partials.navbar-style')
    <header class="g4d-sticky-nav" id="g4d-header">
      <nav class="g4d-bar" aria-label="Main">
        <a href="{{url('/')}}" class="g4d-logo">
            @if(!empty(filter_static_option_value('site_logo',$global_static_field_data)))
                            {!! render_logo_image_markup_by_attachment_id(filter_static_option_value('site_logo',$global_static_field_data)) !!}
                        @else
                            <h2 class="site-title">{{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}</h2>   
          
           @endif
         </a>

        <!-- Middle Centered Menu Links -->
         {!! render_frontend_menu($primary_menu) !!}

        <!-- Right Corner CTA Buttons -->
        <div class="g4d-actions">
          <a href="https://app.go4database.com/login?utm_source=homepage&amp;utm_medium=internal&amp;utm_campaign=app_login&amp;_gl=1*1jpv6ui*_ga*MzgwOTc0MjEuMTc4MTUzNDYyNQ..*_ga_0JCTQKZJCC*czE3ODkwMzcxODQkbzExNSRnMSR0MTc4OTAzNzQ0MiRqMTIkbDAkaDA." class="nav-login-btn">Log in</a>
          <a href="https://app.go4database.com/register" class="nav-signup-btn">Sign up for free</a>
        </div>
      </nav>
    </header> 