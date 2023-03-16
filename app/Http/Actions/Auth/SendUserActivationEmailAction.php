<?php

namespace App\Http\Actions\Auth;

use Illuminate\Support\Facades\Password;

class SendUserActivationEmailAction
{
    public function __construct()
    {
    }

    public static function execute(string $email)
    {
        $status = Password::sendResetLink(['email' => $email]);

        return $status === Password::RESET_LINK_SENT;
    }
}