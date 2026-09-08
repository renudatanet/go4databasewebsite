@if(request()->routeIs('homepage') || request()->routeIs('frontend.homepage.demo'))
    <meta property="og:title"  content="{{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}" />
    {!! render_og_meta_image_by_attachment_id(filter_static_option_value('og_meta_image_for_site',$global_static_field_data)) !!}
    @if(filter_static_option_value('site_meta_'.$user_select_lang_slug.'_tags',$global_static_field_data)!='')
    
        <title>  {{filter_static_option_value('site_meta_'.$user_select_lang_slug.'_tags',$global_static_field_data)}}</title>
    @else
        <title> {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}} - {{filter_static_option_value('site_'.$user_select_lang_slug.'_tag_line',$global_static_field_data)}}</title>
    @endif
    
    
    <meta name="description" content="{{filter_static_option_value('site_meta_'.$user_select_lang_slug.'_description',$global_static_field_data)}}">
    {{--<meta name="tags" content="{{filter_static_option_value('site_meta_'.$user_select_lang_slug.'_tags',$global_static_field_data)}}">--}}
    
        @php echo get_static_option('site_meta_'.$user_select_lang_slug.'_schema_code'); @endphp
   
@else 
@php
    $user_select_lang_slug = $user_select_lang_slug ?? 'en_US';
