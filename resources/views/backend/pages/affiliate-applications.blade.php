@extends('backend.admin-master')
@section('site-title')
    {{__('Affiliate Applications')}}
@endsection
@section('style')
    <style>
        .af-app-table td{vertical-align:middle !important;}
        .af-app-promo{font-size:12.5px;color:#5a6779;max-width:380px;}
    </style>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                @include('backend.partials.message')
                <x-error-msg/>

                <div class="alert alert-info">
                    {{__('People who applied through the form on the Affiliate page. If you set an Apply Button Link under')}}
                    <a href="{{route('admin.affiliate.settings')}}"><strong>{{__('Affiliate Settings')}}</strong></a>,
                    {{__('the buttons send people to that platform instead and this list stops filling up.')}}
                </div>
            </div>

            <div class="col-lg-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="header-title mb-0">{{__('Applications')}}</h4>
                            <form method="GET" action="{{route('admin.affiliate.applications')}}" class="form-inline">
                                <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                    <option value="">{{__('All')}}</option>
                                    @foreach(\App\AffiliateApplication::STATUSES as $k => $label)
                                        <option value="{{$k}}" @if($status === $k) selected @endif>{{__($label)}}</option>
                                    @endforeach
                                </select>
                                <noscript><button type="submit" class="btn btn-sm btn-primary">{{__('Filter')}}</button></noscript>
                            </form>
                        </div>

                        <table class="table table-sm af-app-table">
                            <thead>
                                <tr>
                                    <th style="width:150px">{{__('When')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Email')}}</th>
                                    <th>{{__('Where They Promote')}}</th>
                                    <th>{{__('How')}}</th>
                                    <th style="width:100px">{{__('Status')}}</th>
                                    <th style="width:120px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>{{$app->created_at ? $app->created_at->format('d M Y, H:i') : '—'}}</td>
                                    <td>{{$app->name}}</td>
                                    <td><a href="mailto:{{$app->email}}">{{$app->email}}</a></td>
                                    <td>
                                        @if($app->site)
                                            {{ \Illuminate\Support\Str::limit($app->site, 40) }}
                                        @else — @endif
                                    </td>
                                    <td class="af-app-promo">{{ \Illuminate\Support\Str::limit($app->promotion, 90) ?: '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{$app->status === 'approved' ? 'success' : ($app->status === 'declined' ? 'danger' : 'info')}}">
                                            {{__(\App\AffiliateApplication::STATUSES[$app->status] ?? $app->status)}}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info af-app-btn"
                                                data-toggle="modal" data-target="#afAppModal"
                                                data-id="{{$app->id}}"
                                                data-name="{{$app->name}}"
                                                data-email="{{$app->email}}"
                                                data-site="{{$app->site}}"
                                                data-promotion="{{$app->promotion}}"
                                                data-status="{{$app->status}}"
                                                data-admin_note="{{$app->admin_note}}">
                                            <i class="ti-eye"></i>
                                        </button>
                                        <a href="{{route('admin.affiliate.application.delete', $app->id)}}"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('{{__('Delete this application?')}}')">
                                            <i class="ti-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-muted">{{__('No applications yet.')}}</td></tr>
                            @endforelse
                            </tbody>
                        </table>

                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="afAppModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Application')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{route('admin.affiliate.application.update')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="af_app_id">
                        <div class="form-group">
                            <label>{{__('Name')}}</label>
                            <p class="form-control-static" id="af_app_name"></p>
                        </div>
                        <div class="form-group">
                            <label>{{__('Email')}}</label>
                            <p class="form-control-static" id="af_app_email"></p>
                        </div>
                        <div class="form-group">
                            <label>{{__('Where They Promote')}}</label>
                            <p class="form-control-static" id="af_app_site"></p>
                        </div>
                        <div class="form-group">
                            <label>{{__('How They Will Promote')}}</label>
                            <p class="form-control-static" id="af_app_promotion" style="white-space:pre-wrap"></p>
                        </div>
                        <div class="form-group">
                            <label for="af_app_status">{{__('Status')}}</label>
                            <select name="status" id="af_app_status" class="form-control">
                                @foreach(\App\AffiliateApplication::STATUSES as $k => $label)
                                    <option value="{{$k}}">{{__($label)}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="af_app_note">{{__('Internal Note')}}</label>
                            <textarea name="admin_note" id="af_app_note" rows="3" class="form-control"></textarea>
                            <small class="text-muted">{{__('Only visible here. The applicant never sees this.')}}</small>
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
        $(document).on('click', '.af-app-btn', function () {
            var el = $(this);
            $('#af_app_id').val(el.data('id'));
            // .text(), never .html(): this is text a stranger typed.
            $('#af_app_name').text(el.data('name') || '—');
            $('#af_app_email').text(el.data('email') || '—');
            $('#af_app_site').text(el.data('site') || '—');
            $('#af_app_promotion').text(el.data('promotion') || '—');
            $('#af_app_status').val(el.data('status'));
            $('#af_app_note').val(el.data('admin_note'));
        });
    </script>
@endsection
