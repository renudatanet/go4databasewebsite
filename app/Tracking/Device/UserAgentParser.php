<?php

namespace App\Tracking\Device;

use App\Tracking\Contracts\DeviceParser;
use App\Tracking\Data\DeviceInfo;

/**
 * Pattern-based user agent parsing for the browsers and systems that make up
 * nearly all real traffic. Order matters: several browsers also claim to be
 * Chrome and Safari, so the specific ones are tested first.
 */
final class UserAgentParser implements DeviceParser
{
    /** @var array<string, string> browser name => pattern capturing the version */
    private const BROWSERS = [
        'Edge' => '#Edg(?:e|A|iOS)?/([\d.]+)#',
        'Opera' => '#(?:OPR|Opera)/([\d.]+)#',
        'Samsung Internet' => '#SamsungBrowser/([\d.]+)#',
        'UC Browser' => '#UCBrowser/([\d.]+)#',
        'Yandex' => '#YaBrowser/([\d.]+)#',
        'Vivaldi' => '#Vivaldi/([\d.]+)#',
        'Firefox' => '#(?:Firefox|FxiOS)/([\d.]+)#',
        'Chrome' => '#(?:Chrome|CriOS)/([\d.]+)#',
        'Safari' => '#Version/([\d.]+).*Safari/#',
        'Internet Explorer' => '#(?:MSIE |Trident/.*rv:)([\d.]+)#',
    ];

    public function parse(string $userAgent, bool $touch = false): DeviceInfo
    {
        [$os, $osVersion] = $this->operatingSystem($userAgent);
        [$browser, $browserVersion] = $this->browser($userAgent);

        // iPadOS reports itself as a Mac; only the touch screen gives it away.
        if ($os === 'macOS' && $touch) {
            $os = 'iPadOS';
            $osVersion = null;
        }

        return new DeviceInfo(
            type: $this->type($userAgent, $os),
            browser: $browser,
            browserVersion: $browserVersion,
            os: $os,
            osVersion: $osVersion,
        );
    }

    /** @return array{0: ?string, 1: ?string} */
    private function browser(string $ua): array
    {
        foreach (self::BROWSERS as $name => $pattern) {
            if (preg_match($pattern, $ua, $match)) {
                return [$name, $this->majorMinor($match[1])];
            }
        }

        return [null, null];
    }

    /** @return array{0: ?string, 1: ?string} */
    private function operatingSystem(string $ua): array
    {
        if (preg_match('#(?:iPhone|iPod).*? OS ([\d_]+)#', $ua, $m)) {
            return ['iOS', $this->majorMinor(str_replace('_', '.', $m[1]))];
        }
        if (preg_match('#iPad.*? OS ([\d_]+)#', $ua, $m)) {
            return ['iPadOS', $this->majorMinor(str_replace('_', '.', $m[1]))];
        }
        if (preg_match('#Android ?([\d.]+)?#', $ua, $m)) {
            return ['Android', isset($m[1]) ? $this->majorMinor($m[1]) : null];
        }
        if (preg_match('#Windows NT ([\d.]+)#', $ua, $m)) {
            $versions = ['10.0' => '10/11', '6.3' => '8.1', '6.2' => '8', '6.1' => '7'];

            return ['Windows', $versions[$m[1]] ?? $m[1]];
        }
        if (str_contains($ua, 'CrOS')) {
            return ['ChromeOS', null];
        }
        if (preg_match('#Mac OS X ([\d_.]+)#', $ua, $m)) {
            return ['macOS', $this->majorMinor(str_replace('_', '.', $m[1]))];
        }
        if (str_contains($ua, 'Linux')) {
            return ['Linux', null];
        }

        return [null, null];
    }

    private function type(string $ua, ?string $os): string
    {
        if ($os === 'iPadOS' || preg_match('#Tablet|Kindle|Silk/|PlayBook#i', $ua)) {
            return DeviceInfo::TABLET;
        }
        if ($os === 'Android') {
            return str_contains($ua, 'Mobile') ? DeviceInfo::MOBILE : DeviceInfo::TABLET;
        }
        if ($os === 'iOS' || preg_match('#Mobi|Opera Mini|IEMobile|BlackBerry#i', $ua)) {
            return DeviceInfo::MOBILE;
        }

        return DeviceInfo::DESKTOP;
    }

    private function majorMinor(string $version): string
    {
        $parts = explode('.', $version);

        return isset($parts[1]) && $parts[1] !== '0' ? $parts[0] . '.' . $parts[1] : $parts[0];
    }
}
