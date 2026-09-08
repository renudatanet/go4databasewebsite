<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddTrailingSlash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->getRequestUri(); // Includes path + query
        //echo $uri;die;
        // Skip root URL
        if ($uri === '/') {
            return $next($request);
        }

        // Skip URLs that already have trailing slash or are file requests (.css, .js, etc.)
        if (
            preg_match('/\/($|\?)/', $uri) || // already ends with /
            preg_match('/\.[a-zA-Z0-9]+($|\?)/', $uri) // static files
        ) {
            echo 123456;die;
            return $next($request);
        }
        // Build new URL with slash + preserve query string
        $parsed = parse_url($uri);
        //print_r($parsed);die;
        $path = $parsed['path'] ?? '';
        $query = $request->getQueryString();
        //print_r($query);die;
        if($query!='')
        {
            $newUrl = $path . '/' . ($query ? '?' . $query : '');
            //echo $newUrl;die;
            return redirect($newUrl, 301); // permanent redirect
        }
        else{
            $newUrl = $path . '/';
            //echo $newUrl;die;
            \Log::info('Redirecting to: ' . $newUrl);
            return redirect($newUrl, 301)->withHeaders(['X-Redirected-By' => 'AddTrailingSlash']);
        }
        
    }
}
