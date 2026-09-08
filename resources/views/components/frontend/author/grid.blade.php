<div class="blog-classic-item-01 {{$margin ? 'margin-bottom-60' : ''}}">
    <div class="thumbnail">
        {!! render_image_markup_by_attachment_id($blog->image) !!}
    </div>
    <div class="content">
        
        <h2 class="title"><a href="{{route('frontend.blog.single',$blog->slug)}}">{{$blog->title}}</a></h2>
        <p>{{$blog->excerpt}}</p>
        <div class="btn-wrapper">
            <a href="{{route('frontend.blog.single',$blog->slug)}}" class="boxed-btn reverse-color">{{get_static_option('blog_page_'.$user_select_lang_slug.'_read_more_btn_text')}}</a>
        </div>
    </div>
</div>