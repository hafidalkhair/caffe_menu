<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $responseTime = round((microtime(true) - $start) * 1000, 2);

        $status = $response->getStatusCode();

        $level = match (true) {
            $status >= 500 => 'error',
            $status >= 400 => 'warning',
            default => 'info',
        };

        Log::channel('security')->{$level}('HTTP request', [
            'application' => 'caffe_menu',
            'event' => 'http_request',
            'method' => $request->method(),
            'url' => $request->path(),
            'status' => $status,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'response_time_ms' => $responseTime,
        ]);

        return $response;
    }
}
