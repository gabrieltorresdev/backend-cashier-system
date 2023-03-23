<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetAuthenticatedUserController extends Controller
{
    public function __construct(
        private Request $request
    ) {
    }

    public function __invoke()
    {
        return response_ok(
            message: __("custom.auth.user-returned-successfully"),
            data: ['user' => $this->request->user()]
        );
    }
}
