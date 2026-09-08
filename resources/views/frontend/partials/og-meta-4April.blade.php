@if(request()->routeIs('homepage') || request()->routeIs('frontend.homepage.demo'))
    <meta property="og:title"  content="{{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}" />
    {!! render_og_meta_image_by_attachment_id(filter_static_option_value('og_meta_image_for_site',$global_static_field_data)) !!}
    @if(filter_static_option_value('site_meta_'.$user_select_lang_slug.'_tags',$global_static_field_data)!='')
        <title>{{filter_static_option_value('site_meta_'.$user_select_lang_slug.'_tags',$global_static_field_data)}}</title>
    @else
        <title>{{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}} - {{filter_static_option_value('site_'.$user_select_lang_slug.'_tag_line',$global_static_field_data)}}</title>
    @endif
    
    
    <meta name="description" content="{{filter_static_option_value('site_meta_'.$user_select_lang_slug.'_description',$global_static_field_data)}}">
    {{--<meta name="tags" content="{{filter_static_option_value('site_meta_'.$user_select_lang_slug.'_tags',$global_static_field_data)}}">--}}
    <script type="application/ld+json">
        @php echo get_static_option('site_meta_'.$user_select_lang_slug.'_schema_code'); @endphp
    </script>
@else 
    @php
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    @endphp
    @if(get_static_option('about_page_'.$user_select_lang_slug.'_meta_tags')!='')
        @if(Request::path()=='about')
            <title>
                @if(get_static_option('about_page_'.$user_select_lang_slug.'_meta_tags')!='')
                    {{get_static_option('about_page_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title>
        @elseif(Request::path()=='testimonial')
            <title>
                @if(get_static_option('testimonial_page_'.$user_select_lang_slug.'_meta_tags')!='')
                    {{get_static_option('testimonial_page_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title>
        @elseif(Request::path()=='pricing')
            <title>
                @if(get_static_option('price_plan_page_'.$user_select_lang_slug.'_meta_tags')!='')
                    {{get_static_option('price_plan_page_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title>  
        @elseif(Request::path()=='contact')
            <title>
                @if(get_static_option('contact_page_'.$user_select_lang_slug.'_meta_tags')!='')
                    {{get_static_option('contact_page_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title>  
        @elseif(Request::path()=='faq')
            <title>
                @if(get_static_option('faq_page_'.$user_select_lang_slug.'_meta_tags')!='')
                    {{get_static_option('faq_page_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title> 
        @elseif(Request::path()=='blog')
            <title>
                @if(get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')!='')
                    {{get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title>   
        @elseif(strpos($url,'blog') !== false)
            <title>
                
                @if(isset($blog_post->meta_tags) && $blog_post->meta_tags!='')
                    {{$blog_post->meta_tags}}
                @elseif(isset($blog_post->title) && $blog_post->title!='')
                    {{$blog_post->title}}
                @elseif(isset($blogcat->meta_tags) && $blogcat->meta_tags!='')
                    {{$blogcat->meta_tags}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title> 
        @elseif(strpos($url,'b2b') !== false)
            <title>
                @if(isset($work_item->meta_tag) && $work_item->meta_tag!='')
                    {{$work_item->meta_tag}}
                @elseif(isset($work_item->title) && $work_item->title!='')
                    {{$work_item->title}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title>
            <!--<title>-->
            <!--    @if(isset($blog_post->meta_tags) && $blog_post->meta_tags!='')-->
            <!--        {{$blog_post->meta_tags}}-->
            <!--    @elseif(isset($blog_post->title) && $blog_post->title!='')-->
            <!--        {{$blog_post->title}}-->
            <!--    @else-->
            <!--        @yield('site-title')-->
            <!--        @hasSection('site-title') - @else @yield('page-title') -  @endif-->
            <!--        {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}-->
            <!--    @endif-->
            <!--</title> -->
        @elseif(strpos($url,'case-study') !== false)
            <title>
                @if(isset($work_item->meta_tag) && $work_item->meta_tag!='')
                    {{$work_item->meta_tag}}
                @elseif(isset($work_item->title) && $work_item->title!='')
                    {{$work_item->title}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title>
        
        @elseif(strpos($url,'b2c') !== false)
            <title>
                @if(isset($service_item->meta_tag) && $service_item->meta_tag!='')
                    {{$service_item->meta_tag}}
                @elseif(isset($service_item->title) && $service_item->title!='')
                    {{$service_item->title}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title>
        @else   
            <title>
                @if(isset($page_post->meta_tags) && $page_post->meta_tags!='')
                    {{$page_post->meta_tags}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
                
            </title>
        @endif    
    @else
        <title>
            @if(isset($page_post->meta_tags) && $page_post->meta_tags!='')
                {{$page_post->meta_tags}}
            @elseif(isset($blog_post->meta_tags) && $blog_post->meta_tags!='')
                {{$blog_post->meta_tags}}
            @else
                @yield('site-title')
                @hasSection('site-title') - @else @yield('page-title') -  @endif
                {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
            @endif
        </title>
    @endif
    
    @yield('page-meta-data')
    
    @yield('og-meta')
    
    
    @if(Request::path()=='about')
        @if(get_static_option('about_page_'.$user_select_lang_slug.'_schema_code')!='')
            @php echo get_static_option('about_page_'.$user_select_lang_slug.'_schema_code'); @endphp
        @endif
    @elseif(Request::path()=='testimonial')
        @if(get_static_option('testimonial_page_'.$user_select_lang_slug.'_schema_code')!='')
            @php echo get_static_option('testimonial_page_'.$user_select_lang_slug.'_schema_code'); @endphp
        @endif
    @elseif(Request::path()=='pricing')
        @if(get_static_option('price_plan_page_'.$user_select_lang_slug.'_schema_code')!='')
            @php echo get_static_option('price_plan_page_'.$user_select_lang_slug.'_schema_code'); @endphp
        @endif
    @elseif(Request::path()=='contact')
        @if(get_static_option('contact_page_'.$user_select_lang_slug.'_schema_code')!='')
            @php echo get_static_option('contact_page_'.$user_select_lang_slug.'_schema_code'); @endphp
        @endif
    @elseif(Request::path()=='faq')
        @if(get_static_option('faq_page_'.$user_select_lang_slug.'_schema_code')!='')
            @php echo get_static_option('faq_page_'.$user_select_lang_slug.'_schema_code'); @endphp
        @endif
    @elseif(Request::path()=='blog')
        @if(get_static_option('blog_page_'.$user_select_lang_slug.'_schema_code')!='')
            @php echo get_static_option('blog_page_'.$user_select_lang_slug.'_schema_code'); @endphp
        @endif
    @elseif(strpos($url,'blog') !== false)
        @if(isset($blog_post->schema_code) && $blog_post->schema_code!='')
            @php echo $blog_post->schema_code; @endphp
        @elseif(isset($blogcat->schema_code) && $blogcat->schema_code!='')
            @php echo $blogcat->schema_code; @endphp
        @endif
    @elseif(strpos($url,'b2b') !== false)
        @if(isset($work_item->schema_code) && $work_item->schema_code!='')
            @php echo $work_item->schema_code; @endphp
        @endif  
    @elseif(strpos($url,'case-study') !== false)
        @if(isset($work_item->schema_code) && $work_item->schema_code!='')
            @php echo $work_item->schema_code; @endphp
        @endif 
    @elseif(strpos($url,'b2c') !== false)        
        @if(isset($service_item->schema_code) && $service_item->schema_code!='')
            @php echo $service_item->schema_code; @endphp
        @endif 
    @elseif(strpos($url,'case-study') !== false)        
        @if(isset($CaseStudy->schema_code) && $CaseStudy->schema_code!='')
            @php echo $CaseStudy->schema_code; @endphp
        @endif 
    @else   
        @if(isset($page_post->schema_code) && $page_post->schema_code!='')
            @php echo $page_post->schema_code; @endphp
        @endif
    @endif    
@endif
