@extends('backend.admin-master')
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
@endsection
@section('site-title')
    {{__('SEO Settings')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                @include('backend.partials.message')
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__("SEO Settings")}}</h4>
                        <form action="{{route('admin.general.seo.settings')}}" method="post" enctype="multipart/form-data" id="myForm">
                            @csrf
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    @foreach($all_languages as $key => $lang)
                                        <a class="nav-item nav-link @if($key == 0) active @endif" id="nav-home-tab" data-toggle="tab" href="#nav-home-{{$lang->slug}}" role="tab" aria-controls="nav-home" aria-selected="true">{{$lang->name}}</a>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content margin-top-30" id="nav-tabContent">
                                @foreach($all_languages as $key => $lang)
                                    <div class="tab-pane fade @if($key == 0) show active @endif" id="nav-home-{{$lang->slug}}" role="tabpanel" aria-labelledby="nav-home-tab">
                                        <div class="form-group">
                                            <label for="site_meta_{{$lang->slug}}_tags">{{__('Site Title')}}</label>
                                            <input type="text" name="site_meta_{{$lang->slug}}_tags"  class="form-control site_meta" value="{{get_static_option('site_meta_'.$lang->slug.'_tags')}}" id="site_meta_{{$lang->slug}}_tags" maxlength="60">
                                            <span class="titleLengthMessage"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="site_meta_{{$lang->slug}}_description">{{__('Site Meta Description')}}</label>
                                            <textarea name="site_meta_{{$lang->slug}}_description"  class="form-control site_description" id="site_meta_{{$lang->slug}}_description" maxlength="160">{{get_static_option('site_meta_'.$lang->slug.'_description')}}</textarea>
                                            <span class="descriptionLengthMessage"></span>
                                        </div>

                                        
                                        <div class="form-group">
                                            <label for="site_meta_{{$lang->slug}}_schema_code">{{__('Schema')}}</label>
                                            <textarea name="site_meta_{{$lang->slug}}_schema_code"  class="form-control" rows="5" id="site_meta_{{$lang->slug}}_schema_code ">@php echo get_static_option('site_meta_'.$lang->slug.'_schema_code'); @endphp</textarea>
                                        </div>
                                    </div>                          

                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Changes')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <script>
        
        
        $(document).ready(function() {
            $('.site_meta').on('input', function() {
                let title = $(this).val();
                let length = title.length;

                if (length > 60) {
                    $(this).parent().find('.titleLengthMessage')
                        .text('Title must be 60 characters or less.')
                        .css('color', 'red');
                    $('button[type="submit"]').prop('disabled', true);
                } else {
                    $(this).parent().find('.titleLengthMessage')
                        .text(`Characters used: ${length}/60`)
                        .css('color', 'green');
                    $('button[type="submit"]').prop('disabled', false);
                }
            });

            
            $('.site_description').on('input', function() {
                let title = $(this).val();
                let length = title.length;

                if (length > 160) {
                    $(this).parent().find('.descriptionLengthMessage')
                        .text('Title must be 160 characters or less.')
                        .css('color', 'red');
                    $('button[type="submit"]').prop('disabled', true);
                } else {
                    $(this).parent().find('.descriptionLengthMessage')
                        .text(`Characters used: ${length}/160`)
                        .css('color', 'green');
                    $('button[type="submit"]').prop('disabled', false);
                }
            });
        });
    </script>
@endsection
