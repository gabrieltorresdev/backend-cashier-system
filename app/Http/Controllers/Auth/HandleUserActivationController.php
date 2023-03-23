<?php

namespace App\Http\Controllers\Auth;

use App\Http\Actions\Auth\ChangeUserPasswordAction;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HandleUserActivationController extends Controller
{
    public function __construct(
        private Request $request,
        private UserRepository $userRepository,
        private ChangeUserPasswordAction $changeUserPasswordAction
    ) {
    }
    public function __invoke()
    {
        DB::beginTransaction();
        try {
            $this->validateFields();

            $this->userRepository->whereModel($this->request->input('user_id'));

            $this->verifyUserIsActivated();
            $this->checkVerificationCode();

            if (!$this->userRepository->activate())
                throw_exception(__("custom.auth.user-activation-failed"));

            if (!$this->changeUserPasswordAction->execute(
                $this->userRepository,
                $this->request->get('old_password'),
                $this->request->get('password')
            ))
                throw_validation_exception([
                    'old_password' => __("custom.auth.invalid-password")
                ]);

            DB::commit();

            return response_ok(message: __("custom.auth.user-activation-success"));
        } catch (ValidationException $e) {
            DB::rollBack();
            return response_no(422, $e->errors());
        } catch (\Throwable $e) {
            DB::rollBack();
            return response_no(message: $e->getMessage());
        }
    }

    private function validateFields(): void
    {
        $this->request->validate([
            'user_id' => 'required|string|uuid|exists:users,id',
            'verification_code' => 'required|string|size:8',
            'old_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ]);
    }

    /**
     * @throws \Exception
     */
    private function verifyUserIsActivated(): void
    {
        if ($this->userRepository->isActivated())
            throw_exception(__("custom.auth.user-already-actived"));
    }

    /**
     * @throws ValidationException
     */
    private function checkVerificationCode(): void
    {
        if (!$this->userRepository->checkVerificationCode(
            $this->request->input('verification_code')
        ))
            throw_validation_exception([
                'verification_code' => __("custom.auth.invalid-verification-code")
            ]);
    }
}
