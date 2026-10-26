@extends('backend.admin-master')
@section('site-title')
    {{__('Blog Single Page Settings')}}
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
                        <h4 class="header-title">{{__('Blog Single Page Settings')}}</h4>
                        <form action="{{route('admin.blog.single.settings')}}" method="post" enctype="multipart/form-data">
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
                                        <label for="blog_single_page_{{$lang->slug}}_related_post_title">{{__('Related Post Title')}}</label>
                                        <input type="text" class="form-control"  id="blog_single_page_{{$lang->slug}}_related_post_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_related_post_title')}}" name="blog_single_page_{{$lang->slug}}_related_post_title" placeholder="{{__('Related Post Title')}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_tags_title">{{__('Tags Title')}}</label>
                                        <input type="text" class="form-control"  id="blog_single_page_{{$lang->slug}}_tags_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_tags_title')}}" name="blog_single_page_{{$lang->slug}}_tags_title" placeholder="{{__('Tags Title')}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_share_title">{{__('Share Title')}}</label>
                                        <input type="text" class="form-control"  id="blog_single_page_{{$lang->slug}}_share_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_share_title')}}" name="blog_single_page_{{$lang->slug}}_share_title" placeholder="{{__('Share Title')}}">
                                    </div>
                                    <h5 class="mt-4 mb-3">{{__('Post page wording')}} <small class="text-muted">{{__('(leave any empty to keep the default)')}}</small></h5>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_toc_title">{{__('"On This Page" Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_toc_title" name="blog_single_page_{{$lang->slug}}_toc_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_toc_title')}}">
                                        <small class="text-muted">{{__('Default: On this page')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_cta_title">{{__('Sign-up Card Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_cta_title" name="blog_single_page_{{$lang->slug}}_cta_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_cta_title')}}">
                                        <small class="text-muted">{{__('Default: Get 1200 free credits')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_cta_text">{{__('Sign-up Card Text')}}</label>
                                        <textarea class="form-control" rows="2" id="blog_single_page_{{$lang->slug}}_cta_text" name="blog_single_page_{{$lang->slug}}_cta_text">{{get_static_option('blog_single_page_'.$lang->slug.'_cta_text')}}</textarea>
                                        <small class="text-muted">{{__('Default: Find verified business emails and phone numbers for your next campaign. No credit card required.')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_cta_button">{{__('Sign-up Card Button')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_cta_button" name="blog_single_page_{{$lang->slug}}_cta_button" value="{{get_static_option('blog_single_page_'.$lang->slug.'_cta_button')}}">
                                        <small class="text-muted">{{__('Default: Sign up free')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_cta_url">{{__('Sign-up Card Link')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_cta_url" name="blog_single_page_{{$lang->slug}}_cta_url" value="{{get_static_option('blog_single_page_'.$lang->slug.'_cta_url')}}">
                                        <small class="text-muted">{{__('Default: the app sign-up page, tagged utm_campaign=blog_post_cta')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_faq_title">{{__('FAQ Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_faq_title" name="blog_single_page_{{$lang->slug}}_faq_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_faq_title')}}">
                                        <small class="text-muted">{{__('Default: Frequently asked questions')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_author_title">{{__('Author Box Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_author_title" name="blog_single_page_{{$lang->slug}}_author_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_author_title')}}">
                                        <small class="text-muted">{{__('Default: About the author')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_lists_title">{{__('Mailing Lists Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_lists_title" name="blog_single_page_{{$lang->slug}}_lists_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_lists_title')}}">
                                        <small class="text-muted">{{__('Default: Mailing lists for this topic')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_lists_tab_industry">{{__('Lists Tab 1')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_lists_tab_industry" name="blog_single_page_{{$lang->slug}}_lists_tab_industry" value="{{get_static_option('blog_single_page_'.$lang->slug.'_lists_tab_industry')}}">
                                        <small class="text-muted">{{__('Default: Verified mailing lists')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_lists_tab_roles">{{__('Lists Tab 2')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_lists_tab_roles" name="blog_single_page_{{$lang->slug}}_lists_tab_roles" value="{{get_static_option('blog_single_page_'.$lang->slug.'_lists_tab_roles')}}">
                                        <small class="text-muted">{{__('Default: Decision makers')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_about_title">{{__('About Box Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_about_title" name="blog_single_page_{{$lang->slug}}_about_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_about_title')}}">
                                        <small class="text-muted">{{__('Default: About Go4Database')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_about_text">{{__('About Box Text')}}</label>
                                        <textarea class="form-control" rows="2" id="blog_single_page_{{$lang->slug}}_about_text" name="blog_single_page_{{$lang->slug}}_about_text">{{get_static_option('blog_single_page_'.$lang->slug.'_about_text')}}</textarea>
                                        <small class="text-muted">{{__('Default: the Go4Database introduction shown today')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_about_stat_one">{{__('About Box Figure 1')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_about_stat_one" name="blog_single_page_{{$lang->slug}}_about_stat_one" value="{{get_static_option('blog_single_page_'.$lang->slug.'_about_stat_one')}}">
                                        <small class="text-muted">{{__('Default: 400 Mn records')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_about_stat_two">{{__('About Box Figure 2')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_about_stat_two" name="blog_single_page_{{$lang->slug}}_about_stat_two" value="{{get_static_option('blog_single_page_'.$lang->slug.'_about_stat_two')}}">
                                        <small class="text-muted">{{__('Default: 845 category lists')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_partners_title">{{__('Partner Logos Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_partners_title" name="blog_single_page_{{$lang->slug}}_partners_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_partners_title')}}">
                                        <small class="text-muted">{{__('Default: Brands and partners. The logos come from Brand Logos.')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="blog_single_page_{{$lang->slug}}_case_title">{{__('Case Studies Heading')}}</label>
                                        <input type="text" class="form-control" id="blog_single_page_{{$lang->slug}}_case_title" name="blog_single_page_{{$lang->slug}}_case_title" value="{{get_static_option('blog_single_page_'.$lang->slug.'_case_title')}}">
                                        <small class="text-muted">{{__('Default: Case studies')}}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Blog Page Settings')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
