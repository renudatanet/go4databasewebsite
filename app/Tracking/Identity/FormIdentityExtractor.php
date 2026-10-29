<?php

namespace App\Tracking\Identity;

/**
 * Finds the name, email, phone and company among a form's fields. The
 * contact and quote forms are built in the admin's Form Builder, so their
 * field names vary; they are matched by what they contain.
 */
final class FormIdentityExtractor
{
    /** @param array<string, mixed> $input */
    public function extract(array $input): IdentityDetails
    {
        $fields = [];
        foreach ($input as $key => $value) {
            if (is_string($key) && is_string($value) && $value !== '' && !str_starts_with($key, '_')) {
                $fields[strtolower($key)] = $value;
            }
        }

        $email = null;
        foreach ($fields as $key => $value) {
            if (filter_var(trim($value), FILTER_VALIDATE_EMAIL) && ($email === null || str_contains($key, 'mail'))) {
                $email = $value;
            }
        }

        // Separate first and last name fields win over a lone "name" match,
        // which would otherwise pick up just "first_name".
        $first = $this->first($fields, ['/first[-_]?name/']);
        $name = $first !== null
            ? trim($first . ' ' . ($this->first($fields, ['/last[-_]?name|surname/']) ?? ''))
            : $this->first($fields, ['/^(full[-_]?)?name$/', '/your[-_]?name/', '/^name/', '/(?<!user|company|business|file|last)[-_]?name$/']);

        return new IdentityDetails(
            name: $name,
            email: $email,
            phone: $this->first($fields, ['/phone|mobile|tel|whatsapp/']),
            company: $this->first($fields, ['/company|organi[sz]ation|business/']),
        );
    }

    /**
     * @param array<string, string> $fields
     * @param list<string> $patterns tried in order
     */
    private function first(array $fields, array $patterns): ?string
    {
        foreach ($patterns as $pattern) {
            foreach ($fields as $key => $value) {
                if (preg_match($pattern, $key)) {
                    return $value;
                }
            }
        }

        return null;
    }
}
