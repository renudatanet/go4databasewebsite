@extends('backend.admin-master')
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/media-uploader.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/nice-select.css')}}">
@endsection
@section('site-title')
    {{__('Edit Services')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-error-msg/>
                <x-flash-msg/>
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between">
                            <h4 class="header-title">{{__('Edit List')}}</h4>
                            <a href="{{route('admin.list')}}" class="btn btn-primary">{{__('All List')}}</a>
                        </div>
                        <form action="{{route('admin.list.update')}}" method="post" enctype="multipart/form-data" id="myForm">
                            @csrf
                            <input type="hidden" name="id" value="{{$service->id}}">
                            <div class="form-group">
                                <label for="language">{{__('Language')}}</label>
                                <select name="lang" id="language" class="form-control">
                                    @foreach(get_all_language() as $language)
                                        <option @if($language->slug == $service->lang) selected @endif value="{{$language->slug}}">{{$language->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="title">{{__('Title')}}</label>
                                <input type="text" class="form-control"  id="title"  name="title" value="{{$service->title}}">
                            </div>
                            <div class="form-group">
                                <label for="title">{{__('Slug')}}</label>
                                <input type="text" class="form-control"  id="slug"  name="slug" value="{{$service->slug}}">
                            </div>
                            <div class="form-group">
                                <label for="edit_icon_type">{{__('Icon Type')}}</label>
                                <select name="icon_type" class="form-control" id="edit_icon_type">
                                    <option @if($service->icon_type == 'icon') selected @endif value="icon">{{__("Font Icon")}}</option>
                                    <option @if($service->icon_type == 'image') selected @endif value="image">{{__("Image Icon")}}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="icon" class="d-block">{{__('Icon')}}</label>
                                <div class="btn-group ">
                                    <button type="button" class="btn btn-primary iconpicker-component">
                                        <i class="{{$service->icon}}"></i>
                                    </button>
                                    <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle"
                                            data-selected="{{$service->icon}}" data-toggle="dropdown">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu"></div>
                                </div>
                                <input type="hidden" class="form-control"  id="icon" value="{{$service->icon}}" name="icon">
                            </div>
                            <div class="form-group">
                                <label for="img_icon">{{__('Image Icon')}}</label>
                                <div class="media-upload-btn-wrapper">
                                    <div class="img-wrap">
                                        @php
                                            $service_section_img = get_attachment_image_by_id($service->img_icon,null,true);
                                            $image_btn_label = __('Upload Image Icon');
                                        @endphp
                                        @if (!empty($service_section_img))
                                            <div class="attachment-preview">
                                                <div class="thumbnail">
                                                    <div class="centered">
                                                        <img class="avatar user-thumb" src="{{$service_section_img['img_url']}}" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                            $image_btn_label = __('Update Image Icon');
                                            @endphp
                                        @endif
                                    </div>
                                    <input type="hidden" id="img_icon" name="img_icon" value="{{$service->img_icon}}">
                                    <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                                        {{$image_btn_label}}
                                    </button>
                                </div>
                                <small>{{__('Recommended image size 60x60')}}</small>
                            </div>
                            <div class="form-group">
                                <label for="description">{{__('Description')}}</label>
                                <!--<input type="hidden" name="description" id="description" value="{{$service->description}}">-->
                                
                              <textarea name="description" id="description">{{ old('description', $service->description) }}</textarea>
                                <!--<div class="summernote">{!! $service->description !!}</div>-->
                            </div>
                             <div class="form-group">
                                <label for="excerpt">{{__('Buyer List')}}</label>
                                <textarea name="buyers_list" id="buyers_list" class="form-control max-height-150"  cols="30" rows="10">{{$service->buyerlist}}</textarea>
                              
                            </div>
                            <div class="form-group">
                                <label for="excerpt">{{__('Excerpt')}}</label>
                                <textarea name="excerpt" id="excerpt" class="form-control max-height-150"  cols="30" rows="10">{{$service->excerpt}}</textarea>
                                <small class="info-text">{{__('it will show in home pages service item short details.')}}</small>
                            </div>
                            <div class="form-group">
                                <label for="meta_tags">{{__('Title')}}</label>
                                <input type="text" name="meta_tags"  class="form-control" value="{{$service->meta_tag}}" id="meta_tags" maxlength="60">
                                <span id="titleLengthMessage"></span>
                            </div>
                            <div class="form-group">
                                <label for="meta_description">{{__('Meta Description')}}</label>
                                <textarea name="meta_description"  class="form-control" rows="5" id="meta_description" maxlength="160">{{$service->meta_description}}</textarea>
                                <span id="descriptionLengthMessage"></span>
                            </div>                            

                            <div class="form-group">
                                <label for="schema_code">{{__('Schema')}}</label>
                                <textarea name="schema_code"  class="form-control" rows="5" id="schema_code ">{{$service->schema_code}}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="category">{{__('Category')}}</label>
                                <select name="categories_id" id="category" class="form-control">
                                    <option value="">{{__('Select Category')}}</option>
                                    @foreach($service_category as $data)
                                        <option @if($data->id == $service->categories_id) selected @endif value="{{$data->id}}">{{$data->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="price_plan">{{__('Price Plans')}}</label>
                                <select name="price_plan[]" multiple class="form-control nice-select wide" id="price_plan_select">
                                    @php
                                        $select_plan = !empty($service->price_plan) ? unserialize($service->price_plan) : [];
                                    @endphp
                                    @foreach($price_plans as $data)
                                        <option value="{{$data->id}}" @if(is_array($select_plan) && in_array($data->id,$select_plan)) selected @endif>{{$data->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">{{__('Status')}}</label>
                                <select name="status" id="status" class="form-control">
                                    <option @if($service->status == 'publish') selected @endif value="publish">{{__('Publish')}}</option>
                                    <option @if($service->status == 'draft') selected @endif value="draft">{{__('Draft')}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sr_order">{{__('Order')}}</label>
                                <input type="text" class="form-control"  id="sr_order"  name="sr_order" value="{{$service->sr_order}}">
                                <span class="info-text">{{__('if you set order for it, all service will show in frontend as a per this order')}}</span>
                            </div>
                            <x-media-upload :id="$service->image" :name="'image'" :dimentions="'1920x1280'" :title="__('Image')"/>
                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update List')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('backend.partials.media-upload.media-upload-markup')
@endsection
@section('script')
    <script src="{{asset('assets/backend/js/summernote-bs4.js')}}"></script>
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <script src="{{asset('assets/backend/js/jquery.nice-select.min.js')}}"></script>
    <x-backend.auto-slug-js :url="route('admin.services.slug.check')" :type="'update'"/>
    <script>
        
        $(document).ready(function() {
            $('#meta_tags').on('input', function() {
                let title = $(this).val();
                let length = title.length;

                if (length > 60) {
                    $('#titleLengthMessage')
                        .text('Title must be 60 characters or less.')
                        .css('color', 'red');
                    $('button[type="submit"]').prop('disabled', true);
                } else {
                    $('#titleLengthMessage')
                        .text(`Characters used: ${length}/60`)
                        .css('color', 'green');
                    $('button[type="submit"]').prop('disabled', false);
                }
            });

            
            $('#meta_description').on('input', function() {
                let title = $(this).val();
                let length = title.length;

                if (length > 160) {
                    $('#descriptionLengthMessage')
                        .text('Title must be 160 characters or less.')
                        .css('color', 'red');
                    $('button[type="submit"]').prop('disabled', true);
                } else {
                    $('#descriptionLengthMessage')
                        .text(`Characters used: ${length}/160`)
                        .css('color', 'green');
                    $('button[type="submit"]').prop('disabled', false);
                }
            });
        });
        $(document).ready(function () {
            if($('.nice-select').length > 0){
                $('.nice-select').niceSelect();
            }

            $(document).on('change','select[name="icon_type"]',function (e){
                e.preventDefault();
                var iconType = $(this).val();
                iconTypeFieldVal(iconType);
            });
            defaultIconType();

            function defaultIconType(){
                var iconType = $('select[name="icon_type"]').val();
                iconTypeFieldVal(iconType);
            }

            function iconTypeFieldVal(iconType){
                if (iconType == 'icon'){
                    $('input[name="img_icon"]').parent().parent().hide();
                    $('input[name="icon"]').parent().show();
                }else if(iconType == 'image'){
                    $('input[name="icon"]').parent().hide();
                    $('input[name="img_icon"]').parent().parent().show();
                }
            }

            $('.summernote').summernote({
                height: 250,   //set editable area's height
                codemirror: { // codemirror options
                    theme: 'monokai'
                },
                callbacks: {
                    onChange: function(contents, $editable) {
                        $(this).prev('input').val(contents);
                    }
                }
            });

            $(document).on('change','#language',function (e) {
                e.preventDefault();
                var selectedLang = $(this).val();
                $.ajax({
                    url : "{{route('admin.list.category.by.slug')}}",
                    type: "POST",
                    data: {
                        _token : "{{csrf_token()}}",
                        lang: selectedLang
                    },
                    success:function (data) {
                        $('#category').html('');
                        $.each(data,function (index,value) {
                            $('#category').append('<option value="'+value.id+'">'+value.name+'</option>');
                        })
                    }
                });
                $.ajax({
                    url : "{{route('admin.list.price.plan.by.slug')}}",
                    type: "POST",
                    data: {
                        _token : "{{csrf_token()}}",
                        lang: selectedLang
                    },
                    success:function (data) {
                        $('#price_plan_select').html('');
                        $.each(data,function (index,value) {
                            $('#price_plan_select').append('<option value="'+value.id+'">'+value.title+'</option>');
                        });
                        $('#price_plan_select').niceSelect('update');
                    }
                });
            });

            $('.icp-dd').iconpicker();
            $('.icp-dd').on('iconpickerSelected', function (e) {
                var selectedIcon = e.iconpickerValue;
                $(this).parent().parent().children('input').val(selectedIcon);
            });

        });
    </script>
     <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>

      <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>

<!-- Initialize CKEditor -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof CKEDITOR !== "undefined") {
            CKEDITOR.replace('description');
        } else {
            console.error("CKEditor failed to load.");
        }
        console.log(typeof CKEDITOR);
    });
</script>
    <script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
    @include('backend.partials.media-upload.media-js')
@endsection
