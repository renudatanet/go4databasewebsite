<?php

namespace App\Tracking\Geo;

use App\Tracking\Data\NetworkType;

/**
 * Guesses what kind of network an organisation name belongs to. Hosting is
 * checked first (a cloud provider is never anyone's employer), then
 * consumer ISPs; anything else is taken to be a business network.
 */
final class NetworkClassifier
{
    private const HOSTING = [
        'amazon', 'aws', 'google llc', 'google cloud', 'microsoft', 'azure', 'digitalocean',
        'linode', 'akamai', 'ovh', 'hetzner', 'contabo', 'vultr', 'choopa', 'oracle', 'alibaba',
        'tencent', 'hostinger', 'godaddy', 'cloudflare', 'fastly', 'leaseweb', 'scaleway',
        'm247', 'datacamp', 'hosting', 'data center', 'datacenter', 'datacentre', 'server',
        'colocation', 'cdn', 'vpn', 'proxy', 'ionos', 'hostgator', 'bluehost', 'namecheap',
        'rackspace', 'packet', 'equinix', 'zscaler', 'cloud',
    ];

    private const ISP = [
        'telecom', 'telecommunication', 'communications', 'broadband', 'cable', 'wireless',
        'mobile', 'cellular', 'internet service', 'telekom', 'telefonica', 'vodafone', 'airtel',
        'jio', 'reliance', 'bsnl', 'mtnl', 'comcast', 'charter', 'spectrum', 'verizon', 'at&t',
        't-mobile', 'sprint', 'cox ', 'frontier', 'centurylink', 'british telecom', 'sky ',
        'orange', 'telstra', 'optus', 'rogers', 'bell canada', 'shaw', 'telus', 'virgin',
        'china unicom', 'chinanet', 'act fibernet', 'hathway', 'tata tele', 'excitel', 'fiber',
        'fibre', 'dsl', 'telenet', 'kpn', 'ziggo', 'swisscom', 'starlink', 'spacex', 'etisalat',
        'du ', 'stc', 'ooredoo', 'singtel', 'starhub', 'pldt', 'globe', 'idea', 'vi india',
        'you broadband', 'ptcl', 'grameenphone', 'telmex', 'claro', 'tim ', 'wind tre', 'free sas',
        'bouygues', 'sfr', 'deutsche telekom', '1&1', 'o2', 'ee limited', 'talktalk', 'plusnet',
        'cablevision', 'altice', 'mediacom', 'windstream', 'suddenlink', 'rcn', 'wow!',
    ];

    public function classify(?string $organisation): string
    {
        if ($organisation === null || trim($organisation) === '') {
            return NetworkType::UNKNOWN;
        }

        $name = ' ' . strtolower($organisation) . ' ';

        if ($this->containsAny($name, self::HOSTING)) {
            return NetworkType::HOSTING;
        }
        if ($this->containsAny($name, self::ISP)) {
            return NetworkType::ISP;
        }

        return NetworkType::BUSINESS;
    }

    /** @param list<string> $needles */
    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
