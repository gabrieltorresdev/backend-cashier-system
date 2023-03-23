<?php

namespace App\Http\Controllers\Auth;

use App\Http\Actions\Auth\AttemptLoginAction;
use App\Http\Actions\Auth\SendUserActivationEmailAction;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HandleLoginController extends Controller
{
    public function __construct(
        private Request $request,
        private AttemptLoginAction $attemptLoginAction,
        private UserRepository $userRepository,
        private SendUserActivationEmailAction $sendUserActivationEmailAction,
        private ?bool $isUserActivated = null
    ) {
    }

    public function __invoke()
    {
        try {
            $this->validateFields();

            $token = $this->attemptLoginAction->execute(
                $this->request->get('login'),
                $this->request->get('password')
            );

            if (!$token) {
                throw_validation_exception([
                    "login" => __("custom.auth.invalid-credentials")
                ]);
            }
            
            $this->isUserActivated = $this->userRepository
                ->whereModel(auth()->id())
                ->isActivated();

            if (!$this->isUserActivated) {
                $this->sendActivationEmail();
            }

            return response_ok(
                data: [
                    'token' => $token,
                    'activated' => $this->isUserActivated
                ],
                message: $this->getSuccessResponseMessage()
            );
        } catch (ValidationException $e) {
            return response_no(422, $e->errors());
        } catch (\Throwable $e) {
            return response_no(message: $e->getMessage());
        }
    }

    private function validateFields(): void
    {
        $this->request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);
    }

    private function sendActivationEmail()
    {
        if (!$this->sendUserActivationEmailAction->execute(auth()->user()->id))
            throw_exception();
    }

    private function getSuccessResponseMessage(): string
    {
        return !$this->isUserActivated
            ? __("custom.auth.user-activation-email-sent")
            : __("custom.auth.login-success");
    }
}
