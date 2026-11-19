<?php

namespace App\Http\Controllers;

use App\FormLeads\FormKind;
use App\FormLeads\LeadMailer;
use App\FormLeads\LeadRecorder;
use Illuminate\Http\Request;

/**
 * The list pages' three forms. Each submission is saved as a form lead
 * (Admin > List > Form Leads) before anything else happens, then:
 * - talk to an expert and sample request send the email they always sent;
 * - the free-credits form goes on to the app's sign-up, email filled in,
 *   exactly where it used to go straight to.
 *
 * Each form carries a hidden "website_url" box people never see; a bot that
 * fills it in is answered as if all went well, and nothing is kept.
 */
class ListLeadFormController extends Controller
{
    public const SIGNUP_URL = 'https://app.go4database.com/register';

    private const TRAP = 'website_url';

    public function __construct(private LeadRecorder $recorder, private LeadMailer $mailer)
    {
    }

    public function expert(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'required|string|max:60',
            'email' => 'required|email|max:191',
            'business_details' => 'nullable|string|max:500',
        ]);

        if (!$this->isBot($request)) {
            $lead = $this->recorder->record(FormKind::Expert, [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'company' => $data['business_details'] ?? null,
            ], $request);
            $this->mailer->send($lead, 'mail.lead', 'New Lead From Website', $data);
        }

        return $this->backTo('lead-form')->with('success', 'Lead submitted successfully.');
    }

    public function sample(Request $request)
    {
        $data = $request->validate([
            'fname' => 'required|string|max:120',
            'lname' => 'required|string|max:120',
            'business-email' => 'required|email|max:191',
            'sample-phone' => 'required|string|max:60',
            'sample-message' => 'required|string|max:5000',
        ]);

        if (!$this->isBot($request)) {
            $lead = $this->recorder->record(FormKind::Sample, [
                'name' => trim($data['fname'] . ' ' . $data['lname']),
                'email' => $data['business-email'],
                'phone' => $data['sample-phone'],
                'message' => $data['sample-message'],
            ], $request);
            $this->mailer->send($lead, 'mail.sample', 'New Sample Request Lead', $data);
        }

        return $this->backTo('sample-data-section')->with('sample-success', 'Thank you! Your request has been submitted.');
    }

    /** Keep a valid email, then continue to the app's sign-up as the form always did. */
    public function signup(Request $request)
    {
        $email = trim((string) $request->input('email', ''));

        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && mb_strlen($email) <= 191 && !$this->isBot($request)) {
            $this->recorder->record(FormKind::Signup, ['email' => $email], $request);
        }

        return redirect()->away(self::SIGNUP_URL . ($email !== '' ? '?' . http_build_query(['email' => $email]) : ''));
    }

    /** Back to the page, at the form, so its thank-you message is in view. */
    private function backTo(string $anchor)
    {
        return redirect()->to(strtok(url()->previous(), '#') . '#' . $anchor);
    }

    private function isBot(Request $request): bool
    {
        return trim((string) $request->input(self::TRAP, '')) !== '';
    }
}
