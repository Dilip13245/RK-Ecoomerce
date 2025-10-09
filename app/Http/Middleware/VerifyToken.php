<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserDevice;
use stdClass;

class VerifyToken
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if ($request->hasHeader('token')) {
                $token = $request->header('token');
                
                if ($token != '') {
                    $userDeviceData = UserDevice::where('token', $token)->first();
                    
                    if ($userDeviceData) {
                        $request['user_id'] = $userDeviceData->user_id;
                        $request['token'] = $token;
                        
                        return $next($request);
                    } else {
                        return response()->json([
                            'code' => 401,
                            'message' => 'Token not found',
                            'data' => new stdClass(),
                        ], 401);
                    }
                } else {
                    return response()->json([
                        'code' => 401,
                        'message' => 'Invalid token',
                        'data' => new stdClass(),
                    ], 401);
                }
            } else {
                return response()->json([
                    'code' => 401,
                    'message' => 'Token not found',
                    'data' => new stdClass(),
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 401,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => new stdClass(),
            ], 401);
        }
    }
}