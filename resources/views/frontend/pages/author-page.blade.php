@extends('frontend.frontend-page-master')
@section('site-title')
    {{get_static_option('author_page_'.$user_select_lang_slug.'_name')}}
@endsection
@section('page-title')
    {{__('Author')}}
@endsection
@section('page-meta-data')

    <meta name="description" content="{{get_static_option('author_page_'.$user_select_lang_slug.'_meta_description')}}">
    <meta name="tags" content="{{get_static_option('author_page_'.$user_select_lang_slug.'_meta_tags')}}">
    {!! render_og_meta_image_by_attachment_id(get_static_option('author_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection
@push('styles')
<style>
    
/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 40px 30px;
}

/* AUTHOR ITEM */
.grid .author {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    text-align: left;
    transition: 0.3s;
}

.author:hover {
    transform: translateY(-5px);
}

/* IMAGE */
.grid .author img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 4px;
    filter: grayscale(100%);
}
.author-content h3 {
    font-size: 16px;
    color: #000000;
    margin-bottom: 5px;
    cursor: pointer;
}
.author-content p {
    font-size: 15px;
    color: #333;
    line-height: 1.5;
}
/* SECTION */
.section {
    padding: 60px 80px;
}

.section h1 {
    text-align: center;
    font-size: 42px;
    margin-bottom: 10px;
}
.team-section h1 {
    margin-bottom: 50px;
}
.underline {
    width: 220px;
    height: 4px;
    background: #000000;
    margin: 10px auto 50px;
    border-radius: 10px;
}
/* RESPONSIVE */
@media(max-width: 900px) {
    .section {
        padding: 40px 20px;
    }
}
/* RESPONSIVE */
@media (max-width: 1024px) {
    .grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .section {
        padding: 40px 20px;
    }

    .grid {
        grid-template-columns: 1fr;
    }

    .author {
        align-items: center;
    }
}
.bg-grey{
    background-color: #f2f2f2;
}
</style>
@endpush
@section('content')

<section class="section bg-grey">
    <div class="team-section">
    <h1>Explore Our Other Authors</h1>
</div>
    <div class="grid">
@foreach($all_author as $data)
        <!-- AUTHOR 1 -->
        <div class="author">
            {!! render_image_markup_by_attachment_id($data->image,null,'grid') !!}
            <!--<img src="https://www.go4database.com/assets/uploads/media-uploader/about-image-min1616328572.jpg" alt="">-->
            <div class="author-content">
                <a href="{{route('frontend.author.single',$data->slug)}}">
                <h3> {{__($data->name)}}</h3>
                <p> {{ \Illuminate\Support\Str::words(
    html_entity_decode(strip_tags($data->content)),
    10,
    '...'
) }}</p>
                </a>
            </div>
        </div>

         @endforeach

    </div>
</section>
@endsection
