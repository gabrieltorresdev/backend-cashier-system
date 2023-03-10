<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

if (!function_exists('response_ok')) {
    function response_ok(array $data = [], string $message = "", int $code = 200): JsonResponse
    {
        $response = [
            'data' => $data,
            'message' => $message
        ];

        return response()->json($response, $code);
    }
}

if (!function_exists('response_no')) {
    function response_no(array $data = [], string $message = "", int $code = 500): JsonResponse
    {
        if (empty($message))
            $message = 'Ocorreu um erro ao finalizar sua requisição.';

        $response = [
            'data' => $data,
            'message' => $message
        ];

        return response()->json($response, $code);
    }
}
