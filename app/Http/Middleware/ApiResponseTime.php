<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseTime
{
    /**
     * Handle an incoming request.
     * Adds response time to all API responses
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        // Calculate response time in milliseconds
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);
        
        // Only add response time to JSON responses
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            
            // If data is an array, add response_time
            if (is_array($data)) {
                $data['response_time_ms'] = $responseTime;
                $response->setData($data);
            }
        }
        
        // Also add as header
        $response->headers->set('X-Response-Time', $responseTime . 'ms');
        
        return $response;
    }
}

