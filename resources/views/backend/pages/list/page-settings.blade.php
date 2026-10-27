@extends('backend.admin-master')
@section('site-title')
    {{__('List Page Settings')}}
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
                        <h4 class="header-title">{{__('List Page Settings')}}</h4>
                        <p class="text-muted">{{__('The wording of the lists page. Leave any field empty to keep the default shown under it. The page name, meta tags and address are in General Settings > Page Settings; industries and their lists under List.')}}</p>
                        <form action="{{route('admin.list.page.settings')}}" method="post">
                            @csrf
                            <nav>
                                <div class="nav nav-tabs" role="tablist">
                                    @foreach($all_languages as $key => $lang)
                                    <a class="nav-item nav-link @if($key == 0) active @endif" data-toggle="tab" href="#list-page-{{$lang->slug}}" role="tab" aria-selected="{{ $key == 0 ? 'true' : 'false' }}">{{$lang->name}}</a>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content margin-top-30">
                                @foreach($all_languages as $key => $lang)
                                <div class="tab-pane fade @if($key == 0) show active @endif" id="list-page-{{$lang->slug}}" role="tabpanel">
                                    <h5 class="mt-4 mb-3">{{__('Header')}}</h5>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_badge">{{__('Small Label Above the Heading')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_badge" name="list_page_{{$lang->slug}}_badge" value="{{get_static_option('list_page_'.$lang->slug.'_badge')}}">
                                        <small class="text-muted">{{__('Default: B2B data directory')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_heading">{{__('Heading (H1), First Part')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_heading" name="list_page_{{$lang->slug}}_heading" value="{{get_static_option('list_page_'.$lang->slug.'_heading')}}">
                                        <small class="text-muted">{{__('Default: Every B2B')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_heading_highlight">{{__('Heading, Green Words')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_heading_highlight" name="list_page_{{$lang->slug}}_heading_highlight" value="{{get_static_option('list_page_'.$lang->slug.'_heading_highlight')}}">
                                        <small class="text-muted">{{__('Shown in green, underlined. Default: email list')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_heading_after">{{__('Heading, Last Part')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_heading_after" name="list_page_{{$lang->slug}}_heading_after" value="{{get_static_option('list_page_'.$lang->slug.'_heading_after')}}">
                                        <small class="text-muted">{{__('Default: we hold')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_subtitle">{{__('Header Text')}}</label>
                                        <textarea class="form-control" rows="2" id="list_page_{{$lang->slug}}_subtitle" name="list_page_{{$lang->slug}}_subtitle">{{get_static_option('list_page_'.$lang->slug.'_subtitle')}}</textarea>
                                        <small class="text-muted">{{__('Under the heading. {lists} and {industries} become the live numbers. Default: {lists} verified lists across {industries} industries. Searching is free, credits are only spent when you reveal a contact.')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_search_placeholder">{{__('Search Box Placeholder')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_search_placeholder" name="list_page_{{$lang->slug}}_search_placeholder" value="{{get_static_option('list_page_'.$lang->slug.'_search_placeholder')}}">
                                        <small class="text-muted">{{__('Default: Try hospitals, CEO, mortgage, telehealth…')}}</small>
                                    </div>
                                    <h5 class="mt-4 mb-3">{{__('Search')}}</h5>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_popular_label">{{__('Search Box Label Before Typing')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_popular_label" name="list_page_{{$lang->slug}}_popular_label" value="{{get_static_option('list_page_'.$lang->slug.'_popular_label')}}">
                                        <small class="text-muted">{{__('Heads the lists offered when someone clicks into the empty search box. Default: Popular lists')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_results_title">{{__('Results Heading')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_results_title" name="list_page_{{$lang->slug}}_results_title" value="{{get_static_option('list_page_'.$lang->slug.'_results_title')}}">
                                        <small class="text-muted">{{__('{query} becomes what was searched. Default: Results for “{query}”')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_empty_title">{{__('No Results Heading')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_empty_title" name="list_page_{{$lang->slug}}_empty_title" value="{{get_static_option('list_page_'.$lang->slug.'_empty_title')}}">
                                        <small class="text-muted">{{__('When nothing matches; the call-to-action text and main button show under it. Default: Nothing matches that yet')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_narrow_hint">{{__('Too Many Results Hint')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_narrow_hint" name="list_page_{{$lang->slug}}_narrow_hint" value="{{get_static_option('list_page_'.$lang->slug.'_narrow_hint')}}">
                                        <small class="text-muted">{{__('Under the results when there are more than 60. Default: Narrow your search to see the rest, e.g. add a job title or a country.')}}</small>
                                    </div>
                                    <h5 class="mt-4 mb-3">{{__('Industries')}}</h5>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_browse_title">{{__('Industries Heading')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_browse_title" name="list_page_{{$lang->slug}}_browse_title" value="{{get_static_option('list_page_'.$lang->slug.'_browse_title')}}">
                                        <small class="text-muted">{{__('Default: Browse by industry')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_browse_text">{{__('Industries Text')}}</label>
                                        <textarea class="form-control" rows="2" id="list_page_{{$lang->slug}}_browse_text" name="list_page_{{$lang->slug}}_browse_text">{{get_static_option('list_page_'.$lang->slug.'_browse_text')}}</textarea>
                                        <small class="text-muted">{{__('{with}, {industries} and {lists} become the live numbers. Default: {with} of {industries} industries have lists published, holding {lists} between them.')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_card_count_text">{{__('Card Count Line')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_card_count_text" name="list_page_{{$lang->slug}}_card_count_text" value="{{get_static_option('list_page_'.$lang->slug.'_card_count_text')}}">
                                        <small class="text-muted">{{__('Under each industry name. {count} becomes its number of lists. Default: {count} verified lists')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_all_lists_text">{{__('Card Bottom Link')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_all_lists_text" name="list_page_{{$lang->slug}}_all_lists_text" value="{{get_static_option('list_page_'.$lang->slug.'_all_lists_text')}}">
                                        <small class="text-muted">{{__('{count} and {industry} (or {Industry}, capitalised) become the card’s own. Default: Browse all {count} {industry} lists')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_pipeline_title">{{__('Upcoming Industries Heading')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_pipeline_title" name="list_page_{{$lang->slug}}_pipeline_title" value="{{get_static_option('list_page_'.$lang->slug.'_pipeline_title')}}">
                                        <small class="text-muted">{{__('For industries with no lists yet. Default: Next in the pipeline')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_pipeline_text">{{__('Upcoming Industries Text')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_pipeline_text" name="list_page_{{$lang->slug}}_pipeline_text" value="{{get_static_option('list_page_'.$lang->slug.'_pipeline_text')}}">
                                        <small class="text-muted">{{__('Default: These industries publish with the next data release.')}}</small>
                                    </div>
                                    <h5 class="mt-4 mb-3">{{__('Call to action (bottom of the page)')}}</h5>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_cta_title">{{__('Heading')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_cta_title" name="list_page_{{$lang->slug}}_cta_title" value="{{get_static_option('list_page_'.$lang->slug.'_cta_title')}}">
                                        <small class="text-muted">{{__('Default: Can’t find the list you need?')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_cta_text">{{__('Text')}}</label>
                                        <textarea class="form-control" rows="2" id="list_page_{{$lang->slug}}_cta_text" name="list_page_{{$lang->slug}}_cta_text">{{get_static_option('list_page_'.$lang->slug.'_cta_text')}}</textarea>
                                        <small class="text-muted">{{__('Default: We build to your criteria across the global B2B database: industry, job title, location, company size, revenue.')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_cta_button">{{__('Main Button Text')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_cta_button" name="list_page_{{$lang->slug}}_cta_button" value="{{get_static_option('list_page_'.$lang->slug.'_cta_button')}}">
                                        <small class="text-muted">{{__('Default: Request a custom list')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_cta_url">{{__('Main Button Link')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_cta_url" name="list_page_{{$lang->slug}}_cta_url" value="{{get_static_option('list_page_'.$lang->slug.'_cta_url')}}">
                                        <small class="text-muted">{{__('Default: /contact')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_cta2_button">{{__('Second Button Text')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_cta2_button" name="list_page_{{$lang->slug}}_cta2_button" value="{{get_static_option('list_page_'.$lang->slug.'_cta2_button')}}">
                                        <small class="text-muted">{{__('Also the green chip after the industries in the header. Default: Get 1200 free credits')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_cta2_url">{{__('Second Button Link')}}</label>
                                        <input type="text" class="form-control" id="list_page_{{$lang->slug}}_cta2_url" name="list_page_{{$lang->slug}}_cta2_url" value="{{get_static_option('list_page_'.$lang->slug.'_cta2_url')}}">
                                        <small class="text-muted">{{__('Default: https://app.go4database.com/register?utm_source=ListIndex&utm_medium=Internal&utm_campaign=free_credits')}}</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="list_page_{{$lang->slug}}_cta_notes">{{__('Ticks Under the Buttons')}}</label>
                                        <textarea class="form-control" rows="4" id="list_page_{{$lang->slug}}_cta_notes" name="list_page_{{$lang->slug}}_cta_notes">{{get_static_option('list_page_'.$lang->slug.'_cta_notes')}}</textarea>
                                        <small class="text-muted">{{__('One per line. Default: Real-time email verification / 95% accuracy guarantee / No card required')}}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update List Page Settings')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
