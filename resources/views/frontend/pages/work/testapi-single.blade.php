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
                <div class="col-sm-12">
                <form class="row  row-cols-lg-auto g-3 align-items-center custom-filter-form" enctype="multipart/form-data">
    <!-- CSRF token for Laravel -->
    <input type="hidden" name="_token" value="EM27Ri7a0qTyLbUAG75AAirRoaZXTyr8mjnRDfon" autocomplete="off">     

        <div class="col-12">
        <input type="text" placeholder="Title" class="form-control" id="search-title">
        </div>

        <div class="col-12">
        <input type="text" placeholder="Industry" class="form-control" id="search-industry" value="{{ $work_item->title }}">
        </div>
        <div class="col-12">
        <input type="text" placeholder="Business" class="form-control" id="search-business">
        </div>
        

        <div class="col-12">
        <input type="text" placeholder="Location" class="form-control" id="search-location">
        </div>
        
        <div class="col-12">
            <button  type="button" onclick="fetchLeads()">Search</button>
   

        </div>
     </form>
     </div>
     <h2>Leads List</h2>

<table border="1" cellpadding="10" id="leadsTable">
    <thead>
        <tr>
            <th>Title</th>
            <th>Company</th>
            <th>Person</th>
            <th>Email</th>
            <th>City</th>
            <th>Industry</th>
            
            <th> Biz Category</th>
           
        </tr>
    </thead>
    <tbody></tbody>
</table>
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
                <!--  <div class="col-lg-12 " {!! render_background_image_markup_by_attachment_id(get_static_option('about_page_testimonial_background_image')) !!}>
                    <div class="testimonial-carousel-area margin-top-10 ">
                        <div class="testimonial-carousel global-carousel-init"
                             data-loop="true"
                             data-desktopitem="1"
                             data-mobileitem="1"
                             data-tabletitem="1"
                             data-autoplay="true"
                             data-margin="0"
                        >
                            @foreach($all_testimonial as $data)
                                <div class="single-testimonial-item ">
                                    <div class="content style-01">
                                        <div class="thumb ">
                                            {!! render_image_markup_by_attachment_id($data->image) !!}
                                        </div>
                                        <p class="description ">{{$data->description}}</p>
                                        <div class="author-details ">
                                            <div class="author-meta ">
                                                <h2 class="title ">{{$data->name}}</h2>
                                                <span class="designation ">{{$data->designation}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>-->

                    <!--  <div class="col-md-12" >
                    <div class="client-area">
                        <div class="client-active-area global-carousel-init"
                             data-loop="true"
                             data-desktopitem="5"
                             data-mobileitem="2"
                             data-tabletitem="3"
                             data-autoplay="true"
                             data-margin="80"
                        >
                            @foreach($all_brand_logo as $data)
                                <div class="single-brand">
                                    <div class="img-wrapper">
                                        @if(!empty($data->url) )<a href="{{$data->url}}">@endif
                                            {!! render_image_markup_by_attachment_id($data->image) !!}
                                            @if(!empty($data->url) )  </a>@endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>-->
            </div>
        </div>
    </div>
<script>
function fetchLeads() {

    let title = document.getElementById('search-title').value;
    let industry = document.getElementById('search-industry').value;
    let business = document.getElementById('search-business').value;
    let location = document.getElementById('search-location').value;

    fetch(`https://app.go4database.com/api/getleads?industry=${encodeURIComponent(industry)}&title=${encodeURIComponent(title)}&business=${encodeURIComponent(business)}&location=${encodeURIComponent(location)}`)
    .then(res => res.json())
    .then(data => {
        let rows = '';

        data.forEach(lead => {
            rows += `
                <tr>
                    <td>${lead.title}</td>
                    <td>${lead.company}</td>
                    <td>${lead.person_name}</td>
                    <td>${lead.email}</td>
                    <td>${lead.city}</td>
                    <td>${lead.industry}</td>
                    <td>${lead.biz_category}</td>
                </tr>
            `;
        });

        document.querySelector('#leadsTable tbody').innerHTML = rows;
    })
    .catch(err => console.log(err));
}
</script>
@endsection
