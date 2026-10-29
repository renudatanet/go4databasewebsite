<?php

namespace App\Tracking\Http\Middleware;

use App\Tracking\Identity\FormIdentityExtractor;
use App\Tracking\Identity\VisitorIdentified;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Put on a form's route (`tracking.identify:contact_form`). After the form's
 * own controller has handled a submission successfully, the name and email
 * it contained are attached to the visitor who sent it. The controller is
 * not touched and never knows.
 */
final class CaptureIdentity
{
    public function __construct(private FormIdentityExtractor $extractor, private string $visitorCookie)
    {
    }

    public function handle(Request $request, Closure $next, string $source): Response
    {
        $response = $next($request);

        try {
            $visitorUuid = $request->cookies->get($this->visitorCookie);
            if (is_string($visitorUuid) && $visitorUuid !== '' && $this->succeeded($request, $response)) {
                $details = $this->extractor->extract($request->except(['password', 'password_confirmation', 'captcha_token']));
                if (!$details->isEmpty()) {
                    event(new VisitorIdentified(strtolower($visitorUuid), $source, $details, CarbonImmutable::now('UTC')));
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        return $response;
    }

    /**
     * The site's forms answer in three ways: JSON with "status": "200" (the
     * contact form), a redirect back with a success or danger message (the
     * quote form), or a plain JSON message (the newsletter).
     */
    private function succeeded(Request $request, Response $response): bool
    {
        if ($response->getStatusCode() >= 400) {
            return false;
        }

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            return !is_array($data) || !array_key_exists('status', $data) || (string) $data['status'] === '200';
        }

        if ($response instanceof RedirectResponse && $request->hasSession()) {
            $session = $request->session();

            return !$session->has('errors') && $session->get('type') !== 'danger';
        }

        return true;
    }
}
