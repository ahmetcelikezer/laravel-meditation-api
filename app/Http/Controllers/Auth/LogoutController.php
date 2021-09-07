<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Authentication\AuthenticationService;
use App\Traits\Response\WithAuthResponses;
use Symfony\Component\HttpFoundation\JsonResponse;

class LogoutController extends Controller
{
    use WithAuthResponses;

    public function __construct(private AuthenticationService $authenticationService)
    {
    }

    public function handle(): JsonResponse
    {
        $this->authenticationService->unAuthenticateCurrentUser();

        return $this->createLogoutResponse();
    }
}
