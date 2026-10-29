<?php

namespace App\Tracking\Ingestion;

/**
 * Names a click by where its link goes. The browser reports the destination;
 * the kind is always decided here so it can't be spoofed into the reports.
 */
final class LinkClassifier
{
    public const INTERNAL = 'internal';
    public const OUTBOUND = 'outbound';
    public const APP = 'app';
    public const EMAIL = 'email';
    public const PHONE = 'phone';
    public const DOWNLOAD = 'download';
    public const BUTTON = 'button';

    public const KIND_LABELS = [
        self::APP => 'To the app',
        self::INTERNAL => 'Internal',
        self::OUTBOUND => 'Outbound',
        self::EMAIL => 'Email',
        self::PHONE => 'Phone',
        self::DOWNLOAD => 'Download',
        self::BUTTON => 'Button',
    ];

    /** Event names recorded for clicks, by kind; app links split by intent. */
    public const SIGNUP_CLICK = 'signup_click';
    public const LOGIN_CLICK = 'login_click';

    private const DOWNLOAD_EXTENSIONS = ['pdf', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'txt'];

    public function __construct(private string $siteHost, private string $appHost)
    {
        $this->siteHost = self::bareHost($siteHost);
        $this->appHost = self::bareHost($appHost);
    }

    /** @param string|null $href the sanitised destination, null for a button */
    public function kind(?string $href): string
    {
        if ($href === null || $href === '') {
            return self::BUTTON;
        }
        if (str_starts_with($href, 'mailto:')) {
            return self::EMAIL;
        }
        if (str_starts_with($href, 'tel:')) {
            return self::PHONE;
        }

        $host = self::bareHost((string) parse_url($href, PHP_URL_HOST));
        $extension = strtolower(pathinfo((string) parse_url($href, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (in_array($extension, self::DOWNLOAD_EXTENSIONS, true)) {
            return self::DOWNLOAD;
        }
        if ($host === $this->appHost) {
            return self::APP;
        }
        if ($host === '' || $host === $this->siteHost) {
            return self::INTERNAL;
        }

        return self::OUTBOUND;
    }

    /** The event name a click is stored under, e.g. "signup_click". */
    public function eventName(string $kind, ?string $href): string
    {
        if ($kind === self::APP) {
            $path = strtolower((string) parse_url((string) $href, PHP_URL_PATH));
            if (preg_match('#/(register|sign-?up)#', $path)) {
                return self::SIGNUP_CLICK;
            }
            if (preg_match('#/(login|sign-?in)#', $path)) {
                return self::LOGIN_CLICK;
            }
        }

        return $kind . '_click';
    }

    private static function bareHost(string $host): string
    {
        return preg_replace('/^www\./', '', strtolower(trim($host)));
    }
}
