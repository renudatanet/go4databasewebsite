<?php

namespace App\Tracking\Http\Controllers;

use App\Tracking\Ingestion\Guards\HitGate;
use App\Tracking\Ingestion\HitRecorder;
use App\Tracking\Ingestion\PayloadParser;
use App\Tracking\Ingestion\RecordResult;
use App\Tracking\Ingestion\RequestContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Throwable;

/**
 * POST /t/collect — receives the tracker's events. Runs outside the "web"
 * middleware group: no session, no CSRF, no cookie encryption, so each hit
 * costs a few queries and nothing more.
 *
 * It answers 202 with the tracking mode when a hit is recorded, and 204 for
 * anything refused or malformed, without saying why.
 */
final class CollectController
{
    /** @param array{visitor: string, visitor_days: int, opt_out: string} $cookies */
    public function __construct(
        private PayloadParser $parser,
        private HitGate $gate,
        private HitRecorder $recorder,
        private array $cookies,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $this->parser->parse((string) $request->getContent());
        if ($payload === null) {
            return $this->ignored();
        }

        $context = RequestContext::fromRequest($request, $this->cookies['opt_out']);
        if (!$this->gate->allows($payload, $context)) {
            return $this->ignored();
        }

        try {
            $result = $this->recorder->record($payload, $context);
        } catch (Throwable $e) {
            // A database hiccup loses one hit; it must never surface to the visitor.
            report($e);

            return $this->ignored();
        }

        return $this->recorded($result, $request->isSecure());
    }

    private function recorded(RecordResult $result, bool $secure): JsonResponse
    {
        $response = new JsonResponse(['mode' => $result->mode], 202);

        // Set from the server so Safari keeps it beyond the 7 days it allows
        // cookies written by scripts. Anonymous visitors get any old cookie
        // removed instead.
        $minutes = $result->isFull() ? $this->cookies['visitor_days'] * 24 * 60 : -60;
        $response->headers->setCookie(Cookie::create(
            $this->cookies['visitor'],
            $result->isFull() ? $result->visitorUuid : '',
            time() + $minutes * 60,
            '/',
            null,
            $secure,
            false, // readable by the tracker, which hands it to the app on Sign up
            false,
            Cookie::SAMESITE_LAX
        ));

        return $response;
    }

    private function ignored(): JsonResponse
    {
        return new JsonResponse(null, 204);
    }
}
