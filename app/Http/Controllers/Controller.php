<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Send response to user
     *
     * @return json;
     */
    public function toJsonEnc($result = [], $message = '', $status = 200)
    {
        if (Config::get('constant.ENCRYPTION_ENABLED') == 1) {
            // If encryption is enabled, use EncryptDecrypt helper
            // For now, we'll keep it simple without encryption
            return response()->json([
                'code' => $status,
                'message' => $message,
                'data' => !empty($result) ? $result : new \stdClass(),
            ], $status);
        } else {
            return response()->json([
                'code' => $status,
                'message' => $message,
                'data' => !empty($result) ? $result : new \stdClass(),
            ], $status);
        }
    }

    public function validateResponse($errors, $result = [])
    {
        $err = '';

        foreach ($errors->all() as $key => $val) {
            $err = $val;
            break;
        }
        
        if (Config::get('constant.ENCRYPTION_ENABLED') == 1) {
            // If encryption is enabled, use EncryptDecrypt helper
            // For now, we'll keep it simple without encryption
            return response()->json([
                'code' => Config::get('constant.VALIDATION_ERROR'),
                'message' => $err,
                'data' => !empty($result) ? $result : new \stdClass(),
            ], Config::get('constant.VALIDATION_ERROR'));
        } else {
            return response()->json([
                'code' => Config::get('constant.VALIDATION_ERROR'),
                'message' => $err,
                'data' => !empty($result) ? $result : new \stdClass(),
            ], Config::get('constant.VALIDATION_ERROR'));
        }
    }
}
