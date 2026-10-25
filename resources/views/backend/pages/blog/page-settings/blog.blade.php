@extends('backend.admin-master')
@section('site-title')
    {{__('Blog Page Settings')}}
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
                        <h4 class="header-title">{{__('Blog Page Settings')}}</h4>
                        <form action="{{route('admin.blog.page.settings')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    @foreach($all_languages as $key => $lang)
                                    <a class="nav-item nav-link @if($key == 0) active @endif"  data-toggle="tab" href="#nav-home-{{$lang->slug}}" role="tab" aria-selected="true">{{$lang->name}}</a>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content margin-top-30" id="nav-tabContent">
                                @foreach($all_languages as $key => $lang)
                                <div class="tab-pane fade @if($key == 0) show active @endif" id="nav-home-{{$lang->slug}}" role="tabpanel" >
                                    <div class="form-group">
                                        <label for="blog_page_{{$lang->slug}}_read_more_btn_text">{{__('Blog Read More Text')}}</label>
                                        <input type="text" class="form-control"  id="blog_page_{{$lang->slug}}_read_more_btn_text" name="blog_page_{{$lang->slug}}_read_more_btn_text" value="{{get_static_option('blog_page_'.$lang->slug.'_read_more_btn_text')}}" placeholder="{{__('Blog Read More Text')}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_page_{{$lang->slug}}_subtitle">{{__('Header Text')}}</label>
                                        <textarea class="form-control" rows="2" id="blog_page_{{$lang->slug}}_subtitle" name="blog_page_{{$lang->slug}}_subtitle">{{get_static_option('blog_page_'.$lang->slug.'_subtitle')}}</textarea>
                                        <small class="text-muted">{{__('Shown under the page title. Leave empty to use the meta description from Page Settings.')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_page_{{$lang->slug}}_search_hint">{{__('Search Hint')}}</label>
                                        <input type="text" class="form-control" id="blog_page_{{$lang->slug}}_search_hint" name="blog_page_{{$lang->slug}}_search_hint" value="{{get_static_option('blog_page_'.$lang->slug.'_search_hint')}}">
                                        <small class="text-muted">{{__('Under the search box. Default: Searches the titles and summaries of every article. Results appear as you type.')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_page_{{$lang->slug}}_most_read_title">{{__('"Most Read" Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_page_{{$lang->slug}}_most_read_title" name="blog_page_{{$lang->slug}}_most_read_title" value="{{get_static_option('blog_page_'.$lang->slug.'_most_read_title')}}">
                                        <small class="text-muted">{{__('Default: Most read')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_page_{{$lang->slug}}_latest_title">{{__('"Latest Articles" Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_page_{{$lang->slug}}_latest_title" name="blog_page_{{$lang->slug}}_latest_title" value="{{get_static_option('blog_page_'.$lang->slug.'_latest_title')}}">
                                        <small class="text-muted">{{__('Default: Latest articles')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_page_{{$lang->slug}}_faq_title">{{__('FAQ Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_page_{{$lang->slug}}_faq_title" name="blog_page_{{$lang->slug}}_faq_title" value="{{get_static_option('blog_page_'.$lang->slug.'_faq_title')}}">
                                        <small class="text-muted">{{__('Default: Frequently asked questions')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_page_{{$lang->slug}}_newsletter_title">{{__('Newsletter Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_page_{{$lang->slug}}_newsletter_title" name="blog_page_{{$lang->slug}}_newsletter_title" value="{{get_static_option('blog_page_'.$lang->slug.'_newsletter_title')}}">
                                        <small class="text-muted">{{__('Default: Get new articles by email')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_page_{{$lang->slug}}_newsletter_text">{{__('Newsletter Text')}}</label>
                                        <textarea class="form-control" rows="2" id="blog_page_{{$lang->slug}}_newsletter_text" name="blog_page_{{$lang->slug}}_newsletter_text">{{get_static_option('blog_page_'.$lang->slug.'_newsletter_text')}}</textarea>
                                        <small class="text-muted">{{__('Default: names your three biggest topics, e.g. "New posts on B2B Sales, Industries and Lead Generation, sent to your inbox."')}}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="form-group">
                                <label for="blog_page_item">{{__('Post Items')}}</label>
                                <input type="text" class="form-control"  id="blog_page_item" value="{{get_static_option('blog_page_item')}}" name="blog_page_item" placeholder="{{__('Post Items')}}">
                                <small class="text-danger">{{__('Enter how many post you want to show in blog page')}}</small>
                            </div>
                            <div class="form-group">
                                <label for="blog_page_faq_category">{{__('FAQ Section')}}</label>
                                @php $faq_choice = get_static_option('blog_page_faq_category'); @endphp
                                <select class="form-control" id="blog_page_faq_category" name="blog_page_faq_category">
                                    <option value="" @if($faq_choice === null || $faq_choice === '') selected @endif>{{__('Default (Data & Accuracy)')}}</option>
                                    <option value="none" @if($faq_choice === 'none') selected @endif>{{__("Don't show FAQs on the blog page")}}</option>
                                    @foreach($faq_categories as $faq_category)
                                        <option value="{{$faq_category->id}}" @if((string) $faq_choice === (string) $faq_category->id) selected @endif>{{$faq_category->name}}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{__('Up to six questions from this FAQ category appear under the articles on the blog page, linking to the full FAQ page. Edit the questions themselves under Faq.')}}</small>
                            </div>
                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Blog Page Settings')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
