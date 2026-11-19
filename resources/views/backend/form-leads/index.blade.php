@extends('backend.admin-master')
@section('site-title')
    {{ __('Form Leads') }}
@endsection
@section('style')
<style>
    .g4fl-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:16px}
    .g4fl-head p{margin:4px 0 0;color:#6b7280;max-width:640px}
    .g4fl-chips{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px}
    .g4fl-chips a{font-size:13px;padding:5px 11px;border:1px solid #e6e9f0;border-radius:999px;color:#374151;background:#fff}
    .g4fl-chips a:hover{border-color:#2563eb;color:#2563eb;text-decoration:none}
    .g4fl-chips a.is-on{background:#2563eb;border-color:#2563eb;color:#fff}
    .g4fl-chips b{font-weight:600;margin-left:4px;opacity:.75}
    .g4fl-bar{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px}
    .g4fl-bar .form-control{max-width:260px}
    .g4fl-table td{vertical-align:top;font-size:13px}
    .g4fl-table th{font-size:12px;text-transform:uppercase;letter-spacing:.03em;color:#6b7280;white-space:nowrap}
    .g4fl-who b{display:block;color:#111827}
    .g4fl-who a{color:#2563eb;word-break:break-all}
    .g4fl-form{display:inline-block;font-size:12px;padding:2px 8px;border-radius:999px;background:#eef2ff;color:#3730a3;white-space:nowrap}
    .g4fl-form--list_sample{background:#ecfdf5;color:#065f46}
    .g4fl-form--list_signup{background:#fff7ed;color:#9a3412}
    .g4fl-text{max-width:360px;white-space:pre-line;color:#374151}
    .g4fl-text details summary{cursor:pointer;color:#2563eb}
    .g4fl-muted{color:#9ca3af;font-size:12px}
    .g4fl-status{min-width:120px}
    .g4fl-status.is-new{border-color:#f59e0b}
    .g4fl-empty{padding:40px 0;text-align:center;color:#6b7280}
</style>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                @include('backend.partials.message')
                <div class="card">
                    <div class="card-body">
                        <div class="g4fl-head">
                            <div>
                                <h4 class="header-title mb-0">{{ __('Form leads') }} @if($new_count)<span class="badge badge-warning ml-1">{{ $new_count }} {{ __('new') }}</span>@endif</h4>
                                <p>{{ __('Everything sent through the list pages\' forms: talk to an expert, sample requests and the free-credits sign-up. Each is kept here even if its notification email fails.') }}</p>
                            </div>
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.form.leads.export', request()->query()) }}"><i class="ti-download"></i> {{ __('Download CSV') }}</a>
                        </div>

                        <div class="g4fl-chips" aria-label="{{ __('Filter by form') }}">
                            <a href="{{ route('admin.form.leads', array_filter(['status' => $status, 'q' => $search])) }}" @class(['is-on' => $form === null])>{{ __('All forms') }}<b>{{ $counts->sum() }}</b></a>
                            @foreach($forms as $value => $label)
                                <a href="{{ route('admin.form.leads', array_filter(['form' => $value, 'status' => $status, 'q' => $search])) }}" @class(['is-on' => $form === $value])>{{ __($label) }}<b>{{ $counts[$value] ?? 0 }}</b></a>
                            @endforeach
                        </div>

                        <form class="g4fl-bar" method="GET" action="{{ route('admin.form.leads') }}">
                            @if($form)<input type="hidden" name="form" value="{{ $form }}">@endif
                            <input type="search" class="form-control" name="q" value="{{ $search }}" placeholder="{{ __('Search name, email, phone, list...') }}">
                            <select class="form-control" name="status" style="max-width:170px" onchange="this.form.submit()">
                                <option value="">{{ __('Any status') }}</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected($status === $value)>{{ __($label) }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary btn-sm" type="submit">{{ __('Search') }}</button>
                            @if($form || $status || $search)<a class="btn btn-link btn-sm" href="{{ route('admin.form.leads') }}">{{ __('Clear') }}</a>@endif
                        </form>

                        @if($leads->isEmpty())
                            <p class="g4fl-empty">{{ ($form || $status || $search) ? __('No lead matches these filters.') : __('No form leads yet. They appear here as soon as someone sends a form on a list page.') }}</p>
                        @else
                            <div class="table-responsive">
                                <table class="table g4fl-table">
                                    <thead>
                                    <tr>
                                        <th>{{ __('Received') }}</th>
                                        <th>{{ __('Form') }}</th>
                                        <th>{{ __('Person') }}</th>
                                        <th>{{ __('Details') }}</th>
                                        <th>{{ __('From list') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($leads as $lead)
                                        <tr>
                                            <td style="white-space:nowrap">
                                                {{ $lead->created_at?->format('d M Y') }}<br>
                                                <span class="g4fl-muted">{{ $lead->created_at?->format('H:i') }}</span>
                                            </td>
                                            <td><span class="g4fl-form g4fl-form--{{ $lead->form }}">{{ __($lead->kind()?->label() ?? $lead->form) }}</span>
                                                @if($lead->emailed === false)<br><span class="g4fl-muted" title="{{ __('The notification email could not be sent; the lead is safe here.') }}">{{ __('email not sent') }}</span>@endif
                                            </td>
                                            <td class="g4fl-who">
                                                @if($lead->name)<b>{{ $lead->name }}</b>@endif
                                                @if($lead->email)<a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a><br>@endif
                                                @if($lead->phone)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $lead->phone) }}">{{ $lead->phone }}</a>@endif
                                            </td>
                                            <td class="g4fl-text">
                                                @if($lead->company)<div>{{ $lead->company }}</div>@endif
                                                @if($lead->message)
                                                    @if(mb_strlen($lead->message) > 140)
                                                        <details><summary>{{ \Illuminate\Support\Str::limit($lead->message, 120) }}</summary>{{ $lead->message }}</details>
                                                    @else
                                                        <div>{{ $lead->message }}</div>
                                                    @endif
                                                @endif
                                                @if(!$lead->company && !$lead->message)<span class="g4fl-muted">-</span>@endif
                                            </td>
                                            <td>
                                                @if($lead->list_title)
                                                    @if($lead->page_url)<a href="{{ $lead->page_url }}" target="_blank" rel="noopener">{{ $lead->list_title }} <i class="ti-new-window"></i></a>@else{{ $lead->list_title }}@endif
                                                @else<span class="g4fl-muted">-</span>@endif
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.form.leads.status', $lead->id) }}">
                                                    @csrf
                                                    <select name="status" @class(['form-control form-control-sm g4fl-status', 'is-new' => $lead->status === 'new']) onchange="this.form.submit()" aria-label="{{ __('Status') }}">
                                                        @foreach($statuses as $value => $label)
                                                            <option value="{{ $value }}" @selected($lead->status === $value)>{{ __($label) }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.form.leads.delete', $lead->id) }}" onsubmit="return confirm('{{ __('Delete this lead? This cannot be undone.') }}')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-danger" title="{{ __('Delete') }}"><i class="ti-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $leads->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
