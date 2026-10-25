@extends('frontend.frontend-page-master')
@php $blogcat = $blogcat ?? ($blogCat ?? null); @endphp
@section('page-title')
    {{__('Category:')}} {{' '.$category_name}}
@endsection
@section('site-title')
    @if(!empty($blogcat) && $blogcat->meta_tags!='')
        {{$blogcat->meta_tags}}
    @else
        Category: {{' '.$category_name}}
    @endif
    @if($all_blogs->currentPage() > 1) - Page {{$all_blogs->currentPage()}}@endif
@endsection 
@section('page-meta-data')
    <meta name="description" content="{{$blogcat->meta_description ?? ''}}">
@endsection
{{-- The listing's own header carries the title and breadcrumb. --}}
@section('no-breadcrumb', '1')
@section('content')
    @include('frontend.pages.blog.partials.listing', [
        'mode' => 'topic',
        'heading' => $category_name,
        'lead' => $blogcat->meta_description ?? null,
        'crumb' => $category_name,
        'active_topic' => $blogcat->id ?? null,
        'list_heading' => 'Latest in '.$category_name,
    ])
@endsection
