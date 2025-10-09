<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use stdClass;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('api-key')) {
            if ($request->header('api-key') == config('app.api_key')) {
                return $next($request);
            }
            return response()->json([
                'code' => 401,
                'message' => 'Invalid API key',
                'data' => new stdClass(),
            ], 400);
        }
        
        return response()->json([
            'code' => 401,
            'message' => 'API key not found',
            'data' => new stdClass(),
        ], 400);
    }
}