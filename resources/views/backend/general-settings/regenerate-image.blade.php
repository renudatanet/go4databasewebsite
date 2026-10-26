@extends('backend.admin-master')
@section('site-title')
    {{__('Regenerate Image Settings')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                @include('backend.partials.message')
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__("Regenerate Image Settings")}}</h4>
                        <p class="text-muted">{{__('Makes the smaller copies of each image that pages load instead of the full original (the blog uses them for its cards). Safe to run any time: copies that already exist are left alone. Run it after adding posts whose images did not come through the media uploader.')}}</p>
                       <div class="message-wrap"></div>
                        <button type="button" class="btn btn-primary mt-4 pr-4 pl-4 regenarate_image">{{__('Regenerate Images')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        // Each request works for about 20 seconds; keep asking until the
        // server reports nothing is waiting.
        var regenerateTotal = 0;
        function regenerateBatch() {
            $.ajax({
                type : 'post',
                url: "{{route('admin.general.regenerate.thumbnail')}}",
                data :{
                    _token: "{{csrf_token()}}"
                },
                success: function (data){
                    regenerateTotal += (data.made || 0);
                    $('.message-wrap').html($('<p>').addClass('alert alert-' + data.type).text(data.msg));
                    if (data.waiting > 0) { regenerateBatch(); return; }
                    $('.regenarate_image i').remove();
                    $('.regenarate_image').attr('disabled',false);
                },
                error: function (){
                    $('.message-wrap').html($('<p>').addClass('alert alert-danger').text('{{__('The server stopped responding. Click the button again to carry on; finished images are kept.')}}'));
                    $('.regenarate_image i').remove();
                    $('.regenarate_image').attr('disabled',false);
                }
            });
        }
        $(document).on('click','.regenarate_image',function (){
            $(this).attr('disabled',true);
            $('.message-wrap').html('<p class="alert alert-warning">{{__('Working… this can take a few minutes for a large library.')}}</p>');
            $(this).append('<i class="fas fa-spinner fa-spin"></i>');
            regenerateTotal = 0;
            regenerateBatch();
        });
    </script>
@endsection
