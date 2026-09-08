@extends('frontend.frontend-page-master')
@section('site-title')

@if(get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')!='')
        {{get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')}}
    @else
        {{get_static_option('blog_page_'.$user_select_lang_slug.'_name')}}
    @endif    
     
@endsection
@section('page-title')
    {{get_static_option('blog_page_'.$user_select_lang_slug.'_name')}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{get_static_option('blog_page_'.$user_select_lang_slug.'_meta_description')}}">
    {{-- <meta name="tags" content="{{get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')}}"> --}}
    {!! render_og_meta_image_by_attachment_id(get_static_option('blog_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection
@section('content') 

    <section class="blog-content-area padding-120">
        <div class="container">
          
        </div>
    </section>


@endsection
