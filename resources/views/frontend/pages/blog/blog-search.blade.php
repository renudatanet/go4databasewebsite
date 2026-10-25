@extends('frontend.frontend-page-master')
@section('page-title')
    {{__('Search For: ')}} {{$search_term}}
@endsection
{{-- The listing's own header carries the title and breadcrumb. --}}
@section('no-breadcrumb', '1')
@section('content')
    @include('frontend.pages.blog.partials.listing', [
        'mode' => 'search',
        'heading' => 'Results for “'.$search_term.'”',
        'lead' => $all_blogs->total() === 1 ? '1 article matches your search.' : number_format($all_blogs->total()).' articles match your search.',
        'crumb' => 'Search',
        'search_term' => $search_term,
        'list_heading' => 'Matching articles',
    ])
@endsection
