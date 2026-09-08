@extends('frontend.frontend-page-master')
@section('page-title')
    {{__('Category:')}} {{$category_name}}
@endsection
@section('site-title')
    {{__('Category:')}} {{$category_name}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{ $job_category->meta_description }}">
    <meta name="tags" content="{{ $job_category->meta_tags }}">
    {!! render_og_meta_image_by_attachment_id(get_static_option('career_with_us_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection
@section('content')
    <section class="blog-content-area padding-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        @if(count($all_jobs) > 0)
                        @foreach($all_jobs as $data)
                            <div class="col-lg-12">
                                <x-frontend.job.grid :job="$data" />
                            </div>
                        @endforeach
                        @else
                            <div class="col-lg-12">
                                <div class="alert alert-warning">{{__('Nothing Found In This Category')}}</div>
                            </div>
                        @endif

                    </div>
                    <div class="col-lg-12">
                        <nav class="pagination-wrapper text-center" aria-label="Page navigation ">
                            {{$all_jobs->links()}}
                        </nav>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="widget-area">
                        {!! App\WidgetsBuilder\WidgetBuilderSetup::render_frontend_sidebar('career',['column' => false]) !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
