<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

if (!function_exists('response_ok')) {
    function response_ok(int $code = 200, array $data = [], string $message = ""): JsonResponse
    {
        $response = [
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response, $code);
    }
}

if (!function_exists('response_no')) {
    function response_no(int $code = 500, array $errors = [], string $message = "", array $data = []): JsonResponse
    {
        if (empty($message))
            $message = __('custom.response-error-message');

        $response = [
            'message' => $message,
            'data' => $data,
            'errors' => $errors
        ];

        if ($code === 422) {
            Arr::set($response, 'errors', [
                'fields' => $errors
            ]);
        }

        return response()->json($response, $code);
    }
}

if (!function_exists('is_email')) {
    /**
     * Verify if given string is an valid email
     * @param string $string
     */
    function is_email(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL);
    }
}
