@extends('backend.admin-master')
@section('site-title')
    {{__('Email Verifier Page Content')}}
@endsection
@section('style')
    <style>
        .ev-admin-hint{font-size:12px;color:#8a94a6;display:block;margin-top:4px;}
        .ev-admin-sec{border:1px solid #e9edf2;border-radius:6px;padding:18px;margin-bottom:22px;}
        .ev-admin-sec > h5{font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#5a6779;margin-bottom:16px;}
        .ev-item-table td{vertical-align:middle !important;}
        .ev-item-table .ev-ico{font-family:monospace;font-size:12px;color:#5a6779;}
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
                    {{__('Everything on the public Email Verifier page is edited here. The checker itself (SMTP sender, timeouts) is under')}}
                    <a href="{{route('admin.email.verifier.settings')}}"><strong>{{__('Email Verifier Settings')}}</strong></a>.
                </div>
            </div>

            {{-- ============ TEXT CONTENT ============ --}}
            <div class="col-lg-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Page Text')}}</h4>

                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($all_languages as $key => $lang)
                                <li class="nav-item">
                                    <a class="nav-link @if($key == 0) active @endif" data-toggle="tab"
                                       href="#txt_{{$lang->slug}}" role="tab">{{$lang->name}}</a>
                                </li>
                            @endforeach
                        </ul>

                        <form action="{{route('admin.email.verifier.content')}}" method="POST">
                            @csrf
                            <div class="tab-content mt-4">
                                @foreach($all_languages as $key => $lang)
                                    @php $p = 'ev_'.$lang->slug.'_'; @endphp
                                    <div class="tab-pane fade @if($key == 0) show active @endif" id="txt_{{$lang->slug}}" role="tabpanel">

                                        <div class="ev-admin-sec">
                                            <h5>{{__('Hero')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Badge Text')}}</label>
                                                <input type="text" name="{{$p}}hero_badge" class="form-control"
                                                       value="{{get_static_option($p.'hero_badge')}}" placeholder="Live checker, results in seconds">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8 form-group">
                                                    <label>{{__('Headline')}}</label>
                                                    <input type="text" name="{{$p}}hero_title" class="form-control"
                                                           value="{{get_static_option($p.'hero_title')}}" placeholder="Know if an email is real before you">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Highlighted Words')}}</label>
                                                    <input type="text" name="{{$p}}hero_title_highlight" class="form-control"
                                                           value="{{get_static_option($p.'hero_title_highlight')}}" placeholder="hit send">
                                                    <span class="ev-admin-hint">{{__('Shown in green at the end of the headline.')}}</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Sub Heading')}}</label>
                                                <textarea name="{{$p}}hero_subtitle" rows="2" class="form-control">{{get_static_option($p.'hero_subtitle')}}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Reassurance Line (inside the checker box)')}}</label>
                                                <input type="text" name="{{$p}}tool_foot" class="form-control"
                                                       value="{{get_static_option($p.'tool_foot')}}" placeholder="Free to use, No signup needed, Nothing is stored">
                                                <span class="ev-admin-hint">{{__('Separate each item with a comma.')}}</span>
                                            </div>
                                        </div>

                                        <div class="ev-admin-sec">
                                            <h5>{{__('Section Headings')}}</h5>
                                            @foreach([
                                                ['checks','What We Check'],
                                                ['glossary','Status Glossary'],
                                                ['why','Why It Matters'],
                                                ['testimonial','Testimonials'],
                                                ['faq','FAQ'],
                                            ] as $sec)
                                                <div class="row">
                                                    <div class="col-md-3 form-group">
                                                        <label>{{__($sec[1].' Kicker')}}</label>
                                                        <input type="text" name="{{$p}}{{$sec[0]}}_kicker" class="form-control"
                                                               value="{{get_static_option($p.$sec[0].'_kicker')}}">
                                                    </div>
                                                    <div class="col-md-4 form-group">
                                                        <label>{{__($sec[1].' Title')}}</label>
                                                        <input type="text" name="{{$p}}{{$sec[0]}}_title" class="form-control"
                                                               value="{{get_static_option($p.$sec[0].'_title')}}">
                                                    </div>
                                                    @if(in_array($sec[0], ['checks','why']))
                                                        <div class="col-md-5 form-group">
                                                            <label>{{__($sec[1].' Description')}}</label>
                                                            <input type="text" name="{{$p}}{{$sec[0]}}_lead" class="form-control"
                                                                   value="{{get_static_option($p.$sec[0].'_lead')}}">
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach

                                            <div class="form-group">
                                                <label>{{__('FAQ Section Source')}}</label>
                                                <select name="{{$p}}faq_category_id" class="form-control">
                                                    <option value="">{{__('Use the built-in default questions')}}</option>
                                                    @foreach($all_faq_category as $cat)
                                                        <option value="{{$cat->id}}"
                                                            @if(get_static_option($p.'faq_category_id') == $cat->id) selected @endif>
                                                            {{$cat->name}} ({{$cat->lang}})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="ev-admin-hint">{{__('Pick a Faq category to show its questions here, managed under Faq → All Faq.')}}</span>
                                            </div>
                                        </div>

                                        <div class="ev-admin-sec">
                                            <h5>{{__('Bottom Call To Action')}}</h5>
                                            <div class="form-group">
                                                <label>{{__('Title')}}</label>
                                                <input type="text" name="{{$p}}cta_title" class="form-control" value="{{get_static_option($p.'cta_title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{__('Text')}}</label>
                                                <textarea name="{{$p}}cta_text" rows="2" class="form-control">{{get_static_option($p.'cta_text')}}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label>{{__('Button Label')}}</label>
                                                    <input type="text" name="{{$p}}cta_btn" class="form-control" value="{{get_static_option($p.'cta_btn')}}">
                                                </div>
                                                <div class="col-md-5 form-group">
                                                    <label>{{__('Button Link')}}</label>
                                                    <input type="text" name="{{$p}}cta_url" class="form-control" value="{{get_static_option($p.'cta_url')}}"
                                                           placeholder="https://app.go4database.com/register">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label>{{__('Small Note')}}</label>
                                                    <input type="text" name="{{$p}}cta_note" class="form-control" value="{{get_static_option($p.'cta_note')}}">
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
                        <p class="text-muted">{{__('The trust strip under the hero, the "what we check" cards, the status glossary and the "why it matters" steps.')}}</p>

                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($all_languages as $key => $lang)
                                <li class="nav-item">
                                    <a class="nav-link @if($key == 0) active @endif" data-toggle="tab"
                                       href="#itm_{{$lang->slug}}" role="tab">{{$lang->name}}</a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content mt-4">
                            @foreach($all_languages as $key => $lang)
                                <div class="tab-pane fade @if($key == 0) show active @endif" id="itm_{{$lang->slug}}" role="tabpanel">
                                    @php $items = ($all_items[$lang->slug] ?? collect())->groupBy('section'); @endphp

                                    @foreach(\App\EmailVerifierItem::SECTIONS as $section_key => $section_label)
                                        <div class="ev-admin-sec">
                                            <h5>{{__($section_label)}}</h5>
                                            <table class="table table-sm ev-item-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:60px">{{__('Order')}}</th>
                                                        <th style="width:120px">{{__('Icon')}}</th>
                                                        <th>{{__('Title')}}</th>
                                                        <th>{{__('Description')}}</th>
                                                        <th style="width:90px">{{__('Status')}}</th>
                                                        <th style="width:120px">{{__('Action')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @forelse(($items[$section_key] ?? collect()) as $item)
                                                    <tr>
                                                        <td>{{$item->sr_order}}</td>
                                                        <td class="ev-ico">{{$item->icon ?: '—'}}</td>
                                                        <td>{{$item->title}}</td>
                                                        <td>{{ \Illuminate\Support\Str::limit($item->description, 70) ?: '—' }}</td>
                                                        <td>
                                                            <span class="badge badge-{{$item->status == 'publish' ? 'success' : 'secondary'}}">{{$item->status}}</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info ev-edit-btn"
                                                                    data-toggle="modal" data-target="#evEditModal"
                                                                    data-id="{{$item->id}}"
                                                                    data-title="{{$item->title}}"
                                                                    data-description="{{$item->description}}"
                                                                    data-icon="{{$item->icon}}"
                                                                    data-badge_key="{{$item->badge_key}}"
                                                                    data-sr_order="{{$item->sr_order}}"
                                                                    data-status="{{$item->status}}"
                                                                    data-is_highlight="{{$item->is_highlight}}">
                                                                <i class="ti-pencil"></i>
                                                            </button>
                                                            <a href="{{route('admin.email.verifier.item.delete',$item->id)}}"
                                                               class="btn btn-sm btn-danger"
                                                               onclick="return confirm('{{__('Delete this item?')}}')">
                                                                <i class="ti-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="6" class="text-muted">{{__('Nothing here yet.')}}</td></tr>
                                                @endforelse
                                                </tbody>
                                            </table>

                                            <form action="{{route('admin.email.verifier.item.store')}}" method="POST" class="form-inline">
                                                @csrf
                                                <input type="hidden" name="section" value="{{$section_key}}">
                                                <input type="hidden" name="lang" value="{{$lang->slug}}">
                                                <input type="number" name="sr_order" class="form-control form-control-sm mr-2" style="width:80px" placeholder="{{__('Order')}}">
                                                <input type="text" name="icon" class="form-control form-control-sm mr-2" style="width:130px" placeholder="{{__('lucide icon')}}">
                                                @if($section_key == 'glossary')
                                                    <input type="text" name="badge_key" class="form-control form-control-sm mr-2" style="width:120px" placeholder="{{__('status key')}}">
                                                @endif
                                                <input type="text" name="title" class="form-control form-control-sm mr-2" style="width:190px" placeholder="{{__('Title')}}" required>
                                                <input type="text" name="description" class="form-control form-control-sm mr-2" style="width:280px" placeholder="{{__('Description')}}">
                                                <button type="submit" class="btn btn-sm btn-primary">{{__('Add')}}</button>
                                            </form>
                                            @if($section_key == 'check' || $section_key == 'glossary' || $section_key == 'trust')
                                                <span class="ev-admin-hint">
                                                    {{__('Icon names come from lucide.dev, for example: shield-check, server, globe, users, layers.')}}
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
    <div class="modal fade" id="evEditModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Edit Item')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{route('admin.email.verifier.item.update')}}" method="POST" id="ev-edit-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="ev_edit_id">
                        <div class="form-group">
                            <label>{{__('Title')}}</label>
                            <input type="text" name="title" id="ev_edit_title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{__('Description')}}</label>
                            <textarea name="description" id="ev_edit_description" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label>{{__('Icon')}}</label>
                                <input type="text" name="icon" id="ev_edit_icon" class="form-control">
                            </div>
                            <div class="col-6 form-group">
                                <label>{{__('Order')}}</label>
                                <input type="number" name="sr_order" id="ev_edit_sr_order" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label>{{__('Status Key')}}</label>
                                <input type="text" name="badge_key" id="ev_edit_badge_key" class="form-control">
                                <span class="ev-admin-hint">{{__('Glossary rows only.')}}</span>
                            </div>
                            <div class="col-6 form-group">
                                <label>{{__('Status')}}</label>
                                <select name="status" id="ev_edit_status" class="form-control">
                                    <option value="publish">{{__('Publish')}}</option>
                                    <option value="draft">{{__('Draft')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="is_highlight" id="ev_edit_is_highlight">
                            <label for="ev_edit_is_highlight">{{__('Highlight this one (green tick on the "why it matters" steps)')}}</label>
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
        $(document).on('click', '.ev-edit-btn', function () {
            var el = $(this);
            $('#ev_edit_id').val(el.data('id'));
            $('#ev_edit_title').val(el.data('title'));
            $('#ev_edit_description').val(el.data('description'));
            $('#ev_edit_icon').val(el.data('icon'));
            $('#ev_edit_badge_key').val(el.data('badge_key'));
            $('#ev_edit_sr_order').val(el.data('sr_order'));
            $('#ev_edit_status').val(el.data('status'));
            $('#ev_edit_is_highlight').prop('checked', el.data('is_highlight') == 1);
        });
    </script>
@endsection
