<?php

namespace App\Http\Controllers;

use App\EmailVerifierItem;
use App\FaqCategory;
use App\Language;
use Illuminate\Http\Request;

class EmailVerifierSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /*------------------------------------------------------------------
      VERIFIER SETTINGS, how the live checker behaves
    ------------------------------------------------------------------*/
    public function index()
    {
        return view('backend.pages.email-verifier-settings');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'email_verifier_sender_domain' => 'nullable|string|max:190',
            'email_verifier_sender_email' => 'nullable|email|max:190',
            'email_verifier_smtp_timeout' => 'nullable|integer|min:2|max:30',
        ]);

        update_static_option('email_verifier_sender_domain', $request->email_verifier_sender_domain);
        update_static_option('email_verifier_sender_email', $request->email_verifier_sender_email);
        update_static_option('email_verifier_smtp_timeout', $request->email_verifier_smtp_timeout ?: 8);
        update_static_option('email_verifier_enable_smtp_check', $request->has('email_verifier_enable_smtp_check') ? '1' : '0');
        update_static_option('email_verifier_enable_catchall_check', $request->has('email_verifier_enable_catchall_check') ? '1' : '0');

        return redirect()->back()->with(['msg' => __('Email Verifier Settings Updated...'), 'type' => 'success']);
    }

    /*------------------------------------------------------------------
      PAGE CONTENT, everything the visitor reads on /email-verifier
    ------------------------------------------------------------------*/

    /**
     * This page's copy: the static_option key (minus the ev_{lang}_ prefix)
     * and the value the page falls back to when the admin leaves it blank.
     * Shared with FrontendController so both sides agree on the defaults.
     */
    public static function default_text()
    {
        return [
            'hero_badge' => 'Live checker, results in seconds',
            'hero_title' => 'Know if an email is real before you',
            'hero_title_highlight' => 'hit send',
            'hero_subtitle' => "We check the syntax, the domain, and the live mailbox itself, then tell you plainly whether it's safe to send. No test email is ever delivered.",
            'tool_foot' => 'Free to use, No signup needed, Nothing is stored',
            'checks_kicker' => 'How it works',
            'checks_title' => 'Nine checks on every address',
            'checks_lead' => 'Each address runs through the same layered scan, from a simple format check all the way to a live conversation with the receiving mail server.',
            'glossary_kicker' => 'Reading your result',
            'glossary_title' => 'What each status means',
            'why_kicker' => 'Why it matters',
            'why_title' => 'One bad list can cost you months',
            'why_lead' => "Sender reputation is slow to build and fast to lose. Here's the chain reaction a dirty list sets off.",
            'testimonial_kicker' => 'Customers',
            'testimonial_title' => 'What our users say',
            'faq_kicker' => 'Questions',
            'faq_title' => 'Frequently asked',
            'cta_title' => 'Ready to clean your whole list?',
            'cta_text' => 'Verify thousands of addresses at once and send with confidence.',
            'cta_btn' => 'Try 1200 free credits',
            'cta_note' => 'No credit card required',
            'cta_url' => 'https://app.go4database.com/register',
        ];
    }

    /** Value shown in the admin form / on the page: saved value, else default. */
    public static function text_value($lang, $field)
    {
        $saved = get_static_option('ev_' . $lang . '_' . $field);

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
        $all_languages = Language::all();

        return view('backend.pages.email-verifier-content')->with([
            'all_languages' => $all_languages,
            'all_items' => EmailVerifierItem::orderBy('sr_order')->get()->groupBy('lang'),
            'all_faq_category' => FaqCategory::where('status', 'publish')->orderBy('sr_order')->get(),
        ]);
    }

    public function update_content(Request $request)
    {
        foreach (Language::all() as $language) {
            foreach (self::text_fields() as $field) {
                $key = 'ev_' . $language->slug . '_' . $field;
                if ($request->has($key)) {
                    update_static_option($key, $request->input($key));
                }
            }
        }

        return redirect()->back()->with(['msg' => __('Email Verifier Page Content Updated...'), 'type' => 'success']);
    }

    public function item_store(Request $request)
    {
        $this->validate($request, [
            'section' => 'required|string|max:191',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:191',
            'badge_key' => 'nullable|string|max:191',
            'sr_order' => 'nullable|integer',
            'lang' => 'required|string|max:191',
            'status' => 'nullable|string|max:191',
        ]);

        EmailVerifierItem::create([
            'section' => $request->section,
            'badge_key' => $request->badge_key,
            'icon' => $request->icon,
            'title' => $request->title,
            'description' => $request->description,
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
            'sr_order' => 'nullable|integer',
            'status' => 'nullable|string|max:191',
        ]);

        EmailVerifierItem::findOrFail($request->id)->update([
            'badge_key' => $request->badge_key,
            'icon' => $request->icon,
            'title' => $request->title,
            'description' => $request->description,
            'is_highlight' => $request->has('is_highlight') ? 1 : 0,
            'sr_order' => $request->sr_order ?: 0,
            'status' => $request->status ?: 'publish',
        ]);

        return redirect()->back()->with(['msg' => __('Item Updated...'), 'type' => 'success']);
    }

    public function item_delete($id)
    {
        EmailVerifierItem::findOrFail($id)->delete();

        return redirect()->back()->with(['msg' => __('Item Deleted...'), 'type' => 'danger']);
    }
}
