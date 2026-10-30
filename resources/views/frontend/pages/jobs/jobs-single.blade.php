@extends('frontend.frontend-page-master')
{{-- A role's page (/career/{slug}), from FrontendController::jobs_single.
     Everything is the role's own (Jobs in the admin); the section and fact
     labels are Job Single Page Settings', the other wording Job Page
     Settings' (App\Careers\CareerText, with defaults). Its sections are
     cleaned of pasted-in styling at render (App\Careers\JobContent). --}}
@php
    use App\Careers\Icon;
    use App\Careers\JobContent;
    $career_text = new \App\Careers\CareerText($user_select_lang_slug);
    $page_name = get_static_option('career_with_us_page_' . $user_select_lang_slug . '_name') ?: __('Careers');

    $sections = [];
    foreach ([
        'job_context' => 'job_context_label',
        'job_responsibility' => 'job_responsibility_label',
        'education_requirement' => 'education_requirement_label',
        'experience_requirement' => 'experience_requirement_label',
        'additional_requirement' => 'additional_requirement_label',
        'other_benefits' => 'others_benefits_label',
    ] as $field => $label) {
        $html = JobContent::clean($job->$field);
        if ($html !== '') {
            // Two sections can share a label in the admin; keep both.
            $key = $career_text->label($label);
            $sections[array_key_exists($key, $sections) ? $key . ' ' : $key] = $html;
        }
    }

    $open = $job->isOpen();
    $closes = $job->closesAt();
    $days_left = $closes ? (int) now()->startOfDay()->diffInDays($closes, false) : null;
    $apply_on_site = !empty(get_static_option('job_single_page_apply_form'));
    // The site's own form when Job Single Page Settings turns it on, else an
    // email to the role's address, else the contact page.
    $apply_url = $apply_on_site
        ? route('frontend.jobs.apply', $job->id)
        : (filter_var(trim((string) $job->email), FILTER_VALIDATE_EMAIL)
            ? 'mailto:' . trim($job->email) . '?subject=' . rawurlencode(__('Application') . ' – ' . $job->title)
            : route('frontend.contact'));
    $apply_text = $career_text->label('apply_button_text');
    $share_url = route('frontend.jobs.single', $job->slug);
    $summary = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags(reset($sections) ?: ''), ENT_QUOTES, 'UTF-8'))), 158);
    $cta_subject = $job->title;
@endphp
@section('site-title')
    {{ $job->title }}
@endsection
@section('page-title')
    {{ $job->title }}
@endsection
@section('no-breadcrumb', '1')
@section('page-meta-data')
    <meta name="description" content="{{ $job->meta_description ?: $summary }}">
    @if($job->meta_tags)<meta name="tags" content="{{ $job->meta_tags }}">@endif
    @if(!$open || $job->status !== 'publish')<meta name="robots" content="noindex, follow">@endif
@endsection
@section('og-meta')
    <meta property="og:url" content="{{ $share_url }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $job->title }}" />
    <meta property="og:description" content="{{ $job->meta_description ?: $summary }}" />
