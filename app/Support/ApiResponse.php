<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $text = 'OK',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'code' => $code,
            'text' => $text,
            'data' => $data,
        ], $code);
    }
    
    public static function error(
        string $text = 'Error',
        int $code = 400,
        mixed $data = null
    ): JsonResponse {
        return response()->json([
            'code' => $code,
            'text' => $text,
            'data' => $data,
        ], $code);
    }
}