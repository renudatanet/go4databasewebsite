@extends('backend.admin-master')
@section('site-title')
    {{__('Email Finder Page Content')}}
@endsection
@section('style')
    <style>
        .ef-admin-hint{font-size:12px;color:#8a94a6;display:block;margin-top:4px;}
        .ef-admin-sec{border:1px solid #e9edf2;border-radius:6px;padding:18px;margin-bottom:22px;}
        .ef-admin-sec > h5{font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#5a6779;margin-bottom:16px;}
        .ef-item-table td{vertical-align:middle !important;}
        .ef-item-table .ef-ico{font-family:monospace;font-size:12px;color:#5a6779;}
    </style>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                @include('backend.partials.message')
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul>
                    </div>
                @endif
                <div class="alert alert-info">
                    {{__('Everything on the public Email Finder page is edited here. How the lookup itself behaves is under')}}
                    <a href="{{route('admin.email.finder.settings')}}"><strong>{{__('Email Finder Settings')}}</strong></a>.
                    {{__('The questions in the FAQ come from the Faq section, chosen at the bottom of this screen.')}}
                    <a href="{{url('/email-finder')}}" target="_blank" rel="noopener">{{__('View the live page')}}</a>
                </div>
            </div>

            {{-- ============ TEXT CONTENT ============ --}}
            <div class="col-lg-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Page Text')}}</h4>
                        <p class="text-muted">{{__('Leave a box empty to fall back to the wording the page ships with.')}}</p>

                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($all_languages as $key => $lang)
                                <li class="nav-item">
                                    <a class="nav-link @if($key == 0) active @endif" data-toggle="tab"
                                       href="#eftxt_{{$lang->slug}}" role="tab">{{$lang->name}}</a>
                                </li>
                            @endforeach
                        </ul>

                        <form action="{{route('admin.email.finder.content')}}" method="POST">
                            @csrf
                            <div class="tab-content mt-4">
                                @foreach($all_languages as $key => $lang)
                                    @php
                                        $p = 'ef_'.$lang->slug.'_';
                                        $val = fn($f) => \App\Http\Controllers\EmailFinderSettingsController::text_value($lang->slug, $f);
                                    @endphp
                                    <div class="tab-pane fade @if($key == 0) show active @endif" id="eftxt_{{$lang->slug}}" role="tabpanel">

                                        <div class="ef-admin-sec">
                                            <h5>{{__('Search Engines')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Meta Description')}}</label>
                                                <textarea name="{{$p}}meta_description" rows="2" class="form-control">{{$val('meta_description')}}</textarea>
                                                <span class="ef-admin-hint">{{__('The sentence Google shows under the page title. Around 150-160 characters works best.')}}</span>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Meta Keywords')}}</label>
                                                <input type="text" name="{{$p}}meta_keywords" class="form-control" value="{{$val('meta_keywords')}}">
                                                <span class="ef-admin-hint">{{__('Comma separated.')}}</span>
                                            </div>
                                        </div>

                                        <div class="ef-admin-sec">
                                            <h5>{{__('Hero')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Badge Text')}}</label>
                                                <input type="text" name="{{$p}}hero_badge" class="form-control" value="{{$val('hero_badge')}}">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Headline')}}</label>
                                                    <input type="text" name="{{$p}}hero_title" class="form-control" value="{{$val('hero_title')}}">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Headline, Green Part')}}</label>
                                                    <input type="text" name="{{$p}}hero_title_highlight" class="form-control" value="{{$val('hero_title_highlight')}}">
                                                    <span class="ef-admin-hint">{{__('Shown in brand green, straight after the headline above.')}}</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Subtitle')}}</label>
                                                <textarea name="{{$p}}hero_subtitle" rows="2" class="form-control">{{$val('hero_subtitle')}}</textarea>
                                            </div>
                                        </div>

                                        <div class="ef-admin-sec">
                                            <h5>{{__('The Search Box')}}</h5>
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('First Name Label')}}</label>
                                                    <input type="text" name="{{$p}}first_label" class="form-control" value="{{$val('first_label')}}">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Last Name Label')}}</label>
                                                    <input type="text" name="{{$p}}last_label" class="form-control" value="{{$val('last_label')}}">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Domain Label')}}</label>
                                                    <input type="text" name="{{$p}}domain_label" class="form-control" value="{{$val('domain_label')}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Button Text')}}</label>
                                                    <input type="text" name="{{$p}}tool_btn" class="form-control" value="{{$val('tool_btn')}}">
                                                </div>
                                                <div class="col-md-8 form-group">
                                                    <label>{{__('Working Message')}}</label>
                                                    <input type="text" name="{{$p}}working_label" class="form-control" value="{{$val('working_label')}}">
                                                    <span class="ef-admin-hint">{{__('Shown while the lookup is running.')}}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ef-admin-sec">
                                            <h5>{{__('How It Works')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="{{$p}}steps_title" class="form-control" value="{{$val('steps_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Section Intro')}}</label>
                                                <textarea name="{{$p}}steps_lead" rows="2" class="form-control">{{$val('steps_lead')}}</textarea>
                                            </div>
                                            <span class="ef-admin-hint">{{__('The three numbered steps themselves are edited under Page Blocks below.')}}</span>
                                        </div>

                                        <div class="ef-admin-sec">
                                            <h5>{{__('Email Format Table')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="{{$p}}patterns_title" class="form-control" value="{{$val('patterns_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Section Intro')}}</label>
                                                <textarea name="{{$p}}patterns_lead" rows="2" class="form-control">{{$val('patterns_lead')}}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Column 1 Heading')}}</label>
                                                    <input type="text" name="{{$p}}patterns_col_format" class="form-control" value="{{$val('patterns_col_format')}}">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Column 2 Heading')}}</label>
                                                    <input type="text" name="{{$p}}patterns_col_example" class="form-control" value="{{$val('patterns_col_example')}}">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Column 3 Heading')}}</label>
                                                    <input type="text" name="{{$p}}patterns_col_common" class="form-control" value="{{$val('patterns_col_common')}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ef-admin-sec">
                                            <h5>{{__('Link To The Email Verifier')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Title')}}</label>
                                                <input type="text" name="{{$p}}pair_title" class="form-control" value="{{$val('pair_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Text')}}</label>
                                                <textarea name="{{$p}}pair_text" rows="2" class="form-control">{{$val('pair_text')}}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Button Text')}}</label>
                                                <input type="text" name="{{$p}}pair_btn" class="form-control" value="{{$val('pair_btn')}}">
                                                <span class="ef-admin-hint">{{__('This button always points at the Email Verifier page.')}}</span>
                                            </div>
                                        </div>

                                        <div class="ef-admin-sec">
                                            <h5>{{__('Free Credits Offer')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Title')}}</label>
                                                <input type="text" name="{{$p}}offer_title" class="form-control" value="{{$val('offer_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Text')}}</label>
                                                <textarea name="{{$p}}offer_text" rows="2" class="form-control">{{$val('offer_text')}}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Button Text')}}</label>
                                                    <input type="text" name="{{$p}}offer_btn" class="form-control" value="{{$val('offer_btn')}}">
                                                </div>
                                                <div class="col-md-8 form-group">
                                                    <label>{{__('Button Link')}}</label>
                                                    <input type="text" name="{{$p}}offer_url" class="form-control" value="{{$val('offer_url')}}">
                                                    <span class="ef-admin-hint">{{__('Keep the utm_ part of the link so signups from this page stay traceable.')}}</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Small Print')}}</label>
                                                <input type="text" name="{{$p}}offer_note" class="form-control" value="{{$val('offer_note')}}">
                                            </div>
                                        </div>

                                        <div class="ef-admin-sec">
                                            <h5>{{__('FAQ')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="{{$p}}faq_title" class="form-control" value="{{$val('faq_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Questions Come From This Faq Section')}}</label>
                                                <select name="{{$p}}faq_category_id" class="form-control">
                                                    <option value="">{{__('Use the built-in questions')}}</option>
                                                    @foreach($all_faq_category as $category)
                                                        <option value="{{$category->id}}"
                                                            @if($val('faq_category_id') == $category->id) selected @endif>
                                                            {{$category->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="ef-admin-hint">
                                                    {{__('Pick the "Email Finder" section. Add or reword its questions under Faq in the sidebar. Whatever is set here also feeds the FAQ that Google can show under the search listing.')}}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="ef-admin-sec">
                                            <h5>{{__('Closing Banner')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Title')}}</label>
                                                <input type="text" name="{{$p}}cta_title" class="form-control" value="{{$val('cta_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Text')}}</label>
                                                <textarea name="{{$p}}cta_text" rows="2" class="form-control">{{$val('cta_text')}}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Green Button')}}</label>
                                                    <input type="text" name="{{$p}}cta_btn" class="form-control" value="{{$val('cta_btn')}}">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Outline Button')}}</label>
                                                    <input type="text" name="{{$p}}cta_btn_ghost" class="form-control" value="{{$val('cta_btn_ghost')}}">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary pr-4 pl-4">{{__('Update Page Text')}}</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ============ REPEATABLE BLOCKS ============ --}}
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Page Blocks')}}</h4>
                        <p class="text-muted">{{__('The tick line under the search box, the three numbered steps, and the rows of the email format table.')}}</p>

                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($all_languages as $key => $lang)
                                <li class="nav-item">
                                    <a class="nav-link @if($key == 0) active @endif" data-toggle="tab"
                                       href="#efitm_{{$lang->slug}}" role="tab">{{$lang->name}}</a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content mt-4">
                            @foreach($all_languages as $key => $lang)
                                <div class="tab-pane fade @if($key == 0) show active @endif" id="efitm_{{$lang->slug}}" role="tabpanel">
                                    @php $items = ($all_items[$lang->slug] ?? collect())->groupBy('section'); @endphp

                                    @foreach(\App\EmailFinderItem::SECTIONS as $section_key => $section_label)
                                        @php $is_pattern = $section_key == 'pattern'; @endphp
                                        <div class="ef-admin-sec">
                                            <h5>{{__($section_label)}}</h5>

                                            @if($is_pattern)
                                                <p class="text-muted">
                                                    {{__('Title is the email format, Description is the example address, Share is the label on the right, and Bar is how long the green bar is drawn (0-100). Bar and Share are separate on purpose: the bars are scaled so the smaller shares stay visible.')}}
                                                </p>
                                            @endif

                                            <table class="table table-sm ef-item-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:60px">{{__('Order')}}</th>
                                                        @if($is_pattern)
                                                            <th>{{__('Format')}}</th>
                                                            <th>{{__('Example')}}</th>
                                                            <th style="width:90px">{{__('Share')}}</th>
                                                            <th style="width:70px">{{__('Bar')}}</th>
                                                        @else
                                                            <th style="width:120px">{{__('Icon')}}</th>
                                                            <th>{{__('Title')}}</th>
                                                            <th>{{__('Description')}}</th>
                                                        @endif
                                                        <th style="width:90px">{{__('Status')}}</th>
                                                        <th style="width:120px">{{__('Action')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @forelse(($items[$section_key] ?? collect()) as $item)
                                                    <tr>
                                                        <td>{{$item->sr_order}}</td>
                                                        @if($is_pattern)
                                                            <td class="ef-ico">{{$item->title}}</td>
                                                            <td class="ef-ico">{{$item->description ?: '—'}}</td>
                                                            <td>{{$item->badge_key ?: '—'}}</td>
                                                            <td>{{$item->bar_width ?? '—'}}</td>
                                                        @else
                                                            <td class="ef-ico">{{$item->icon ?: '—'}}</td>
                                                            <td>{{$item->title}}</td>
                                                            <td>{{ \Illuminate\Support\Str::limit($item->description, 70) ?: '—' }}</td>
                                                        @endif
                                                        <td>
                                                            <span class="badge badge-{{$item->status == 'publish' ? 'success' : 'secondary'}}">{{$item->status}}</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info ef-edit-btn"
                                                                    data-toggle="modal" data-target="#efEditModal"
                                                                    data-id="{{$item->id}}"
                                                                    data-title="{{$item->title}}"
                                                                    data-description="{{$item->description}}"
                                                                    data-icon="{{$item->icon}}"
                                                                    data-badge_key="{{$item->badge_key}}"
                                                                    data-bar_width="{{$item->bar_width}}"
                                                                    data-sr_order="{{$item->sr_order}}"
                                                                    data-status="{{$item->status}}"
                                                                    data-is_highlight="{{$item->is_highlight}}">
                                                                <i class="ti-pencil"></i>
                                                            </button>
                                                            <a href="{{route('admin.email.finder.item.delete',$item->id)}}"
                                                               class="btn btn-sm btn-danger"
                                                               onclick="return confirm('{{__('Delete this item?')}}')">
                                                                <i class="ti-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="7" class="text-muted">{{__('Nothing here yet, so the page is showing its built-in wording.')}}</td></tr>
                                                @endforelse
                                                </tbody>
                                            </table>

                                            <form action="{{route('admin.email.finder.item.store')}}" method="POST" class="form-inline">
                                                @csrf
                                                <input type="hidden" name="section" value="{{$section_key}}">
                                                <input type="hidden" name="lang" value="{{$lang->slug}}">
                                                <input type="number" name="sr_order" class="form-control form-control-sm mr-2" style="width:80px" placeholder="{{__('Order')}}">
                                                @if($is_pattern)
                                                    <input type="text" name="title" class="form-control form-control-sm mr-2" style="width:150px" placeholder="{{__('first.last@')}}" required>
                                                    <input type="text" name="description" class="form-control form-control-sm mr-2" style="width:200px" placeholder="{{__('jane.doe@acme.com')}}">
                                                    <input type="text" name="badge_key" class="form-control form-control-sm mr-2" style="width:100px" placeholder="{{__('~60%')}}">
                                                    <input type="number" min="0" max="100" name="bar_width" class="form-control form-control-sm mr-2" style="width:90px" placeholder="{{__('Bar')}}">
                                                @else
                                                    <input type="text" name="icon" class="form-control form-control-sm mr-2" style="width:130px" placeholder="{{__('lucide icon')}}">
                                                    <input type="text" name="title" class="form-control form-control-sm mr-2" style="width:190px" placeholder="{{__('Title')}}" required>
                                                    <input type="text" name="description" class="form-control form-control-sm mr-2" style="width:280px" placeholder="{{__('Description')}}">
                                                @endif
                                                <button type="submit" class="btn btn-sm btn-primary">{{__('Add')}}</button>
                                            </form>

                                            @if($section_key == 'trust')
                                                <span class="ef-admin-hint">
                                                    {{__('Icon names come from lucide.dev, for example: check, shield-check, server, lock. Leave blank for a tick.')}}
                                                </span>
                                            @elseif($section_key == 'step')
                                                <span class="ef-admin-hint">
                                                    {{__('The number on each step is drawn automatically from the order, so there is no icon here.')}}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- edit modal --}}
    <div class="modal fade" id="efEditModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Edit Item')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{route('admin.email.finder.item.update')}}" method="POST" id="ef-edit-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="ef_edit_id">
                        <div class="form-group">
                            <label>{{__('Title')}}</label>
                            <input type="text" name="title" id="ef_edit_title" class="form-control" required>
                            <span class="ef-admin-hint">{{__('On a format table row this is the email format, such as first.last@')}}</span>
                        </div>
                        <div class="form-group">
                            <label>{{__('Description')}}</label>
                            <textarea name="description" id="ef_edit_description" rows="3" class="form-control"></textarea>
                            <span class="ef-admin-hint">{{__('On a format table row this is the example address.')}}</span>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label>{{__('Icon')}}</label>
                                <input type="text" name="icon" id="ef_edit_icon" class="form-control">
                                <span class="ef-admin-hint">{{__('Tick line only.')}}</span>
                            </div>
                            <div class="col-6 form-group">
                                <label>{{__('Order')}}</label>
                                <input type="number" name="sr_order" id="ef_edit_sr_order" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 form-group">
                                <label>{{__('Share Label')}}</label>
                                <input type="text" name="badge_key" id="ef_edit_badge_key" class="form-control">
                                <span class="ef-admin-hint">{{__('Format table only, e.g. ~60%')}}</span>
                            </div>
                            <div class="col-4 form-group">
                                <label>{{__('Bar Length')}}</label>
                                <input type="number" min="0" max="100" name="bar_width" id="ef_edit_bar_width" class="form-control">
                                <span class="ef-admin-hint">{{__('0-100.')}}</span>
                            </div>
                            <div class="col-4 form-group">
                                <label>{{__('Status')}}</label>
                                <select name="status" id="ef_edit_status" class="form-control">
                                    <option value="publish">{{__('Publish')}}</option>
                                    <option value="draft">{{__('Draft')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="is_highlight" id="ef_edit_is_highlight">
                            <label for="ef_edit_is_highlight">{{__('Draw this row\'s bar in a muted grey (used for the "everything else" row)')}}</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).on('click', '.ef-edit-btn', function () {
            var el = $(this);
            $('#ef_edit_id').val(el.data('id'));
            $('#ef_edit_title').val(el.data('title'));
            $('#ef_edit_description').val(el.data('description'));
            $('#ef_edit_icon').val(el.data('icon'));
            $('#ef_edit_badge_key').val(el.data('badge_key'));
            $('#ef_edit_bar_width').val(el.data('bar_width'));
            $('#ef_edit_sr_order').val(el.data('sr_order'));
            $('#ef_edit_status').val(el.data('status'));
            $('#ef_edit_is_highlight').prop('checked', el.data('is_highlight') == 1);
        });
    </script>
@endsection
