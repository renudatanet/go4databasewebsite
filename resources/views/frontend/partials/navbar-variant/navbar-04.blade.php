<!-- Navbar -->
 <style>
    

/* --- Style Section 1 --- */
 .nav-dropdown-wrapper {
      position: relative;
      display: inline-block;
    }

    .nav-link-item {
      font-family: 'Nunito', sans-serif;
      color: #0b132a;
      font-size: 16px;
      font-weight: 700;
      white-space: nowrap;
      padding: 10px 14px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .nav-dropdown-wrapper:hover .nav-link-item,
    .nav-link-item:hover {
      color: #6fd943 !important;
    }

    .nav-link-item svg {
      transition: transform 0.25s ease, color 0.25s ease;
      color: #0b132a;
    }

    .nav-dropdown-wrapper:hover .nav-link-item svg,
    .nav-link-item:hover svg {
      color: #6fd943 !important;
      transform: rotate(180deg);
    }

    .nav-dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0 !important;
      min-width: 250px;
      background: #ffffff;
      border-radius: 0 0 6px 6px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
      opacity: 0;
      visibility: hidden;
      transform: translateY(6px);
      transition: all 0.2s ease;
      z-index: 1000;
      padding: 0 !important;
      overflow: hidden;
      border: 1px solid #eef2f6;
      border-bottom: none;
    }

    .nav-dropdown-wrapper:hover .nav-dropdown-menu {
      opacity: 1;
      visibility: visible;
    }

    .simple-dropdown-item {
      display: block;
      padding: 18px 24px;
      color: #556070;
      font-family: 'Nunito', sans-serif;
      font-size: 18px;
      font-weight: 700;
      text-decoration: none;
      border-bottom: 1px solid #f0f4f8;
      transition: background 0.18s ease, color 0.18s ease;
      background: #ffffff;
      text-align: left;
    }

    .simple-dropdown-item:hover {
      background: #f7fcf5 !important;
      color: #6fd943 !important;
    }
    .mega-menu-dropdown {
    position: absolute;
    top: 100%;
    left: 50% !important;
    transform: translateX(-38%) translateY(6px);
    width: 1050px;
    max-width: calc(100vw - 40px);
    background: #ffffff;
    border-radius: 0 0 10px 10px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
    z-index: 1000;
    padding: 0 !important;
    overflow: hidden;
    border: 1px solid #eef2f6;
    border-bottom: none;
}
.mega-menu-body {
    display: grid;
    grid-template-columns: 1.15fr 1.3fr 1fr 1.15fr;
    gap: 32px;
    padding: 34px 38px 28px 38px;
    background: #ffffff;
    text-align: left;
}
.mega-menu-col {
    display: flex;
    flex-direction: column;
}
.mega-col-title {
    font-family: 'Nunito', sans-serif;
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 16px 0;
    padding: 0;
    letter-spacing: -0.2px;
}
.mega-col-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 13px;
}
.mega-menu-item {
    font-family: 'Nunito', sans-serif;
    font-size: 15px;
    font-weight: 600;
    color: #64748b;
    text-decoration: none;
    line-height: 1.45;
    transition: all 0.18s ease;
    display: inline-block;
}

    .mega-menu-link:hover {
      color: #1f7a2e;
    }

    .mega-menu-header {
      font-size: 16px;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 16px;
    }

    .nav-login-btn {
      color: #0f172a;
      font-size: 14px;
      font-weight: 700;
      padding: 10px 20px;
      border-radius: 99px;
      text-decoration: none;
      transition: all 0.25s ease;
      display: inline-block;
    }

    .nav-login-btn:hover {
      background: #f1f5f9;
      color: #2563eb;
      transform: translateY(-1px);
    }

    .nav-signup-btn {
      background: #6fd943;
      color: #0b132a;
      font-size: 14px;
      font-weight: 800;
      padding: 10px 22px;
      border-radius: 99px;
      text-decoration: none;
      box-shadow: 0 4px 14px rgba(111, 217, 67, 0.35);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      display: inline-block;
    }

    .nav-signup-btn:hover {
      background: #5ec734 !important;
      transform: translateY(-2px) scale(1.03);
      box-shadow: 0 8px 24px rgba(111, 217, 67, 0.55) !important;
      color: #0b132a !important;
    }

    .dropdown-green-bar {
      height: 5px;
      background-color: #6fd943;
      width: 100%;
    }
 </style>
    <header class="g4d-sticky-nav">
      <nav
        style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;max-width:1440px;margin:0 auto;position:relative;z-index:100;overflow:visible">
        <a href="{{url('/')}}" style="display:flex;align-items:center;flex-shrink:0">
            @if(!empty(filter_static_option_value('site_logo',$global_static_field_data)))
                            {!! render_logo_image_markup_by_attachment_id(filter_static_option_value('site_logo',$global_static_field_data)) !!}
                        @else
                            <h2 class="site-title">{{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}</h2>   
          
           @endif
         </a>

        <!-- Middle Centered Menu Links -->
         {!! render_frontend_menu($primary_menu) !!}

        <!-- Right Corner CTA Buttons -->
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;margin-left:auto;">
          <a href="https://app.go4database.com/login?utm_source=homepage&amp;utm_medium=internal&amp;utm_campaign=app_login&amp;_gl=1*1jpv6ui*_ga*MzgwOTc0MjEuMTc4MTUzNDYyNQ..*_ga_0JCTQKZJCC*czE3ODkwMzcxODQkbzExNSRnMSR0MTc4OTAzNzQ0MiRqMTIkbDAkaDA." class="nav-login-btn">Log in</a>
          <a href="https://app.go4database.com/register" class="nav-signup-btn">Sign up for free</a>
        </div>
      </nav>
    </header> 