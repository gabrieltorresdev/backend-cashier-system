<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

class GetAuthenticableUsersController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $users = User::select('id', 'name')->get();

        return response_ok(['users' => $users]);
    }
}
