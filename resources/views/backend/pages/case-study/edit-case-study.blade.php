@extends('backend.admin-master')
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/nice-select.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/media-uploader.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
@endsection
@section('site-title')
    {{__('Edit Case Study')}}
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
                            <h4 class="header-title">{{__('Edit Case Study')}}</h4>
                            <a href="{{route('admin.case-study')}}" class="btn btn-primary">{{__('All Case Study')}}</a>
                        </div>
                        <form action="{{route('admin.case-study.update')}}" method="post" enctype="multipart/form-data" id="myForm">
                            <input type="hidden" name="id" value="{{$CaseStudy->id}}">
                            @csrf
                            <div class="form-group">
                                <label for="language">{{__('Language')}}</label>
                                <select name="lang" id="language" class="form-control">
                                    @foreach(get_all_language() as $language)
                                        <option  @if($language->slug == $CaseStudy->lang) selected @endif value="{{$language->slug}}">{{$language->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="title">{{__('Title')}}</label>
                                <input type="text" class="form-control"  id="title"  name="title" value="{{$CaseStudy->title}}">
                            </div>
                            <div class="form-group">
                                <label for="slug">{{__('Slug')}}</label>
                                <input type="text" class="form-control"  id="slug"  name="slug" value="{{$CaseStudy->slug}}">
                            </div>
                            <div class="form-group">
                                <label for="clients">{{__('Clients')}}</label>
                                <input type="text" class="form-control"  id="clients"  name="clients" value="{{$CaseStudy->clients}}">
                            </div>
                            <div class="form-group">
                                <label for="duration">{{__('Duration')}}</label>
                                <input type="text" class="form-control"  id="duration"  name="duration" value="{{$CaseStudy->duration}}">
                            </div>
                            <div class="form-group">
                                <label for="budget">{{__('Budget')}}</label>
                                <input type="text" class="form-control"  id="budget"  name="budget" value="{{$CaseStudy->budget}}">
                            </div>
                            <div class="form-group">
                                <label for="description">{{__('Description')}}</label>
                                <!--<input type="hidden" name="description" id="description" value="{{$CaseStudy->description}}">-->
                                
        <textarea name="description" id="description">{{ old('description', $CaseStudy->description) }}</textarea>
                                <!--<div class="summernote" data-content='{{$CaseStudy->description}}'></div>-->
                            </div>
                            
                            <div class="form-group">
                                <label for="image">{{__('Gallery')}}</label>
                                @php
                                    $gallery_images = !empty( $CaseStudy->gallery) ? explode('|', $CaseStudy->gallery) : [];
                                @endphp
                                <div class="media-upload-btn-wrapper">
                                    <div class="img-wrap">
                                        @foreach($gallery_images as $gl_img)
                                            @php
                                                $work_section_img = get_attachment_image_by_id($gl_img,null,true);
                                            @endphp
                                            @if (!empty($work_section_img))
                                                <div class="attachment-preview">
                                                    <div class="thumbnail">
                                                        <div class="centered">
                                                            <img class="avatar user-thumb" src="{{$work_section_img['img_url']}}" alt="">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="gallery" value="{{$CaseStudy->gallery}}">
                                    <button type="button" class="btn btn-info media_upload_form_btn" data-mulitple="true" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                                        {{__('Upload Image')}}
                                    </button>
                                </div>
                                <small>{{__('Recommended image size 1920x1280')}}</small>
                            </div>
                            <div class="form-group">
                                <label for="excerpt">{{__('Excerpt')}}</label>
                                <textarea name="excerpt"  class="form-control" rows="5" id="excerpt">{{$CaseStudy->excerpt}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="categories_id">{{__('Category')}}</label>
                                @php
                                    $all_category = $CaseStudy->categories_id;
                                @endphp
                                <select name="categories_id[]" multiple id="category" class="form-control nice-select wide">
                                    @foreach($CaseStudyCategory as $data)
                                        <option @if(in_array($data->id,$all_category)) selected @endif value="{{$data->id}}">{{$data->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="meta_tags">{{__('Title')}}</label>
                                <input type="text" name="meta_tags" value="{{$CaseStudy->meta_tag}}" class="form-control" id="meta_tags" maxlength="60">
                                <span id="titleLengthMessage"></span>
                            </div>
                            <div class="form-group">
                                <label for="meta_description">{{__('Meta Description')}}</label>
                                <textarea name="meta_description"  class="form-control" rows="5" id="meta_description" maxlength="160">{{$CaseStudy->meta_description}}</textarea>
                            
                                <span id="descriptionLengthMessage"></span>
                            </div>                            

                            <div class="form-group">
                                <label for="schema_code">{{__('Schema')}}</label>
                                <textarea name="schema_code"  class="form-control" rows="5" id="schema_code ">{{$CaseStudy->schema_code}}</textarea>
                            </div>


                            <div class="form-group">
                                <label for="status">{{__('Status')}}</label>
                                <select name="status" id="status" class="form-control">
                                    <option @if($CaseStudy->status == 'draft') selected @endif value="draft">{{__('Draft')}}</option>
                                    <option @if($CaseStudy->status == 'publish') selected @endif value="publish">{{__('Publish')}}</option>
                                </select>
                            </div>
                            <x-media-upload :id="$CaseStudy->image" :name="'image'" :dimentions="'1920x1280'" :title="__('Image')"/>

                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Case Study')}}</button>
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
    <script src="{{asset('assets/backend/js/jquery.nice-select.min.js')}}"></script>
    <x-backend.auto-slug-js :url="route('admin.case-study.slug.check')" :type="'update'"/>
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

            if($('.nice-select').length > 0){
                $('.nice-select').niceSelect();
            }
            if($('.summernote').length > 0){
                $('.summernote').each(function(index,value){
                    $(this).summernote('code', $(this).data('content'));
                });
            }

            $(document).on('change','#language',function (e) {
                e.preventDefault();
                var selectedLang = $(this).val();
                $.ajax({
                    url : "{{route('admin.case-study.category.by.slug')}}",
                    type: "POST",
                    data: {
                        _token : "{{csrf_token()}}",
                        lang: selectedLang
                    },
                    success:function (data) {
                        $('#category').html('');
                        $.each(data,function (index,value) {
                            $('#category').append('<option value="'+value.id+'">'+value.name+'</option>');
                            $('.nice-select').niceSelect('update');
                        });
                    }
                });
            });
        });
    </script>
      <!--<script src="{{asset('assets/backend/ckeditor/ckeditor.js')}}"></script>-->
      <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>


<!-- Textarea for CKEditor -->
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

   <!--  <script>
   
    $(document).ready(function() {
   $('#description').summernote({
  height: 300, // Set the height of the editor
  toolbar: [
     ['style', ['style']],  // Add Style dropdown (Headers)
    ['style', ['bold', 'italic', 'underline', 'clear']], // Customize the style dropdown
   
    ['color', ['color', 'backColor']], // Add text and background color options
     ['font', ['fontsize', 'fontname']],
    ['para', ['ul', 'ol', 'paragraph']],
    ['table', ['table']],// Lists and paragraph formatting
    ['insert', ['link', 'picture', 'video']], // Insertion options
    ['view', ['fullscreen', 'codeview', 'help']] // View options
  ],   
  styleTags: [
      'p',  // Default paragraph
      { title: 'Header 1', tag: 'h1', className: 'header1', value: 'h1' },
      { title: 'Header 2', tag: 'h2', className: 'header2', value: 'h2' },
      { title: 'Header 3', tag: 'h3', className: 'header3', value: 'h3' },
      { title: 'Header 4', tag: 'h4', className: 'header4', value: 'h4' },
      { title: 'Header 5', tag: 'h5', className: 'header5', value: 'h5' },
      { title: 'Header 6', tag: 'h6', className: 'header6', value: 'h6' }
  ]
});
 $('#myForm').submit(function(event) {
      var descriptionContent = $('#description').summernote('code');
      
      $('#description').val(descriptionContent); // Update textarea value before submission
    });
 
  $('#description').on('summernote.init', function() {
    $('.note-editable *').removeAttr('style'); // Remove inline styles
        let content = $('#description').summernote('code');
    content = content.replace(/<h5>/g, '<p>').replace(/<\/h5>/g, '</p>');
    $('#description').summernote('code', content);

});
});

    </script>-->
    <script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    @include('backend.partials.media-upload.media-js')
    
@endsection
