<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use App\Notifications\SendActivationCodeEmail;

class SendUserActivationEmailAction
{
    public function __construct()
    {
    }

    public static function execute(string $email): void
    {
        $user = User::query()->firstWhere('email', '=', $email);
        
        $user->verification_code = verification_token();

        $user->update();

        $user->notify(new SendActivationCodeEmail);
    }
}