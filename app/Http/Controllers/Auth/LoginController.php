<?php

namespace App\Http\Controllers\Auth;

use App\DataTransferObjects\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Authentication\AuthenticationService;
use App\Services\Authentication\Exception\InvalidCredentialsException;
use App\Traits\Response\WithAuthResponses;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    use WithAuthResponses;

    public function __construct(private AuthenticationService $authenticationService)
    {
    }

    public function handle(LoginRequest $request): JsonResponse
    {
        $data = LoginDTO::fromArray($request->validated());

        try {
            $authenticatedUser = $this->authenticationService->authenticate($data);
        } catch (InvalidCredentialsException) {
            return $this->createInvalidCredentialsResponse();
        }

        return $this->createAuthResponse($authenticatedUser->user, $authenticatedUser->token, JsonResponse::HTTP_OK);
    }
}
