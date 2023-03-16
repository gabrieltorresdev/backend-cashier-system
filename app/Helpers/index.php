<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

if (!function_exists('response_ok')) {
    function response_ok(int $code = 200, array $data = [], string $message = ""): JsonResponse
    {
        if (empty($message))
            $message = __('custom.response-success-message');
            
        $response = [
            'message' => $message,
            'data' => camelizeArrayKeys($data)
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
            'errors' => camelizeArrayKeys($errors)
        ];

        if ($code === 422) {
            Arr::set($response, 'errors', [
                'fields' => camelizeArrayKeys($errors)
            ]);
        }

        return response()->json($response, $code);
    }
}

if (!function_exists('is_email')) {
    /**
     * Verify if given string is an valid email
     * @param ?string $string
     */
    function is_email(?string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL);
    }
}

if (!function_exists('camelize')) {
    /**
     * Turn given string in camelCase
     * @param string $string
     */
    function camelize(string $string): string
    {
        return str($string)->camel()->value();
    }
}

if (!function_exists('snakelize')) {
    /**
     * Turn given string in snake_case
     * @param string $string
     */
    function snakelize(string $string): string
    {
        return str($string)->snake()->value();
    }
}

if (!function_exists('camelizeArrayKeys')) {
    /**
     * Turn given array keys into camelCase
     * @param array $array
     */
    function camelizeArrayKeys(array $array): array
    {
        $camelized = [];

        foreach ($array as $key => $value) {
            $camelized[camelize($key)] = $value;

            if (is_array($value))
                $camelized[camelize($key)] = camelizeArrayKeys($value);
        }
            
        return $camelized;
    }
}

if (!function_exists('snakelizeArrayKeys')) {
    /**
     * Turn given array keys into snake_case
     * @param array $array
     */
    function snakelizeArrayKeys(array $array): array
    {
        $snakelized = [];

        foreach ($array as $key => $value) {
            $snakelized[snakelize($key)] = $value;
            
            if (is_array($value))
                $snakelized[snakelize($key)] = snakelizeArrayKeys($value);
        }

        return $snakelized;
    }
}
