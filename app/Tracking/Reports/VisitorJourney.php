<?php

namespace App\Tracking\Reports;

use App\Tracking\Models\Event;
use App\Tracking\Models\Identity;
use App\Tracking\Models\PageView;
use App\Tracking\Models\Session;
use App\Tracking\Models\Visitor;
use Illuminate\Support\Collection;

/**
 * Everything known about one visitor, as a timeline: each visit with its
 * page views, clicks, site events, app steps and the moments they gave
 * their name, in the order they happened. Never cached.
 */
final class VisitorJourney
{
    private const MAX_SESSIONS = 100;

    /** @return array{visitor: Visitor, sessions: list<array>, identities: Collection, other_devices: Collection, totals: array}|null */
    public function run(int $visitorId): ?array
    {
        $visitor = Visitor::find($visitorId);
        if ($visitor === null) {
            return null;
        }

        $sessions = Session::where('visitor_id', $visitor->id)
            ->with(['landingPage:id,path,title'])
            ->orderByDesc('started_at')
            ->limit(self::MAX_SESSIONS)
            ->get();
        $sessionIds = $sessions->pluck('id');

        $pageViews = PageView::whereIn('session_id', $sessionIds)
            ->with('page:id,path,title')
            ->orderBy('viewed_at')
            ->get()
            ->groupBy('session_id');

        $events = Event::whereIn('session_id', $sessionIds)
            ->with(['link:id,href,kind,label', 'page:id,path'])
            ->orderBy('occurred_at')
            ->get()
            ->groupBy('session_id');

        $identities = Identity::where('visitor_id', $visitor->id)->orderBy('occurred_at')->get();

        $timeline = $sessions->map(fn (Session $session) => [
            'session' => $session,
            'steps' => $this->steps(
                $pageViews->get($session->id, collect()),
                $events->get($session->id, collect()),
                $identities->filter(fn (Identity $i) => $i->occurred_at !== null
                    && $i->occurred_at->gte($session->started_at)
                    && $i->occurred_at->lte($session->last_activity_at->copy()->addMinutes(30)))
            ),
        ])->all();

        return [
            'visitor' => $visitor,
            'sessions' => $timeline,
            'identities' => $identities,
            'other_devices' => $visitor->email
                ? Visitor::where('email', $visitor->email)->where('id', '!=', $visitor->id)->orderByDesc('last_seen_at')->limit(10)->get()
                : collect(),
            'totals' => [
                'sessions' => (int) $visitor->sessions_count,
                'page_views' => (int) $visitor->page_views_count,
                'engaged_ms' => (int) Session::where('visitor_id', $visitor->id)->sum('engaged_ms'),
                'clicks' => (int) Session::where('visitor_id', $visitor->id)->sum('clicks'),
            ],
        ];
    }

    /**
     * @return list<array{kind: string, at: \DateTimeInterface, item: object}>
     */
    private function steps(Collection $pageViews, Collection $events, Collection $identities): array
    {
        $steps = [];
        foreach ($pageViews as $pageView) {
            $steps[] = ['kind' => 'pageview', 'at' => $pageView->viewed_at, 'item' => $pageView];
        }
        foreach ($events as $event) {
            $steps[] = ['kind' => $event->type, 'at' => $event->occurred_at, 'item' => $event];
        }
        foreach ($identities as $identity) {
            $steps[] = ['kind' => 'identity', 'at' => $identity->occurred_at, 'item' => $identity];
        }

        usort($steps, fn ($a, $b) => $a['at'] <=> $b['at']);

        return $steps;
    }
}
