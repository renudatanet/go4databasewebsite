<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RemoveTrailingSlash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $path = $request->getPathInfo();

        if ($path !== '/' && substr($path, -1) === '/') {
            $query = $request->getQueryString();

            $url = rtrim($request->url(), '/');

            if ($query) {
                $url .= '?' . $query;
            }

            return redirect($url, 301);
        }

        return $next($request);
    }
}
