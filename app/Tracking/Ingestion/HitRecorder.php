<?php

namespace App\Tracking\Ingestion;

use App\Tracking\Contracts\GeoLocator;
use App\Tracking\Ingestion\Enrichment\SessionDraft;
use App\Tracking\Models\Event;
use App\Tracking\Models\Page;
use App\Tracking\Models\PageView;
use App\Tracking\Models\Session;
use App\Tracking\Models\Visitor;
use App\Tracking\Support\UrlSanitizer;
use Carbon\CarbonImmutable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Stores one collect request: finds the visitor and their current visit
 * (starting a new one after 30 quiet minutes), then records each event.
 *
 * Everything for one request happens in one transaction, with the visitor
 * row locked, so two requests from the same browser arriving together can't
 * both start a visit. Replays are harmless: page views are keyed by the id
 * the browser gave them.
 */
final class HitRecorder
{
    /** @param list<class-string|object> $enrichmentSteps */
    public function __construct(
        private GeoLocator $geo,
        private ConsentPolicy $consent,
        private AnonymousId $anonymousId,
        private PageRegistry $pages,
        private LinkRegistry $links,
        private LinkClassifier $linkClassifier,
        private Pipeline $pipeline,
        private array $enrichmentSteps,
        private int $sessionTimeoutMinutes,
    ) {
    }

    public function record(CollectPayload $payload, RequestContext $context): RecordResult
    {
        $geo = $this->geo->locate($context->ip);
        $mode = $this->consent->decide($geo->countryCode, $payload->consent);
        $now = $context->receivedAt;

        $visitorUuid = $mode === ConsentPolicy::ANONYMOUS
            ? $this->anonymousId->for($context->ip, $context->userAgent, $now)
            : ($payload->visitorUuid ?? (string) Str::uuid());

        DB::transaction(function () use ($payload, $context, $geo, $mode, $now, $visitorUuid) {
            $visitor = $this->lockVisitor($visitorUuid, $mode, $now);
            $session = $this->session($visitor, new SessionDraft($payload, $context, $geo, $mode), $now);
            if ($session === null) {
                return;
            }

            foreach ($payload->events as $event) {
                switch ($event['type']) {
                    case CollectPayload::PAGEVIEW:
                        $this->recordPageview($visitor, $session, $payload, $now);
                        break;
                    case CollectPayload::ENGAGEMENT:
                        $this->recordEngagement($visitor, $session, $payload, $event);
                        break;
                    case CollectPayload::CLICK:
                        $this->recordClick($visitor, $session, $payload, $event, $now);
                        break;
                    case CollectPayload::EVENT:
                        $this->recordEvent($visitor, $session, $payload, $event, $now);
                        break;
                }
            }

            $session->last_activity_at = $now;
            $session->save();

            $visitor->last_seen_at = $now;
            $visitor->save();
        }, 3);

        return new RecordResult($mode, $visitorUuid);
    }

    private function lockVisitor(string $uuid, string $mode, CarbonImmutable $now): Visitor
    {
        DB::table('tracking_visitors')->insertOrIgnore([
            'uuid' => $uuid,
            'is_anonymous' => $mode === ConsentPolicy::ANONYMOUS,
            'first_seen_at' => $now,
            'last_seen_at' => $now,
        ]);

        /** @var Visitor $visitor */
        $visitor = Visitor::where('uuid', $uuid)->lockForUpdate()->firstOrFail();

        return $visitor;
    }

    /**
     * The visit this request belongs to. Only a page view starts a new visit:
     * a time report or click arriving after the visit went quiet (a tab left
     * open, then closed) is added to the visit its page was viewed in.
     */
    private function session(Visitor $visitor, SessionDraft $draft, CarbonImmutable $now): ?Session
    {
        $active = Session::where('visitor_id', $visitor->id)
            ->where('last_activity_at', '>=', $now->subMinutes($this->sessionTimeoutMinutes))
            ->orderByDesc('last_activity_at')
            ->first();
        if ($active !== null) {
            return $active;
        }

        if ($draft->payload->hasPageview()) {
            return $this->startSession($visitor, $draft, $now);
        }

        $pageView = $this->pageView($visitor, $draft->payload);

        return $pageView !== null ? Session::find($pageView->session_id) : null;
    }

