<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailVerifierSettingsController extends Controller
{
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
}
