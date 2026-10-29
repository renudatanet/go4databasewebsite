<?php

namespace Tests\Unit\Tracking;

use App\Tracking\Data\DeviceInfo;
use App\Tracking\Device\PatternBotDetector;
use App\Tracking\Device\UserAgentParser;
use PHPUnit\Framework\TestCase;

class DeviceDetectionTest extends TestCase
{
    /** @dataProvider userAgents */
    public function test_reads_device_browser_and_os(string $ua, bool $touch, string $type, string $browser, string $os): void
    {
        $device = (new UserAgentParser())->parse($ua, $touch);

        $this->assertSame([$type, $browser, $os], [$device->type, $device->browser, $device->os]);
    }

    public static function userAgents(): array
    {
        return [
            'Chrome on a Mac' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', false, DeviceInfo::DESKTOP, 'Chrome', 'macOS'],
            'iPad pretending to be a Mac' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15', true, DeviceInfo::TABLET, 'Safari', 'iPadOS'],
            'Safari on an iPhone' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1', true, DeviceInfo::MOBILE, 'Safari', 'iOS'],
            'Chrome on an Android phone' => ['Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36', true, DeviceInfo::MOBILE, 'Chrome', 'Android'],
            'Android tablet' => ['Mozilla/5.0 (Linux; Android 13; SM-X710) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', true, DeviceInfo::TABLET, 'Chrome', 'Android'],
            'Edge on Windows' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Edg/130.0.0.0', false, DeviceInfo::DESKTOP, 'Edge', 'Windows'],
            'Firefox on Linux' => ['Mozilla/5.0 (X11; Linux x86_64; rv:131.0) Gecko/20100101 Firefox/131.0', false, DeviceInfo::DESKTOP, 'Firefox', 'Linux'],
            'Samsung Internet' => ['Mozilla/5.0 (Linux; Android 14; SM-S921B) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/25.0 Chrome/121.0.0.0 Mobile Safari/537.36', true, DeviceInfo::MOBILE, 'Samsung Internet', 'Android'],
        ];
    }

    /** @dataProvider bots */
    public function test_recognises_bots(string $ua): void
    {
        $this->assertTrue((new PatternBotDetector())->isBot($ua));
    }

    public static function bots(): array
    {
        return [
            'Googlebot' => ['Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'],
            'Lighthouse' => ['Mozilla/5.0 (Linux; Android 11; moto g power (2022)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36 Chrome-Lighthouse'],
            'Headless Chrome' => ['Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/130.0.0.0 Safari/537.36'],
            'curl' => ['curl/8.4.0'],
            'empty' => [''],
            'GPTBot' => ['Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.2; +https://openai.com/gptbot)'],
            'LinkedIn preview' => ['LinkedInBot/1.0 (compatible; Mozilla/5.0; Apache-HttpClient +http://www.linkedin.com)'],
        ];
    }

    public function test_does_not_mistake_people_for_bots(): void
    {
        $detector = new PatternBotDetector();

        foreach (array_column(self::userAgents(), 0) as $ua) {
            $this->assertFalse($detector->isBot($ua), $ua);
        }
        // A Cubot phone: "bot" inside the brand name.
        $this->assertFalse($detector->isBot('Mozilla/5.0 (Linux; Android 12; Cubot X30) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36'));
        // The Pinterest app's own browser is a person.
        $this->assertFalse($detector->isBot('Mozilla/5.0 (iPhone; CPU iPhone OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [Pinterest/iOS]'));
    }
}
