<?php

namespace App\Tracking\Identity;

/** What a visitor told us about themselves. Every field is optional. */
final class IdentityDetails
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $company = null,
        public ?string $reference = null,
    ) {
        $this->name = self::clean($name, 150);
        $this->email = $email !== null && filter_var(trim($email), FILTER_VALIDATE_EMAIL) ? strtolower(trim($email)) : null;
        $this->phone = self::clean($phone, 40);
        $this->company = self::clean($company, 150);
        $this->reference = self::clean($reference, 64);
    }

    public function isEmpty(): bool
    {
        return $this->name === null && $this->email === null && $this->phone === null;
    }

    private static function clean(?string $value, int $max): ?string
    {
        $value = trim(preg_replace('/\s+/u', ' ', (string) $value) ?? '');

        return $value === '' ? null : mb_substr(strip_tags($value), 0, $max);
    }
}
