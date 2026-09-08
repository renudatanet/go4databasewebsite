<div class="single-case-studies-item">
    <div class="thumb">
        {!! render_image_markup_by_attachment_id($service->image) !!}
    </div>
    <div class="cart-icon">
        <p class="title"><a href="{{route('frontend.services.single',$service->slug)}}"> {{$service->title}}</a></p>
    </div>
</div> 