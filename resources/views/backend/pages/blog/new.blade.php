@extends('backend.admin-master')
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/nice-select.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/media-uploader.css')}}">
@endsection
@section('site-title')
    {{__('New Blog Post')}}
@endsection
@section('content')
<style>
    .faq-container {
    max-width: 800px;
}

.faq-item {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    position: relative;
}

.faq-item input,
.faq-item textarea {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 10px;
    font-size: 14px;
}

.faq-item textarea {
    min-height: 80px;
    resize: vertical;
}

.faq-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.add-btn {
    background: #2563eb;
    color: #fff;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
}

.add-btn:hover {
    background: #1d4ed8;
}

.delete-btn {
    background: #ef4444;
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
}

.delete-btn:hover {
    background: #dc2626;
}
</style>
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
                            <h4 class="header-title">{{__('Add New Blog Post')}}</h4>
                            <a href="{{route('admin.blog')}}" class="btn btn-primary">{{__('All Blog')}}</a>
                        </div>

                        <form action="{{route('admin.blog.new')}}" method="post" enctype="multipart/form-data" id="myForm">
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
                                        <label for="title">{{__('Title')}}</label>
                                        <input type="text" class="form-control"  value="{{old('title')}}" name="title" placeholder="{{__('Title')}}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('Content')}}</label>
                                         <textarea name="blog_content" id="description"></textarea>
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
                                    
                                    <div class="form-group">
                                        <label for="schema_code">{{__('Schema')}}</label>
                                        <textarea name="schema_code"  class="form-control" rows="5" id="schema_code "></textarea>
                                    </div>
                                    <div class="faq-container">
                                     <div id="faq-wrapper">
        <div class="faq-item">
            <input type="text" name="faqs[0][question]" placeholder="Enter question">
            <textarea name="faqs[0][answer]" placeholder="Enter answer"></textarea>

            <div class="faq-actions">
                <span></span>
                <button type="button" class="delete-btn" onclick="removeFaq(this)">Delete</button>
            </div>
        </div>
    </div>

    <button type="button" class="add-btn" onclick="addFaq()">+ Add FAQ</button>
                                    </div>

                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="title">{{__('Slug')}}</label>
                                        <input type="text" class="form-control"  id="slug"  value="{{old('slug')}}"  name="slug" placeholder="{{__('Slug')}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="title">{{__('Excerpt')}}</label>
                                        <textarea name="excerpt" id="excerpt" class="form-control max-height-150" cols="30" rows="10"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="category">{{__('Category')}}</label>
                                        <select name="category" class="form-control" id="category">
                                            <option value="">{{__("Select Category")}}</option>
                                        </select>
                                        <span class="info-text">{{__('select language to get category by language')}}</span>
                                    </div>
                                           <div class="form-group">
                                        <label for="category">{{__('Industry')}}</label>
                                         <select name="categories_id[]" multiple id="category" class="form-control nice-select wide">
                                    @foreach($works_category as $data)
                                        <option  value="{{$data->id}}">{{$data->name}}</option>
                                    @endforeach
                                </select>
                                        <span class="info-text">{{__('select language to get category by language')}}</span>
                                    </div>
                                   
                                    <div class="form-group">
                                        <label for="title">{{__('Tags')}}</label>
                                        <input type="text" class="form-control" name="tags" value="{{old('tags')}}" data-role="tagsinput">
                                    </div>
                                    <div class="form-group">
                                        <label for="author">{{__('Author Name')}}</label>
                                         <select name="author" class="form-control" id="author">
                                            <option value="">{{__("Select Author")}}</option>
                                            @foreach($all_author as $author)
                                            <option value="{{$author->id}}">{{$author->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="video_url">{{__('Video Url')}}</label>
                                        <input type="text" class="form-control" name="video_url" value="{{old('video_url')}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="breaking_news"><strong>{{__('Is Breaking News')}}</strong></label>
                                        <label class="switch">
                                            <input type="checkbox" name="breaking_news">
                                            <span class="slider onff"></span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">{{__('Status')}}</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="publish">{{__('Publish')}}</option>
                                            <option value="draft">{{__('Draft')}}</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        @php
                                            $today = \Carbon\Carbon::now()->toDateString(); // "2025-07-22"
                                        @endphp
                                        <label for="video_url">{{__('Publish Date')}}</label>
                                        <input type="date" class="form-control" name="publish_date" value="{{$today}}" required>
                                    </div>

                                    
                                    <div class="form-group">
                                        <label for="video_url">{{__('Total Visitors')}}</label>
                                        <input type="number" class="form-control" name="total_visitors" value="327" required>
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


        $(document).ready(function () {



            $(document).on('click','.category_edit_btn',function(){
                var el = $(this);
                var id = el.data('id');
                var name = el.data('name');
                var status = el.data('status');
                var modal = $('#category_edit_modal');
                modal.find('#category_id').val(id);
                modal.find('#edit_status option[value="'+status+'"]').attr('selected',true);
                modal.find('#edit_name').val(name);
            });
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
            if($('.summernote').length > 1){
                $('.summernote').each(function(index,value){
                    $(this).summernote('code', $(this).data('content'));
                });
            }

            $(document).on('change','#language',function(e){
               e.preventDefault();
               var selectedLang = $(this).val();
               $.ajax({
                  url: "{{route('admin.blog.lang.cat')}}",
                  type: "POST",
                  data: {
                      _token : "{{csrf_token()}}",
                      lang : selectedLang
                  },
                  success:function (data) {
                      $('#category').html('<option value="">Select Category</option>');
                      $.each(data,function(index,value){
                          $('#category').append('<option value="'+value.id+'">'+value.name+'</option>')
                      });
                  }
               });
            });
        });
    </script>
<script>
let index = 1;

function addFaq() {
    let html = `
        <div class="faq-item">
            <input type="text" name="faqs[${index}][question]" placeholder="Enter question">
            <textarea name="faqs[${index}][answer]" placeholder="Enter answer"></textarea>

            <div class="faq-actions">
                <span></span>
                <button type="button" class="delete-btn" onclick="removeFaq(this)">Delete</button>
            </div>
        </div>
    `;

    document.getElementById('faq-wrapper').insertAdjacentHTML('beforeend', html);
    index++;
}

function removeFaq(btn) {
    let wrapper = document.getElementById('faq-wrapper');
    if (wrapper.children.length > 1) {
        btn.closest('.faq-item').remove();
    } else {
        alert('At least one FAQ is required');
    }
}
</script>
     <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script src="{{asset('assets/backend/js/jquery.nice-select.min.js')}}"></script>


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
