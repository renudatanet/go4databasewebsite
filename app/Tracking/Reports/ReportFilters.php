<?php

namespace App\Tracking\Reports;

use App\Tracking\Data\DeviceInfo;
use App\Tracking\Data\NetworkType;
use App\Tracking\Data\TrafficSource;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

/** Narrows every report on a screen to some visits: a country, a device, a channel... */
final class ReportFilters
{
    public function __construct(
        public ?string $country = null,
        public ?string $device = null,
        public ?string $channel = null,
        public bool $identifiedOnly = false,
        public bool $hideHosting = false,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $country = strtoupper((string) $request->query('country'));
        $device = (string) $request->query('device');
        $channel = (string) $request->query('channel');

        return new self(
            country: preg_match('/^[A-Z]{2}$/', $country) ? $country : null,
            device: in_array($device, [DeviceInfo::DESKTOP, DeviceInfo::MOBILE, DeviceInfo::TABLET], true) ? $device : null,
            channel: array_key_exists($channel, TrafficSource::LABELS) ? $channel : null,
            identifiedOnly: $request->boolean('identified'),
            hideHosting: $request->boolean('hide_hosting'),
        );
    }

    /** Applies the filters to a query that has tracking_sessions joined as $alias. */
    public function apply(Builder $query, string $alias = 's'): Builder
    {
        return $query
            ->when($this->country, fn (Builder $q) => $q->where("{$alias}.country_code", $this->country))
            ->when($this->device, fn (Builder $q) => $q->where("{$alias}.device_type", $this->device))
            ->when($this->channel, fn (Builder $q) => $q->where("{$alias}.channel", $this->channel))
            ->when($this->hideHosting, fn (Builder $q) => $q->where("{$alias}.network_type", '!=', NetworkType::HOSTING))
            ->when($this->identifiedOnly, fn (Builder $q) => $q->whereExists(
                fn (Builder $sub) => $sub->selectRaw('1')
                    ->from('tracking_visitors as fv')
                    ->whereColumn('fv.id', "{$alias}.visitor_id")
                    ->whereNotNull('fv.identified_at')
            ));
    }

    public function isEmpty(): bool
    {
        return $this->country === null && $this->device === null && $this->channel === null
            && !$this->identifiedOnly && !$this->hideHosting;
    }

    /** @return array<string, string> query-string form, for links that keep the filters */
    public function toQuery(): array
    {
        return array_filter([
            'country' => $this->country,
            'device' => $this->device,
            'channel' => $this->channel,
            'identified' => $this->identifiedOnly ? '1' : null,
            'hide_hosting' => $this->hideHosting ? '1' : null,
        ]);
    }

    public function cacheKey(): string
    {
        return md5(json_encode($this->toQuery()));
    }
}
