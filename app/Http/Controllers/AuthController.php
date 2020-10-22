<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (!$token = auth()->attempt([
            'email'     => $request->email,
            'password'  => $request->password,
        ])) {
            return response()->json('Invalid login credentials.', 401);
        }

        return response()->json([
            'token'         => $token,
            'token_type'    => 'bearer',
            'expires'       => auth()->factory()->getTTL(),
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

}
