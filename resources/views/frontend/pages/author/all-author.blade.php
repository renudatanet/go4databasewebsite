@extends('frontend.frontend-page-master')
@section('site-title')

    @if(get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')!='')
        {{get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')}}
    @else
        {{get_static_option('blog_page_'.$user_select_lang_slug.'_name')}}
    @endif    
     
@endsection
@section('page-title')
    {{get_static_option('blog_page_'.$user_select_lang_slug.'_name')}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{get_static_option('blog_page_'.$user_select_lang_slug.'_meta_description')}}">
    {{-- <meta name="tags" content="{{get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')}}"> --}}
    {!! render_og_meta_image_by_attachment_id(get_static_option('blog_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection
@section('content') 

    <section class="blog-content-area padding-top-100 padding-bottom-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                     <div id="blog-content">
                        
                    @if(count($all_authors) < 1)
                        <div class="alert alert-danger">
                            {{__('No Post Available In ').$author_name.__(' Author')}}
                        </div>
                    @endif
                    
                        @foreach($all_authors as $data)
                            <x-frontend.author.grid :blog="$data" :margin="true"/>
                        @endforeach
                    
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
