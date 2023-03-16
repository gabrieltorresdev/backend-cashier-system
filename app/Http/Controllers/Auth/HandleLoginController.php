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

            $user = User::findByEmailOrUsername($request->get('login'));

            if ($user->isNotEmpty()) {
                if (!$user->get('activated')) {
                    if (SendUserActivationEmailAction::execute($user->get('email')))
                        return response_ok(message: __("custom.user-activation-email-sent"));
    
                    throw new Exception(message: __("custom.user-activation-email-not-sent"));
                }

                $token = AttemptLoginAction::execute(
                    $user->get('email'),
                    $request->get('password')
                );
    
                if ($token) return response_ok(data: ['token' => $token]);
            }

            throw ValidationException::withMessages([
                "login" => __("custom.invalid-credentials")
            ]);
        } catch (ValidationException $e) {
            return response_no(422, $e->errors());
        } catch (Exception $e) {
            return response_no(message: $e->getMessage());
        }
    }
}
