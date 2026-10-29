<?php

namespace App\Tracking\Data;

/** Where an IP address is, and who runs its network. Every field may be unknown. */
final class GeoResult
{
    public function __construct(
        public ?string $countryCode = null,
        public ?string $country = null,
        public ?string $region = null,
        public ?string $city = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?int $asn = null,
        public ?string $networkOrg = null,
        public string $networkType = NetworkType::UNKNOWN,
    ) {
    }

    public static function unknown(): self
    {
        return new self();
    }

    /** Fills this result's unknown fields from another one. */
    public function mergedWith(self $other): self
    {
        return new self(
            $this->countryCode ?? $other->countryCode,
            $this->country ?? $other->country,
            $this->region ?? $other->region,
            $this->city ?? $other->city,
            $this->latitude ?? $other->latitude,
            $this->longitude ?? $other->longitude,
            $this->asn ?? $other->asn,
            $this->networkOrg ?? $other->networkOrg,
            $this->networkType !== NetworkType::UNKNOWN ? $this->networkType : $other->networkType,
        );
    }
}
