<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class ChangeUserPasswordAction
{
    public function execute(
        UserRepository $userRepository,
        string $oldPassword,
        string $newPassword
    ): bool {
        if (!$userRepository->checkPassword($oldPassword)) return false;

        return $userRepository->update([
            'password' => bcrypt($newPassword)
        ]);
    }
}
