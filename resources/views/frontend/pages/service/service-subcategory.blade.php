@extends('frontend.frontend-page-master')
@section('page-title')
    {{get_static_option('service_page_'.$user_select_lang_slug.'_name')}} {{__('Category:')}} {{$category_name}}
@endsection
@section('site-title')
    {{get_static_option('service_page_'.$user_select_lang_slug.'_name')}} {{__('Category:')}} {{$category_name}}
@endsection
@section('content')
    <section class="blog-content-area padding-100">
        <div class="container">
            <div class="row">
                @if(empty($service_items))
                    <div class="col-lg-12">
                        <div class="alert alert-danger">{{__('No Post Available In This Category')}}</div>
                    </div>
                @endif
                    @php $a = 1; @endphp
                    @foreach($service_items as $data)
                        <div class="col-lg-4 col-md-6">
                           <div class="single-what-we-cover-item-02 margin-bottom-30">
    <div class="single-what-img">
        {!! render_image_markup_by_attachment_id($data->image) !!}
    </div>
    
    @if($data->icon_type === 'icon' || $data->icon_type == '')
        <div class="icon-02 style-0{{$increment ?? '' }}">
            <i class="{{$data->icon}}"></i>
        </div>
    @else
        <div class="img-icon style-0{{$increment ?? ''}}">
            {!! render_image_markup_by_attachment_id($data->img_icon) !!}
        </div>
    @endif
   
    <div class="content">
        @php

    $subcategory = \App\ServiceSubcategory::find($data->subcategories_id);

    $url = route('frontend.services.single', [
        'category_slug' => $category_slug,
        'child_category_slug' => $subcategory_slug,
        'service_slug' => $data->slug
    ]);

    if($subcategory && $subcategory->service_type == 1){

        $url = route('frontend.services.single.b2b', [
            'category_slug' => $category_slug,
            'child_category_slug' => $subcategory_slug,
            'title' => \Illuminate\Support\Str::slug($subcategory->title),
            'service_slug' => $data->slug
        ]);

    }

@endphp

<a href="{{ $url }}" >
       
    <h4 class="title">{{ $data->title }}</h4>
</a>
    </div>
</div>
                        </div>
                        @php  if($a == 4){ $a = 1;}else{$a++;}; @endphp
                    @endforeach
                <nav class="pagination-wrapper" aria-label="Page navigation">
                    {{$service_items->links()}}
                </nav>
            </div>
        </div>
    </section>
@endsection
