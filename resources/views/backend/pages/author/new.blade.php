@extends('backend.admin-master')
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/media-uploader.css')}}">
@endsection
@section('site-title')
    {{__('New Author')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-flash-msg/>
                <x-error-msg/>
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between">
                            <h4 class="header-title">{{__('Add New Author ')}}</h4>
                            <a href="{{route('admin.author')}}" class="btn btn-primary">{{__('All Author')}}</a>
                        </div>

                        <form action="{{route('admin.author.new')}}" method="post" enctype="multipart/form-data" id="myForm">
                            @csrf
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="form-group">
                                        <label for="language"><strong>{{__('Language')}}</strong></label>
                                        <select name="lang" id="language" class="form-control">
                                            <option value="">{{__('Select Language')}}</option>
                                            @foreach($all_languages as $lang)
                                            <option value="{{$lang->slug}}">{{$lang->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="title">{{__('Name')}}</label>
                                        <input type="text" class="form-control"  value="{{old('name')}}" name="name" placeholder="{{__('Name')}}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('Position')}}</label>
                                        <input type="text" class="form-control"  value="{{old('position')}}" name="position" placeholder="{{__('Position')}}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('Since Year')}}</label>
                                        <input type="text" class="form-control"  value="{{old('since_date')}}" name="since_date" placeholder="{{__('Since Year')}}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('Content')}}</label>
                                         <textarea name="author_content" id="description"></textarea>
                                    </div>
                                    
                                   
                                    <div class="form-group">
                                        <label for="meta_tags">{{__('Title')}}</label>
                                        <input type="text" name="meta_tags"  class="form-control" value="{{old('meta_tags')}}" id="meta_tags" maxlength="60">
                                        <span id="titleLengthMessage"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="meta_description">{{__('Meta Description')}}</label>
                                        <textarea name="meta_description"  class="form-control" rows="5" id="meta_description"  maxlength="160"></textarea>
                                        <span id="descriptionLengthMessage"></span>
                                    </div>
                                    
                                    
                                </div>
                                <div class="col-lg-4">
                                     <div class="form-group">
                                        <label for="meta_tags">{{__('Facebook')}}</label>
                                        <input type="text" name="facebook"  class="form-control" value="{{old('facebook')}}" id="facebook" >
                                        <span id="titleLengthMessage"></span>
                                    </div>
                                     <div class="form-group">
                                        <label for="meta_tags">{{__('Twitter')}}</label>
                                        <input type="text" name="twitter"  class="form-control" value="{{old('twitter')}}" id="twitter" >
                                        <span id="titleLengthMessage"></span>
                                    </div>
                                     <div class="form-group">
                                        <label for="meta_tags">{{__('Linkedin')}}</label>
                                        <input type="text" name="linkedin"  class="form-control" value="{{old('linkedin')}}" id="linkedin" >
                                        <span id="titleLengthMessage"></span>
                                    </div>
                                     <div class="form-group">
                                        <label for="meta_tags">{{__('Instagram')}}</label>
                                        <input type="text" name="instagram"  class="form-control" value="{{old('instagram')}}" id="instagram" >
                                        <span id="titleLengthMessage"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="title">{{__('Slug')}}</label>
                                        <input type="text" class="form-control"  id="slug"  value="{{old('slug')}}"  name="slug" placeholder="{{__('Slug')}}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="status">{{__('Status')}}</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="publish">{{__('Publish')}}</option>
                                            <option value="draft">{{__('Draft')}}</option>
                                        </select>
                                    </div>
                                    
                                   
                                    
                               

                                    <x-media-upload :id="''" :name="'image'" :dimentions="'1920x1280'" :title="__('Image')"/>
                                    <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Add New Post')}}</button>
                                </div>
                            </div>
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
    <x-backend.auto-slug-js :url="route('admin.blog.slug.check')" :type="'new'"/>
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


    </script>
     <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>


<!-- Textarea for CKEditor -->
<!-- Initialize CKEditor -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof CKEDITOR !== "undefined") {
            CKEDITOR.replace('description');
            CKEDITOR.replace('faq_content');
         
            
        } else {
            console.error("CKEditor failed to load.");
        }
        console.log(typeof CKEDITOR);
    });
    
</script>
    <script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
    @include('backend.partials.media-upload.media-js')
@endsection
