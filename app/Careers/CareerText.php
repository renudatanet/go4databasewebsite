<?php

namespace App\Careers;

/**
 * The career pages' wording in one language: what was set in Jobs > Job
 * Page Settings (career_page_{lang}_{key}), else the default here. The
 * admin screen shows these same defaults under each field, so the two can't
 * drift apart.
 */
final class CareerText
{
    public const DEFAULTS = [
        // Header of /career
        'badge' => 'We’re hiring',
        'heading' => 'Help build the future of',
        'heading_highlight' => 'B2B data',
        'subtitle' => 'Join the team behind Go4Database and help sales and marketing teams around the world reach the right people. {roles} open roles right now.',
        'search_placeholder' => 'Search roles, e.g. sales, SEO, intern',
        'all_label' => 'All roles',
        'category_heading' => '{category} roles',
        'category_text' => 'Open roles in our {category} team.',
        // Why join
        'perks_title' => 'Why people join us',
        'perks' => "Grow fast | A high-growth team where results, not titles, decide how fast you move up.\n"
            . "Earn on what you close | Attractive incentives on top of salary in every sales role.\n"
            . "Low hierarchy | Work directly with the founders and see your ideas go live.\n"
            . "Global customers | Work with sales and marketing teams across the US, UK and beyond.",
        // The roles
        'roles_title' => 'Open roles',
        'results_title' => 'Roles matching “{query}”',
        'empty_title' => 'No open roles right now',
        'empty_text' => 'New roles open often. Send us your CV and we’ll get in touch when one fits.',
        // Call to action, foot of every career page
        'cta_title' => 'Don’t see your role?',
        'cta_text' => 'We’re always glad to hear from great people. Send your CV and tell us what you’d like to work on.',
        'cta_button' => 'Send your CV',
        'cta_email' => '',
        // A role's page
        'related_title' => 'Other open roles',
        'closed_text' => 'Applications for this role have closed.',
        // The application form
        'apply_title' => 'Apply for {role}',
        'apply_intro' => 'Tell us a little about yourself. Fields marked * are required.',
        'apply_button' => 'Submit application',
        'steps_title' => 'What happens next',
        'steps' => "We read your CV and message\nA short call with our team\nAn interview with the hiring manager\nAn offer, if it’s a match",
        'privacy_note' => 'We only use your details to consider you for this role.',
    ];

    /** The role page's section and fact labels (Jobs > Job Single Page Settings), when left empty there. */
    public const LABELS = [
        'job_context_label' => 'About the role',
        'job_responsibility_label' => 'What you’ll do',
        'education_requirement_label' => 'Education',
        'experience_requirement_label' => 'Experience',
        'additional_requirement_label' => 'Good to know',
        'others_benefits_label' => 'Benefits',
        'apply_button_text' => 'Apply now',
        'job_info_text' => 'Role overview',
        'company_name_text' => 'Company',
        'job_category_text' => 'Team',
        'job_position_text' => 'Position',
        'job_type_text' => 'Job type',
        'salary_text' => 'Salary',
        'job_location_text' => 'Location',
        'job_deadline_text' => 'Apply by',
        'job_application_fee_text' => 'Application fee',
    ];

    public function __construct(private string $lang)
    {
    }

    /** @param array<string, string|int> $fill placeholders, e.g. ['{roles}' => 9] */
    public function get(string $key, array $fill = []): string
    {
        $value = trim((string) get_static_option('career_page_' . $this->lang . '_' . $key));

        return strtr($value !== '' ? $value : (self::DEFAULTS[$key] ?? ''), array_map('strval', $fill));
    }

    /** A many-line field as its non-empty lines; "none" hides what it lists. */
    public function lines(string $key): array
    {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->get($key)))));

        return count($lines) === 1 && strcasecmp($lines[0], 'none') === 0 ? [] : $lines;
    }

    /** "Title | text" lines as [title, text] pairs. */
    public function pairs(string $key): array
    {
        return array_map(function (string $line) {
            [$title, $text] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');

            return [$title, $text];
        }, $this->lines($key));
    }

    public function label(string $key): string
    {
        $value = trim((string) get_static_option('job_single_page_' . $this->lang . '_' . $key));

        return $value !== '' ? $value : (self::LABELS[$key] ?? '');
    }
}
