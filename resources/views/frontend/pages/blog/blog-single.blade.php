@extends('frontend.frontend-page-master')
@php
  $post_img = null;
  $blog_image = get_attachment_image_by_id($blog_post->image,"full",false);
  $post_img = !empty($blog_image) ? $blog_image['img_url'] : '';
  $share_title = $blog_post->meta_tags ?: $blog_post->title;
  $share_description = $blog_post->meta_description ?: $blog_post->excerpt;
  // Publish Date is free text in the admin; one that isn't a readable date
  // falls back to when the post was created rather than breaking the page.
  $published_at = $blog_post->created_at;
  if (!empty($blog_post->publish_date)) {
      try { $published_at = \Carbon\Carbon::parse($blog_post->publish_date); } catch (\Throwable $e) {}
  }
 @endphp

@section('og-meta')
    <meta property="og:url"  content="{{route('frontend.blog.single',$blog_post->slug)}}" />
    <meta property="og:type"  content="article" />
    <meta property="og:title"  content="{{ $share_title }}" />
    @if(!empty($share_description))
    <meta property="og:description" content="{{ $share_description }}" />
    @endif
    @if(!empty($post_img))
    <meta property="og:image" content="{{$post_img}}" />
    @endif
    @if(!empty(get_static_option('site_'.$user_select_lang_slug.'_title')))
    <meta property="og:site_name" content="{{ get_static_option('site_'.$user_select_lang_slug.'_title') }}" />
    @endif
    @if($published_at)
    <meta property="article:published_time" content="{{ \Carbon\Carbon::parse($published_at)->toIso8601String() }}" />
    @endif
    @if($blog_post->updated_at)
    <meta property="article:modified_time" content="{{ \Carbon\Carbon::parse($blog_post->updated_at)->toIso8601String() }}" />
    @endif
    @if($blog_post->category)
    <meta property="article:section" content="{{ $blog_post->category->name }}" />
    @endif
    <meta name="twitter:card" content="{{ $post_img ? 'summary_large_image' : 'summary' }}" />
    <meta name="twitter:title" content="{{ $share_title }}" />
    @if(!empty($share_description))
    <meta name="twitter:description" content="{{ $share_description }}" />
    @endif
    @if(!empty($post_img))
    <meta name="twitter:image" content="{{$post_img}}" />
    @endif
@endsection
@section('page-meta-data')
    <meta name="description" content="{{$blog_post->meta_description}}">
    <meta name="tags" content="{{$blog_post->tags}}">
@endsection
@section('site-title')
    @if($blog_post->meta_tags!='')
        {{$blog_post->meta_tags}}
    @else
        {{$blog_post->title}}
    @endif
@endsection
@section('page-title')
    {{$blog_post->title}}
@endsection
{{-- The lead image is the largest thing on a post's first screen. Its <img>
     sits after ~80KB of inline styles, so the browser only found it late;
     asking for it from the <head> starts it straight away. Same copy as the
     <img> in partials/post (the 740px large- one). --}}
@php
  $hero_preload = get_attachment_image_by_id($blog_post->image, 'large');
@endphp
@if(!empty($hero_preload['img_url']))
@section('style')
    <link rel="preload" as="image" href="{{ $hero_preload['img_url'] }}" fetchpriority="high">
@endsection
@endif
{{-- The post's own header carries the title and breadcrumb. --}}
@section('no-breadcrumb', '1')
{{-- The layout, styles and script are in pages/blog/partials/post. The page
     no longer loads Font Awesome or blog.css (both were loaded twice), or
     the Disqus script, whose comment box had been switched off. --}}
@section('content')
    @include('frontend.pages.blog.partials.post')
@endsection
