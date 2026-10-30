@extends('backend.admin-master')
@section('site-title')
    {{__('Job Page Settings')}}
@endsection
@section('content')
@php
    // Every text of the career pages (/career, its categories and search, each
    // role's page and the application form), saved per language as
    // career_page_{lang}_{key}. The defaults shown are App\Careers\CareerText's,
    // the same ones the pages use when a field is empty.
    $defaults = \App\Careers\CareerText::DEFAULTS;
    $groups = [
        __('Header of the career page') => [
            'badge' => [__('Small Label Above the Heading'), 'input', ''],
            'heading' => [__('Heading (H1)'), 'input', ''],
            'heading_highlight' => [__('Heading, Green Words'), 'input', __('Shown in green after the heading.')],
            'subtitle' => [__('Header Text'), 'textarea', __('{roles} becomes the number of open roles.')],
            'search_placeholder' => [__('Search Box Placeholder'), 'input', ''],
            'all_label' => [__('“All Roles” Chip'), 'input', __('The first of the category chips under the search box.')],
            'category_heading' => [__('Category Page Heading'), 'input', __('{category} becomes the category name.')],
            'category_text' => [__('Category Page Text'), 'textarea', __('Used when the category has no meta description of its own (Job Category). {category} becomes its name.')],
        ],
        __('Why join us') => [
            'perks_title' => [__('Heading'), 'input', ''],
            'perks' => [__('Points'), 'textarea', __('One per line, as Title | text. Up to four look best. Type none to hide this section.')],
        ],
        __('Roles') => [
            'roles_title' => [__('Roles Heading'), 'input', ''],
            'results_title' => [__('Search Results Heading'), 'input', __('{query} becomes what was searched.')],
            'empty_title' => [__('No Roles Heading'), 'input', __('When nothing is open, or nothing matches.')],
            'empty_text' => [__('No Roles Text'), 'textarea', ''],
        ],
        __('Call to action (foot of every career page)') => [
            'cta_title' => [__('Heading'), 'input', ''],
            'cta_text' => [__('Text'), 'textarea', ''],
            'cta_button' => [__('Button'), 'input', ''],
            'cta_email' => [__('Email for CVs'), 'input', __('The button writes to this address. Leave empty to link to the contact page instead.')],
        ],
        __('Role page') => [
            'related_title' => [__('Other Roles Heading'), 'input', ''],
            'closed_text' => [__('Deadline Passed Message'), 'input', __('Shown instead of the apply button once the deadline has passed.')],
        ],
        __('Application form') => [
            'apply_title' => [__('Heading'), 'input', __('{role} becomes the role’s title.')],
            'apply_intro' => [__('Text Under the Heading'), 'textarea', ''],
            'apply_button' => [__('Submit Button'), 'input', ''],
            'steps_title' => [__('Next Steps Heading'), 'input', ''],
            'steps' => [__('Next Steps'), 'textarea', __('One step per line, shown numbered beside the form.')],
            'privacy_note' => [__('Note Under the Button'), 'input', ''],
        ],
    ];
@endphp
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                <x-flash-msg/>
                <x-error-msg/>
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__("Job Page Settings")}}</h4>
                        <p class="text-muted">{{__('The wording of the career pages. Leave a field empty to use the default shown under it. The page name, meta tags and address are in General Settings > Page Settings; the labels on each role’s page in Job Single Page Settings; the application form’s fields in Form Builder > Apply Job Form.')}}</p>
                        <form action="{{route('admin.jobs.page.settings')}}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="site_job_post_items">{{__('Roles Shown Before “Show More”')}}</label>
                                <input type="number" min="1" max="100" name="site_job_post_items" class="form-control" value="{{get_static_option('site_job_post_items')}}" id="site_job_post_items">
                            </div>

                            <nav class="mt-4">
                                <div class="nav nav-tabs" role="tablist">
                                    @foreach($all_languages as $key => $lang)
                                        <a class="nav-item nav-link @if($key == 0) active @endif" data-toggle="tab" href="#career-page-{{$lang->slug}}" role="tab" aria-selected="{{ $key == 0 ? 'true' : 'false' }}">{{$lang->name}}</a>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content margin-top-30">
                                @foreach($all_languages as $key => $lang)
                                    <div class="tab-pane fade @if($key == 0) show active @endif" id="career-page-{{$lang->slug}}" role="tabpanel">
                                        @foreach($groups as $group => $fields)
                                            <h5 class="mt-4 mb-3">{{$group}}</h5>
                                            @foreach($fields as $field => [$label, $type, $help])
                                                @php $name = 'career_page_'.$lang->slug.'_'.$field; @endphp
                                                <div class="form-group">
                                                    <label for="{{$name}}">{{$label}}</label>
                                                    @if($type === 'textarea')
                                                        <textarea class="form-control" rows="{{ in_array($field, ['perks', 'steps'], true) ? 4 : 2 }}" id="{{$name}}" name="{{$name}}">{{get_static_option($name)}}</textarea>
                                                    @else
                                                        <input type="text" class="form-control" id="{{$name}}" name="{{$name}}" value="{{get_static_option($name)}}">
                                                    @endif
                                                    <small class="text-muted d-block">
                                                        @if($help !== ''){{$help}} @endif
                                                        @if(($defaults[$field] ?? '') !== '')
                                                            {{__('Default:')}} <span style="white-space:pre-line">{{$defaults[$field]}}</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <h5 class="mt-5 mb-1">{{__('Google job listings')}}</h5>
                            <p class="text-muted">{{__('Each role’s page tells Google where the job is, so it can show in Google’s job search. The street is the role’s own Job Location; these fill in the rest.')}}</p>
                            <div class="row">
                                @foreach([
                                    'career_schema_locality' => [__('City'), 'Noida'],
                                    'career_schema_region' => [__('State'), 'Uttar Pradesh'],
                                    'career_schema_postal_code' => [__('Postal Code'), '201301'],
                                    'career_schema_country' => [__('Country Code (2 letters)'), 'IN'],
                                    'career_salary_currency' => [__('Salary Currency (3 letters)'), 'INR'],
                                ] as $name => [$label, $default])
                                    <div class="form-group col-md-4">
                                        <label for="{{$name}}">{{$label}}</label>
                                        <input type="text" class="form-control" id="{{$name}}" name="{{$name}}" value="{{get_static_option($name)}}" placeholder="{{$default}}">
                                        <small class="text-muted">{{__('Default:')}} {{$default}}</small>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted d-block">{{__('A salary is sent to Google only when it is written as a range of monthly amounts, e.g. 25K-60K.')}}</small>

                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Changes')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection