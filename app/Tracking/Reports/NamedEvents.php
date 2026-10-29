<?php

namespace App\Tracking\Reports;

use App\Tracking\Models\Event;

/** The site's named events (view_email, search, ask_ai...) and app steps, with counts. */
final class NamedEvents implements Report
{
    public function key(): string
    {
        return 'named-events';
    }

    /** @return list<array{type: string, name: string, count: int, visitors: int}> */
    public function run(ReportContext $context): array
    {
        return $context->events()
            ->whereIn('e.type', [Event::TYPE_CUSTOM, Event::TYPE_APP])
            ->groupBy('e.type', 'e.name')
            ->selectRaw('e.type, e.name, COUNT(*) as count, COUNT(DISTINCT e.visitor_id) as visitors')
            ->orderByDesc('count')
            ->limit(100)
            ->get()
            ->map(fn ($r) => ['type' => $r->type, 'name' => $r->name, 'count' => (int) $r->count, 'visitors' => (int) $r->visitors])
            ->all();
    }
}
