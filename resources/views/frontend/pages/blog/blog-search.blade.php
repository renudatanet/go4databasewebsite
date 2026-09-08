@extends('frontend.frontend-page-master')
@section('page-title')
    {{__('Search For: ')}} {{$search_term}}
@endsection
@section('content')
    <section class="blog-content-area padding-top-100 padding-bottom-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div id="blog-content">
                        @if(count($all_blogs) < 1)
                            <div class="alert alert-danger">
                                {{__('Nothing found related to').' '.$search_term}}
                            </div>
                        @endif
                        @foreach($all_blogs as $data)
                                <x-frontend.blog.grid :blog="$data" :margin="true"/>
                        @endforeach
                    <div class="pagination-wrapper" aria-label="Page navigation ">
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

        // Get the page number from the clicked link's data-page attribute
        const page = $(this).data('page');
        if (!page) return;

        // Get current URL query params (e.g. search=b2b)
        const urlParams = new URLSearchParams(window.location.search);

        // Set or update the 'page' param to the clicked page number
        urlParams.set('page', page);

        // Compose the AJAX URL with full query string including search, page, etc.
        const ajaxUrl = window.location.pathname + '?' + urlParams.toString();

        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            success: function(data) {
                // Replace the blog content and pagination with the new page's content
                const newContent = $(data).find('#blog-content').html();
                $('#blog-content').html(newContent);
                //window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            error: function() {
                alert('Failed to load page.');
            }
        });
    });
</script>

@endsection