@endphp
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
             @elseif(Request::path()=='career')
            <title> 
                @if(get_static_option('career_with_us_page_'.$user_select_lang_slug.'_meta_tags')!='')
                    {{get_static_option('career_with_us_page_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title>
            @elseif(isset($job_category) && $job_category)

    {{-- Job CATEGORY PAGE --}}
    <title>
        @if(!empty($job_category->meta_tags))
            {{ $job_category->meta_tags }}
        @elseif(!empty($job_category->title))
            {{ $job_category->title }}
        @else
            @yield('site-title')
            @hasSection('site-title') - @else @yield('page-title') - @endif
            {{ filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data) }}
        @endif
    </title>
            @elseif(isset($job) && $job)

    {{-- Job Details PAGE --}}
    <title> 
        @if(!empty($job->meta_tags))
            {{ $job->meta_tags }}
        @elseif(!empty($job->name))
        
            {{ $job->meta_tags }}
        @else
            @yield('site-title')
            @hasSection('site-title') - @else @yield('page-title') - @endif
            {{ filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data) }}
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
                 @elseif(Request::path()=='author')
            <title> 
                @if(get_static_option('author_page_'.$user_select_lang_slug.'_meta_tags')!='')
                    {{get_static_option('author_page_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title> 
          

@elseif(isset($blogauthor) && $blogauthor)
    {{-- Author PAGE --}}

    <title>
        @if(!empty($blogauthor->meta_tags))
            {{ $blogauthor->meta_tags }}
        @elseif(!empty($blogauthor->name))
            {{ $blogauthor->meta_tags }}
        @else
            @yield('site-title')
            @hasSection('site-title') - @else @yield('page-title') - @endif
            {{ filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data) }}
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
            @elseif(Request::path() == 'b2b')

    <title>
        @if(get_static_option('work_page_'.$user_select_lang_slug.'_meta_tags') != '')
            {{ get_static_option('work_page_'.$user_select_lang_slug.'_meta_tags') }}
        @else
            @yield('site-title')
            @hasSection('site-title') - @else @yield('page-title') - @endif
            {{ filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data) }}
        @endif
    </title>


@elseif(isset($work_category) && $work_category)

    {{-- B2B CATEGORY PAGE --}}
    <title> 
        @if(!empty($work_category->meta_tag))
            {{ $work_category->meta_tag }}
        @elseif(!empty($work_category->name))
            {{ $work_category->meta_tags }}
        @else
            @yield('site-title')
            @hasSection('site-title') - @else @yield('page-title') - @endif
            {{ filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data) }}
        @endif
    </title>
    

@elseif(isset($work_item) && $work_item)

    {{-- B2B DETAIL PAGE --}}
    <title> 
        @if(!empty($work_item->meta_tag))
            {{ $work_item->meta_tag }}
        @elseif(!empty($work_item->title))
            {{ $work_item->title }}
        @else
            @yield('site-title')
            @hasSection('site-title') - @else @yield('page-title') - @endif
            {{ filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data) }}
        @endif
    </title>
            
            @elseif(Request::path()=='case-study')
            <title> 
              
                @if(get_static_option('case_study_'.$user_select_lang_slug.'_meta_tags')!='')
                   {{get_static_option('case_study_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title>   
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
        @elseif(Request::path()=='b2c')
       
            <title>
              
                @if(get_static_option('service_page_'.$user_select_lang_slug.'_meta_tags')!='')
                   {{get_static_option('service_page_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title> 
            
@elseif(isset($service_category) && $service_category)
    {{-- B2C CATEGORY PAGE --}}
    <title>
        @if(!empty($service_category->meta_tag))
            {{ $service_category->meta_tag }} | 
        @elseif(!empty($service_category->name))
            {{ $service_category->meta_tags }}
        @else
            @yield('site-title')
            @hasSection('site-title') - @else @yield('page-title') - @endif
            {{ filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data) }}
        @endif
    </title>
     @elseif(isset($service_item) && $service_item)
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
             @elseif(Request::path()=='list')
       
            <title>
                @if(get_static_option('list_page_'.$user_select_lang_slug.'_meta_tags')!='')
                   {{get_static_option('list_page_'.$user_select_lang_slug.'_meta_tags')}}
                @else
                    @yield('site-title')
                    @hasSection('site-title') - @else @yield('page-title') -  @endif
                    {{filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)}}
                @endif
            </title> 
             @elseif(isset($list_item) && $list_item)
            <title>
                @if(isset($list_item->meta_tag) && $list_item->meta_tag!='')
                 
                    {{$list_item->meta_tag}}
                @elseif(isset($list_item->title) && $list_item->title!='')
                   {{$list_item->title}}
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
                
    <script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "BreadcrumbList", 
  "itemListElement": [{
    "@type": "ListItem", 
    "position": 1, 
    "name": "Home",
    "item": "{{ url('/') }}"  
  },{
    "@type": "ListItem", 
    "position": 2, 
    "name": "Blog",
    "item": "{{ url(Request::path()) }}"  
  }]
}
</script>
        @endif
    @elseif(Request::path()=='testimonial')
        @if(get_static_option('testimonial_page_'.$user_select_lang_slug.'_schema_code')!='')
            @php echo get_static_option('testimonial_page_'.$user_select_lang_slug.'_schema_code'); @endphp
                
    <script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "BreadcrumbList", 
  "itemListElement": [{
    "@type": "ListItem", 
    "position": 1, 
    "name": "Home",
    "item": "{{ url('/') }}"  
  },{
    "@type": "ListItem", 
    "position": 2, 
    "name": "Blog",
    "item": "{{ url(Request::path()) }}"  
  }]
}
</script>
        @endif
    @elseif(Request::path()=='pricing')
        @if(get_static_option('price_plan_page_'.$user_select_lang_slug.'_schema_code')!='')
            @php echo get_static_option('price_plan_page_'.$user_select_lang_slug.'_schema_code'); @endphp
                
    <script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "BreadcrumbList", 
  "itemListElement": [{
    "@type": "ListItem", 
    "position": 1, 
    "name": "Home",
    "item": "{{ url('/') }}"  
  },{
    "@type": "ListItem", 
    "position": 2, 
    "name": "Blog",
    "item": "{{ url(Request::path()) }}"  
  }]
}
</script>
        @endif
    @elseif(Request::path()=='contact')
        @if(get_static_option('contact_page_'.$user_select_lang_slug.'_schema_code')!='')
            @php echo get_static_option('contact_page_'.$user_select_lang_slug.'_schema_code'); @endphp
                
    <script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "BreadcrumbList", 
  "itemListElement": [{
    "@type": "ListItem", 
    "position": 1, 
    "name": "Home",
    "item": "{{ url('/') }}"  
  },{
    "@type": "ListItem", 
    "position": 2, 
    "name": "Blog",
    "item": "{{ url(Request::path()) }}"  
  }]
}
</script>
        @endif
    @elseif(Request::path()=='faq')
        @if(get_static_option('faq_page_'.$user_select_lang_slug.'_schema_code')!='')
            @php echo get_static_option('faq_page_'.$user_select_lang_slug.'_schema_code'); @endphp
       
        @endif
    @elseif(Request::path()=='blog')
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "{{ url('/') }}"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "Blog",
          "item": "{{ url(Request::path()) }}"  
        }]
    }
  ]
}
</script>

  @elseif(strpos($url,'blog') !== false)
     @if(isset($blogcat) && !empty($blogcat))
        <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
  
  {
     "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "{{ url('/') }}"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "Blog",
          "item": "{{ url('/blog') }}"  
        },{
          "@type": "ListItem", 
          "position": 3, 
          "name": "{{ $blogcat->name}}",
          "item": "{{ url(Request::path()) }}"  
        }]
    }
  ]
}
</script>

