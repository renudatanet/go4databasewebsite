<?php

namespace App\Tracking\Data;

/** What kind of network a visit came from. */
final class NetworkType
{
    /** A company's own network: the organisation is probably the visitor's employer. */
    public const BUSINESS = 'business';
    /** A home broadband or mobile provider. */
    public const ISP = 'isp';
    /** A cloud or hosting provider: servers, VPNs, proxies. */
    public const HOSTING = 'hosting';
    public const UNKNOWN = 'unknown';

    public const LABELS = [
        self::BUSINESS => 'Business network',
        self::ISP => 'Internet provider',
        self::HOSTING => 'Hosting / VPN',
        self::UNKNOWN => 'Unknown',
    ];
}
