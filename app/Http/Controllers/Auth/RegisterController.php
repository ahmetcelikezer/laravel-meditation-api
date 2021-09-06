<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function handle(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User $user */
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $accessToken = $user->createToken($request->getClientIp() ?? $data['email']);

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
            'user' => $user,
        ], JsonResponse::HTTP_CREATED);
    }
}
