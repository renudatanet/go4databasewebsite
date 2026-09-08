<div class="blog-classic-item-01 {{$margin ? 'margin-bottom-60' : ''}}">
    <div class="thumbnail">
        {!! render_image_markup_by_attachment_id($blog->image) !!}
    </div>
    <div class="content">
        <ul class="post-meta">
            <li><a href="{{route('frontend.blog')}}?author={{$blog->author}}"><i class="fa fa-user"></i> {{$blog->author}}</a></li>
            <li>
                <a href="{{route('frontend.blog.single',$blog->slug)}}">Last updated <!-- <i class="far fa-clock"></i> -->      
                    <!-- {{date_format($blog->created_at,'d M y')}} -->
                    @if(isset($blog->publish_date) && $blog->publish_date!='')
                        {{ \Carbon\Carbon::parse($blog->publish_date)->format('d M y') }}
                    @else
                        {{date_format($blog->created_at,'d M y')}}
                    @endif
                </a>
            </li>
            <li>
                <div class="cats"><i class="fas fa-microchip"></i>
                    {!! get_blog_category_by_id($blog->blog_categories_id,'link') !!}
                </div>
            </li>
            <li>
                <div class="cats">
                    <i class="fas fa-users"></i>
                    @php
                        $Counting = '327';
                        $randomNumber = random_int(3, 10);
                        //$TotalVisitors = $Counting+$randomNumber;
                        $TotalVisitors = $blog->total_visitors;
                    @endphp
                    {{$TotalVisitors}}
                </div>
            </li>
        </ul>
        <h4 class="title"><a href="{{route('frontend.blog.single',$blog->slug)}}">{{$blog->title}}</a></h4>
        <p>{{$blog->excerpt}}</p>
        <div class="btn-wrapper">
            <a href="{{route('frontend.blog.single',$blog->slug)}}" class="boxed-btn reverse-color">{{get_static_option('blog_page_'.$user_select_lang_slug.'_read_more_btn_text')}}</a>
        </div>
    </div>
</div>