<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "{{ url()->current() }}",          
            "name": "{{ $blogcat->name}}"
          } 
      </script>
     @elseif(isset($blog_post) && !empty($blog_post))
          <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "{{ url('/') }}"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "Blog",
          "item": "{{ url('blog') }}"  
        },{
          "@type": "ListItem", 
          "position": 3, 
          "name": "{{ $blog_post->title }} ",
          "item": "{{ url()->current() }}"  
        }]
    },
    {
        "@type": "BlogPosting",
        "mainEntityOfPage": {
          "@type": "WebPage",
          "@id": "{{ url()->current() }}"
        },
        "headline": "{{ $blog_post->title }}",
        "description": "{{ \Illuminate\Support\Str::limit(strip_tags($blog_post->content),150) }}",
        "image": "{{ get_attachment_image_by_id($blog_post->image,'full',true)['img_url'] ?? '' }}",  
        "author": {
          "@type": "Person",
          "name": "{{ $blog_post->author }}",
          "url": "{{ url()->current() }}"

        },  
        "publisher": {
          "@type": "Organization",
          "name": "{{ $blog_post->author }}",
          "logo": {
            "@type": "ImageObject",
            "url": "{{ url()->current() }}"
          }
        },
        "datePublished": "{{ date('c', strtotime($blog_post->created_at)) }}",
"dateModified": "{{ date('c', strtotime($blog_post->updated_at)) }}"
    },
    @if(!empty($blog_post->faqs))
{!! json_encode([
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => collect($blog_post->faqs)->map(function ($faq) {
        return [
            "@type" => "Question",
            "name" => $faq['question'],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => strip_tags($faq['answer'])
            ]
        ];
    })->values()
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}

@endif
  ]
}
</script>
     


<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "{{ url()->current() }}",          
            "name": "{{ $blog_post->title }}"
          } 
      </script>
      @endif
     @elseif(Request::path()=='b2b')
   
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },
    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    }
  ]
}
</script>
               
    <script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "BreadcrumbList", 
  "itemListElement": [{
    "@type": "ListItem", 
    "position": 1, 
    "name": "Home",
    "item": "{{ url('/') }}"  
  },{
    "@type": "ListItem", 
    "position": 2, 
    "name": "B2b",
    "item": "{{ url(Request::path()) }}"  
  }]
}
</script>
  @elseif(strpos($url,'b2b') !== false)
     @if(isset($category_name) && !empty($category_name))
        
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },
    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "{{ url('/') }}"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "b2b",
          "item": "{{ url('/b2b') }}"  
        },{
          "@type": "ListItem", 
          "position": 3, 
          "name": "{{ $category_name }}",
          "item": "{{ url()->current() }}"  
        }]
    }
  ]
}
</script>
<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "{{ url()->current() }}",          
            "name": "{{ $category_name }}"
          } 
      </script>
     @elseif(isset($work_item) && !empty($work_item))


