<?php

namespace App\Tracking\Identity;

use App\Tracking\Models\Identity;
use App\Tracking\Models\Visitor;
use Illuminate\Support\Facades\DB;

/**
 * Puts a name on a visitor. The newest details win for the profile, every
 * occasion is kept as its own identity row, and the time they first became
 * known is never moved.
 */
final class IdentityRecorder
{
    /** @return bool false when there is no such (non-anonymous) visitor */
    public function record(VisitorIdentified $identified): bool
    {
        if ($identified->details->isEmpty()) {
            return false;
        }

        return DB::transaction(function () use ($identified) {
            /** @var Visitor|null $visitor */
            $visitor = Visitor::where('uuid', $identified->visitorUuid)->lockForUpdate()->first();
            if ($visitor === null || $visitor->is_anonymous) {
                return false;
            }

            $details = $identified->details;

            Identity::create([
                'visitor_id' => $visitor->id,
                'source' => $identified->source,
                'name' => $details->name,
                'email' => $details->email,
                'phone' => $details->phone,
                'company' => $details->company,
                'reference' => $details->reference,
                'occurred_at' => $identified->occurredAt,
            ]);

            $visitor->name = $details->name ?? $visitor->name;
            $visitor->email = $details->email ?? $visitor->email;
            $visitor->phone = $details->phone ?? $visitor->phone;
            $visitor->company = $details->company ?? $visitor->company;
            $visitor->identified_via = $identified->source;
            $visitor->identified_at ??= $identified->occurredAt;
            if ($details->reference !== null && str_starts_with($identified->source, 'app_')) {
                $visitor->app_user_id = $details->reference;
            }
            $visitor->save();

            return true;
        });
    }
}
