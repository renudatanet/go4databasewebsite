<?php

namespace App\HomePage;

/**
 * How Page Manage > Home > Homepage content shows HomePageText's fields:
 * one card per section of the page, top to bottom, each field as
 * [label, type, help]. Types: input, textarea, lines (one item per line),
 * url, image (a media library pick) and switch (show or hide the section).
 * The screen and its validation both read this, so they can't drift apart;
 * every key here is one of HomePageText::DEFAULTS.
 */
final class HomePageFields
{
    private const LIMITS = ['input' => 300, 'url' => 500, 'textarea' => 2000, 'lines' => 8000, 'image' => 20, 'switch' => 1];

    /** @return array<string, array<string, array{0: string, 1: string, 2: string}>> section => key => [label, type, help] */
    public static function sections(): array
    {
        $card = fn (int $n, string $where) => [
            "card{$n}_label" => ['Label', 'input', ''],
            "card{$n}_title" => ['Heading', 'input', ''],
            "card{$n}_text" => ['Text', 'textarea', ''],
            "card{$n}_note" => ['Highlight line', 'input', ''],
            "card{$n}_button" => ['Button', 'input', ''],
            "card{$n}_button_url" => ['Button link', 'url', ''],
            "card{$n}_button2" => ['Second button', 'input', ''],
            "card{$n}_button2_url" => ['Second button link', 'url', ''],
            "card{$n}_image" => ['Screenshot', 'image', "Empty: the built-in {$where} screenshot."],
        ];

        return [
            'Hero (top banner)' => [
                'hero_title' => ['Heading', 'input', 'The first line of the big heading.'],
                'hero_words' => ['Rotating words', 'lines', 'One per line; they type themselves out after the heading in turn.'],
                'hero_button' => ['Main button', 'input', ''],
                'hero_button_url' => ['Main button link', 'url', ''],
                'hero_button2' => ['Second button', 'input', ''],
                'hero_button2_url' => ['Second button link', 'url', '#contact scrolls to the form at the foot of the page.'],
                'hero_note' => ['Small line under the buttons', 'input', ''],
                'show_hero_cards' => ['Show the floating contact cards', 'switch', ''],
                'hero_cards' => ['Floating contact cards', 'lines', 'One per line: Name | Job title • Company. The first four are shown.'],
            ],
            'Search card' => [
                'search_tabs' => ['Tabs', 'lines', 'One per line: Tab name | job titles it searches (comma separated). The first is selected when the page opens.'],
                'search_title_placeholder' => ['Title box placeholder', 'input', ''],
                'search_industry_placeholder' => ['Industry box placeholder', 'input', ''],
                'search_location_placeholder' => ['Location box placeholder', 'input', ''],
                'search_button' => ['Button', 'input', ''],
                'search_button_url' => ['Button link', 'url', ''],
            ],
            'Numbers' => [
                'show_stats' => ['Show this section', 'switch', ''],
                'stats_title' => ['Heading', 'input', ''],
                'stats' => ['Numbers', 'lines', 'One per line: Number | What it counts.'],
            ],
            'Logo strip' => [
                'show_logos' => ['Show this section', 'switch', ''],
                'logos_title' => ['Heading', 'input', ''],
                'logos' => ['Logos', 'lines', 'Leave empty to keep the built-in logos. Or one per line: Name | image address (from the media library, e.g. /assets/uploads/media-uploader/logo.png); a name without an image is shown as text.'],
            ],
            'Free credits offer' => [
                'show_promo' => ['Show this section', 'switch', ''],
                'promo_badge' => ['Badge', 'input', ''],
                'promo_title' => ['Heading', 'input', 'Put *stars* around the words to show in green.'],
                'promo_text' => ['Text', 'textarea', ''],
                'promo_points' => ['Check list', 'lines', 'One per line.'],
                'promo_button' => ['Button', 'input', ''],
                'promo_button_url' => ['Button link', 'url', ''],
                'promo_image' => ['Image', 'image', 'Empty: the built-in picture.'],
            ],
            'The smarter B2B contact database' => [
                'show_ai' => ['Show this section', 'switch', ''],
                'ai_title' => ['Heading', 'input', 'Put *stars* around the words to show in the green gradient.'],
                'ai_text' => ['Text', 'textarea', ''],
                'ai_cards' => ['Cards', 'lines', 'One per line: Small label | Big word. The big word\'s first letter goes in the badge.'],
            ],
            'Features' => [
                'show_features' => ['Show this section', 'switch', ''],
                'features_title' => ['Heading', 'input', ''],
                'features_text' => ['Text', 'textarea', ''],
                'features' => ['Feature cards', 'lines', 'One per line: Title | Description. The icons follow in the same order.'],
            ],
            'Comparison' => [
                'show_compare' => ['Show this section', 'switch', ''],
                'compare_title' => ['Heading', 'input', ''],
                'compare_text' => ['Text', 'textarea', ''],
                'compare_us_title' => ['Left column heading', 'input', ''],
                'compare_us_badge' => ['Left column badge', 'input', ''],
                'compare_us' => ['Left column points', 'lines', 'One per line (shown with a tick).'],
                'compare_them_title' => ['Right column heading', 'input', ''],
                'compare_them_badge' => ['Right column badge', 'input', ''],
                'compare_them' => ['Right column points', 'lines', 'One per line (shown with a cross).'],
            ],
            'Platform capabilities' => [
                'show_integrations' => ['Show this section', 'switch', ''],
                'integ_badge' => ['Badge', 'input', ''],
                'integ_title' => ['Heading', 'input', ''],
                'integ_text' => ['Text', 'textarea', ''],
            ],
            'Platform capabilities: card 1 (pink)' => $card(1, 'leads table'),
            'Platform capabilities: card 2 (purple)' => $card(2, 'filters'),
            'Platform capabilities: card 3 (blue)' => $card(3, 'AI lead finder'),
            'Testimonials' => [
                'show_reviews' => ['Show this section', 'switch', 'The quotes themselves are the Testimonial records (Testimonial in the sidebar).'],
                'reviews_title' => ['Heading', 'input', ''],
                'reviews_text' => ['Text', 'textarea', ''],
            ],
            'Most active users mailing lists' => [
                'show_lists' => ['Show this section', 'switch', 'The cards are the B2B records and their categories (B2B in the sidebar).'],
                'lists_title' => ['Heading', 'input', ''],
                'lists_text' => ['Text', 'textarea', ''],
                'lists_all_tab' => ['First tab', 'input', ''],
                'lists_button' => ['Button', 'input', ''],
            ],
            'FAQ' => [
                'show_faq' => ['Show this section', 'switch', ''],
                'faq_badge' => ['Badge', 'input', ''],
                'faq_title' => ['Heading', 'input', ''],
                'faq_text' => ['Text', 'textarea', ''],
                'faqs' => ['Questions', 'lines', 'One per line: Question | Answer. The first opens by default.'],
                'faq_help_title' => ['Help box heading', 'input', ''],
                'faq_help_text' => ['Help box text', 'textarea', ''],
                'faq_help_button' => ['Help box button', 'input', ''],
                'faq_help_url' => ['Help box button link', 'url', ''],
            ],
            'Closing banner' => [
                'show_cta' => ['Show this section', 'switch', ''],
                'cta_title' => ['Heading', 'input', ''],
                'cta_text' => ['Text', 'input', ''],
                'cta_button' => ['Main button', 'input', ''],
                'cta_button_url' => ['Main button link', 'url', ''],
                'cta_button2' => ['Second button', 'input', ''],
                'cta_button2_url' => ['Second button link', 'url', ''],
            ],
            'Contact form (bottom of the page)' => [
                'show_contact' => ['Show this section', 'switch', 'Sent forms are saved under List > Form Leads ("Homepage: talk to an expert").'],
                'contact_title' => ['Left heading', 'input', ''],
                'contact_partners_title' => ['Partners heading', 'input', ''],
                'contact_partners' => ['Partner logos', 'lines', 'Leave empty to keep the built-in partner logos. Or one per line: Name | image address (optional).'],
                'contact_form_title' => ['Form heading', 'input', ''],
                'contact_email_label' => ['Email box label', 'input', ''],
                'contact_name_label' => ['Name box label', 'input', ''],
                'contact_phone_label' => ['Phone box label', 'input', ''],
                'contact_button' => ['Button', 'input', ''],
                'contact_success' => ['Message after sending', 'input', ''],
            ],
        ];
    }

    /** @return array<string, string> key => type, for every field */
    public static function types(): array
    {
        $types = [];
        foreach (self::sections() as $fields) {
            foreach ($fields as $key => [, $type]) {
                $types[$key] = $type;
            }
        }

        return $types;
    }

    /** The validation rule for a field of $type. */
    public static function rule(string $type): string
    {
        return match ($type) {
            'image' => 'nullable|integer',
            'switch' => 'nullable|in:0,1',
            default => 'nullable|string|max:' . self::LIMITS[$type],
        };
    }
}
