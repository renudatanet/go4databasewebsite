<div class="blog-classic-item-01 {{$margin ? 'margin-bottom-60' : ''}}">
    <div class="thumbnail">
        {!! render_image_markup_by_attachment_id($blog->image) !!}
    </div>
    <div class="content">
        <ul class="post-meta">
            <li>
          @if($blog->authorData && $blog->authorData->slug)
    <a href="{{ route('frontend.author.single', $blog->authorData->slug) }}">
       <i class="fa fa-user"></i>   {{ $blog->authorData->name }}
    </a>
@else
    <span>{{ $blog->authorData->name ?? 'Unknown Author' }}</span>
@endif
       
</li>
            <li>
                <a href="{{route('frontend.blog.single',$blog->slug)}}">Last updated <!-- <i class="far fa-clock"></i> -->      
                    <!-- {{date_format($blog->created_at,'d M y')}} -->
                    @if(isset($blog->updated_at) && $blog->updated_at!='')
                        {{ \Carbon\Carbon::parse($blog->updated_at)->format('d M y') }}
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
        <h2 class="title"><a href="{{route('frontend.blog.single',$blog->slug)}}">{{$blog->title}}</a></h2>
        <p>{{$blog->excerpt}}</p>
        <div class="btn-wrapper">
            <a href="{{route('frontend.blog.single',$blog->slug)}}" class="boxed-btn reverse-color">{{get_static_option('blog_page_'.$user_select_lang_slug.'_read_more_btn_text')}}</a>
        </div>
    </div>
</div>