<?php

namespace App\Services\Meditation;

use App\Models\Meditation;
use App\Repositories\UserMeditationRepositoryInterface;
use App\Services\Authentication\AuthenticationService;
use LogicException;

class MeditationLoggerService
{
    public function __construct(private AuthenticationService $authenticationService, private UserMeditationRepositoryInterface $userMeditationRepository)
    {
    }

    public function logMeditationCompletion(Meditation $meditation): void
    {
        $user = $this->authenticationService->getCurrentUser();
        if (!$user) {
            throw new LogicException('User not found!');
        }

        $this->userMeditationRepository->createIfNotExists([
            'user_id' => $user->getKey(),
            'meditation_id' => $meditation->getKey(),
        ]);
    }
}
