@extends('frontend.frontend-page-master')
{{-- The application form (/apply/{id}), from FrontendController::jobs_apply,
     posting to JobPaymentController::store_jobs_applicant_data as before.
     Its extra fields are the Form Builder's Apply Job Form
     (apply_job_page_form_fields), drawn here with labels; their names,
     required flags and file types are the Form Builder's, so the server's
     checks are unchanged. Wording: Jobs > Job Page Settings. --}}
@php
    use App\Careers\Icon;
    $career_text = new \App\Careers\CareerText($user_select_lang_slug);
    $page_name = get_static_option('career_with_us_page_' . $user_select_lang_slug . '_name') ?: __('Careers');
    $title = $career_text->get('apply_title', ['{role}' => $role->title]);
    $open = $role->isOpen();
    $closes = $role->closesAt();
    $sent = session('type') === 'success' && session('msg');

    // The Form Builder's fields, as [type, name, label, required, accept, options].
    $form = json_decode((string) get_static_option('apply_job_page_form_fields'));
    $fields = [];
    $select_index = 0;
    foreach ((array) optional($form)->field_type as $i => $type) {
        if (empty($type)) { continue; }
        $mimes = (string) (((array) optional($form)->mimes_type)[$i] ?? '');
        $fields[] = [
            'type' => $type,
            'name' => $form->field_name[$i] ?? 'field_' . $i,
            'label' => __($form->field_placeholder[$i] ?? ''),
            'required' => !empty(((array) optional($form)->field_required)[$i] ?? null),
            'accept' => $mimes !== '' ? '.' . implode(',.', array_filter(array_map('trim', explode(',', str_replace('mimes:', '', $mimes))))) : '',
            'types' => strtoupper(str_replace(',', ', ', str_replace('mimes:', '', $mimes))),
            'options' => $type === 'select' ? array_filter(array_map('trim', explode("\n", (string) ($form->select_options[$select_index++] ?? '')))) : [],
        ];
    }
@endphp
@section('site-title')
    {{ $title }}
@endsection
@section('page-title')
    {{ $title }}
@endsection
@section('no-breadcrumb', '1')
@section('page-meta-data')
    <meta name="robots" content="noindex, follow">
@endsection
@section('career-schema', '')

@section('content')
@include('frontend.pages.blog.partials.style')
@include('frontend.pages.jobs.partials.style')

