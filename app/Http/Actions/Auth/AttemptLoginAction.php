<?php

namespace App\Http\Actions\Auth;

use Tymon\JWTAuth\Facades\JWTAuth;

class AttemptLoginAction
{
    public function execute(string $emailOrUsername, string $password): ?string
    {
        $field = is_email($emailOrUsername) ? 'email' : 'username';

        return JWTAuth::attempt([
            $field => $emailOrUsername,
            'password' => $password
        ]);
    }
}