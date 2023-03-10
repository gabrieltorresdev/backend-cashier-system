<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HandleLoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->validate($request, [
                'user_id' => ['required', 'string', 'exists:users,id'],
                'password' => ['required', 'string']
            ]);

            $user = User::find($request->get('user_id'));

            if (is_null($user->password)) {
                $this->validate($request, [
                    'password' => ['required', 'string', 'confirmed'],
                ]);

                $user->password = bcrypt($request->get('password'));

                $user->update();

                DB::commit();
            }

            $token = auth()->attempt([
                'id' => $request->get('user_id'),
                'password' => $request->get('password')
            ]);

            if ($token)
                return response_ok([
                    'token' => $token
                ]);

            throw new Exception("Usuário ou senha inválidos");
        } catch (ValidationException $e) {
            DB::rollBack();
            return response_no($e->errors(), 422);
        } catch (Exception $e) {
            DB::rollBack();
            return response_no([], $e->getMessage() . " -- " . $e->getLine());
        }
    }
}
