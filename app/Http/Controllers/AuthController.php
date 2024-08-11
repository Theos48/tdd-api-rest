<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller {
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {}


    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request) {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return ApiResponseHelper::errorResponse('Unauthorized', 401);
        }

        return ApiResponseHelper::successResponse(
            [
                'token' => $token,
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        );
    }

    public function sendPasswordResetLink(Request $request) {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        $sent = $status === Password::RESET_LINK_SENT;

        return ApiResponseHelper::successResponse(message: $sent ? 'OK' : 'Error', code: $sent ? 200 : 500);
    }

    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required'
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            });

        return match ($status) {
            Password::INVALID_USER => ApiResponseHelper::errorResponse('Invalid email'),
            Password::INVALID_TOKEN => ApiResponseHelper::errorResponse('Invalid token'),
            default => ApiResponseHelper::successResponse(message: 'OK'),
        };
    }

}