<div class="g4b g4c">
    <section class="g4b-hero g4c-hero">
        <div class="g4b-wrap">
            <nav class="g4b-crumbs" aria-label="Breadcrumb">
                <ol>
                    <li><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('frontend.jobs') }}">{{ $page_name }}</a></li>
                    <li><a href="{{ route('frontend.jobs.single', $role->slug) }}">{{ $role->title }}</a></li>
                    <li aria-current="page">{{ __('Apply') }}</li>
                </ol>
            </nav>
            <div class="g4b-hero-copy">
                <h1 class="g4b-title">{{ $title }}</h1>
                <p class="g4b-lead">{{ $career_text->get('apply_intro') }}</p>
            </div>
        </div>
    </section>

    <div class="g4b-wrap g4c-main">
        <div class="g4c-layout">
            <div class="g4c-card">
                @if($sent)
                    <div class="g4c-alert g4c-alert--ok" role="status">
                        {!! Icon::svg('check') !!}
                        <div>
                            <strong>{{ session('msg') }}</strong>
                            <p class="mb-0 mt-1">{{ __('We’ll be in touch by email.') }}</p>
                        </div>
                    </div>
                    <a class="g4b-btn g4b-btn--ghost" href="{{ route('frontend.jobs') }}">{{ __('See other open roles') }}</a>
                @elseif(!$open)
                    <p class="g4c-closed" role="status">{!! Icon::svg('alert') !!}{{ $career_text->get('closed_text') }}</p>
                    <a class="g4b-btn g4b-btn--ghost mt-3" href="{{ route('frontend.jobs') }}">{{ __('See open roles') }}</a>
                @else
                    @if($errors->any() || session('type') === 'danger')
                        <div class="g4c-alert g4c-alert--error" role="alert">
                            <ul>
                                @foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach
                                @if(session('type') === 'danger' && session('msg'))<li>{{ session('msg') }}</li>@endif
                            </ul>
                        </div>
                    @endif
                    <form class="g4c-form" action="{{ route('frontend.jobs.apply.store') }}" method="post" enctype="multipart/form-data" data-job="{{ $role->slug }}">
                        @csrf
                        <input type="hidden" name="job_id" value="{{ $role->id }}">
                        <div class="form-group">
                            <label class="g4c-label" for="g4c-name">{{ __('Full name') }} <em aria-hidden="true">*</em></label>
                            <input class="g4c-input" id="g4c-name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>
                        </div>
                        <div class="form-group">
                            <label class="g4c-label" for="g4c-email">{{ __('Email') }} <em aria-hidden="true">*</em></label>
                            <input class="g4c-input" id="g4c-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                        </div>

                        @foreach($fields as $field)
                            @php $id = 'g4c-f-' . $loop->index; $star = $field['required'] ? '<em aria-hidden="true">*</em>' : ''; @endphp
                            <div class="form-group">
                                @switch($field['type'])
                                    @case('file')
                                        <span class="g4c-label" id="{{ $id }}-label">{{ $field['label'] ?: __('Attachment') }} {!! $star !!}</span>
                                        <label class="g4c-drop" for="{{ $id }}">
                                            <input type="file" id="{{ $id }}" name="{{ $field['name'] }}" @if($field['accept']) accept="{{ $field['accept'] }}" @endif @if($field['required']) required @endif aria-labelledby="{{ $id }}-label">
                                            <span class="g4c-drop-icon">{!! Icon::svg('upload') !!}</span>
                                            <span class="g4c-drop-text">
                                                <strong>{{ __('Choose a file or drop it here') }}</strong>
                                                @if($field['types'])<span>{{ $field['types'] }}</span>@endif
                                            </span>
                                        </label>
                                        @break
                                    @case('textarea')
                                        <label class="g4c-label" for="{{ $id }}">{{ $field['label'] }} {!! $star !!}</label>
                                        <textarea class="g4c-input" id="{{ $id }}" name="{{ $field['name'] }}" rows="6" @if($field['required']) required @endif>{{ old($field['name']) }}</textarea>
                                        @break
                                    @case('select')
                                        <label class="g4c-label" for="{{ $id }}">{{ $field['label'] }} {!! $star !!}</label>
                                        <select class="g4c-input" id="{{ $id }}" name="{{ $field['name'] }}" @if($field['required']) required @endif>
                                            @foreach($field['options'] as $option)
                                                <option value="{{ \Illuminate\Support\Str::slug($option) }}" @if(old($field['name']) === \Illuminate\Support\Str::slug($option)) selected @endif>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        @break
                                    @case('checkbox')
                                        <label class="g4c-check" for="{{ $id }}">
                                            <input type="checkbox" id="{{ $id }}" name="{{ $field['name'] }}" @if(old($field['name'])) checked @endif @if($field['required']) required @endif>
                                            <span>{{ $field['label'] }} {!! $star !!}</span>
                                        </label>
                                        @break
                                    @default
                                        <label class="g4c-label" for="{{ $id }}">{{ $field['label'] }} {!! $star !!}</label>
                                        <input class="g4c-input" id="{{ $id }}" type="{{ in_array($field['type'], ['email', 'tel', 'date', 'url'], true) ? $field['type'] : 'text' }}" name="{{ $field['name'] }}" value="{{ old($field['name']) }}" @if($field['required']) required @endif>
                                @endswitch
                            </div>
                        @endforeach

                        @if($role->hasApplicationFee())
                            <input type="hidden" name="application_fee" value="{{ $role->application_fee }}">
                            <p class="g4c-label">{{ $career_text->label('job_application_fee_text') }}: {{ amount_with_currency_symbol($role->application_fee) }}</p>
                            {!! render_payment_gateway_for_form() !!}
                            @if(!empty(get_static_option('manual_payment_gateway')))
                                <div class="form-group manual_payment_transaction_field @if(get_static_option('site_default_payment_gateway') === 'manual_payment') d-block @else d-none @endif">
                                    <span class="g4c-label">{{ __('Attach your bank document') }}</span>
                                    <input class="g4c-input" type="file" name="manual_payment_attachment">
                                    <span class="g4c-note d-block text-left">{!! get_manual_payment_description() !!}</span>
                                </div>
                            @endif
                        @endif

                        <button class="g4b-btn" type="submit" data-busy="{{ __('Sending…') }}">{{ $career_text->get('apply_button') }}</button>
                        @if($career_text->get('privacy_note') !== '')<p class="g4c-note">{{ $career_text->get('privacy_note') }}</p>@endif
                    </form>
                @endif
            </div>

            <aside class="g4c-aside" aria-label="{{ __('About the role') }}">
                <div class="g4c-card">
                    @if($role_category)<span class="g4c-role-team">{{ $role_category->title }}</span>@endif
                    <h2 class="g4c-mini-title"><a href="{{ route('frontend.jobs.single', $role->slug) }}">{{ $role->title }}</a></h2>
                    <ul class="g4c-facts">
                        @if(trim((string) $role->job_location) !== '')<li>{!! Icon::svg('pin') !!}<span>{{ $role->job_location }}</span></li>@endif
                        @if($role->employment_status)<li>{!! Icon::svg('clock') !!}<span>{{ $role->typeLabel() }}</span></li>@endif
                        @if(trim((string) $role->salary) !== '')<li>{!! Icon::svg('wallet') !!}<span>{{ $role->salary }}</span></li>@endif
                        @if($closes && $open)<li>{!! Icon::svg('calendar') !!}<span>{{ $career_text->label('job_deadline_text') }} {{ $closes->format('j M Y') }}</span></li>@endif
                    </ul>
                    <a class="g4c-back" href="{{ route('frontend.jobs.single', $role->slug) }}">{!! Icon::svg('back') !!} {{ __('Read the role') }}</a>
                </div>
                @if(count($career_text->lines('steps')))
                    <div class="g4c-card">
                        <h3>{{ $career_text->get('steps_title') }}</h3>
                        <ol class="g4c-steps">
                            @foreach($career_text->lines('steps') as $step)<li>{{ $step }}</li>@endforeach
                        </ol>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    @if($role->hasApplicationFee())
    <script>
        // The payment gateway picker, for roles with an application fee. Plain
        // script: jQuery loads deferred, after this runs.
        document.addEventListener('click', function (e) {
            var item = e.target.closest('.payment-gateway-wrapper > ul > li');
            if (!item) { return; }
            e.preventDefault();
            var gateway = item.getAttribute('data-gateway');
            item.parentNode.querySelectorAll('li').forEach(function (li) { li.classList.toggle('selected', li === item); });
            document.querySelectorAll('input[name="selected_payment_gateway"], .payment-gateway-wrapper input').forEach(function (input) { input.value = gateway; });
            document.querySelectorAll('.manual_payment_transaction_field').forEach(function (field) {
                field.classList.toggle('d-block', gateway === 'manual_payment');
                field.classList.toggle('d-none', gateway !== 'manual_payment');
            });
        });
    </script>
    @endif
@endsection
