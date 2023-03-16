<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangeUserPasswordAction
{
    public function __construct()
    {
    }

    public static function execute(
        User $user,
        string $oldPassword,
        string $newPassword
    ): bool
    {
        if (!Hash::check($oldPassword, $user->password)) return false;

        $user->password = bcrypt($newPassword);
        
        return $user->update();
    }
}
