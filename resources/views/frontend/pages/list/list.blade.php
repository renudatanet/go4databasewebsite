@extends('frontend.frontend-page-master')
@section('site-title')
    {{get_static_option('list_page_'.$user_select_lang_slug.'_name')}}
@endsection
@section('page-title')
    {{get_static_option('list_page_'.$user_select_lang_slug.'_name')}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{get_static_option('list_page_'.$user_select_lang_slug.'_meta_description')}}">
    <meta name="tags" content="{{get_static_option('list_page_'.$user_select_lang_slug.'_meta_tags')}}">
    {!! render_og_meta_image_by_attachment_id(get_static_option('list_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection
<style>
   .page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: #6fd943 !important;
    border-color: #6fd943 !important;
}
    </style>
@section('content')
    <section class="service-area service-page padding-120">
        <div class="container">
                <div id="blog-content">
            <div class="row">
                @php $a = 1; @endphp
                @foreach($all_list as $data)
                    <div class="col-lg-4 col-md-6">
                        <x-frontend.list.grid :increment="$a" :list="$data" :allowed-slugs="$allowedSlugs"/>
                       <!-- <pre>{{ print_r($allowedSlugs, true) }}</pre> -->
                    </div>
                    @php
                        if($a == 4){ $a = 1;}else{$a++;}; @endphp
                @endforeach
                <div class="col-lg-12">
                    <div class="pagination-wrapper">
                        <!-- {{$all_list->links()}} -->
                            
                         <nav class="pagination-wrapper" aria-label="Page navigation ">
                            <ul class="pagination justify-content-center" role="navigation">
    @for($i = 1; $i <= $all_list->lastPage(); $i++)
    <li class="page-item {{ ($all_list->currentPage() == $i) ? 'active' : '' }}">
        <a href="javascript:void(0)"
           class="page-link"
           data-page="{{ $i }}">
            {{ $i }}
        </a>
</li>
    @endfor
</nav>
                    </div>
                 </div>
               </div>
            </div>
        </div>
    </section>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '.pagination a.page-link', function(e) {
        e.preventDefault();

        const page = $(this).data('page');
        if (!page) return;

        $.ajax({
            url: window.location.pathname + '?page=' + page,
            type: 'GET',
            success: function(data) {
                const newContent = $(data).find('#blog-content').html();
                $('#blog-content').html(newContent);
               // window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            error: function() {
                alert('Failed to load page.');
            }
        });
    });
</script>
@endsection
