<?php

namespace App\HomePage;

use App\Careers\PageText;

/**
 * The homepage's wording, links and lists, from Page Manage > Home >
 * Homepage content (home_page_text_{lang}_{key}), else the default here,
 * which is the page exactly as it was built. A list field is one item per
 * line ("Title | text" where an item has two parts); a show_* switch hides
 * its section when saved as "0".
 */
final class HomePageText extends PageText
{
    protected const PREFIX = 'home_page_text_';

    private const REGISTER = 'https://app.go4database.com/register?utm_source=HomePage&utm_medium=Internal&utm_campaign=';

    public const DEFAULTS = [
        // Hero
        'show_hero_cards' => '1',
        'hero_cards' => "Laura Trent | CTO • Nexus AI\nJames Roberts | CEO • TechCorp Inc\nSarah Mitchell | CMO • GrowthBridge\nMarcus Vance | VP Sales • DataFlow",
        'hero_title' => 'Supercharge Your Sales with',
        'hero_words' => "AI Lead Generation\nHigh-Intent B2B Sales Leads\nReal-Time Verified B2B Emails\nExecutive Decision Makers\nAI-Powered B2B Prospecting",
        'hero_button' => 'Get 1200 Free Credits',
        'hero_button_url' => self::REGISTER . 'hero_free_credits',
        'hero_button2' => 'Book a Live Demo',
        'hero_button2_url' => '#contact',
        'hero_note' => 'No Credit Card Required',
        // Search card
        'search_tabs' => "CEO / C-Level List | CEO\nVP & Director List | VP, Director\nIT & Tech Leaders | CTO, IT Head\nHealthcare Executives | Healthcare Executive\nSales & Marketing Heads | VP Sales, CMO\nFinancial Decision Makers | CFO",
        'search_title_placeholder' => 'Title',
        'search_industry_placeholder' => 'Industry / Business',
        'search_location_placeholder' => 'Location',
        'search_button' => 'Download',
        'search_button_url' => self::REGISTER . 'search_download',
        // Numbers
        'show_stats' => '1',
        'stats_title' => 'GLOBAL B2B DATA REACH & SCALE',
        'stats' => "400Mn | Global Business Contacts\n130Mn | Verified Mobile No\n22629+ | B2B Contracts Signed\n845 | B2B Category List",
        // Logo strip
        'show_logos' => '1',
        'logos_title' => 'TRUSTED BY 4,000+ HIGH-GROWTH COMPANIES',
        'logos' => '',
        // Free credits offer
        'show_promo' => '1',
        'promo_badge' => '🔥 EXCLUSIVE WELCOME OFFER — LIMITED TIME',
        'promo_title' => 'Get *1200 Free Credits* to Test Real-Time Data',
        'promo_text' => 'Experience the unmatched accuracy of Go4Database risk free. Instantly access 1200 verified B2B emails, direct mobile numbers, and intent signals with zero credit card required.',
        'promo_points' => "1200 Free Credits Instantly\n95%+ Accuracy Guarantee\nZero Credit Card Required\nInstant Export to CRM & CSV",
        'promo_button' => 'Claim Your 1200 Free Credits',
        'promo_button_url' => self::REGISTER . 'mid_free_credits',
        'promo_image' => '',
        // The smarter B2B contact database
        'show_ai' => '1',
        'ai_title' => 'The Smarter *B2B Contact Database* for Outbound Sales',
        'ai_text' => 'Filter 400M+ decision-makers with live real-time validation, deep intent data, and effortless CRM exports.',
        'ai_cards' => "Active Email List | Active\nSpecific Email List | Specific\nEffortless Email List | Effortless",
        // Features
        'show_features' => '1',
        'features_title' => 'High-Quality Data Drives Better Outreach Results',
        'features_text' => 'Why leading sales, growth, and marketing teams choose Go4Database to power their outbound pipeline',
        'features' => "95%+ Data Accuracy | Real-time email verification ensures you never waste time or damage domain reputation on bounced emails.\n"
            . "50+ Search Filters | Filter decision makers by job title, industry, company size, revenue, location, and technographics.\n"
            . "Live Verification | Every contact email is verified live at the moment of download. No stale static database lists.\n"
            . "Buyer Intent Signals | Identify prospects actively searching for solutions like yours right now before competitors reach them.\n"
            . "16,000+ Technographics | Target companies based on the exact software, CRM, or cloud infrastructure tools they currently run.\n"
            . "Friendly Refund Credits | We automatically credit back any invalid contact or email bounce. You only pay for verified leads.\n"
            . "Automated Email Outreach | Launch personalized email sequences directly to verified decision makers with built-in deliverability protection.\n"
            . "Instant CRM Sync | 1 click sync to HubSpot, Salesforce, Outreach, Lemlist, and custom webhook CSV exports.",
        // Comparison
        'show_compare' => '1',
        'compare_title' => 'Why Sales Teams Switch to Us',
        'compare_text' => 'Compare what you get with Go4Database versus traditional lead providers',
        'compare_us_title' => 'With Go4Database',
        'compare_us_badge' => '95%+ ACCURACY',
        'compare_us' => "Real-time email verification live on export\nAutomatic credit refund for invalid records\nVerified direct-dial mobile numbers\nReal-time buyer intent data & technographics\n1 click CRM & sales tool integrations",
        'compare_them_title' => 'With Others',
        'compare_them_badge' => '80-90% ACCURACY',
        'compare_them' => "Static database updated once every 3-6 months\nNo refunds for email bounces or bad phone numbers\nOutdated company switchboard numbers\nNo intent signal tracking or tech stack filters\nManual CSV exports requiring cleaning",
        // Platform capabilities (three cards)
        'show_integrations' => '1',
        'integ_badge' => 'PLATFORM CAPABILITIES',
        'integ_title' => 'Everything You Need to Find Your Next Customer',
        'integ_text' => 'From B2B data and advanced search to AI-powered ICP targeting.',
        'card1_label' => 'AI LEAD GENERATION TOOL',
        'card1_title' => 'Get data from the most complete B2B Marketplace',
        'card1_text' => 'Access 400M+ decision makers with 50+ granular search filters. Filter by job title, company revenue, location, and real-time verified email status.',
        'card1_note' => '95%+ Data Accuracy Guarantee with instant live verification',
        'card1_button' => 'Start Free Trial',
        'card1_button_url' => self::REGISTER . 'card_database',
        'card1_button2' => 'Explore Database',
        'card1_button2_url' => '#search-section',
        'card1_image' => '',
        'card2_label' => 'ADVANCED SEARCH & FILTERS',
        'card2_title' => 'Advanced filter section for targeted search',
        'card2_text' => 'Filter decision makers across 50+ granular search criteria including job title, industry, company turnover, location, technographics, and verified contact status for high-converting sales leads.',
        'card2_note' => 'Pinpoint your exact ideal target buyers with 50+ real-time search filters',
        'card2_button' => 'Start Free Trial',
        'card2_button_url' => self::REGISTER . 'card_filters',
        'card2_button2' => 'Explore Advanced Filters',
        'card2_button2_url' => '#search-section',
        'card2_image' => '',
        'card3_label' => 'AI LEAD GENERATION SOFTWARE',
        'card3_title' => 'AI search with website URL & location to generate your ICP',
        'card3_text' => 'Input your company website URL and target location. Our AI engine automatically analyzes your domain to generate your exact Ideal Customer Profile (ICP) and pre-qualify lead lists.',
        'card3_note' => 'Generate high-converting ICP lead lists directly from website URL & location',
        'card3_button' => 'Start Free Trial',
        'card3_button_url' => self::REGISTER . 'card_ai_icp',
        'card3_button2' => 'Generate AI ICP',
        'card3_button2_url' => '#contact',
        'card3_image' => '',
        // Testimonials (the quotes are Testimonial records)
        'show_reviews' => '1',
        'reviews_title' => 'Loved by Outbound Teams Everywhere',
        'reviews_text' => 'See how top revenue teams use Go4Database to scale predictable pipeline with 95%+ accurate B2B data.',
        // Most active users mailing lists (the cards are B2B records)
        'show_lists' => '1',
        'lists_title' => 'Most Active Users Mailing List',
        'lists_text' => 'High-converting, 95%+ accurate verified contact directories curated for outbound sales and marketing teams.',
        'lists_all_tab' => 'Hot List',
        'lists_button' => 'Know More',
        // FAQ
        'show_faq' => '1',
        'faq_badge' => 'FREQUENTLY ASKED QUESTIONS',
        'faq_title' => "Have Questions? We've Got Answers.",
        'faq_text' => 'Everything you need to know about Go4Database real-time data accuracy, credits, integrations, and compliance.',
        'faqs' => "How accurate is Go4Database B2B Contact Database? | We guarantee 95%+ data accuracy. Unlike traditional providers that supply stale static lists, Go4Database performs real-time email verification live at the moment of download so you never send emails to dead addresses.\n"
            . "How does real-time email verification work? | When you click export, our system pings the target mail server in real time to verify mailbox existence, catch-all status, and spam traps. If an email address is invalid, it is filtered out and your credit is preserved.\n"
            . "What happens if an email bounces or data is inaccurate? | We offer a 100% Friendly Credit refund policy. Any bounce or invalid contact reported gets automatically refunded back to your credit balance immediately.\n"
            . "Can I export leads directly to my CRM? | Yes! We offer 1 click native integrations with Salesforce, HubSpot, Pipedrive, Outreach, Zoho, and 1,000+ tools via Zapier.\n"
            . "What is Intent Data and how does it work? | Buyer Intent Data tracks companies that are actively searching for software or services in your category across the web, allowing you to reach buyers right when they are ready to purchase.",
        'faq_help_title' => 'Still have questions?',
        'faq_help_text' => "Can't find the answer you're looking for? Please chat with our friendly team.",
        'faq_help_button' => 'Get in Touch',
        'faq_help_url' => 'https://www.go4database.com/faq',
        // Closing banner
        'show_cta' => '1',
        'cta_title' => 'Ready to Build a High-Converting Pipeline?',
        'cta_text' => 'Start your 1-Month free trial today',
        'cta_button' => 'Start Free 1-Month Trial',
        'cta_button_url' => self::REGISTER . 'bottom_cta',
        'cta_button2' => 'Book Live Demo',
        'cta_button2_url' => '#contact',
        // Contact form
        'show_contact' => '1',
        'contact_title' => 'Get Custom Prospecting & Enterprise Pricing',
        'contact_partners_title' => 'OUR PARTNERS',
        'contact_partners' => '',
        'contact_form_title' => 'Start Your Free Trial',
        'contact_email_label' => 'Work Email',
        'contact_name_label' => 'Full Name',
        'contact_phone_label' => 'Phone Number',
        'contact_button' => 'Talk to an Expert →',
        'contact_success' => 'Thank you! Our team will get in touch with you shortly.',
    ];

