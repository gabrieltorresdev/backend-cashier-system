<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class HandleLoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            $login = $request->get('login');

            $isEmail = is_email($login);

            $field = $isEmail ? 'email' : 'username';

            $request->merge([$field => $login]);

            $request->validate([
                'username' => 'required_without:email|string|max:255',
                'email' => 'required_without:username|string|email|max:255',
                'password' => 'required|string'
            ]);

            $user = User::query()->firstWhere($field, '=', $login);

            if ($user) {
                if (!$user->activated) {
                    $status = Password::sendResetLink(['email' => $user->email]);

                    if ($status === Password::RESET_LINK_SENT)
                        return response_no(401, [], "Enviamos instruções para o e-mail cadastrado para ativação do usuário.");

                    return response_no(500, [], "Não foi possível enviar o email para ativação do usuário, tente novamente mais tarde.");
                }

                $authToken = auth()->attempt([
                    'email' => $user->email,
                    'password' => $request->get('password')
                ]);

                if ($authToken)
                    return response_ok(200, [
                        'token' => $authToken
                    ]);
            }

            throw ValidationException::withMessages(["login" => "Credenciais inválidas."]);
        } catch (ValidationException $e) {
            return response_no(422, $e->errors());
        } catch (Exception $e) {
            return response_no(500, [], $e->getMessage() . " -- " . $e->getLine());
        }
    }
}
