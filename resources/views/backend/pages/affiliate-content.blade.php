@extends('backend.admin-master')
@section('site-title')
    {{__('Affiliate Page Content')}}
@endsection
@section('style')
    <style>
        .af-admin-hint{font-size:12px;color:#8a94a6;display:block;margin-top:4px;}
        .af-admin-sec{border:1px solid #e9edf2;border-radius:6px;padding:18px;margin-bottom:22px;}
        .af-admin-sec > h5{font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#5a6779;margin-bottom:16px;}
        .af-item-table td{vertical-align:middle !important;}
        .af-item-table .af-ico{font-family:monospace;font-size:12px;color:#5a6779;}
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
                    {{__('Everything a visitor reads on the Affiliate page is edited here. The commission rate, cookie window and payout terms are under')}}
                    <a href="{{route('admin.affiliate.settings')}}"><strong>{{__('Affiliate Settings')}}</strong></a>,
                    {{__('because they are used by the earnings calculator as well as the page. FAQ questions come from the Faq section chosen at the bottom of this screen.')}}
                    <a href="{{url('/affiliate')}}" target="_blank" rel="noopener">{{__('View the live page')}}</a>
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
                                       href="#aftxt_{{$lang->slug}}" role="tab">{{$lang->name}}</a>
                                </li>
                            @endforeach
                        </ul>

                        <form action="{{route('admin.affiliate.content')}}" method="POST">
                            @csrf
                            <div class="tab-content mt-4">
                                @foreach($all_languages as $key => $lang)
                                    @php
                                        $p = 'af_'.$lang->slug.'_';
                                        $val = fn($f) => \App\Http\Controllers\AffiliateSettingsController::text_value($lang->slug, $f);
                                    @endphp
                                    <div class="tab-pane fade @if($key == 0) show active @endif" id="aftxt_{{$lang->slug}}" role="tabpanel">

                                        <div class="af-admin-sec">
                                            <h5>{{__('Page Basics')}}</h5>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Page Title')}}</label>
                                                    <input type="text" name="{{$p}}page_title" class="form-control" value="{{$val('page_title')}}">
                                                    <span class="af-admin-hint">{{__('The browser tab, the breadcrumb, and the page name search engines read.')}}</span>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Text For Unset Terms')}}</label>
                                                    <input type="text" name="{{$p}}tbc_label" class="form-control" value="{{$val('tbc_label')}}">
                                                    <span class="af-admin-hint">{{__('Shown wherever a programme term has not been filled in yet, for example "To be confirmed".')}}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="af-admin-sec">
                                            <h5>{{__('Search Engines')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Meta Description')}}</label>
                                                <textarea name="{{$p}}meta_description" rows="2" class="form-control">{{$val('meta_description')}}</textarea>
                                                <span class="af-admin-hint">{{__('The sentence Google shows under the page title. Around 150-160 characters works best.')}}</span>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Meta Keywords')}}</label>
                                                <input type="text" name="{{$p}}meta_keywords" class="form-control" value="{{$val('meta_keywords')}}">
                                            </div>
                                        </div>

                                        <div class="af-admin-sec">
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
                                                    <span class="af-admin-hint">{{__('Shown in brand green, straight after the headline above.')}}</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Subtitle')}}</label>
                                                <textarea name="{{$p}}hero_subtitle" rows="2" class="form-control">{{$val('hero_subtitle')}}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Green Button')}}</label>
                                                    <input type="text" name="{{$p}}hero_btn" class="form-control" value="{{$val('hero_btn')}}">
                                                    <span class="af-admin-hint">{{__('Where it goes is the Apply Button Link in Affiliate Settings.')}}</span>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Outline Button')}}</label>
                                                    <input type="text" name="{{$p}}hero_btn_alt" class="form-control" value="{{$val('hero_btn_alt')}}">
                                                    <span class="af-admin-hint">{{__('Scrolls down to the calculator.')}}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="af-admin-sec">
                                            <h5>{{__('Earnings Calculator')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="{{$p}}calc_title" class="form-control" value="{{$val('calc_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Section Intro')}}</label>
                                                <textarea name="{{$p}}calc_lead" rows="2" class="form-control">{{$val('calc_lead')}}</textarea>
                                                <span class="af-admin-hint">{{__('Write :months where the number of months should appear. It is filled in from Months To Project in Affiliate Settings, so the wording and the sums always agree.')}}</span>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Slider Label')}}</label>
                                                    <input type="text" name="{{$p}}calc_refs_label" class="form-control" value="{{$val('calc_refs_label')}}">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Plan Picker Label')}}</label>
                                                    <input type="text" name="{{$p}}calc_plan_label" class="form-control" value="{{$val('calc_plan_label')}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Result 1 Label')}}</label>
                                                    <input type="text" name="{{$p}}calc_monthly_label" class="form-control" value="{{$val('calc_monthly_label')}}">
                                                    <span class="af-admin-hint">{{__(':months works here too.')}}</span>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Result 2 Label')}}</label>
                                                    <input type="text" name="{{$p}}calc_year_label" class="form-control" value="{{$val('calc_year_label')}}">
                                                    <span class="af-admin-hint">{{__(':months works here too.')}}</span>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Button Text')}}</label>
                                                    <input type="text" name="{{$p}}calc_btn" class="form-control" value="{{$val('calc_btn')}}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Message When No Rate Is Set')}}</label>
                                                <textarea name="{{$p}}calc_unset_note" rows="2" class="form-control">{{$val('calc_unset_note')}}</textarea>
                                                <span class="af-admin-hint">{{__('Shown in place of the assumptions line until a commission rate exists in Affiliate Settings.')}}</span>
                                            </div>
                                        </div>

                                        <div class="af-admin-sec">
                                            <h5>{{__('Terms Row')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="{{$p}}terms_title" class="form-control" value="{{$val('terms_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Section Intro')}}</label>
                                                <textarea name="{{$p}}terms_lead" rows="2" class="form-control">{{$val('terms_lead')}}</textarea>
                                            </div>
                                            <p class="text-muted">{{__('The five values themselves live in Affiliate Settings. These are only the labels above and below each one.')}}</p>
                                            @foreach([
                                                'rate' => 'Commission',
                                                'term' => 'Duration',
                                                'cookie' => 'Cookie Window',
                                                'payout' => 'Paid By',
                                                'minimum' => 'Minimum Payout',
                                            ] as $tk => $tlabel)
                                                <div class="row">
                                                    <div class="col-md-5 form-group">
                                                        <label>{{__($tlabel)}} &mdash; {{__('Label')}}</label>
                                                        <input type="text" name="{{$p}}terms_{{$tk}}_label" class="form-control" value="{{$val('terms_'.$tk.'_label')}}">
                                                    </div>
                                                    <div class="col-md-7 form-group">
                                                        <label>{{__($tlabel)}} &mdash; {{__('Note Under It')}}</label>
                                                        <input type="text" name="{{$p}}terms_{{$tk}}_note" class="form-control" value="{{$val('terms_'.$tk.'_note')}}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="af-admin-sec">
                                            <h5>{{__('How It Works')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="{{$p}}steps_title" class="form-control" value="{{$val('steps_title')}}">
                                            </div>
                                            <span class="af-admin-hint">{{__('The three numbered steps themselves are under Page Blocks below.')}}</span>
                                        </div>

                                        <div class="af-admin-sec">
                                            <h5>{{__('Affiliate vs Reseller Table')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="{{$p}}compare_title" class="form-control" value="{{$val('compare_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Section Intro')}}</label>
                                                <textarea name="{{$p}}compare_lead" rows="2" class="form-control">{{$val('compare_lead')}}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Left Column Heading')}}</label>
                                                    <input type="text" name="{{$p}}compare_col_aff" class="form-control" value="{{$val('compare_col_aff')}}">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Right Column Heading')}}</label>
                                                    <input type="text" name="{{$p}}compare_col_res" class="form-control" value="{{$val('compare_col_res')}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5 form-group">
                                                    <label>{{__('Footer Text')}}</label>
                                                    <input type="text" name="{{$p}}compare_cta_text" class="form-control" value="{{$val('compare_cta_text')}}">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label>{{__('Footer Button')}}</label>
                                                    <input type="text" name="{{$p}}compare_cta_btn" class="form-control" value="{{$val('compare_cta_btn')}}">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Footer Button Link')}}</label>
                                                    <input type="text" name="{{$p}}compare_cta_url" class="form-control" value="{{$val('compare_cta_url')}}">
                                                    <span class="af-admin-hint">{{__('Defaults to your existing /reseller page.')}}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="af-admin-sec">
                                            <h5>{{__('Who It Suits')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="{{$p}}who_title" class="form-control" value="{{$val('who_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Section Intro')}}</label>
                                                <textarea name="{{$p}}who_lead" rows="2" class="form-control">{{$val('who_lead')}}</textarea>
                                            </div>
                                        </div>

                                        <div class="af-admin-sec">
                                            <h5>{{__('What Partners Get')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="{{$p}}gets_title" class="form-control" value="{{$val('gets_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Section Intro')}}</label>
                                                <textarea name="{{$p}}gets_lead" rows="2" class="form-control">{{$val('gets_lead')}}</textarea>
                                            </div>
                                        </div>

                                        <div class="af-admin-sec">
                                            <h5>{{__('Testimonials and FAQ')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Testimonials Title')}}</label>
                                                <input type="text" name="{{$p}}testimonial_title" class="form-control" value="{{$val('testimonial_title')}}">
                                                <span class="af-admin-hint">{{__('The quotes come from the Testimonial section in the sidebar. If there are none published, the whole strip is hidden.')}}</span>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('FAQ Title')}}</label>
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
                                                <span class="af-admin-hint">
                                                    {{__('Pick the "Affiliate" section. Whatever is set here also feeds the FAQ that Google can show under the search listing.')}}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="af-admin-sec">
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
                                                    <label>{{__('Button Text')}}</label>
                                                    <input type="text" name="{{$p}}cta_btn" class="form-control" value="{{$val('cta_btn')}}">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>{{__('Small Print')}}</label>
                                                    <input type="text" name="{{$p}}cta_note" class="form-control" value="{{$val('cta_note')}}">
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
                        <p class="text-muted">{{__('The tick line in the hero, the three steps, the rows of the comparison table, the audience cards and the list of what partners get.')}}</p>

                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($all_languages as $key => $lang)
                                <li class="nav-item">
                                    <a class="nav-link @if($key == 0) active @endif" data-toggle="tab"
                                       href="#afitm_{{$lang->slug}}" role="tab">{{$lang->name}}</a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content mt-4">
                            @foreach($all_languages as $key => $lang)
                                <div class="tab-pane fade @if($key == 0) show active @endif" id="afitm_{{$lang->slug}}" role="tabpanel">
                                    @php $items = ($all_items[$lang->slug] ?? collect())->groupBy('section'); @endphp

                                    @foreach(\App\AffiliateItem::SECTIONS as $section_key => $section_label)
                                        @php $is_cmp = $section_key == 'compare'; @endphp
                                        <div class="af-admin-sec">
                                            <h5>{{__($section_label)}}</h5>

                                            @if($is_cmp)
                                                <p class="text-muted">
                                                    {{__('Title is the row label on the left, Affiliate is the green middle column, and Reseller is the right column.')}}
                                                </p>
                                            @endif

                                            <table class="table table-sm af-item-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:60px">{{__('Order')}}</th>
                                                        @if($is_cmp)
                                                            <th>{{__('Row Label')}}</th>
                                                            <th>{{__('Affiliate')}}</th>
                                                            <th>{{__('Reseller')}}</th>
                                                        @else
                                                            <th style="width:110px">{{__('Icon')}}</th>
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
                                                        @if($is_cmp)
                                                            <td>{{$item->title}}</td>
                                                            <td>{{ \Illuminate\Support\Str::limit($item->description, 50) ?: '—' }}</td>
                                                            <td>{{ \Illuminate\Support\Str::limit($item->alt_text, 50) ?: '—' }}</td>
                                                        @else
                                                            <td class="af-ico">{{$item->icon ?: '—'}}</td>
                                                            <td>{{$item->title}}</td>
                                                            <td>{{ \Illuminate\Support\Str::limit($item->description, 60) ?: '—' }}</td>
                                                        @endif
                                                        <td>
                                                            <span class="badge badge-{{$item->status == 'publish' ? 'success' : 'secondary'}}">{{$item->status}}</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info af-edit-btn"
                                                                    data-toggle="modal" data-target="#afEditModal"
                                                                    data-id="{{$item->id}}"
                                                                    data-title="{{$item->title}}"
                                                                    data-description="{{$item->description}}"
                                                                    data-alt_text="{{$item->alt_text}}"
                                                                    data-icon="{{$item->icon}}"
                                                                    data-sr_order="{{$item->sr_order}}"
                                                                    data-status="{{$item->status}}">
                                                                <i class="ti-pencil"></i>
                                                            </button>
                                                            <a href="{{route('admin.affiliate.item.delete',$item->id)}}"
                                                               class="btn btn-sm btn-danger"
                                                               onclick="return confirm('{{__('Delete this item?')}}')">
                                                                <i class="ti-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="6" class="text-muted">{{__('Nothing here yet, so the page is showing its built-in wording.')}}</td></tr>
                                                @endforelse
                                                </tbody>
                                            </table>

                                            <form action="{{route('admin.affiliate.item.store')}}" method="POST" class="form-inline">
                                                @csrf
                                                <input type="hidden" name="section" value="{{$section_key}}">
                                                <input type="hidden" name="lang" value="{{$lang->slug}}">
                                                <input type="number" name="sr_order" class="form-control form-control-sm mr-2" style="width:80px" placeholder="{{__('Order')}}">
                                                @if($is_cmp)
                                                    <input type="text" name="title" class="form-control form-control-sm mr-2" style="width:180px" placeholder="{{__('Row label')}}" required>
                                                    <input type="text" name="description" class="form-control form-control-sm mr-2" style="width:230px" placeholder="{{__('Affiliate column')}}">
                                                    <input type="text" name="alt_text" class="form-control form-control-sm mr-2" style="width:230px" placeholder="{{__('Reseller column')}}">
                                                @else
                                                    <input type="text" name="icon" class="form-control form-control-sm mr-2" style="width:120px" placeholder="{{__('icon / letter')}}">
                                                    <input type="text" name="title" class="form-control form-control-sm mr-2" style="width:190px" placeholder="{{__('Title')}}" required>
                                                    <input type="text" name="description" class="form-control form-control-sm mr-2" style="width:280px" placeholder="{{__('Description')}}">
                                                @endif
                                                <button type="submit" class="btn btn-sm btn-primary">{{__('Add')}}</button>
                                            </form>

                                            @if($section_key == 'trust')
                                                <span class="af-admin-hint">{{__('Icon names come from lucide.dev, for example: check, shield-check, wallet. Leave blank for a tick.')}}</span>
                                            @elseif($section_key == 'who')
                                                <span class="af-admin-hint">{{__('Icon can be a single letter (shown in the green square) or a lucide icon name.')}}</span>
                                            @elseif($section_key == 'step')
                                                <span class="af-admin-hint">{{__('The number on each step is drawn automatically from the order, so there is no icon here.')}}</span>
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
    <div class="modal fade" id="afEditModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Edit Item')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{route('admin.affiliate.item.update')}}" method="POST" id="af-edit-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="af_edit_id">
                        <div class="form-group">
                            <label>{{__('Title')}}</label>
                            <input type="text" name="title" id="af_edit_title" class="form-control" required>
                            <span class="af-admin-hint">{{__('On a comparison row this is the label on the left.')}}</span>
                        </div>
                        <div class="form-group">
                            <label>{{__('Description')}}</label>
                            <textarea name="description" id="af_edit_description" rows="3" class="form-control"></textarea>
                            <span class="af-admin-hint">{{__('On a comparison row this is the Affiliate column.')}}</span>
                        </div>
                        <div class="form-group">
                            <label>{{__('Reseller Column')}}</label>
                            <textarea name="alt_text" id="af_edit_alt_text" rows="2" class="form-control"></textarea>
                            <span class="af-admin-hint">{{__('Comparison rows only.')}}</span>
                        </div>
                        <div class="row">
                            <div class="col-4 form-group">
                                <label>{{__('Icon')}}</label>
                                <input type="text" name="icon" id="af_edit_icon" class="form-control">
                            </div>
                            <div class="col-4 form-group">
                                <label>{{__('Order')}}</label>
                                <input type="number" name="sr_order" id="af_edit_sr_order" class="form-control">
                            </div>
                            <div class="col-4 form-group">
                                <label>{{__('Status')}}</label>
                                <select name="status" id="af_edit_status" class="form-control">
                                    <option value="publish">{{__('Publish')}}</option>
                                    <option value="draft">{{__('Draft')}}</option>
                                </select>
                            </div>
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
        $(document).on('click', '.af-edit-btn', function () {
            var el = $(this);
            $('#af_edit_id').val(el.data('id'));
            $('#af_edit_title').val(el.data('title'));
            $('#af_edit_description').val(el.data('description'));
            $('#af_edit_alt_text').val(el.data('alt_text'));
            $('#af_edit_icon').val(el.data('icon'));
            $('#af_edit_sr_order').val(el.data('sr_order'));
            $('#af_edit_status').val(el.data('status'));
        });
    </script>
@endsection
