@extends('frontend.frontend-page-master')
@php
    $page_name = get_static_option('work_page_'.$user_select_lang_slug.'_name')
@endphp
@section('site-title')
    {{$page_name}} : {{$category_name}}
@endsection
@section('page-title')
    {{$page_name}} : {{$category_name}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{get_static_option('work_page_'.$user_select_lang_slug.'_meta_description')}}">
    <meta name="tags" content="{{get_static_option('work_page_'.$user_select_lang_slug.'_meta_tags')}}">
    {!! render_og_meta_image_by_attachment_id(get_static_option('work_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection
@section('content')
    <div class="page-content portfolio padding-top-120 padding-bottom-90">
        <div class="container">
            <div class="row">
                <div id="work-content">
                @forelse($all_work as $data)
                    <div class="col-lg-6 col-md-6 margin-bottom-40">
                        <x-frontend.work.grid :work="$data" />
                    </div>
                @empty
                      <div class="col-lg-12">
                          <div class="alert alert-warning">{{__('No ')}} {{$page_name}} {{__('Found')}} {{__('In')}} {{$category_name}}</div>
                      </div>
                @endforelse
                </div>
                <div class="col-lg-12">
                    <div class="post-pagination-wrapper">
                        <!--{{$all_work->links()}}-->
                         {{ $all_work->links('vendor.pagination.default') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                const newContent = $(data).find('#work-content').html();
                $('#work-content').html(newContent);
               // window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            error: function() {
                alert('Failed to load page.');
            }
        });
    });
</script>

@endsection
