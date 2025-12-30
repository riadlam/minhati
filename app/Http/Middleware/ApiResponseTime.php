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
            
            // Check if data is an array
            if (is_array($data)) {
                // Check if it's a sequential array (list) or associative array (object)
                if (array_keys($data) === range(0, count($data) - 1)) {
                    // Sequential array (list) - wrap it in a data property
                    $response->setData([
                        'data' => $data,
                        'response_time_ms' => $responseTime
                    ]);
                } else {
                    // Associative array (object) - add response_time directly
                    $data['response_time_ms'] = $responseTime;
                    $response->setData($data);
                }
            }
        }
        
        // Also add as header
        $response->headers->set('X-Response-Time', $responseTime . 'ms');
        
        return $response;
    }
}

