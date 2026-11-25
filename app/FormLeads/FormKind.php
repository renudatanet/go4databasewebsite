<?php

namespace App\FormLeads;

/**
 * The forms whose submissions are kept as form leads (the list pages' three
 * and the homepage's contact form). The value is what is
 * stored, and also the source name Tracking gives the visitor who sent it
 * (App\Tracking\Models\Identity::LABELS).
 */
enum FormKind: string
{
    case Expert = 'list_expert';
    case Sample = 'list_sample';
    case Signup = 'list_signup';
    case HomeExpert = 'home_expert';

    public function label(): string
    {
        return match ($this) {
            self::Expert => 'Talk to an expert',
            self::Sample => 'Sample request',
            self::Signup => 'Free credits sign-up',
            self::HomeExpert => 'Homepage: talk to an expert',
        };
    }

    /** @return array<string, string> value => label */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $kind) {
            $options[$kind->value] = $kind->label();
        }

        return $options;
    }
}
