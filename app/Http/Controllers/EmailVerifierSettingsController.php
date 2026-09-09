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

    /** static_option keys, per language, that hold this page's copy. */
    public static function text_fields()
    {
        return [
            'hero_badge', 'hero_title', 'hero_title_highlight', 'hero_subtitle', 'tool_foot',
            'checks_kicker', 'checks_title', 'checks_lead',
            'glossary_kicker', 'glossary_title',
            'why_kicker', 'why_title', 'why_lead',
            'testimonial_kicker', 'testimonial_title',
            'faq_kicker', 'faq_title', 'faq_category_id',
            'cta_title', 'cta_text', 'cta_btn', 'cta_note', 'cta_url',
        ];
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
