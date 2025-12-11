<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        
        if (!$apiKey) {
            return response()->json([
                'error' => 'API key is required',
                'message' => 'Please provide X-API-KEY header'
            ], 401);
        }
        
        // Verify API key against environment variable
        $validApiKey = env('API_KEY_SALT');
        
        if (!$validApiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'The provided API key is not valid'
            ], 403);
        }
        
        return $next($request);
    }
}
