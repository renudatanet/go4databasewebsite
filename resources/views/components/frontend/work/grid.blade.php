<div class="single-case-studies-item">
    <div class="thumb">
        {!! render_image_markup_by_attachment_id($work->image) !!}
    </div>
    <div class="cart-icon">
        <p class="title"><a href="{{route('frontend.work.single',$work->slug)}}"> {{$work->title}}</a></p>
    </div>
</div> 