<?php

namespace App\Http\Controllers;

use App\FormLeads\FormKind;
use App\FormLeads\LeadMailer;
use App\FormLeads\LeadRecorder;
use App\HomePage\HomePageText;
use Illuminate\Http\Request;

/**
 * The homepage's "Talk to an Expert" form (#contact). Each submission is
 * kept as a form lead (Admin > List > Form Leads) before the email it sends
 * to the team. The page's script sends it and shows the answer in the form;
 * without script the form posts and comes back to #contact. A bot that fills
 * the hidden "website_url" box is answered as if all went well, and nothing
 * is kept.
 */
class HomeLeadFormController extends Controller
{
    public function __construct(private LeadRecorder $recorder, private LeadMailer $mailer)
    {
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:191',
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:60',
        ]);

        if (trim((string) $request->input('website_url', '')) === '') {
            $lead = $this->recorder->record(FormKind::HomeExpert, $data, $request);
            $this->mailer->send($lead, 'mail.lead', 'New Lead From Website (homepage)', $data + ['phone' => '', 'business_details' => 'Homepage: talk to an expert']);
        }

        $message = (new HomePageText(get_user_lang()))->get('contact_success');

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : redirect()->to(strtok(url()->previous(), '#') . '#contact')->with('home_contact_success', $message);
    }
}
