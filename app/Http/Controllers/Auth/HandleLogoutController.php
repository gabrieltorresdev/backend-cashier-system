<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class HandleLogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::parseToken());

            return response_ok(204);
        } catch (JWTException $e) {
            return response_no(message: $e->getMessage());
        }
    }
}