    private function startSession(Visitor $visitor, SessionDraft $draft, CarbonImmutable $now): Session
    {
        /** @var SessionDraft $draft */
        $draft = $this->pipeline->send($draft)->through($this->enrichmentSteps)->thenReturn();

        $session = Session::create($draft->attributes + [
            'visitor_id' => $visitor->id,
            'started_at' => $now,
            'last_activity_at' => $now,
            'is_anonymous' => $draft->isAnonymous(),
            'page_views' => 0,
            'clicks' => 0,
            'events_count' => 0,
            'engaged_ms' => 0,
        ]);

        $visitor->sessions_count++;
        if ($visitor->first_channel === null) {
            $visitor->first_channel = $session->channel;
            $visitor->first_source = $session->source;
        }

        return $session;
    }

    private function recordPageview(Visitor $visitor, Session $session, CollectPayload $payload, CarbonImmutable $now): void
    {
        $page = $this->pages->touch(UrlSanitizer::pagePath($payload->url), $payload->title, $now);
        $isEntrance = $session->page_views === 0;

        $inserted = DB::table('tracking_page_views')->insertOrIgnore([
            'uuid' => $payload->pageViewUuid,
            'session_id' => $session->id,
            'visitor_id' => $visitor->id,
            'page_id' => $page->id,
            'previous_page_id' => $isEntrance ? null : $session->exit_page_id,
            'viewed_at' => $now,
            'is_entrance' => $isEntrance,
        ]);
        if ($inserted === 0) {
            return; // the same page view sent twice
        }

        $session->page_views++;
        $session->exit_page_id = $page->id;
        if ($isEntrance) {
            $session->landing_page_id = $page->id;
        }

        $visitor->page_views_count++;
        $visitor->first_page_id ??= $page->id;
    }

    /** @param array<string, mixed> $event */
    private function recordEngagement(Visitor $visitor, Session $session, CollectPayload $payload, array $event): void
    {
        $pageView = $this->pageView($visitor, $payload);
        if ($pageView === null) {
            return;
        }

        // The browser reports running totals, so only growth is added: a
        // repeated or out-of-order report changes nothing.
        $added = max(0, $event['engaged_ms'] - $pageView->engaged_ms);
        $pageView->engaged_ms = max($pageView->engaged_ms, $event['engaged_ms']);
        $pageView->max_scroll = max($pageView->max_scroll, $event['scroll']);
        $pageView->save();

        if ((int) $pageView->session_id === (int) $session->id) {
            $session->engaged_ms += $added;
        } elseif ($added > 0) {
            Session::whereKey($pageView->session_id)->increment('engaged_ms', $added);
        }
    }

    /** @param array<string, mixed> $event */
    private function recordClick(Visitor $visitor, Session $session, CollectPayload $payload, array $event, CarbonImmutable $now): void
    {
        $href = $event['href'] !== null ? UrlSanitizer::linkHref($event['href'], $payload->url) : null;
        $kind = $this->linkClassifier->kind($href);
        $link = $this->links->resolve($href, $event['label'], $kind);

        $this->insertEvent($visitor, $session, $payload, [
            'type' => Event::TYPE_CLICK,
            'name' => $this->linkClassifier->eventName($kind, $href),
            'link_id' => $link->id,
            'label' => $event['label'],
            'section' => $event['section'],
            'occurred_at' => $now,
        ]);

        $session->clicks++;
        $session->events_count++;
    }

    /** @param array<string, mixed> $event */
    private function recordEvent(Visitor $visitor, Session $session, CollectPayload $payload, array $event, CarbonImmutable $now): void
    {
        $this->insertEvent($visitor, $session, $payload, [
            'type' => Event::TYPE_CUSTOM,
            'name' => $event['name'],
            'properties' => $event['properties'] !== [] ? json_encode($event['properties']) : null,
            'occurred_at' => $now,
        ]);

        $session->events_count++;
    }

    /** @param array<string, mixed> $fields */
    private function insertEvent(Visitor $visitor, Session $session, CollectPayload $payload, array $fields): void
    {
        $pageView = $this->pageView($visitor, $payload);
        $pageId = $pageView?->page_id ?? $this->pages->find(UrlSanitizer::pagePath($payload->url))?->id;

        DB::table('tracking_events')->insert($fields + [
            'session_id' => $session->id,
            'visitor_id' => $visitor->id,
            'page_view_id' => $pageView?->id,
            'page_id' => $pageId,
        ]);
    }

    /** The page view this request belongs to, only if it is this visitor's. */
    private function pageView(Visitor $visitor, CollectPayload $payload): ?PageView
    {
        return PageView::where('uuid', $payload->pageViewUuid)
            ->where('visitor_id', $visitor->id)
            ->first();
    }
}
