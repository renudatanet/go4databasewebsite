<?php

namespace App\Tracking\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Whoever opens the Tracking screens is staff: their browser gets a cookie
 * that keeps it out of the data for a year, logged in or not.
 */
final class MarkBrowserAsInternal
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $name = (string) config('tracking.cookies.opt_out');

        if (!$request->cookies->has($name)) {
            $response->headers->setCookie(Cookie::create(
                $name,
                '1',
                time() + 365 * 24 * 60 * 60,
                '/',
                null,
                $request->isSecure(),
                false,
                false,
                Cookie::SAMESITE_LAX
            ));
        }

        return $response;
    }
}
