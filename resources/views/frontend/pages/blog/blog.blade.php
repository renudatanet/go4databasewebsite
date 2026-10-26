@extends('frontend.frontend-page-master')
@php
    $blog_page_name = get_static_option('blog_page_'.$user_select_lang_slug.'_name');
    $author_filter = request()->query('author');
@endphp
@section('site-title')

    @if(get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')!='')
        {{get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')}}
    @else
        {{$blog_page_name}}
    @endif
    {{-- Every page of the listing had the same title. --}}
    @if($all_blogs->currentPage() > 1) - Page {{$all_blogs->currentPage()}}@endif

@endsection
@section('page-title')
    {{$blog_page_name}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{get_static_option('blog_page_'.$user_select_lang_slug.'_meta_description')}}">
    {{-- <meta name="tags" content="{{get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')}}"> --}}
    @include('frontend.pages.blog.partials.meta', [
        'share_title' => get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags') ?: $blog_page_name,
        'share_description' => get_static_option('blog_page_'.$user_select_lang_slug.'_meta_description'),
        'share_image' => get_attachment_image_url(get_static_option('blog_page_'.$user_select_lang_slug.'_meta_image')),
    ])
@endsection
{{-- The listing's own header carries the title and breadcrumb. --}}
@section('no-breadcrumb', '1')
@section('content')
    @include('frontend.pages.blog.partials.listing', $author_filter ? [
        'mode' => 'author',
        'heading' => $author_filter,
        'lead' => null,
        'crumb' => $author_filter,
        'list_heading' => 'Articles by '.$author_filter,
    ] : [
        'mode' => 'index',
        // Page Heading (H1) from Blog Page Settings, else the page name.
        'heading' => get_static_option('blog_page_'.$user_select_lang_slug.'_heading') ?: ($blog_page_name ?: 'Blog'),
        // Header text from Blog Page Settings, else the SEO description.
        'lead' => get_static_option('blog_page_'.$user_select_lang_slug.'_subtitle') ?: get_static_option('blog_page_'.$user_select_lang_slug.'_meta_description'),
        'list_heading' => 'All articles',
    ])
@endsection
