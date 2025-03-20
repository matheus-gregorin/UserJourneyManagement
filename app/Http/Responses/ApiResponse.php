<?php

namespace App\Http\Responses;

use App\Domain\Enums\ErrorsEnum;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(array $data = [], string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => ErrorsEnum::statusSuccess,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error(array $errors = [], string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => ErrorsEnum::statusError,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}
