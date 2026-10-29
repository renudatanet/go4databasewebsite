<?php

namespace App\Tracking\Http\Requests;

use App\Tracking\Support\IpAddress;
use App\Tracking\Support\TrackingSettings;
use DateTimeZone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class UpdateTrackingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // the route is already behind the admin login and the Tracking permission
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'enabled' => ['nullable', 'boolean'],
            'excluded_ips' => ['nullable', 'string', 'max:5000'],
            'consent_mode' => ['required', 'in:' . implode(',', TrackingSettings::CONSENT_MODES)],
            'ip_retention_days' => ['required', 'integer', 'between:0,3650'],
            'data_retention_days' => ['required', 'integer', 'between:30,3650'],
            'timezone' => ['required', 'string', 'in:' . implode(',', DateTimeZone::listIdentifiers())],
            'app_host' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9.-]+\.[a-z]{2,}$/i'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $invalid = array_filter($this->excludedIpLines(), fn ($line) => !self::isIpOrRange($line));
            if ($invalid !== []) {
                $validator->errors()->add('excluded_ips', __('These are not IP addresses or ranges: :list', ['list' => implode(', ', array_slice($invalid, 0, 5))]));
            }
        });
    }

    /** @return array{enabled: bool, excluded_ips: string, consent_mode: string, ip_retention_days: int, data_retention_days: int, timezone: string, app_host: string} */
    public function settings(): array
    {
        return [
            'enabled' => $this->boolean('enabled'),
            'excluded_ips' => implode("\n", $this->excludedIpLines()),
            'consent_mode' => (string) $this->input('consent_mode'),
            'ip_retention_days' => (int) $this->input('ip_retention_days'),
            'data_retention_days' => (int) $this->input('data_retention_days'),
            'timezone' => (string) $this->input('timezone'),
            'app_host' => (string) $this->input('app_host'),
        ];
    }

    /** @return list<string> */
    private function excludedIpLines(): array
    {
        $lines = preg_split('/[\s,]+/', (string) $this->input('excluded_ips', '')) ?: [];

        return array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));
    }

    private static function isIpOrRange(string $value): bool
    {
        if (!str_contains($value, '/')) {
            return IpAddress::isValid($value);
        }

        [$ip, $bits] = explode('/', $value, 2);
        $max = str_contains($ip, ':') ? 128 : 32;

        return IpAddress::isValid($ip) && ctype_digit($bits) && (int) $bits <= $max;
    }
}
