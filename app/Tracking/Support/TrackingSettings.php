<?php

namespace App\Tracking\Support;

use App\Tracking\Contracts\OptionStore;
use DateTimeZone;

/**
 * Typed access to the owner-editable settings (Admin > Tracking > Settings).
 * Every value has a safe default, so a fresh install tracks sensibly with
 * nothing saved.
 */
final class TrackingSettings
{
    public const CONSENT_EU = 'eu_consent';
    public const CONSENT_EVERYWHERE = 'consent_everywhere';
    public const CONSENT_NONE = 'everyone';

    public const CONSENT_MODES = [self::CONSENT_EU, self::CONSENT_EVERYWHERE, self::CONSENT_NONE];

    private const KEY_ENABLED = 'tracking_status';
    private const KEY_EXCLUDED_IPS = 'tracking_excluded_ips';
    private const KEY_CONSENT_MODE = 'tracking_consent_mode';
    private const KEY_IP_RETENTION = 'tracking_ip_retention_days';
    private const KEY_DATA_RETENTION = 'tracking_data_retention_days';
    private const KEY_TIMEZONE = 'tracking_timezone';
    private const KEY_APP_HOST = 'tracking_app_host';
    private const KEY_APP_SECRET = 'tracking_app_secret';
    private const KEY_LAST_PRUNE = 'tracking_last_prune_at';
    private const KEY_GEO_UPDATED = 'tracking_geo_updated_at';

    public function __construct(private OptionStore $store)
    {
    }

    public function enabled(): bool
    {
        return $this->store->get(self::KEY_ENABLED, '1') === '1';
    }

    /** @return list<string> IPs and CIDR ranges, one per line in the admin. */
    public function excludedIps(): array
    {
        $raw = (string) $this->store->get(self::KEY_EXCLUDED_IPS, '');
        $lines = preg_split('/[\s,]+/', $raw) ?: [];

        return array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));
    }

    public function consentMode(): string
    {
        $mode = (string) $this->store->get(self::KEY_CONSENT_MODE, self::CONSENT_EU);

        return in_array($mode, self::CONSENT_MODES, true) ? $mode : self::CONSENT_EU;
    }

    /** Full IP addresses are masked after this many days. */
    public function ipRetentionDays(): int
    {
        return $this->boundedInt(self::KEY_IP_RETENTION, 30, 0, 3650);
    }

    /** Visits, page views and clicks are deleted after this many days. */
    public function dataRetentionDays(): int
    {
        return $this->boundedInt(self::KEY_DATA_RETENTION, 395, 30, 3650);
    }

    /** Reports group days and show times in this zone. */
    public function timezone(): DateTimeZone
    {
        $name = (string) $this->store->get(self::KEY_TIMEZONE, 'Asia/Kolkata');

        try {
            return new DateTimeZone($name);
        } catch (\Exception) {
            return new DateTimeZone('UTC');
        }
    }

    /** Host of the app whose Sign up / Log in links get the visitor id. */
    public function appHost(): string
    {
        $host = strtolower(trim((string) $this->store->get(self::KEY_APP_HOST, 'app.go4database.com')));

        return $host !== '' ? $host : 'app.go4database.com';
    }

    /** Shared secret the app signs its events with; null until generated. */
    public function appSecret(): ?string
    {
        $secret = $this->store->get(self::KEY_APP_SECRET);

        return $secret !== null && strlen($secret) >= 32 ? $secret : null;
    }

    public function lastPruneAt(): ?string
    {
        return $this->store->get(self::KEY_LAST_PRUNE);
    }

    public function geoUpdatedAt(): ?string
    {
        return $this->store->get(self::KEY_GEO_UPDATED);
    }

    /**
     * Saves the editable settings. Values arrive already validated by
     * the settings form request.
     *
     * @param array{enabled: bool, excluded_ips: string, consent_mode: string,
     *              ip_retention_days: int, data_retention_days: int,
     *              timezone: string, app_host: string} $values
     */
    public function update(array $values): void
    {
        $this->store->set(self::KEY_ENABLED, $values['enabled'] ? '1' : '0');
        $this->store->set(self::KEY_EXCLUDED_IPS, trim($values['excluded_ips']));
        $this->store->set(self::KEY_CONSENT_MODE, $values['consent_mode']);
        $this->store->set(self::KEY_IP_RETENTION, (string) $values['ip_retention_days']);
        $this->store->set(self::KEY_DATA_RETENTION, (string) $values['data_retention_days']);
        $this->store->set(self::KEY_TIMEZONE, $values['timezone']);
        $this->store->set(self::KEY_APP_HOST, strtolower(trim($values['app_host'])));
    }

    public function setAppSecret(string $secret): void
    {
        $this->store->set(self::KEY_APP_SECRET, $secret);
    }

    public function markPruned(string $at): void
    {
        $this->store->set(self::KEY_LAST_PRUNE, $at);
    }

    public function markGeoUpdated(string $at): void
    {
        $this->store->set(self::KEY_GEO_UPDATED, $at);
    }

    private function boundedInt(string $key, int $default, int $min, int $max): int
    {
        $value = $this->store->get($key);
        if ($value === null || !is_numeric($value)) {
            return $default;
        }

        return max($min, min($max, (int) $value));
    }
}
