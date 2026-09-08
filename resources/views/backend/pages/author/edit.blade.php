@extends('backend.admin-master')
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/media-uploader.css')}}">
@endsection
@section('site-title')
    {{__('Edit Author')}}
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
                            <h4 class="header-title">{{__('Edit Author')}}</h4>
                            <a href="{{route('admin.author')}}" class="btn btn-primary">{{__('All Author')}}</a>
                        </div>

                        <form action="{{route('admin.author.update',$author->id)}}" method="post" enctype="multipart/form-data" id="myForm">
                            @csrf
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="form-group">
                                        <label for="language"><strong>{{__('Language')}}</strong></label>
                                        <select name="lang" id="language" class="form-control">
                                            @foreach($all_languages as $lang)
                                                <option @if($lang->slug == $author->lang) selected @endif value="{{$lang->slug}}">{{$lang->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="title">{{__('Name')}}</label>
                                        <input type="text" class="form-control"  id="name" name="name" value="{{$author->name}}">
                                    </div>
                                     <div class="form-group">
                                        <label>{{__('Position')}}</label>
                                        <input type="text" class="form-control"  value="{{$author->position}}" name="position" placeholder="{{__('Position')}}">
                                    </div>
                                   <div class="form-group">
                                        <label>{{__('Since Year')}}</label>
                                        <input type="text" class="form-control"  value="{{$author->since_date}}" name="since_date" placeholder="{{__('Since Year')}}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('Content')}}</label>
                                        <textarea name="author_content" id="description">{{ old('content', $author->content) }}</textarea>
                                    </div>
                                   
                                    <div class="form-group">
                                        <label for="meta_tags">{{__('Title')}}</label>
                                        <input type="text" name="meta_tags"  class="form-control" value="{{$author->meta_tags}}" id="meta_tags" maxlength="60">
                                        <span id="titleLengthMessage"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="meta_description">{{__('Meta Description')}}</label>
                                        <textarea name="meta_description"  class="form-control" rows="5" id="meta_description"  maxlength="160">{{$author->meta_description}}</textarea>
                                        <span id="descriptionLengthMessage"></span>
                                    </div>
                                   
                                </div>
                                <div class="col-lg-4">
                                     <div class="form-group">
                                        <label for="meta_tags">{{__('Facebook')}}</label>
                                        <input type="text" name="facebook"  class="form-control" value="{{$author->facebook}}" id="facebook" >
                                        <span id="titleLengthMessage"></span>
                                    </div>
                                     <div class="form-group">
                                        <label for="meta_tags">{{__('Twitter')}}</label>
                                        <input type="text" name="twitter"  class="form-control" value="{{$author->twitter}}" id="twitter" >
                                        <span id="titleLengthMessage"></span>
                                    </div>
                                     <div class="form-group">
                                        <label for="meta_tags">{{__('Linkedin')}}</label>
                                        <input type="text" name="linkedin"  class="form-control" value="{{$author->linkedin}}" id="linkedin" >
                                        <span id="titleLengthMessage"></span>
                                    </div>
                                     <div class="form-group">
                                        <label for="meta_tags">{{__('Instagram')}}</label>
                                        <input type="text" name="instagram"  class="form-control" value="{{$author->instagram}}" id="instagram" >
                                        <span id="titleLengthMessage"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="title">{{__('Slug')}}</label>
                                        <input type="text" class="form-control"  id="slug" value="{{$author->slug}}"  name="slug" placeholder="{{__('Slug')}}">
                                    </div>
                                   
                                    <div class="form-group">
                                        <label for="status">{{__('Status')}}</label>
                                        <select name="status" id="status" class="form-control">
                                            <option  @if($author->status == 'publish') selected @endif value="publish">{{__('Publish')}}</option>
                                            <option  @if($author->status == 'draft') selected @endif value="draft">{{__('Draft')}}</option>
                                        </select>
                                    </div>

                                    
                                    
                                   

                                    <x-media-upload :id="$author->image" :name="'image'" :dimentions="'1920x1280'" :title="__('Image')"/>
                                    <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Post')}}</button>
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
    <x-backend.auto-slug-js :url="route('admin.blog.slug.check')" :type="'update'"/>
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

            

            $('.summernote').summernote({
                height: 400,   //set editable area's height
                codemirror: { // codemirror options
                    theme: 'monokai'
                },
                callbacks: {
                    onChange: function(contents, $editable) {
                        $(this).prev('input').val(contents);
                    }
                }
            });
            if($('.summernote').length > 0){
                $('.summernote').each(function(index,value){
                    $(this).summernote('code', $(this).data('content'));
                });
            }

  

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
