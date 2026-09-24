<?php

namespace App\Http\Controllers;

use App\AffiliateItem;
use App\FaqCategory;
use App\Language;
use Illuminate\Http\Request;

class AffiliateSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /*------------------------------------------------------------------
      PROGRAMME TERMS

      These are the numbers the page promises to real people, so every one
      of them defaults to EMPTY rather than to a plausible-looking figure.
      A blank term renders as "To be confirmed" on the page and the
      earnings calculator refuses to quote a number, which is the honest
      failure: better a visibly unfinished page than a made-up rate
      someone signs up on the strength of.
    ------------------------------------------------------------------*/
    public static function term_defaults()
    {
        return [
            'commission_rate' => '',      // e.g. 20   (percent, digits only)
            // What the PARTNER'S AUDIENCE gets, e.g. "25%". Deliberately a
            // separate field from commission_rate: one is what the partner
            // earns, the other is what their referral saves, and conflating
            // the two would put a false promise on the page.
            'referral_discount' => '',
            'commission_term' => '',      // e.g. "12 months" or "Lifetime"
            'cookie_days' => '',          // e.g. "90 days"
            'payout_method' => '',        // e.g. "PayPal"
            'payout_minimum' => '',       // e.g. "$50"
            'apply_url' => '',            // where the Apply button goes
            // Who gets told when someone applies. Blank means nobody is
            // emailed and applications are only seen in the admin panel.
            'notify_email' => '',
            'plan_values' => '49,99,199', // calculator preset plan prices
            'max_referrals' => '50',      // calculator slider ceiling
            'default_referrals' => '10',  // where the slider starts
            'default_plan' => '',         // which plan button starts selected
            // How many months the calculator projects. This must match the
            // real commission term, or the page quotes earnings for a period
            // partners do not actually get paid for.
            'calc_months' => '12',
            'testimonial_count' => '3',
        ];
    }

    /**
     * Sections the admin can switch off. Everything is on by default, so an
     * existing page never changes just because this was added.
     */
    public static function section_defaults()
    {
        return [
            'discount' => 'Audience discount strip',
            'calculator' => 'Earnings calculator',
            'terms' => 'Terms row',
            'steps' => 'How it works',
            'compare' => 'Affiliate vs reseller table',
            'who' => 'Who it suits',
            'gets' => 'What partners get',
            'testimonials' => 'Testimonials',
            'faq' => 'FAQ',
            'apply' => 'Apply form / closing banner',
        ];
    }

    /** Whether a section renders. Defaults to on. */
    public static function show($section)
    {
        return get_static_option('affiliate_show_' . $section, '1') !== '0';
    }

    /** Months the calculator projects over, clamped to something sane. */
    public static function calc_months()
    {
        $n = (int) self::term('calc_months');

        return ($n >= 1 && $n <= 60) ? $n : 12;
    }

    public static function term($key)
    {
        $saved = get_static_option('affiliate_' . $key);

        return ($saved === null || $saved === '')
            ? (self::term_defaults()[$key] ?? '')
            : $saved;
    }

    /** The rate as a number, or null when nobody has set one yet. */
    public static function rate()
    {
        $raw = trim((string) self::term('commission_rate'));
        if ($raw === '' || !is_numeric($raw)) {
            return null;
        }
        $n = (float) $raw;

        return ($n > 0 && $n <= 100) ? $n : null;
    }

    /** Calculator plan presets, always at least one usable number. */
    public static function plans()
    {
        $parts = array_filter(array_map(
            fn($p) => (int) trim($p),
            explode(',', (string) self::term('plan_values'))
        ), fn($n) => $n > 0);

        return !empty($parts) ? array_values(array_slice($parts, 0, 4)) : [49, 99, 199];
    }

    public function index()
    {
        return view('backend.pages.affiliate-settings');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'affiliate_commission_rate' => 'nullable|numeric|min:0.1|max:100',
            'affiliate_referral_discount' => 'nullable|string|max:60',
            'affiliate_commission_term' => 'nullable|string|max:60',
            'affiliate_cookie_days' => 'nullable|string|max:60',
            'affiliate_payout_method' => 'nullable|string|max:60',
            'affiliate_payout_minimum' => 'nullable|string|max:60',
            'affiliate_apply_url' => 'nullable|url|max:500',
            'affiliate_notify_email' => 'nullable|email|max:190',
            'affiliate_plan_values' => 'nullable|string|max:100',
            'affiliate_max_referrals' => 'nullable|integer|min:5|max:500',
            'affiliate_default_referrals' => 'nullable|integer|min:1|max:500',
            'affiliate_default_plan' => 'nullable|integer|min:1',
            'affiliate_calc_months' => 'nullable|integer|min:1|max:60',
            'affiliate_testimonial_count' => 'nullable|integer|min:1|max:12',
        ]);

        foreach (array_keys(self::term_defaults()) as $key) {
            update_static_option('affiliate_' . $key, $request->input('affiliate_' . $key));
        }

        // Unchecked boxes are simply absent from the request, so each section
        // is written explicitly rather than only the ones that came back.
        foreach (array_keys(self::section_defaults()) as $section) {
            update_static_option(
                'affiliate_show_' . $section,
                $request->has('affiliate_show_' . $section) ? '1' : '0'
            );
        }

        return redirect()->back()->with(['msg' => __('Affiliate Settings Updated...'), 'type' => 'success']);
    }

    /*------------------------------------------------------------------
      PAGE CONTENT
    ------------------------------------------------------------------*/

    /**
     * This page's copy: the static_option key (minus the af_{lang}_ prefix)
     * and the value the page falls back to when the admin leaves it blank.
     */
    public static function default_text()
    {
        return [
            // Browser tab, breadcrumb and the name in the page's schema.
            'page_title' => 'Affiliate Program',
            // Shown wherever a programme term has not been set yet.
            'tbc_label' => 'To be confirmed',
            'meta_description' => 'Earn recurring commission for every customer you refer to Go4Database. Free to join, no targets, tracked links and monthly payouts for agencies, creators and consultants.',
            'meta_keywords' => 'affiliate program, referral program, b2b data affiliate, lead generation affiliate, recurring commission, partner program',

            'hero_badge' => 'Partner Program',
            'hero_title' => 'Get paid every month for the',
            'hero_title_highlight' => 'customers you send us',
            'hero_subtitle' => 'Share Go4Database with your audience and earn a commission on every plan they buy, not just the first one. Free to join, no targets, no minimum audience size.',
            'hero_btn' => 'Apply to join',
            'hero_btn_alt' => 'See what you would earn',

            // The discount strip. :discount is replaced with the value set in
            // Affiliate Settings, so the figure exists in exactly one place.
            'discount_title' => 'Your audience gets :discount off',
            'discount_text' => 'Your link carries a discount, so you are not just recommending us, you are bringing people a better price than they would get on their own. That is what makes referrals convert.',

            'apply_title' => 'Apply to become an affiliate',
            'apply_lead' => 'Tell us where your audience is. We read every application ourselves and usually reply the same working day.',
            'apply_name' => 'Your name',
            'apply_email' => 'Email address',
            'apply_site' => 'Website, channel or profile',
            'apply_promo' => 'How will you promote Go4Database?',
            'apply_btn' => 'Send application',
            'apply_success' => 'Thanks, your application is in. We read every one and will come back to you by email, usually the same working day.',

            'calc_title' => 'What could you earn?',
            // :months is replaced with the projection length set in Affiliate
            // Settings, so the copy and the sums can never disagree.
            'calc_lead' => 'Drag the slider. Because commission is recurring, month :months pays you for every customer you referred in months one through :months.',
            'calc_refs_label' => 'Customers you refer per month',
            'calc_plan_label' => 'Average plan they buy',
            'calc_monthly_label' => 'Monthly commission by month :months',
            'calc_year_label' => 'Total earned over :months months',
            'calc_btn' => 'Start earning',
            'calc_unset_note' => 'The commission rate has not been set yet, so this calculator cannot show a figure. Add it under Affiliate Settings in the admin panel.',

            'terms_title' => 'The terms, in plain numbers',
            'terms_lead' => 'No small print and no "contact us for rates". Everything that decides what you get paid is on this page.',
            'terms_rate_label' => 'Commission',
            'terms_rate_note' => 'of every payment your referral makes',
            'terms_term_label' => 'Duration',
            'terms_term_note' => 'how long you keep earning per customer',
            'terms_cookie_label' => 'Cookie window',
            'terms_cookie_note' => 'time between their click and signup',
            'terms_payout_label' => 'Paid by',
            'terms_payout_note' => 'monthly, once cleared',
            'terms_minimum_label' => 'Minimum payout',
            'terms_minimum_note' => 'balance before we send money',

            'steps_title' => 'Three steps, then it runs itself',

            'compare_title' => 'Affiliate or reseller?',
            'compare_lead' => 'Two different partnerships, and people mix them up constantly. If you want to refer and move on, you want this page. If you want to sell Go4Database as part of your own service, you want the reseller programme.',
            'compare_col_aff' => 'Affiliate, this page',
            'compare_col_res' => 'Reseller',
            'compare_cta_text' => 'Want to sell under your own brand and own the customer?',
            'compare_cta_btn' => 'Reseller programme',
            'compare_cta_url' => '/reseller',

            'who_title' => 'Who does well with this',
            'who_lead' => 'Anyone whose audience is trying to find customers. You do not need to be technical, and you do not need a big following.',

            'gets_title' => 'What we give you',
            'gets_lead' => 'You bring the audience. Everything needed to convert them is already made.',

            'testimonial_title' => 'Partners already earning',
            'faq_title' => 'Questions partners ask',

            'cta_title' => 'Start earning this month',
            'cta_text' => 'Applications are reviewed by a person, usually the same working day. Free to join and nothing to pay, ever.',
            'cta_btn' => 'Apply to become an affiliate',
            'cta_note' => 'No credit card. No minimum audience.',
        ];
    }

    public static function text_value($lang, $field)
    {
        $saved = get_static_option('af_' . $lang . '_' . $field);

        return ($saved === null || $saved === '')
            ? (self::default_text()[$field] ?? '')
            : $saved;
    }

    public static function text_fields()
    {
        return array_merge(array_keys(self::default_text()), ['faq_category_id']);
    }

    /**
     * The repeatable blocks the page ships with, in the same shape the
     * affiliate_items rows use. Safety net only: the migration seeds these
     * same rows, and once seeded the database wins. It exists so a server
     * that has this code but not its migrations still renders a whole page.
     */
    public static function default_items()
    {
        return [
            'trust' => [
                ['icon' => 'check', 'title' => 'Free to join'],
                ['icon' => 'check', 'title' => 'Recurring commission'],
                ['icon' => 'check', 'title' => 'Paid monthly'],
            ],
            'step' => [
                ['title' => 'Apply in two minutes', 'description' => 'Tell us where your audience is. We review by hand, usually the same working day, and there is no minimum following.'],
                ['title' => 'Share your link', 'description' => 'You get a tracked link plus banners, screenshots and copy you can lift straight into a post, newsletter or video description.'],
                ['title' => 'Get paid every month', 'description' => 'Watch clicks, signups and earnings in your dashboard. We pay out monthly for as long as your referrals keep their plan.'],
            ],
            'compare' => [
                ['title' => 'Who owns the customer', 'description' => 'We do. You refer and hand off.', 'alt_text' => 'You do. You hold the billing relationship.'],
                ['title' => 'Who does the selling', 'description' => 'Nobody. A link does the work.', 'alt_text' => 'You sell, quote and close.'],
                ['title' => 'Who gives support', 'description' => 'Our team.', 'alt_text' => 'You, as first line.'],
                ['title' => 'How you earn', 'description' => 'Commission on their payments.', 'alt_text' => 'Margin on wholesale pricing.'],
                ['title' => 'Effort to start', 'description' => 'Minutes. Apply and share.', 'alt_text' => 'A conversation with our partnerships team.'],
            ],
            'who' => [
                ['icon' => 'A', 'title' => 'Agencies', 'description' => 'You already run outreach for clients. Refer the data source you use anyway.'],
                ['icon' => 'C', 'title' => 'Creators', 'description' => 'Newsletters, YouTube and podcasts covering sales, marketing or startups.'],
                ['icon' => 'K', 'title' => 'Consultants', 'description' => 'You advise on pipeline and GTM. This fits into advice you already give.'],
                ['icon' => 'S', 'title' => 'Software partners', 'description' => 'CRM, outreach and automation tools whose users constantly need contacts.'],
            ],
            'get' => [
                ['title' => 'Tracked referral link', 'description' => 'One link, works anywhere, attributes every signup back to you.'],
                ['title' => 'Live dashboard', 'description' => 'Clicks, signups, conversions and confirmed balance, updated as they happen.'],
                ['title' => 'Ready-made creative', 'description' => 'Banners, product screenshots and copy blocks you can use unchanged.'],
                ['title' => 'A named contact', 'description' => 'A real person to email about a campaign or a payout question.'],
                ['title' => 'Deal support', 'description' => 'For bigger referrals we will join a call and help you close it.'],
                ['title' => 'No earnings cap', 'description' => 'No ceiling, no limit on referrals, no clawback on renewals.'],
            ],
        ];
    }

    public static function default_faqs()
    {
        return [
            ['title' => 'Does it cost anything to join?', 'description' => 'No. Joining is free, there is nothing to buy, and there are no targets you have to hit to stay in the programme.'],
            ['title' => 'When exactly do I get paid?', 'description' => 'Commission is confirmed once a referral\'s payment clears and the refund window closes. Your confirmed balance is paid out monthly, provided you are above the minimum payout shown above.'],
            ['title' => 'Do I keep earning if my referral renews?', 'description' => 'Yes, for as long as the commission term above lasts. You are paid on their renewals during that period, not only on their first payment.'],
            ['title' => 'Can I refer customers outside my country?', 'description' => 'Yes. Go4Database sells worldwide and referrals count wherever the customer is based.'],
            ['title' => 'What happens if someone asks for a refund?', 'description' => 'Commission on a refunded payment is reversed, because it was never really earned. This is why there is a short holding period before a balance is marked confirmed.'],
            ['title' => 'Can I bid on the Go4Database brand name in ads?', 'description' => 'No. Paid search on our brand name and close variants is not allowed, because it competes with our own ads and bids up the cost of traffic we would have received anyway.'],
            ['title' => 'How do I know a signup was credited to me?', 'description' => 'Every click on your link is tracked and shown in your dashboard, along with signups, conversions and the commission each one earned.'],
        ];
    }

    public function content()
    {
        return view('backend.pages.affiliate-content')->with([
            'all_languages' => Language::all(),
            'all_items' => AffiliateItem::orderBy('sr_order')->get()->groupBy('lang'),
            'all_faq_category' => FaqCategory::where('status', 'publish')->orderBy('sr_order')->get(),
        ]);
    }

    public function update_content(Request $request)
    {
        foreach (Language::all() as $language) {
            foreach (self::text_fields() as $field) {
                $key = 'af_' . $language->slug . '_' . $field;
                if ($request->has($key)) {
                    update_static_option($key, $request->input($key));
                }
            }
        }

        return redirect()->back()->with(['msg' => __('Affiliate Page Content Updated...'), 'type' => 'success']);
    }

    public function item_store(Request $request)
    {
        $this->validate($request, [
            'section' => 'required|string|max:191',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'alt_text' => 'nullable|string',
            'icon' => 'nullable|string|max:191',
            'sr_order' => 'nullable|integer',
            'lang' => 'required|string|max:191',
            'status' => 'nullable|string|max:191',
        ]);

        AffiliateItem::create([
            'section' => $request->section,
            'icon' => $request->icon,
            'title' => $request->title,
            'description' => $request->description,
            'alt_text' => $request->alt_text,
            'is_highlight' => $request->has('is_highlight') ? 1 : 0,
            'sr_order' => $request->sr_order ?: 0,
            'lang' => $request->lang,
            'status' => $request->status ?: 'publish',
        ]);

        return redirect()->back()->with(['msg' => __('New Item Added...'), 'type' => 'success']);
    }

    public function item_update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'alt_text' => 'nullable|string',
            'icon' => 'nullable|string|max:191',
            'sr_order' => 'nullable|integer',
            'status' => 'nullable|string|max:191',
        ]);

        AffiliateItem::findOrFail($request->id)->update([
            'icon' => $request->icon,
            'title' => $request->title,
            'description' => $request->description,
            'alt_text' => $request->alt_text,
            'is_highlight' => $request->has('is_highlight') ? 1 : 0,
            'sr_order' => $request->sr_order ?: 0,
            'status' => $request->status ?: 'publish',
        ]);

        return redirect()->back()->with(['msg' => __('Item Updated...'), 'type' => 'success']);
    }

    public function item_delete($id)
    {
        AffiliateItem::findOrFail($id)->delete();

        return redirect()->back()->with(['msg' => __('Item Deleted...'), 'type' => 'danger']);
    }

    /*------------------------------------------------------------------
      APPLICATIONS sent from the form on /affiliate
    ------------------------------------------------------------------*/
    public function applications(Request $request)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('affiliate_applications')) {
            return view('backend.pages.affiliate-applications')->with([
                'applications' => collect()->paginate(20),
                'status' => '',
            ]);
        }

        $status = $request->input('status');
        $query = \App\AffiliateApplication::query()->latest();
        if (!empty($status) && array_key_exists($status, \App\AffiliateApplication::STATUSES)) {
            $query->where('status', $status);
        }

        return view('backend.pages.affiliate-applications')->with([
            'applications' => $query->paginate(20)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function application_update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'status' => 'required|string|in:new,approved,declined',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        \App\AffiliateApplication::findOrFail($request->id)->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->back()->with(['msg' => __('Application Updated...'), 'type' => 'success']);
    }

    public function application_delete($id)
    {
        \App\AffiliateApplication::findOrFail($id)->delete();

        return redirect()->back()->with(['msg' => __('Application Deleted...'), 'type' => 'danger']);
    }
}