<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },

    {
      "@type": "Product",
      "@id": "{{ url()->current() }}#product",
      "name": "{{ $work_item->title }}",
      "image": "{{ get_attachment_image_by_id($work_item->image,'full',true)['img_url'] ?? '' }}",
      "description": "{{ \Illuminate\Support\Str::limit(
    trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($work_item->description)))),
    350,
    ''
) }}",
      "brand": { "@id": "{{ url('/') }}#organization" },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.7",
        "reviewCount": "39"
      }
    },

    {
      "@type": "BreadcrumbList",
      "@id": "{{ url()->current() }}#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "item": {
            "@type": "WebPage",
            "@id": "{{ url('/') }}",
            "url": "{{ url('/') }}",
            "name": "Home"
          }
        },
        {
          "@type": "ListItem",
          "position": 2,
          "item": {
            "@type": "WebPage",
            "@id": "{{ url('b2b') }}",
            "url": "{{ url('b2b') }}",
            "name": "B2B"
          }
        },
        {
          "@type": "ListItem",
          "position": 3,
          "item": {
            "@type": "WebPage",
            "@id": "{{ url()->current() }}",
            "url": "{{ url()->current() }}",
            "name": "{{ $work_item->title }}"
          }
        }
      ]
    },

    {
      "@type": "WebPage",
      "@id": "{{ url()->current() }}#webpage",
      "url": "{{ url()->current() }}",
      "name": "{{ $work_item->title }}",
      "isPartOf": {
        "@id": "{{ url('/') }}#website"
      },
      "breadcrumb": {
        "@id": "{{ url()->current() }}#breadcrumb"
      },
      "mainEntity": {
        "@id": "{{ url()->current() }}#product"
      },
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    }

  ]
}
</script>
 
      @endif
       
    @elseif(Request::path()=='case-study')
   <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
      "itemListElement": [{
        "@type": "ListItem", 
        "position": 1, 
        "name": "Home",
        "item": "{{ url('/') }}"  
      },{
        "@type": "ListItem", 
        "position": 2, 
        "name": "Case Study",
        "item": "{{ url(Request::path()) }}"  
      }]
    }
  ]
}
</script>
               
   
  @elseif(strpos($url,'case-study') !== false)
     @if(isset($all_work_category) && !empty($all_work_category))
        
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "{{ url('/') }}"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "Case Study",
          "item": "{{ url('/case-study') }}"  
        },{
          "@type": "ListItem", 
          "position": 3, 
          "name": "{{ $all_work_category }}",
          "item": "{{ url()->current() }}"  
        }]
    }
  ]
}
</script>
<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "{{ url()->current() }}",          
            "name": "{{ $all_work_category }}"
          } 
      </script>
     @elseif(isset($work_item) && !empty($work_item))
     
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },
    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
      "@type": "BreadcrumbList", 
      "itemListElement": [{
        "@type": "ListItem", 
        "position": 1, 
        "name": "Home",
        "item": "{{ url('/') }}"  
      },{
        "@type": "ListItem", 
        "position": 2, 
        "name": "Case Study",
        "item": "{{ url('case-study') }}"  
      },{
        "@type": "ListItem", 
        "position": 3, 
        "name": "{{ $work_item->title }} ",
        "item": "{{ url()->current() }}"  
      }]
    },
    {
      "@type": "Article",
      "mainEntityOfPage": {
      "@type": "WebPage",
      "@id": "{{ url()->current() }}"
      },
      "headline": "{{ $work_item->title }}",
      "description": "{{ \Illuminate\Support\Str::limit(strip_tags($work_item->description),150) }}",
      "image": "{{ get_attachment_image_by_id($work_item->image,'full',true)['img_url'] ?? '' }}",
      "author": {
      "url": "{{ url()->current() }}",
      "@type": "Organization",
      "name": "Go4Database",
      "logo": {
      "@type": "ImageObject",
      "url": "{{ get_attachment_image_by_id($work_item->image,'full',true)['img_url'] ?? '' }}"
      }
      },
      "publisher": {
      "@type": "Organization",
      "name": "Go4Database",
      "logo": {
      "@type": "ImageObject",
      "url": "{{ get_attachment_image_by_id($work_item->image,'full',true)['img_url'] ?? '' }}"
      }
      },
      "datePublished": "{{ $work_item->created_at?->utc()->toIso8601String() }}",
"dateModified": "{{ $work_item->updated_at?->utc()->toIso8601String() }}"
    }
  ]
}
</script>
    




