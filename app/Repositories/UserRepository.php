<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    /** @var User */
    protected $model;

    public function isActivated(): bool
    {
        return $this->model
            ->activated;
    }

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->model->password);
    }

    public function checkVerificationCode(string $verificationCode): bool
    {
        return $this->model
            ->verification_code === $verificationCode;
    }

    public function activate(): bool
    {
        return $this->model
            ->update([
                'activated' => true
            ]);
    }

    public function updateValidationToken()
    {
        $this->model->verification_code = verification_token();

        return $this->model->update();
    }

    public function sendValidationToken()
    {
        $this->model->notify(new \App\Notifications\SendActivationCodeEmail);
    }
}
