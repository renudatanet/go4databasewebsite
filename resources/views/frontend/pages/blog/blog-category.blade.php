@extends('frontend.frontend-page-master')
@section('page-title')
    {{__('Category:')}} {{' '.$category_name}}
@endsection
@section('site-title')
    @if($blogcat->meta_tags!='')
        {{$blogcat->meta_tags}}
    @else
        Category: {{' '.$category_name}}
    @endif
@endsection 
@section('page-meta-data')
    <meta name="description" content="{{$blogcat->meta_description}}">
@endsection

@section('content')

    <section class="blog-content-area padding-top-100 padding-bottom-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                     <div id="blog-content">
                    @if(count($all_blogs) < 1)
                        <div class="alert alert-danger">
                            {{__('No Post Available In ').$category_name.__(' Category')}}
                        </div>
                    @endif
                        @foreach($all_blogs as $data)
                            <x-frontend.blog.grid :blog="$data" :margin="true"/>
                        @endforeach
                    <div class="pagination-wrapper" aria-label="Page navigation">
                      {{ $all_blogs->links('vendor.pagination.default') }}
                    </div>
                </div>
                
</div>
                <div class="col-lg-4">
                   @include('frontend.pages.blog.sidebar')
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