<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "{{ url()->current() }}",          
            "name": "{{ $work_item->title }}"
          } 
      </script>
      @endif
       
     @elseif(Request::path()=='b2c')
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {

      "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "{{ url('/') }}"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "B2c",
          "item": "{{ url(Request::path()) }}"  
        }]
    }
  ]
}
</script>
   
  @elseif(strpos($url,'b2c') !== false)
     @if(isset($category_name) && !empty($category_name))
   <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
      "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "{{ url('/') }}"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "b2c",
          "item": "{{ url('/b2c') }}"  
        },{
          "@type": "ListItem", 
          "position": 3, 
          "name": "{{ $category_name }}",
          "item": "{{ url()->current() }}"  
        }]
    }
  ]
}
</script>
<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "{{ url()->current() }}",          
            "name": "{{ $category_name }}"
          } 
      </script>
     @elseif(isset($service_item) && !empty($service_item))
     
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
      "@type": "BreadcrumbList",
      "@id": "{{ url('/') }}/b2b/dentists-mailing-list#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "item": {
            "@type": "WebPage",
            "@id": "{{ url('/') }}",
            "url": "{{ url('/') }}",
            "name": "Home"
          }
        },
        {
          "@type": "ListItem",
          "position": 2,
          "item": {
            "@type": "WebPage",
            "@id": "{{ url('b2c') }}",
            "url": "{{ url('b2c') }}",
            "name": "B2C"
          }
        },
        {
          "@type": "ListItem",
          "position": 3,
          "item": {
            "@type": "WebPage",
            "@id": "{{ url()->current() }}",
            "url": "{{ url()->current() }}",
            "name": "{{ $service_item->title }}"
          }
        }
      ]
    },
    {
    "@type": "Product",
    "name": "{{ $service_item->title }}",
    "image": "{{ get_attachment_image_by_id($service_item->image,'full',true)['img_url'] ?? '' }}",
    "description": "{{ \Illuminate\Support\Str::limit(strip_tags($service_item->description),150) }}",
   "brand": { "@id": "https://www.go4database.com#organization" },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.7",
        "reviewCount": "39"
      }
  }
]
}
</script>
     
@if(!empty($service_item->faqs))
<script type="application/ld+json">
{!! json_encode([
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => collect($service_item->faqs)->map(function ($faq) {
        return [
            "@type" => "Question",
            "name" => $faq['question'],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => strip_tags($faq['answer'])
            ]
        ];
    })->values()
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif

<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "{{ url()->current() }}",          
            "name": "{{ $service_item->title }}"
          } 
      </script>
      @endif


 @elseif(strpos($url,'list') !== false)
     @if(isset($category_name) && !empty($category_name))
        
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
   {
       "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
   },
   {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
      "@type": "BreadcrumbList",
      "@id": "{{ url()->current() }}#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "item": {
            "@type": "WebPage",
            "@id": "{{ url('/') }}",
            "url": "{{ url('/') }}",
            "name": "Home"
          }
        },
        {
          "@type": "ListItem",
          "position": 2,
          "item": {
            "@type": "WebPage",
            "@id": "{{ url('list') }}",
            "url": "{{ url('list') }}",
            "name": "List"
          }
        },
        {
          "@type": "ListItem",
          "position": 3,
          "item": {
            "@type": "WebPage",
            "@id": "{{ url()->current() }}",
            "url": "{{ url()->current() }}",
            "name": "{{ $category_name }}"
          }
        }
      ]
    }
   ],
}
</script>
<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "{{ url()->current() }}",          
            "name": "{{ $category_name }}"
          } 
      </script>
     @elseif(isset($service_item) && !empty($service_item))
     
     
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
   {
       "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "@id": "{{ url('/') }}#logo",
        "url": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "{{ url('/') }}/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "{{ url('/') }}#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
   },
   {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "name": "Go4Database",
      "url": "{{ url('/') }}",
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      }
    },
    {
      "@type": "Product",
      "name": "{{ $service_item->title }}",
      "image": "{{ get_attachment_image_by_id($service_item->image,'full',true)['img_url'] ?? '' }}",
      "description": "{{ \Illuminate\Support\Str::limit(strip_tags($service_item->description),150) }}",
      "brand": { "@id": "https://www.go4database.com#organization" },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.7",
        "reviewCount": "39"
      }
    },
    
    {
      "@type": "BreadcrumbList",
      "@id": "{{ url()->current() }}#breadcrumb",
      "itemListElement": [
      {
      "@type": "ListItem", 
      "position": 1, 
      "name": "Home",
      "item": "{{ url('/') }}"  
      },
      {
        "@type": "ListItem", 
        "position": 2, 
        "name": "list",
        "item": "{{ url('list') }}"  
      },
      {
      "@type": "ListItem", 
      "position": 3, 
      "name": "{{ $service_item->title }} ",
      "item": "{{ url()->current() }}"  
      }
    ]
    }
  ]
}
</script>

