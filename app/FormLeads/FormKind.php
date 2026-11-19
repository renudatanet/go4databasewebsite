<?php

namespace App\FormLeads;

/**
 * The forms whose submissions are kept as form leads. The value is what is
 * stored, and also the source name Tracking gives the visitor who sent it
 * (App\Tracking\Models\Identity::LABELS).
 */
enum FormKind: string
{
    case Expert = 'list_expert';
    case Sample = 'list_sample';
    case Signup = 'list_signup';

    public function label(): string
    {
        return match ($this) {
            self::Expert => 'Talk to an expert',
            self::Sample => 'Sample request',
            self::Signup => 'Free credits sign-up',
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
