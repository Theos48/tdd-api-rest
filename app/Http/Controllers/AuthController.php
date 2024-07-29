<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use Illuminate\Http\Request;

class AuthController extends Controller {
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
    }


    public function login(Request $request){

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return ApiResponseHelper::errorResponse('Unauthorized', 401);
        }

        return  ApiResponseHelper::successResponse(
            [
                'token' => $token,
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        );

    }
}
