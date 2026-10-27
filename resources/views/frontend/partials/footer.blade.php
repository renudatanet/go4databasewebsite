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
    /* Ask AI: each tile in its assistant's own colours, so they read at a glance,
       and the same size and spacing as the social icons above, so the rows line up. */
    .g4f-ask{margin-top:26px;}
    .g4f-ask-title{margin:0;font-size:13px;line-height:1.45;font-weight:800;letter-spacing:.4px;text-transform:uppercase;color:#10241a;}
    .g4f-ask-list{margin:12px 0 0;padding:0;list-style:none;display:flex;flex-wrap:wrap;gap:9px;}
    .g4f-ask a{position:relative;width:38px;height:38px;border-radius:10px;color:#fff;
      display:flex;align-items:center;justify-content:center;
      box-shadow:inset 0 0 0 1px rgba(255,255,255,.08),0 1px 2px rgba(16,36,26,.14);
      transition:transform .2s cubic-bezier(.2,.8,.2,1),box-shadow .2s ease;}
    .g4f-ask a:hover,.g4f-ask a:focus-visible{transform:translateY(-2px);
      box-shadow:inset 0 0 0 1px rgba(255,255,255,.08),0 10px 20px -8px rgba(16,36,26,.5);}
    .g4f-ask a:focus{outline:none;}
    .g4f-ask a:focus-visible{outline:2px solid #145a0b;outline-offset:2px;}
    .g4f-ask svg{width:20px;height:20px;fill:currentColor;}
    .g4f-ask-gpt,.g4f-ask-grok,.g4f-ask-gemini{background:#0d0d0d;}
    .g4f-ask-pplx{background:#20808d;}
    .g4f-ask-claude{background:#d97757;}
    /* The assistant's name, shown under the tile on hover or keyboard focus
       (above it would cover the heading). */
    .g4f-ask a::after{content:attr(data-label);position:absolute;left:50%;top:calc(100% + 8px);
      transform:translate(-50%,-4px);padding:5px 9px;border-radius:7px;background:#10241a;color:#fff;
      font-size:12px;font-weight:700;line-height:1.2;white-space:nowrap;
      opacity:0;pointer-events:none;transition:opacity .15s ease,transform .15s ease;}
    .g4f-ask a:hover::after,.g4f-ask a:focus-visible::after{opacity:1;transform:translate(-50%,0);}
    @media (prefers-reduced-motion:reduce){
      .g4f-ask a,.g4f-ask a::after{transition:none;}
      .g4f-ask a:hover,.g4f-ask a:focus-visible{transform:none;}
    }
    .g4f-col{min-width:0;}
    .g4f-head{font-size:13px;line-height:1.45;font-weight:800;letter-spacing:.4px;text-transform:uppercase;
      color:#10241a;margin:0;min-height:2.6em;}
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

        {{-- Opens each assistant with a question about Go4Database already
             typed in, pointing it at the live site so the answer starts from
             our own pages. The link formats are the ones each assistant
             accepts for a prefilled prompt; Gemini goes through Google's AI
             Mode, which is the only Gemini surface that takes one. --}}
        @php
          $askAiPrompt = rawurlencode("I'm evaluating B2B contact data providers. Explain how Go4Database helps sales and marketing teams find verified business emails and phone numbers, which industries and job titles it covers, and how its free credits and pricing work. Summarize the highlights from Go4Database's website: https://www.go4database.com");
        @endphp
        <div class="g4f-ask">
          <p class="g4f-ask-title">Ask AI about Go4Database</p>
          <ul class="g4f-ask-list">
            <li><a class="g4f-ask-gpt" href="https://chatgpt.com/?prompt={{ $askAiPrompt }}" target="_blank" rel="noopener noreferrer" data-label="ChatGPT" data-assistant="gpt" aria-label="Ask ChatGPT about Go4Database (opens in a new tab)"><svg viewBox="0 0 24 24" fill-rule="evenodd" aria-hidden="true" focusable="false"><path d="M9.205 8.658v-2.26c0-.19.072-.333.238-.428l4.543-2.616c.619-.357 1.356-.523 2.117-.523 2.854 0 4.662 2.212 4.662 4.566 0 .167 0 .357-.024.547l-4.71-2.759a.797.797 0 00-.856 0l-5.97 3.473zm10.609 8.8V12.06c0-.333-.143-.57-.429-.737l-5.97-3.473 1.95-1.118a.433.433 0 01.476 0l4.543 2.617c1.309.76 2.189 2.378 2.189 3.948 0 1.808-1.07 3.473-2.76 4.163zM7.802 12.703l-1.95-1.142c-.167-.095-.239-.238-.239-.428V5.899c0-2.545 1.95-4.472 4.591-4.472 1 0 1.927.333 2.712.928L8.23 5.067c-.285.166-.428.404-.428.737v6.898zM12 15.128l-2.795-1.57v-3.33L12 8.658l2.795 1.57v3.33L12 15.128zm1.796 7.23c-1 0-1.927-.332-2.712-.927l4.686-2.712c.285-.166.428-.404.428-.737v-6.898l1.974 1.142c.167.095.238.238.238.428v5.233c0 2.545-1.974 4.472-4.614 4.472zm-5.637-5.303l-4.544-2.617c-1.308-.761-2.188-2.378-2.188-3.948A4.482 4.482 0 014.21 6.327v5.423c0 .333.143.571.428.738l5.947 3.449-1.95 1.118a.432.432 0 01-.476 0zm-.262 3.9c-2.688 0-4.662-2.021-4.662-4.519 0-.19.024-.38.047-.57l4.686 2.71c.286.167.571.167.856 0l5.97-3.448v2.26c0 .19-.07.333-.237.428l-4.543 2.616c-.619.357-1.356.523-2.117.523zm5.899 2.83a5.947 5.947 0 005.827-4.756C22.287 18.339 24 15.84 24 13.296c0-1.665-.713-3.282-1.998-4.448.119-.5.19-.999.19-1.498 0-3.401-2.759-5.947-5.946-5.947-.642 0-1.26.095-1.88.31A5.962 5.962 0 0010.205 0a5.947 5.947 0 00-5.827 4.757C1.713 5.447 0 7.945 0 10.49c0 1.666.713 3.283 1.998 4.448-.119.5-.19 1-.19 1.499 0 3.401 2.759 5.946 5.946 5.946.642 0 1.26-.095 1.88-.309a5.96 5.96 0 004.162 1.713z"></path></svg></a></li>
            <li><a class="g4f-ask-pplx" href="https://www.perplexity.ai/search/new?q={{ $askAiPrompt }}" target="_blank" rel="noopener noreferrer" data-label="Perplexity" data-assistant="pplx" aria-label="Ask Perplexity about Go4Database (opens in a new tab)"><svg viewBox="0 0 24 24" fill-rule="evenodd" aria-hidden="true" focusable="false"><path d="M19.785 0v7.272H22.5V17.62h-2.935V24l-7.037-6.194v6.145h-1.091v-6.152L4.392 24v-6.465H1.5V7.188h2.884V0l7.053 6.494V.19h1.09v6.49L19.786 0zm-7.257 9.044v7.319l5.946 5.234V14.44l-5.946-5.397zm-1.099-.08l-5.946 5.398v7.235l5.946-5.234V8.965zm8.136 7.58h1.844V8.349H13.46l6.105 5.54v2.655zm-8.982-8.28H2.59v8.195h1.8v-2.576l6.192-5.62zM5.475 2.476v4.71h5.115l-5.115-4.71zm13.219 0l-5.115 4.71h5.115v-4.71z"></path></svg></a></li>
            <li><a class="g4f-ask-grok" href="https://x.com/i/grok?text={{ $askAiPrompt }}" target="_blank" rel="noopener noreferrer" data-label="Grok" data-assistant="grok" aria-label="Ask Grok about Go4Database (opens in a new tab)"><svg viewBox="0 0 24 24" fill-rule="evenodd" aria-hidden="true" focusable="false"><path d="M9.27 15.29l7.978-5.897c.391-.29.95-.177 1.137.272.98 2.369.542 5.215-1.41 7.169-1.951 1.954-4.667 2.382-7.149 1.406l-2.711 1.257c3.889 2.661 8.611 2.003 11.562-.953 2.341-2.344 3.066-5.539 2.388-8.42l.006.007c-.983-4.232.242-5.924 2.75-9.383.06-.082.12-.164.179-.248l-3.301 3.305v-.01L9.267 15.292M7.623 16.723c-2.792-2.67-2.31-6.801.071-9.184 1.761-1.763 4.647-2.483 7.166-1.425l2.705-1.25a7.808 7.808 0 00-1.829-1A8.975 8.975 0 005.984 5.83c-2.533 2.536-3.33 6.436-1.962 9.764 1.022 2.487-.653 4.246-2.34 6.022-.599.63-1.199 1.259-1.682 1.925l7.62-6.815"></path></svg></a></li>
            <li><a class="g4f-ask-claude" href="https://claude.ai/new?q={{ $askAiPrompt }}" target="_blank" rel="noopener noreferrer" data-label="Claude" data-assistant="claude" aria-label="Ask Claude about Go4Database (opens in a new tab)"><svg viewBox="0 0 24 24" fill-rule="evenodd" aria-hidden="true" focusable="false"><path d="M4.709 15.955l4.72-2.647.08-.23-.08-.128H9.2l-.79-.048-2.698-.073-2.339-.097-2.266-.122-.571-.121L0 11.784l.055-.352.48-.321.686.06 1.52.103 2.278.158 1.652.097 2.449.255h.389l.055-.157-.134-.098-.103-.097-2.358-1.596-2.552-1.688-1.336-.972-.724-.491-.364-.462-.158-1.008.656-.722.881.06.225.061.893.686 1.908 1.476 2.491 1.833.365.304.145-.103.019-.073-.164-.274-1.355-2.446-1.446-2.49-.644-1.032-.17-.619a2.97 2.97 0 01-.104-.729L6.283.134 6.696 0l.996.134.42.364.62 1.414 1.002 2.229 1.555 3.03.456.898.243.832.091.255h.158V9.01l.128-1.706.237-2.095.23-2.695.08-.76.376-.91.747-.492.584.28.48.685-.067.444-.286 1.851-.559 2.903-.364 1.942h.212l.243-.242.985-1.306 1.652-2.064.73-.82.85-.904.547-.431h1.033l.76 1.129-.34 1.166-1.064 1.347-.881 1.142-1.264 1.7-.79 1.36.073.11.188-.02 2.856-.606 1.543-.28 1.841-.315.833.388.091.395-.328.807-1.969.486-2.309.462-3.439.813-.042.03.049.061 1.549.146.662.036h1.622l3.02.225.79.522.474.638-.079.485-1.215.62-1.64-.389-3.829-.91-1.312-.329h-.182v.11l1.093 1.068 2.006 1.81 2.509 2.33.127.578-.322.455-.34-.049-2.205-1.657-.851-.747-1.926-1.62h-.128v.17l.444.649 2.345 3.521.122 1.08-.17.353-.608.213-.668-.122-1.374-1.925-1.415-2.167-1.143-1.943-.14.08-.674 7.254-.316.37-.729.28-.607-.461-.322-.747.322-1.476.389-1.924.315-1.53.286-1.9.17-.632-.012-.042-.14.018-1.434 1.967-2.18 2.945-1.726 1.845-.414.164-.717-.37.067-.662.401-.589 2.388-3.036 1.44-1.882.93-1.086-.006-.158h-.055L4.132 18.56l-1.13.146-.487-.456.061-.746.231-.243 1.908-1.312-.006.006z"></path></svg></a></li>
            <li><a class="g4f-ask-gemini" href="https://www.google.com/search?udm=50&amp;aep=11&amp;q={{ $askAiPrompt }}" target="_blank" rel="noopener noreferrer" data-label="Gemini" data-assistant="gemini" aria-label="Ask Gemini about Go4Database (opens in a new tab)"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20.616 10.835a14.147 14.147 0 01-4.45-3.001 14.111 14.111 0 01-3.678-6.452.503.503 0 00-.975 0 14.134 14.134 0 01-3.679 6.452 14.155 14.155 0 01-4.45 3.001c-.65.28-1.318.505-2.002.678a.502.502 0 000 .975c.684.172 1.35.397 2.002.677a14.147 14.147 0 014.45 3.001 14.112 14.112 0 013.679 6.453.502.502 0 00.975 0c.172-.685.397-1.351.677-2.003a14.145 14.145 0 013.001-4.45 14.113 14.113 0 016.453-3.678.503.503 0 000-.975 13.245 13.245 0 01-2.003-.678z" fill="#3186FF"></path><path d="M20.616 10.835a14.147 14.147 0 01-4.45-3.001 14.111 14.111 0 01-3.678-6.452.503.503 0 00-.975 0 14.134 14.134 0 01-3.679 6.452 14.155 14.155 0 01-4.45 3.001c-.65.28-1.318.505-2.002.678a.502.502 0 000 .975c.684.172 1.35.397 2.002.677a14.147 14.147 0 014.45 3.001 14.112 14.112 0 013.679 6.453.502.502 0 00.975 0c.172-.685.397-1.351.677-2.003a14.145 14.145 0 013.001-4.45 14.113 14.113 0 016.453-3.678.503.503 0 000-.975 13.245 13.245 0 01-2.003-.678z" fill="url(#g4f-gem-a)"></path><path d="M20.616 10.835a14.147 14.147 0 01-4.45-3.001 14.111 14.111 0 01-3.678-6.452.503.503 0 00-.975 0 14.134 14.134 0 01-3.679 6.452 14.155 14.155 0 01-4.45 3.001c-.65.28-1.318.505-2.002.678a.502.502 0 000 .975c.684.172 1.35.397 2.002.677a14.147 14.147 0 014.45 3.001 14.112 14.112 0 013.679 6.453.502.502 0 00.975 0c.172-.685.397-1.351.677-2.003a14.145 14.145 0 013.001-4.45 14.113 14.113 0 016.453-3.678.503.503 0 000-.975 13.245 13.245 0 01-2.003-.678z" fill="url(#g4f-gem-b)"></path><path d="M20.616 10.835a14.147 14.147 0 01-4.45-3.001 14.111 14.111 0 01-3.678-6.452.503.503 0 00-.975 0 14.134 14.134 0 01-3.679 6.452 14.155 14.155 0 01-4.45 3.001c-.65.28-1.318.505-2.002.678a.502.502 0 000 .975c.684.172 1.35.397 2.002.677a14.147 14.147 0 014.45 3.001 14.112 14.112 0 013.679 6.453.502.502 0 00.975 0c.172-.685.397-1.351.677-2.003a14.145 14.145 0 013.001-4.45 14.113 14.113 0 016.453-3.678.503.503 0 000-.975 13.245 13.245 0 01-2.003-.678z" fill="url(#g4f-gem-c)"></path><defs><linearGradient gradientUnits="userSpaceOnUse" id="g4f-gem-a" x1="7" x2="11" y1="15.5" y2="12"><stop stop-color="#08B962"></stop><stop offset="1" stop-color="#08B962" stop-opacity="0"></stop></linearGradient><linearGradient gradientUnits="userSpaceOnUse" id="g4f-gem-b" x1="8" x2="11.5" y1="5.5" y2="11"><stop stop-color="#F94543"></stop><stop offset="1" stop-color="#F94543" stop-opacity="0"></stop></linearGradient><linearGradient gradientUnits="userSpaceOnUse" id="g4f-gem-c" x1="3.5" x2="17.5" y1="13.5" y2="12"><stop stop-color="#FABC12"></stop><stop offset=".46" stop-color="#FABC12" stop-opacity="0"></stop></linearGradient></defs></svg></a></li>
          </ul>
        </div>
      </div>

      @foreach($footerCols as $col)
        <nav class="g4f-col" aria-label="{{ $col['title'] }}">
          {{-- Labels, not headings: as H2s they put footer menus into every page's outline. --}}
          <p class="g4f-head">{{ $col['title'] }}</p>
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
        style="max-width:1300px;margin:0 auto;display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:12px">
        <div style="color:#0b132a;font-size:14.5px;font-weight:700;">{!! get_footer_copyright_text() !!}
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

{{-- GA4: which assistant visitors ask about us. --}}
<script>
document.addEventListener('click', function (e) {
  var a = e.target.closest && e.target.closest('.g4f-ask a');
  if (a && typeof gtag === 'function') { gtag('event', 'ask_ai', { assistant: a.getAttribute('data-assistant') }); }
});
</script>

{{-- Chat widget. Lives here rather than in home-01 so it reaches every
     page: both frontend-master and frontend-page-master include this
     footer. It renders nothing at all unless it is switched on in the
     admin panel. --}}
@include('frontend.partials.chat-widget')

</body>
</html>
