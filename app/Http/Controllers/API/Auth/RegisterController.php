<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\User;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = bcrypt($request->password);
        $validatedData['email_verified_at'] = now();
        $user = User::create($validatedData);

        $accessToken = $user->createToken('access_token')->accessToken;

        return response(['user' => $user, 'access_token' => $accessToken]);
    }
}
