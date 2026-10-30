{{-- "Don't see your role?", at the foot of every career page. The button
     writes to Job Page Settings' CV address, or opens the contact page. --}}
@php
    $cv_email = trim($career_text->get('cta_email'));
    $cv_href = filter_var($cv_email, FILTER_VALIDATE_EMAIL)
        ? 'mailto:' . $cv_email . '?subject=' . rawurlencode(__('Application') . ' – ' . ($cta_subject ?? __('Open application')))
        : route('frontend.contact');
@endphp
<section class="g4c-section">
    <div class="g4c-cta">
        <div>
            <h2>{{ $career_text->get('cta_title') }}</h2>
            <p>{{ $career_text->get('cta_text') }}</p>
        </div>
        <a class="g4b-btn" href="{{ $cv_href }}" data-cta="career_send_cv">{{ $career_text->get('cta_button') }}</a>
    </div>
</section>
