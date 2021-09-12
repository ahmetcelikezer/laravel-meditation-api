<?php

namespace App\Http\Controllers\Meditation;

use App\Http\Controllers\Controller;
use App\Services\Meditation\MeditationLoggerService;
use App\Services\Meditation\MeditationService;
use App\Traits\Response\WithMeditationResponses;
use Illuminate\Http\JsonResponse;

class MeditationController extends Controller
{
    use WithMeditationResponses;

    public function __construct(private MeditationLoggerService $meditationLoggerService, private MeditationService $meditationService)
    {
    }

    public function completeAction(string $meditationId): JsonResponse
    {
        $meditation = $this->meditationService->getById($meditationId);
        if (!$meditation) {
            return $this->createMeditationNotFoundResponse();
        }

        $this->meditationLoggerService->logMeditationCompletion($meditation);

        return $this->createMeditationCompletionLoggedResponse();
    }
}
