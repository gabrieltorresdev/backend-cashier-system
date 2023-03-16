<?php

namespace App\Http\Controllers\Auth;

use App\Http\Actions\Auth\AttemptLoginAction;
use App\Http\Actions\Auth\SendUserActivationEmailAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HandleLoginController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $request->validate([
                'login' => 'required|string',
                'password' => 'required|string'
            ]);

            $token = AttemptLoginAction::execute(
                $request->get('login'),
                $request->get('password')
            );

            if (!$token)
                throw ValidationException::withMessages([
                    "login" => __("custom.invalid-credentials")
                ]);

            $isUserActivated = auth()->user()->activated;

            if (!$isUserActivated)
                SendUserActivationEmailAction::execute(auth()->user()->email);

            return response_ok(
                data: [
                    'token' => $token,
                    'activated' => $isUserActivated
                ],
                message: !$isUserActivated
                    ? __("custom.user-activation-email-sent")
                    : ""
            );
        } catch (ValidationException $e) {
            return response_no(422, $e->errors());
        } catch (Exception $e) {
            return response_no(message: $e->getMessage());
        }
    }
}
