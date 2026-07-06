<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectLanguageSwitcherMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (app()->environment('testing')) {
            return $response;
        }

        // Do not inject on AJAX requests, JSON requests or if the response is redirect/binary/stream
        if ($request->ajax() || $request->wantsJson()) {
            return $response;
        }

        // Only inject for HTML responses
        $contentType = $response->headers->get('Content-Type');
        if ($contentType && str_contains($contentType, 'text/html')) {
            $content = $response->getContent();

            // Inject preconnect link tags in head to speed up Google Translate loading
            if (preg_match('/<head[^>]*>/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
                $matchStr = $matches[0][0];
                $matchOffset = $matches[0][1];
                $insertPos = $matchOffset + strlen($matchStr);
                
                $preconnect = "\n    <link rel=\"preconnect\" href=\"https://translate.google.com\">\n    <link rel=\"preconnect\" href=\"https://translate.googleapis.com\">\n    <link rel=\"preconnect\" href=\"https://www.gstatic.com\" crossorigin>\n";
                $content = substr($content, 0, $insertPos) . $preconnect . substr($content, $insertPos);
            }

            // Find the last closing </body> tag (case-insensitive)
            $pos = strripos($content, '</body>');
            if ($pos !== false) {
                // Render the switcher HTML and JS partial view
                try {
                    $injection = view('layouts.language_switcher')->render();
                    
                    // Inject right before the closing </body> tag
                    $newContent = substr($content, 0, $pos) . $injection . substr($content, $pos);
                    $response->setContent($newContent);
                } catch (\Exception $e) {
                    // Log or handle error silently so page rendering doesn't break if something goes wrong
                    logger()->error('InjectLanguageSwitcherMiddleware error: ' . $e->getMessage());
                }
            }
        }

        return $response;
    }
}
