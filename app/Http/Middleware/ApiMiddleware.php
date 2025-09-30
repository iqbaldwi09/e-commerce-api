<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        if ($request->is('api/payment/callback')) {
            return $next($request);
        }
        
        $accessKey = env('API_ACCESS_KEY');

        $headerAccessKey = $request->header('X-Access-Key');

        if ($headerAccessKey !== $accessKey) {
            return response()->json([
                'message' => 'Unauthorized. Invalid API keys.'
            ], 401);
        }

        return $next($request);
    }
}
