<?php

namespace App\FormLeads;

use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Sends a form lead's notification email, the same email these forms have
 * always sent. The lead is saved first, so a failed send is only noted on it
 * (emailed = false) and logged; the visitor never sees an error for it.
 */
final class LeadMailer
{
    public const TO = 'success@go4database.com';

    /** @param array<string, mixed> $data what the email view prints, as the form sent it */
    public function send(FormLead $lead, string $view, string $subject, array $data): bool
    {
        try {
            Mail::send($view, ['data' => $data], function ($message) use ($lead, $subject) {
                $message->to(self::TO)->subject($subject);
                if ($lead->email) {
                    $message->replyTo($lead->email);
                }
            });
            $sent = true;
        } catch (Throwable $e) {
            report($e);
            $sent = false;
        }
        $lead->forceFill(['emailed' => $sent])->save();

        return $sent;
    }
}
