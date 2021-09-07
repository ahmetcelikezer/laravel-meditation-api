<?php

namespace App\Services\Authentication;

use App\DataTransferObjects\LoginDTO;
use App\DataTransferObjects\RegisterDTO;
use App\Models\User;
use App\Services\Authentication\Exception\InvalidCredentialsException;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    public function __construct(private Request $request, private UserService $userService)
    {
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function authenticate(LoginDTO $loginDTO): AuthenticatedUser
    {
        $user = $this->userService->getByEmail($loginDTO->email);
        if (!$user) {
            throw new InvalidCredentialsException();
        }

        $isPasswordValid = $this->isPasswordValid($user, $loginDTO->password);
        if (!$isPasswordValid) {
            throw new InvalidCredentialsException();
        }

        $token = $this->createAccessToken($user);

        return new AuthenticatedUser($user, $token);
    }

    public function unAuthenticateCurrentUser(): bool
    {
        return $this->request->user()->currentAccessToken()->delete() ?? true;
    }

    public function register(RegisterDTO $registerDTO): AuthenticatedUser
    {
        $user = $this->userService->create([
            'email' => $registerDTO->email,
            'password' => $registerDTO->password,
        ]);

        $accessToken = $this->createAccessToken($user);

        return new AuthenticatedUser($user, $accessToken);
    }

    private function createAccessToken(User $user): string
    {
        return $user->createToken(
            $this->request->getClientIp() ?? $user->getAttribute('email')
        )->plainTextToken;
    }

    private function isPasswordValid(User $user, string $password): bool
    {
        return Hash::check($password, $user->getAttribute('password'));
    }
}
