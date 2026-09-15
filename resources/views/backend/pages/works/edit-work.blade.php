@extends('backend.admin-master')
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/nice-select.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/media-uploader.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
@endsection
@section('site-title')
    {{__('Edit B2B')}}
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
               <x-error-msg/>
                <x-flash-msg/>
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between">
                            <h4 class="header-title">{{__('Edit B2B')}}</h4>
                            <a href="{{route('admin.work')}}" class="btn btn-primary">{{__('All B2B')}}</a>
                        </div>
                        <form action="{{route('admin.work.update')}}" method="post" enctype="multipart/form-data" id="myForm">
                            <input type="hidden" name="id" value="{{$work_details->id}}">
                            @csrf
                            <div class="form-group">
                                <label for="language">{{__('Language')}}</label>
                                <select name="lang" id="language" class="form-control">
                                    @foreach(get_all_language() as $language)
                                        <option  @if($language->slug == $work_details->lang) selected @endif value="{{$language->slug}}">{{$language->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="title">{{__('Title')}}</label>
                                <input type="text" class="form-control"  id="title"  name="title" value="{{$work_details->title}}">
                            </div>
                            <div class="form-group">
                                <label for="slug">{{__('Slug')}}</label>
                                <input type="text" class="form-control"  id="slug"  name="slug" value="{{$work_details->slug}}">
                            </div>
                            <div class="form-group">
                                <label for="clients">{{__('Clients')}}</label>
                                <input type="text" class="form-control"  id="clients"  name="clients" value="{{$work_details->clients}}">
                            </div>
                            <div class="form-group">
                                <label for="duration">{{__('Duration')}}</label>
                                <input type="text" class="form-control"  id="duration"  name="duration" value="{{$work_details->duration}}">
                            </div>
                            <div class="form-group">
                                <label for="budget">{{__('Budget')}}</label>
                                <input type="text" class="form-control"  id="budget"  name="budget" value="{{$work_details->budget}}">
                            </div>
                            <div class="form-group">
                                <label for="description">{{__('Description')}}</label>
                                <!--<input type="hidden" name="description" id="description" value="{{$work_details->description}}">-->
                                
        <textarea name="description" id="description">{{ old('description', $work_details->description) }}</textarea>
                                <!--<div class="summernote" data-content='{{$work_details->description}}'></div>-->
                            </div>
                            <div class="faq-container">
    <div id="faq-wrapper">

        @if(!empty($work_details->faqs))
            @foreach($work_details->faqs as $i => $faq)
                <div class="faq-item">
                    <input type="text" 
                           name="faqs[{{ $i }}][question]" 
                           value="{{ $faq['question'] }}" 
                           placeholder="Enter question">

                    <textarea name="faqs[{{ $i }}][answer]" 
                              placeholder="Enter answer">{{ $faq['answer'] }}</textarea>

                    <div class="faq-actions">
                        <span></span>
                        <button type="button" class="delete-btn" onclick="removeFaq(this)">Delete</button>
                    </div>
                </div>
            @endforeach

            <script>
                // 👇 important: continue index from last item
                let index = {{ count($work_details->faqs) }};
            </script>

        @else
            <div class="faq-item">
                <input type="text" name="faqs[0][question]" placeholder="Enter question">
                <textarea name="faqs[0][answer]" placeholder="Enter answer"></textarea>

                <div class="faq-actions">
                    <span></span>
                    <button type="button" class="delete-btn" onclick="removeFaq(this)">Delete</button>
                </div>
            </div>

            <script>
                let index = 1;
            </script>
        @endif

    </div>

    <button type="button" class="add-btn" onclick="addFaq()">+ Add FAQ</button>
</div>
                            <div class="form-group">
                                <label for="image">{{__('Gallery')}}</label>
                                @php
                                    $gallery_images = !empty( $work_details->gallery) ? explode('|', $work_details->gallery) : [];
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
                                    <input type="hidden" name="gallery" value="{{$work_details->gallery}}">
                                    <button type="button" class="btn btn-info media_upload_form_btn" data-mulitple="true" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                                        {{__('Upload Image')}}
                                    </button>
                                </div>
                                <small>{{__('Recommended image size 1920x1280')}}</small>
                            </div>
                            <div class="form-group">
                                <label for="excerpt">{{__('Excerpt')}}</label>
                                <textarea name="excerpt"  class="form-control" rows="5" id="excerpt">{{$work_details->excerpt}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="categories_id">{{__('Category')}}</label>
                                @php
                                    $all_category = $work_details->categories_id;
                                @endphp
                                <select name="categories_id[]" multiple id="category" class="form-control nice-select wide">
                                    @foreach($works_category as $data)
                                        <option @if(in_array($data->id,$all_category)) selected @endif value="{{$data->id}}">{{$data->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="meta_tags">{{__('Title')}}</label>
                                <input type="text" name="meta_tags" value="{{$work_details->meta_tag}}" class="form-control" id="meta_tags" maxlength="60">
                                <span id="titleLengthMessage"></span>
                            </div>

                            <div class="form-group">
                                <label for="meta_description">{{__('Meta Description')}}</label>
                                <textarea name="meta_description"  class="form-control" rows="5" id="meta_description" maxlength="160">{{$work_details->meta_description}}</textarea>
                                <span id="descriptionLengthMessage"></span>
                            </div>

                            <div class="form-group">
                                <label for="schema_code">{{__('Schema')}}</label>
                                <textarea name="schema_code"  class="form-control" rows="5" id="schema_code ">{{$work_details->schema_code}}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="status">{{__('Status')}}</label>
                                <select name="status" id="status" class="form-control">
                                    <option @if($work_details->status == 'draft') selected @endif value="draft">{{__('Draft')}}</option>
                                    <option @if($work_details->status == 'publish') selected @endif value="publish">{{__('Publish')}}</option>
                                </select>
                            </div>
                            <x-media-upload :id="$work_details->image" :name="'image'" :dimentions="'1920x1280'" :title="__('Image')"/>

                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update B2B')}}</button>
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
    <x-backend.auto-slug-js :url="route('admin.work.slug.check')" :type="'update'"/>
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
                    url : "{{route('admin.work.category.by.slug')}}",
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
 <script>
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
