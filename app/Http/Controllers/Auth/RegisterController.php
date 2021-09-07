<?php

namespace App\Http\Controllers\Auth;

use App\DataTransferObjects\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\Authentication\AuthenticationService;
use App\Traits\Response\WithAuthResponses;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    use WithAuthResponses;

    public function __construct(private AuthenticationService $authenticationService)
    {
    }

    public function handle(RegisterRequest $request): JsonResponse
    {
        $data = RegisterDTO::fromArray($request->validated());

        $authenticatedUser = $this->authenticationService->register($data);

        return $this->createAuthResponse($authenticatedUser->user, $authenticatedUser->token);
    }
}
