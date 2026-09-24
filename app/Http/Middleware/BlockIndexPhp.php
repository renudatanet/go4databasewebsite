<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockIndexPhp
{
    /**
     * Make /index.php stop resolving.
     *
     * index.php is Laravel's front controller, but it is also a real file in
     * the web root, so it can be requested directly and answers with the home
     * page. That makes a second, duplicate address for the same content, which
     * splits ranking between the two.
     *
     * The .htaccess rule that was meant to handle this only matches
     * /index.php/something, and on LiteSpeed a bare /index.php maps straight
     * to the file and is handed to PHP without the rewrite running at all.
     * Doing it here instead means it works on any web server, it is in version
     * control, and it can be tested.
     *
     * Internal rewrites do not change REQUEST_URI, so a request for /affiliate
     * still reads as "/affiliate" here even though the server runs index.php to
     * serve it. Only a URI the visitor actually wrote as /index.php matches.
     */
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->getRequestUri();

        if (preg_match('#^/index\.php(?=$|[/?])#i', $uri)) {
            // 410 Gone, not 404 and not a redirect to the home page.
            //
            // A redirect would have been the gentler option, but the owner
            // wants this address to stop resolving rather than quietly
            // forwarding. That is safe here because nothing links to it: it is
            // absent from the sitemap and from every page on the site, so
            // there is no inbound value to preserve.
            //
            // 410 rather than 404 because this is deliberate and permanent.
            // A 404 says "not found, maybe later"; 410 says "removed on
            // purpose, stop asking", and search engines drop it sooner.
            abort(410);
        }

        return $next($request);
    }
}
