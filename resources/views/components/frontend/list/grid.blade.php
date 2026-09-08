<div class="single-what-we-cover-item-02 margin-bottom-30">
    <div class="single-what-img">
        {!! render_image_markup_by_attachment_id($list->image) !!}
    </div>
    
    @if($list->icon_type === 'icon' || $list->icon_type == '')
        <div class="icon-02 style-0{{$increment ?? '' }}">
            <i class="{{$list->icon}}"></i>
        </div>
    @else
        <div class="img-icon style-0{{$increment ?? ''}}">
            {!! render_image_markup_by_attachment_id($list->img_icon) !!}
        </div>
    @endif
   
    <div class="content">
        <a href="{{ in_array($list->slug, $allowedSlugs) 
    ? route('frontend.list.single', $list->slug) 
    : 'javascript:void(0)' }}">
    <h4 class="title">{{ $list->name }}</h4>
</a>
        <p>{{$list->excerpt}}</p>
    </div>
</div>