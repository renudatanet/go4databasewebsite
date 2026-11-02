@extends('backend.admin-master')
@section('site-title')
    {{__('Working at Go4Database')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/media-uploader.css')}}">
    <style>
        .wp-items td { vertical-align: middle; }
        .wp-items .wp-thumb { width: 56px; height: 42px; border-radius: 6px; object-fit: cover; background: #eef3f0; }
        .wp-edit-row td { background: #f8faf9; }
        .wp-help { display: block; color: #7b8794; }
    </style>
@endsection
@section('content')
@php
    // The page's wording, saved per language as working_page_{lang}_{key};
    // the defaults shown are App\Careers\WorkingPageText's, the same ones
    // the page uses when a field is empty.
    use App\Careers\WorkingPageText;
    use App\CareerPageItem;
    $defaults = WorkingPageText::DEFAULTS;
    $groups = [
        __('Header') => [
            'title' => [__('Heading (H1)'), 'input', ''],
            'intro' => [__('Text Under the Heading'), 'textarea', ''],
            'hero_button' => [__('Button'), 'input', __('Opens the career page.')],
            'meta_description' => [__('Meta Description (Google)'), 'textarea', ''],
            'quotes_title' => [__('Quotes Label (for screen readers)'), 'input', __('The quotes under the header are added under “Quotes from the team” below, and show only when one is published.')],
        ],
        __('Who we are') => [
            'story_title' => [__('Heading'), 'input', ''],
            'story_text' => [__('Story'), 'textarea', __('Each line is a paragraph; the first is shown larger. Type none to hide the section.')],
        ],
        __('What we do') => [
            'offer_title' => [__('Heading'), 'input', __('The cards are added under “What we do” below.')],
            'offer_text' => [__('Text'), 'textarea', ''],
            'offer_link' => [__('Card Link'), 'input', ''],
        ],
        __('How we work') => [
            'values_title' => [__('Heading'), 'input', __('The values are added under “How we work” below.')],
            'values_text' => [__('Text'), 'textarea', ''],
        ],
        __('What our customers say') => [
            'customers_title' => [__('Heading'), 'input', __('The testimonials are your customer testimonials (Testimonial in the sidebar).')],
            'customers_text' => [__('Text'), 'textarea', ''],
            'customers_count' => [__('How Many to Show'), 'input', __('The newest ones are shown. 0 hides the section.')],
        ],
        __('Where we work') => [
            'where_title' => [__('Heading'), 'input', __('Offices are added under “Offices” below.')],
            'where_text' => [__('Text'), 'textarea', ''],
            'office_link' => [__('Office Link'), 'input', __('Shown when the office has open roles; searches the roles for its filter word.')],
        ],
        __('Our teams') => [
            'teams_title' => [__('Heading'), 'input', __('One card per team with open roles, from Job Category. Photos are set under “Team photos” below.')],
            'teams_text' => [__('Text'), 'textarea', ''],
            'team_count' => [__('Open Roles Count'), 'input', __('{count} becomes the number.')],
            'team_count_one' => [__('Open Roles Count, When One'), 'input', ''],
            'team_link' => [__('Team Link'), 'input', __('Opens the team’s page on the career page.')],
        ],
        __('Optional band (e.g. remote or hybrid work)') => [
            'remote_title' => [__('Heading'), 'input', __('Leave empty to hide this band.')],
            'remote_text' => [__('Text'), 'textarea', ''],
            'remote_button' => [__('Button'), 'input', __('Opens the career page.')],
        ],
        __('Closing banner') => [
            'join_title' => [__('Heading'), 'input', ''],
            'join_text' => [__('Text'), 'input', ''],
            'join_button' => [__('Button'), 'input', __('Opens the career page.')],
        ],
    ];
    $thumb = function ($id) {
        $img = !empty($id) ? get_attachment_image_by_id($id, 'thumb', false) : null;
        return is_array($img) && !empty($img['img_url']) ? $img['img_url'] : null;
    };
@endphp
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                <x-flash-msg/>
                <x-error-msg/>

                {{-- Wording and team photos --}}
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:10px">
                            <div>
                                <h4 class="header-title mb-1">{{__('Working at Go4Database')}}</h4>
                                <p class="text-muted mb-0">{{__('The page at')}} <a href="{{ route('frontend.jobs.working') }}" target="_blank" rel="noopener">{{ parse_url(route('frontend.jobs.working'), PHP_URL_PATH) }}</a>. {{__('A page about the company. Leave a field empty to use the default shown under it. Open roles stay on the career page, which this page links to.')}}</p>
                            </div>
                            <a class="btn btn-primary" href="{{ route('frontend.jobs.working') }}" target="_blank" rel="noopener">{{__('View Page')}}</a>
                        </div>

                        <form action="{{ route('admin.jobs.working.page') }}" method="POST" class="mt-4">
                            @csrf
                            <nav>
                                <div class="nav nav-tabs" role="tablist">
                                    @foreach($all_languages as $key => $lang)
                                        <a class="nav-item nav-link @if($key == 0) active @endif" data-toggle="tab" href="#working-{{$lang->slug}}" role="tab">{{$lang->name}}</a>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content margin-top-30">
                                @foreach($all_languages as $key => $lang)
                                    <div class="tab-pane fade @if($key == 0) show active @endif" id="working-{{$lang->slug}}" role="tabpanel">
                                        @foreach($groups as $group => $fields)
                                            <h5 class="mt-4 mb-3">{{$group}}</h5>
                                            @foreach($fields as $field => [$label, $type, $help])
                                                @php $name = WorkingPageText::optionName($lang->slug, $field); @endphp
                                                <div class="form-group">
                                                    <label for="{{$name}}">{{$label}}</label>
                                                    @if($type === 'textarea')
                                                        <textarea class="form-control" rows="2" id="{{$name}}" name="{{$name}}">{{get_static_option($name)}}</textarea>
                                                    @else
                                                        <input type="text" class="form-control" id="{{$name}}" name="{{$name}}" value="{{get_static_option($name)}}">
                                                    @endif
                                                    <small class="wp-help">
                                                        @if($help !== ''){{$help}} @endif
                                                        @if(($defaults[$field] ?? '') !== ''){{__('Default:')}} {{$defaults[$field]}}@endif
                                                    </small>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <h5 class="mt-5 mb-1" id="team-photos">{{__('Team photos')}}</h5>
                            <p class="text-muted">{{__('A photo for each team’s card under “What we do”. A team without one shows an icon. Teams appear on the page only while they have open roles.')}}</p>
                            <div class="row">
                                @forelse($teams as $team)
                                    <div class="col-md-4">
                                        <x-media-upload :name="\App\Careers\WorkingPage::TEAM_IMAGE_OPTION . $team->id" :title="$team->title" :dimentions="'1200x750'"/>
                                    </div>
                                @empty
                                    <div class="col-12"><p class="text-muted">{{__('No published teams yet. Add them in Jobs > Category.')}}</p></div>
                                @endforelse
                            </div>

                            <button type="submit" class="btn btn-primary mt-3 pr-4 pl-4">{{__('Save Changes')}}</button>
                        </form>
                    </div>
                </div>

                {{-- Quotes and offices --}}
                @foreach(CareerPageItem::SECTIONS as $section => $labels)
                    @php $items = $all_items[$section] ?? collect(); @endphp
                    <div class="card mt-4" id="items-{{ $section }}">
                        <div class="card-body">
                            <h4 class="header-title mb-1">{{ __($labels['name']) }}</h4>
                            <p class="text-muted">{{ [
                                'offering' => __('A card for each thing Go4Database offers, optionally linking to its page.'),
                                'value' => __('The values in “How we work”, numbered in this order.'),
                                'quote' => __('Real words from people on the team, with their permission. Shown as cards in the page header.'),
                                'office' => __('Each office is a card under “Where we work”.'),
                            ][$section] ?? '' }}</p>

                            @if($items->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table wp-items">
                                        <thead>
                                        <tr>
                                            @if($labels['image'])<th>{{ __($labels['image']) }}</th>@endif
                                            <th>{{ __($labels['title']) }}</th>
                                            <th>{{ __($labels['subtitle'] ?? $labels['body']) }}</th>
                                            <th>{{__('Language')}}</th>
                                            <th>{{__('Order')}}</th>
                                            <th>{{__('Status')}}</th>
                                            <th>{{__('Action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                @if($labels['image'])<td>@if($thumb($item->image))<img class="wp-thumb" src="{{ $thumb($item->image) }}" alt="">@else<span class="wp-thumb d-inline-block"></span>@endif</td>@endif
                                                <td>{{ $item->title }}</td>
                                                <td>{{ $labels['subtitle'] ? $item->subtitle : \Illuminate\Support\Str::limit((string) $item->body, 70) }}</td>
                                                <td>{{ get_language_by_slug($item->lang) ?: $item->lang }}</td>
                                                <td>{{ $item->sr_order }}</td>
                                                <td><span class="badge badge-{{ $item->status === 'publish' ? 'success' : 'warning' }}">{{ $item->status === 'publish' ? __('Shown') : __('Hidden') }}</span></td>
                                                <td style="white-space:nowrap">
                                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#edit-item-{{ $item->id }}" aria-expanded="false">{{__('Edit')}}</button>
                                                    <form action="{{ route('admin.jobs.working.item.delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this item?') }}')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger">{{__('Delete')}}</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <tr class="collapse wp-edit-row" id="edit-item-{{ $item->id }}">
                                                <td colspan="{{ $labels['image'] ? 7 : 6 }}">
                                                    <form action="{{ route('admin.jobs.working.item.update') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                                        @include('backend.jobs.partials.working-item-fields', ['section' => $section, 'labels' => $labels, 'item' => $item, 'prefix' => 'edit-' . $item->id])
                                                        <button type="submit" class="btn btn-primary">{{__('Save Item')}}</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted"><em>{{__('None yet.')}}</em></p>
                            @endif

                            <h5 class="mt-4">{{__('Add')}} {{ __($labels['add']) }}</h5>
                            <form action="{{ route('admin.jobs.working.item.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="section" value="{{ $section }}">
                                <div class="form-group">
                                    <label for="new-{{ $section }}-lang">{{__('Language')}}</label>
                                    <select class="form-control" name="lang" id="new-{{ $section }}-lang">
                                        @foreach($all_languages as $lang)<option value="{{ $lang->slug }}">{{ $lang->name }}</option>@endforeach
                                    </select>
                                </div>
                                @include('backend.jobs.partials.working-item-fields', ['section' => $section, 'labels' => $labels, 'item' => null, 'prefix' => 'new-' . $section])
                                <button type="submit" class="btn btn-primary">{{__('Add')}}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @include('backend.partials.media-upload.media-upload-markup')
@endsection
@section('script')
    <script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
    @include('backend.partials.media-upload.media-js')
@endsection
