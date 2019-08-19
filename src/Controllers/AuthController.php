<?php

namespace Dannerz\Api\Controllers;

use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login']]);
    }

    public function login()
    {
        $credentials = request(['email', 'username', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            $response = ['error' => [
                'title' => 'Unauthorized',
                'message' => 'Incorrect login details.',
            ]];
            return response()->json($response, 401);
        }

        return $this->respondWithToken($token);
    }

    public function user()
    {
        return response()->json(['data' => auth()->user()]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json();
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'token_expires_in' => auth()->factory()->getTTL()*60,
        ]);
    }
}