@if(!empty($service_item->faqs))
<script type="application/ld+json">
{!! json_encode([
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => collect($service_item->faqs)->map(function ($faq) {
        return [
            "@type" => "Question",
            "name" => $faq['question'],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => strip_tags($faq['answer'])
            ]
        ];
    })->values()
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif

<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "{{ url()->current() }}",          
            "name": "{{ $service_item->title }}"
          } 
      </script>
      @endif

        @elseif(Request::path()=='career')
        
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "https://www.go4database.com#organization",
      "name": "Go4Database",
      "url": "https://www.go4database.com",
      "logo": {
        "@type": "ImageObject",
        "@id": "https://www.go4database.com#logo",
        "url": "https://www.go4database.com/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "https://www.go4database.com/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "https://www.go4database.com#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [
            {
              "@type": "ListItem", 
              "position": 1, 
              "name": "Home",
              "item": "{{ url('/') }}"  
            },
            {
            "@type": "ListItem", 
            "position": 2, 
            "name": "Career",
            "item": "{{ url(Request::path()) }}"  
            } 
       ]
    }
  ]
}
</script>
  
 @elseif(strpos($url,'career') !== false)
  
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "https://www.go4database.com#organization",
      "name": "Go4Database",
      "url": "https://www.go4database.com",
      "logo": {
        "@type": "ImageObject",
        "@id": "https://www.go4database.com#logo",
        "url": "https://www.go4database.com/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "https://www.go4database.com/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "https://www.go4database.com#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [
            {
              "@type": "ListItem", 
              "position": 1, 
              "name": "Home",
              "item": "{{ url('/') }}"  
            },
            {
            "@type": "ListItem", 
            "position": 2, 
            "name": "Career",
            "item": "{{ url(Request::path()) }}"  
            } 
            {
            "@type": "ListItem", 
            "position": 3, 
            "name": "{{ isset($job) ? $job->title : $category_name }}",
            "item": "{{ url()->current() }}"  
            }
       ]
    }
  ]
}
</script>
@if(isset($job))

@php
function convertToNumber($value)
{
    $value = strtoupper(trim($value));

    if (str_contains($value, 'K')) {
        return (int) str_replace('K', '', $value) * 1000;
    }

    if (str_contains($value, 'M')) {
        return (int) str_replace('M', '', $value) * 1000000;
    }

    return (int) $value;
}

$range = $job->salary;

[$min, $max] = explode('-', $range);

$minValue = convertToNumber($min);
$maxValue = convertToNumber($max);
@endphp

<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "JobPosting",
  "title": "{{ $job->title }}",
  "description": "{{ strip_tags($job->job_context) }}",
  "hiringOrganization": {
    "@type": "Organization",
    "name": "Go4Database",
    "sameAs": "https://www.go4database.com/",
    "logo": "https://go4database.com/assets/uploads/media-uploader/go4database-logo1751528079.png"
  },
  "industry": "{{ $category_name }}",
  "employmentType": "{{ $job->employment_status }}",
  "workHours": "1pm-10pm",
  "datePosted": "{{ $job->created_at->format('Y-m-d') }}",
  "validThrough": "{{ $job->deadline }}",
  "jobLocation": {
    "@type": "Place",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "{{ $job->job_location }}",
      "addressLocality": "Noida",
      "postalCode": "201301",
      "addressCountry": "IN"
    }
  },
  "baseSalary": {
    "@type": "MonetaryAmount",
    "currency": "INR",
    "value": {
      "@type": "QuantitativeValue",
      "minValue": {{ $minValue }},
      "maxValue": {{ $maxValue }},
      "unitText": "MONTH"
    }
  },
  "responsibilities": "{{ strip_tags($job->job_responsibility) }}",
  "qualifications": "{{ strip_tags($job->education_requirement) }}",
  "educationRequirements": "{{ strip_tags($job->education_requirement) }}",
  "experienceRequirements": "{{ strip_tags($job->experience_requirement) }}"
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "WebPage",
  "@id": "#WebPage",
  "url": "{{ url()->current() }}",
  "name": "{{ $job->title }}"
}
</script>

@else

<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "CollectionPage",
  "name": "{{ $category_name }} Jobs",
  "url": "{{ url()->current() }}"
}
</script>

@endif
    @else   
        @if(isset($page_post->schema_code) && $page_post->schema_code!='')
            @php echo $page_post->schema_code; @endphp
        @endif
    @endif    
@endif