@endsection
@section('career-schema')
    <script type="application/ld+json">{!! json_encode((new \App\Careers\JobSchema($job_board))->role($job, $page_name, $sections), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endsection

@section('content')
@include('frontend.pages.blog.partials.style')
@include('frontend.pages.jobs.partials.style')

<div class="g4b g4c g4c-role-page">
    <section class="g4b-hero g4c-hero">
        <div class="g4b-wrap">
            <nav class="g4b-crumbs" aria-label="Breadcrumb">
                <ol>
                    <li><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('frontend.jobs') }}">{{ $page_name }}</a></li>
                    @if($role_category)<li><a href="{{ route('frontend.jobs.single', $role_category->urlSlug()) }}">{{ $role_category->title }}</a></li>@endif
                    <li aria-current="page">{{ $job->title }}</li>
                </ol>
            </nav>

            <div class="g4b-hero-copy">
                <div class="g4c-tags">
                    @if($role_category)<a class="g4c-tag" href="{{ route('frontend.jobs.single', $role_category->urlSlug()) }}">{!! Icon::svg('team') !!}{{ $role_category->title }}</a>@endif
                    @if($job->employment_status)<span class="g4c-tag g4c-tag--muted">{{ $job->typeLabel() }}</span>@endif
                    @if(!$open)<span class="g4c-tag g4c-tag--closed">{{ __('Closed') }}</span>@endif
                    @if($job->status !== 'publish')<span class="g4c-tag g4c-tag--closed">{{ __('Draft: only admins can see this page') }}</span>@endif
                </div>
                <h1 class="g4b-title">{{ $job->title }}</h1>
                <ul class="g4c-facts">
                    @if(trim((string) $job->job_location) !== '')<li>{!! Icon::svg('pin') !!}<span>{{ $job->job_location }}</span></li>@endif
                    @if(trim((string) $job->salary) !== '')<li>{!! Icon::svg('wallet') !!}<span>{{ $job->salary }}</span></li>@endif
                    @if($closes && $open)<li>{!! Icon::svg('calendar') !!}<span>{{ $career_text->label('job_deadline_text') }} {{ $closes->format('j M Y') }}</span></li>@endif
                </ul>

                @if($open)
                    <div class="g4c-actions" data-apply-anchor>
                        <a class="g4b-btn" href="{{ $apply_url }}" data-cta="job_apply_top">{{ $apply_text }}</a>
                        <button type="button" class="g4c-share" data-url="{{ $share_url }}" data-title="{{ $job->title }}">{!! Icon::svg('share') !!}<span>{{ __('Share role') }}</span></button>
                    </div>
                @else
                    <p class="g4c-closed" role="status">{!! Icon::svg('alert') !!}{{ $career_text->get('closed_text') }}</p>
                @endif
            </div>
        </div>
    </section>

    <div class="g4b-wrap g4c-main">
        <div class="g4c-layout">
            <article class="g4c-prose">
                @forelse($sections as $label => $html)
                    <section>
                        <h2>{{ trim($label) }}</h2>
                        {!! $html !!}
                    </section>
                @empty
                    <p>{{ __('The full description of this role is coming soon.') }}</p>
                @endforelse
            </article>

            <aside class="g4c-aside" aria-label="{{ $career_text->label('job_info_text') }}">
                <div class="g4c-card">
                    <h2>{{ $career_text->label('job_info_text') }}</h2>
                    <dl class="g4c-overview">
                        @foreach([
                            ['building', 'company_name_text', $job->company_name],
                            ['team', 'job_category_text', $role_category ? $role_category->title : null],
                            ['user', 'job_position_text', $job->position],
                            ['clock', 'job_type_text', $job->employment_status ? $job->typeLabel() : null],
                            ['wallet', 'salary_text', $job->salary],
                            ['pin', 'job_location_text', $job->job_location],
                            ['calendar', 'job_deadline_text', $closes ? $closes->format('j M Y') : null],
                            ['fee', 'job_application_fee_text', $job->hasApplicationFee() ? amount_with_currency_symbol($job->application_fee) : null],
                        ] as [$icon, $label, $value])
                            @if(trim((string) $value) !== '')
                                <div>
                                    <dt>{!! Icon::svg($icon) !!}<span>{{ $career_text->label($label) }}</span></dt>
                                    <dd class="g4c-k" aria-hidden="true">{{ $career_text->label($label) }}</dd>
                                    <dd>
                                        @if($icon === 'team' && $role_category)<a href="{{ route('frontend.jobs.single', $role_category->urlSlug()) }}">{{ $value }}</a>@else{{ $value }}@endif
                                    </dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                    @if($open)
                        <a class="g4b-btn" href="{{ $apply_url }}" data-cta="job_apply_card" data-apply-anchor>{{ $apply_text }}</a>
                        @if($days_left !== null)
                            <p class="g4c-note">{{ $days_left <= 0 ? __('Applications close today') : __('Applications close in') . ' ' . $days_left . ' ' . ($days_left === 1 ? __('day') : __('days')) }}</p>
                        @endif
                    @else
                        <a class="g4b-btn g4b-btn--ghost" href="{{ route('frontend.jobs') }}">{{ __('See open roles') }}</a>
                    @endif
                </div>
            </aside>
        </div>

        @if(count($related_jobs))
            <section class="g4c-section" aria-labelledby="g4c-related-title">
                <div class="g4c-head">
                    <h2 id="g4c-related-title">{{ $career_text->get('related_title') }}</h2>
                    <a class="g4c-back" href="{{ route('frontend.jobs') }}">{{ __('All open roles') }} {!! Icon::svg('arrow') !!}</a>
                </div>
                <ul class="g4c-roles">
                    @foreach($related_jobs as $related)
                        @include('frontend.pages.jobs.partials.role-card', ['job' => $related])
                    @endforeach
                </ul>
            </section>
        @endif

        @include('frontend.pages.jobs.partials.cta')
    </div>

    @if($open)
        <div class="g4c-applybar" aria-hidden="true">
            <span class="g4c-applybar-title">{{ $job->title }}</span>
            <a class="g4b-btn" href="{{ $apply_url }}" tabindex="-1" data-cta="job_apply_bar">{{ $apply_text }}</a>
        </div>
    @endif
</div>
@endsection
