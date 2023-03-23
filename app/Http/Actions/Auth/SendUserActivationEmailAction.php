<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use App\Notifications\SendActivationCodeEmail;
use App\Repositories\UserRepository;

class SendUserActivationEmailAction
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function execute(string $userId): bool
    {
        $this->userRepository->whereModel($userId);
        
        if (!$this->userRepository->updateValidationToken())
            return false;

        $this->userRepository->sendValidationToken();

        return true;
    }
}
