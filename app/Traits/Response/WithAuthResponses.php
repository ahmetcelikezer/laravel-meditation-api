<?php

namespace App\Traits\Response;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

trait WithAuthResponses
{
    private function createAuthResponse(User $user, string $token, int $status = JsonResponse::HTTP_CREATED): JsonResponse
    {
        return response()->json(
            [
                'data' => new UserResource($user),
                'access_token' => $token,
            ],
            $status,
        );
    }

    private function createInvalidCredentialsResponse(): JsonResponse
    {
        return response()->json(
            [
                'message' => 'Invalid credentials.',
            ],
            JsonResponse::HTTP_UNAUTHORIZED
        );
    }

    private function createLogoutResponse(): JsonResponse
    {
        return response()->json(
            [
                'message' => 'Logged out.',
            ],
            JsonResponse::HTTP_OK,
        );
    }
}