    /** Whether a section is shown: its show_* switch is not saved as "0". */
    public function on(string $section): bool
    {
        return $this->get('show_' . $section) !== '0';
    }

    /** A link field, never a script: anything else falls back to the default. */
    public function url(string $key): string
    {
        $url = $this->get($key);

        return preg_match('~^(https?://|/|#|mailto:|tel:)~i', $url) ? $url : (self::DEFAULTS[$key] ?? '#');
    }

    /**
     * A text with its *highlighted words* wrapped in a span with $style, the
     * rest escaped, e.g. "Get *1200 Free Credits* to Test Real-Time Data".
     */
    public function marked(string $key, string $style): string
    {
        $parts = explode('*', $this->get($key));
        $html = '';
        foreach ($parts as $i => $part) {
            $html .= $i % 2 === 1 && $i < count($parts) - 1
                ? '<span style="' . e($style) . '">' . e($part) . '</span>'
                : e($part);
        }

        return $html;
    }

    /**
     * A logo list: "Name | image address" lines as [name, image url|null].
     * An empty list means the page keeps its built-in logos.
     *
     * @return list<array{0: string, 1: ?string}>
     */
    public function logos(string $key): array
    {
        return array_map(function (array $pair) {
            [$name, $image] = $pair;
            $image = preg_match('~^(https?://|/)~i', $image) ? $image : ($image !== '' ? '/' . ltrim($image, '/') : null);

            return [$name, $image];
        }, $this->pairs($key));
    }

    /** The image a media field picked, else null (the page keeps its built-in one). */
    public function image(string $key): ?array
    {
        $id = $this->get($key);
        $img = $id !== '' ? get_attachment_image_by_id($id, null, false) : null;

        return is_array($img) && !empty($img['img_url']) ? $img : null;
    }
}
