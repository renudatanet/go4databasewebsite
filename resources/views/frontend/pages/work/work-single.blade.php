@extends('frontend.frontend-page-master')

@section('page-meta-data')
    <meta name="description" content="{{$work_item->meta_description}}">
    <meta name="tags" content="{{$work_item->meta_tag}}">
@endsection 
@section('og-meta')
    <meta property="og:url"  content="{{route('frontend.work.single',$work_item->slug)}}" />
    <meta property="og:type"  content="article" />
    <meta property="og:title"  content="{{$work_item->title}}" />
    {!! render_og_meta_image_by_attachment_id($work_item->image) !!}
@endsection
@section('site-title')
    @if($work_item->meta_tag!='')
        {{$work_item->meta_tag}}
    @else
        {{$work_item->title}} - {{get_static_option('work_page_'.$user_select_lang_slug.'_name')}}
    @endif    
@endsection
@section('page-title')
     {{$work_item->title}}
@endsection
@section('content')
<style>
    .boxed-btn{
        background-color: var(--main-color-one);
    color: #fff;
    display: inline-block;
    padding: 12px 40px;
    border-radius: 25px;
    min-width: 160px;
    text-align: center;
    -webkit-transition: all .3s ease-in;
    -o-transition: all .3s ease-in;
    transition: all .3s ease-in;
    font-weight: 600;
    border: none;
    }
    .post-description table{
         width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch; /* smooth scrolling on iOS */
    }
</style>
    <div class="work-details-content-area padding-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="portfolio-details-item">
                        <div class="thumb">
                          {!! render_image_markup_by_attachment_id($work_item->image) !!}
                        </div>
                        <div class="post-description">
                            {!! $work_item->description !!} 
                        </div>

                        @php $gallery_item = $work_item->gallery ? explode('|',$work_item->gallery) : []; @endphp
                        @if(!empty($gallery_item))
                        
                        <div class="case-study-gallery-wrapper">
                            <h2 class="main-title">{{get_static_option('case_study_'.$user_select_lang_slug.'_gallery_title')}}</h2>
                            <div class="case-study-gallery-carousel global-carousel-init"
                                 data-loop="true"
                                 data-desktopitem="1"
                                 data-mobileitem="1"
                                 data-tabletitem="1"
                                 data-nav="true"
                                 data-autoplay="true"
                                 data-margin="0"
                            >
                                @foreach($gallery_item as $gall)
                                <div class="single-gallery-item">
                                    {!! render_image_markup_by_attachment_id($gall) !!}
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="project-widget">
                        <div class="widget-nav-menu margin-bottom-30">
                          
                        </div>
                        {!! App\WidgetsBuilder\WidgetBuilderSetup::render_frontend_sidebar('case_study',['column' => false]) !!}
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="related-work-area padding-top-100">
                        <div class="section-title margin-bottom-30">
                            <h2 class="title">{{get_static_option('case_study_'.$user_select_lang_slug.'_related_title')}}</h2>
                        </div>
                            <div class="related-case-study-carousel global-carousel-init"
                                 data-loop="true"
                                 data-desktopitem="3"
                                 data-mobileitem="1"
                                 data-tabletitem="1"
                                 data-nav="true"
                                 data-autoplay="true"
                                 data-margin="40"
                            >
                            @foreach($related_works as $data)
                                <div class="single-related-case-study-item">
                                    <div class="thumb">
                                        {!! render_image_markup_by_attachment_id($data->image) !!}
                                    </div>
                                    <div class="content">
                                        <p class="title"><a href="{{route('frontend.work.single',$data->slug)}}"> {{$data->title}}</a></p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
@endsection
