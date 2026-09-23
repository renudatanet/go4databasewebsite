<?php

namespace App\Http\Controllers;

use App\EmailFinderItem;
use App\FaqCategory;
use App\Language;
use Illuminate\Http\Request;

class EmailFinderSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /*------------------------------------------------------------------
      FINDER SETTINGS, how the lookup itself behaves.

      The SMTP identity (sender domain, sender address, timeout, whether
      live checks run at all) is deliberately NOT repeated here. The finder
      probes through the same engine as the verifier and reads the same
      email_verifier_* options, so the site has one mail identity rather
      than two that can drift apart.
    ------------------------------------------------------------------*/
    public function index()
    {
        return view('backend.pages.email-finder-settings');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'email_finder_max_probes' => 'nullable|integer|min:1|max:10',
            'email_finder_unknown_bail' => 'nullable|integer|min:1|max:10',
        ]);

        update_static_option('email_finder_max_probes', $request->email_finder_max_probes ?: 6);
        update_static_option('email_finder_unknown_bail', $request->email_finder_unknown_bail ?: 2);
        update_static_option('email_finder_enable_catchall_check', $request->has('email_finder_enable_catchall_check') ? '1' : '0');

        return redirect()->back()->with(['msg' => __('Email Finder Settings Updated...'), 'type' => 'success']);
    }

    /*------------------------------------------------------------------
      PAGE CONTENT, everything the visitor reads on /email-finder
    ------------------------------------------------------------------*/

    /**
     * This page's copy: the static_option key (minus the ef_{lang}_ prefix)
     * and the value the page falls back to when the admin leaves it blank.
     * Shared with FrontendController so both sides agree on the defaults.
     */
    public static function default_text()
    {
        return [
            /* --- search engines ---------------------------------------- */
            'meta_description' => "Find anyone's work email address from their name and company domain. Checks the real mail server live, tells you honestly when an address is confirmed and when it is a pattern-based guess.",
            'meta_keywords' => 'email finder, find email address, work email lookup, email address finder, b2b email finder, find company email, email pattern finder',

            /* --- hero and tool ----------------------------------------- */
            'hero_badge' => 'Free email finder',
            'hero_title' => "Find anyone's",
            // Rendered inside <em>, which the stylesheet paints brand green.
            'hero_title_highlight' => 'work email',
            'hero_subtitle' => 'Give us a name and a company domain. We work through the email patterns companies actually use, check them against the real mail server, and tell you which one exists.',
            'first_label' => 'First name',
            'last_label' => 'Last name',
            'domain_label' => 'Company domain',
            'tool_btn' => 'Find email',
            'working_label' => 'Checking the patterns this company is most likely to use',

            /* --- how it works ------------------------------------------ */
            'steps_title' => 'How the finder works',
            'steps_lead' => "There is no magic list of everyone's email. What there is: companies pick one format and stick to it. So we work out the format, then confirm the address against the server that would receive it.",

            /* --- format table ------------------------------------------ */
            'patterns_title' => 'The formats companies use',
            'patterns_lead' => 'Corporate email is far less varied than it looks. These few formats cover the overwhelming majority of work addresses, which is exactly why pattern-based finding works at all.',
            'patterns_col_format' => 'Format',
            'patterns_col_example' => 'Example',
            'patterns_col_common' => 'How common',

            /* --- cross-link to the verifier ----------------------------- */
            'pair_title' => 'Found an address? Verify it before you send',
            'pair_text' => 'The finder tells you which address exists. The verifier goes further: disposable domains, role inboxes, catch-all servers, and whether the mailbox is safe to send to without risking your sender reputation.',
            'pair_btn' => 'Open Email Verifier',

            /* --- free credits offer ------------------------------------- */
            'offer_title' => 'Need more than one at a time?',
            'offer_text' => 'Go4Database holds verified work emails and direct dials for millions of decision makers. Search by job title, industry and location, and export whole lists instead of looking people up one by one.',
            'offer_btn' => 'Get 1200 free credits',
            'offer_url' => 'https://app.go4database.com/register?utm_source=EmailFinder&utm_medium=Internal&utm_campaign=free_credits',
            'offer_note' => 'No credit card required',

            /* --- faq and closing cta ------------------------------------ */
            'faq_title' => 'Questions people ask',
            'cta_title' => 'Stop guessing at email addresses',
            'cta_text' => 'Find the address, verify it, then reach the person. All of it free to try.',
            'cta_btn' => 'Find an email',
            'cta_btn_ghost' => 'Verify an email',
        ];
    }

    /**
     * The repeatable blocks the page ships with, in the same shape the
     * email_finder_items rows use.
     *
     * This is the safety net, not the normal path: the create-table migration
     * seeds these same rows, and once seeded the database wins. It exists so
     * that a server which has the new code but has not run migrations yet
     * still renders a complete page instead of three empty sections.
     */
    public static function default_items()
    {
        return [
            'trust' => [
                ['icon' => 'check', 'title' => 'No signup needed'],
                ['icon' => 'check', 'title' => 'Checked against the live mail server'],
                ['icon' => 'check', 'title' => 'We never send a test email'],
            ],
            'step' => [
                ['title' => 'Build the likely addresses', 'description' => 'From the name and domain we generate the formats companies actually use, ordered by how common each one is in the real world.'],
                ['title' => 'Ask the mail server', 'description' => "We open a conversation with the company's mail server and ask whether each address would be accepted, starting with the most likely. Nothing is ever delivered."],
                ['title' => 'Tell you what we actually know', 'description' => 'If the server confirms a mailbox, you get it marked confirmed. If the server refuses to answer, or accepts everything, we say so instead of dressing a guess up as a fact.'],
            ],
            'pattern' => [
                ['title' => 'first.last@', 'description' => 'jane.doe@acme.com', 'badge_key' => '~60%', 'bar_width' => 100, 'is_highlight' => 0],
                ['title' => 'first@', 'description' => 'jane@acme.com', 'badge_key' => '~15–20%', 'bar_width' => 30, 'is_highlight' => 0],
                ['title' => 'flast@', 'description' => 'jdoe@acme.com', 'badge_key' => '~10–15%', 'bar_width' => 21, 'is_highlight' => 0],
                ['title' => 'everything else', 'description' => 'janedoe@, j.doe@, doe.jane@…', 'badge_key' => '<10%', 'bar_width' => 16, 'is_highlight' => 1],
            ],
        ];
    }

    /**
     * Fallback questions, used only when no Faq category is linked to this
     * page or that category has no published questions. Same safety net as
     * default_items(): the seed migration puts these into the Faq CRUD.
     */
    public static function default_faqs()
    {
        return [
            ['title' => 'Is the Email Finder free?', 'description' => 'Yes. You can look up addresses here without an account, a card, or a trial. Bulk lookups and exporting whole lists are part of the paid Go4Database platform.'],
            ['title' => 'How accurate is it?', 'description' => 'When we mark an address "Confirmed", the company\'s own mail server told us that mailbox exists, which is as close to certain as you get without sending an email. When it says "Best guess", we are showing the most common format because the server would not confirm either way, and we label it that way rather than pretending otherwise.'],
            ['title' => 'What does "catch-all" mean?', 'description' => 'Some companies configure their mail server to accept mail sent to any address at their domain, real or not. On those domains no single address can be confirmed, because the server says yes to everything, so we tell you it is a catch-all instead of claiming we verified something.'],
            ['title' => 'Do you send a test email to the person?', 'description' => 'No. We start the delivery conversation with the mail server and ask whether the address would be accepted, then stop before anything is sent. The person never receives a message and never sees the check.'],
            ['title' => 'Why can\'t it find some addresses?', 'description' => 'Large providers like Microsoft and Google often refuse to answer address checks from senders they do not recognise. When that happens we cannot confirm anything, so you get the most likely pattern marked as a guess. The company may also simply not use any common format.'],
            ['title' => 'Can I look up a Gmail or Yahoo address?', 'description' => 'No, and that is deliberate. Personal inboxes do not follow a company pattern, so there is nothing to work out. The finder is built for work addresses at company domains.'],
        ];
    }

    /** Value shown in the admin form / on the page: saved value, else default. */
    public static function text_value($lang, $field)
    {
        $saved = get_static_option('ef_' . $lang . '_' . $field);

        return ($saved === null || $saved === '')
            ? (self::default_text()[$field] ?? '')
            : $saved;
    }

    public static function text_fields()
    {
        return array_merge(array_keys(self::default_text()), ['faq_category_id']);
    }

    public function content()
    {
        return view('backend.pages.email-finder-content')->with([
            'all_languages' => Language::all(),
            'all_items' => EmailFinderItem::orderBy('sr_order')->get()->groupBy('lang'),
            'all_faq_category' => FaqCategory::where('status', 'publish')->orderBy('sr_order')->get(),
        ]);
    }

    public function update_content(Request $request)
    {
        foreach (Language::all() as $language) {
            foreach (self::text_fields() as $field) {
                $key = 'ef_' . $language->slug . '_' . $field;
                if ($request->has($key)) {
                    update_static_option($key, $request->input($key));
                }
            }
        }

        return redirect()->back()->with(['msg' => __('Email Finder Page Content Updated...'), 'type' => 'success']);
    }

    public function item_store(Request $request)
    {
        $this->validate($request, [
            'section' => 'required|string|max:191',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:191',
            'badge_key' => 'nullable|string|max:191',
            'bar_width' => 'nullable|integer|min:0|max:100',
            'sr_order' => 'nullable|integer',
            'lang' => 'required|string|max:191',
            'status' => 'nullable|string|max:191',
        ]);

        EmailFinderItem::create([
            'section' => $request->section,
            'icon' => $request->icon,
            'title' => $request->title,
            'description' => $request->description,
            'badge_key' => $request->badge_key,
            'bar_width' => $request->bar_width,
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
            'icon' => 'nullable|string|max:191',
            'badge_key' => 'nullable|string|max:191',
            'bar_width' => 'nullable|integer|min:0|max:100',
            'sr_order' => 'nullable|integer',
            'status' => 'nullable|string|max:191',
        ]);

        EmailFinderItem::findOrFail($request->id)->update([
            'icon' => $request->icon,
            'title' => $request->title,
            'description' => $request->description,
            'badge_key' => $request->badge_key,
            'bar_width' => $request->bar_width,
            'is_highlight' => $request->has('is_highlight') ? 1 : 0,
            'sr_order' => $request->sr_order ?: 0,
            'status' => $request->status ?: 'publish',
        ]);

        return redirect()->back()->with(['msg' => __('Item Updated...'), 'type' => 'success']);
    }

    public function item_delete($id)
    {
        EmailFinderItem::findOrFail($id)->delete();

        return redirect()->back()->with(['msg' => __('Item Deleted...'), 'type' => 'danger']);
    }
}
