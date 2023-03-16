<?php

namespace App\Http\Controllers\Auth;

use App\Http\Actions\Auth\ChangeUserPasswordAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HandleUserActivationController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|string|exists:users,id',
                'verification_code' => 'required|string|size:8',
                'old_password' => 'required|string',
                'password' => 'required|string|min:6|confirmed'
            ]);

            $user = User::find($request->get('user_id'));

            if ($user->activated)
                throw new Exception(__("custom.user-already-actived"));

            if ($user->verification_code !== $request->get('verification_code'))
                throw ValidationException::withMessages([
                    'verification_code' => __("custom.invalid-verification-code")
                ]);

            if (!ChangeUserPasswordAction::execute(
                $user,
                $request->get('old_password'),
                $request->get('password')
            ))
                throw ValidationException::withMessages([
                    'old_password' => __("custom.invalid-password")
                ]);

            if (!$user->activate())
                throw new Exception(__("custom.user-activation-failed"));

            DB::commit();

            return response_ok(message: __("custom.user-activation-success"));
        } catch (ValidationException $e) {
            DB::rollBack();
            return response_no(422, $e->errors());
        } catch (Exception $e) {
            DB::rollBack();
            return response_no(message: $e->getMessage());
        }
    }
}
