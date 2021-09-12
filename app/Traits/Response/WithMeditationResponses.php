<?php

namespace App\Traits\Response;

use Illuminate\Http\JsonResponse;

trait WithMeditationResponses
{
    private function createMeditationNotFoundResponse(): JsonResponse
    {
        return response()->json(
            [
                'message' => 'Meditation not found!',
            ],
            JsonResponse::HTTP_NOT_FOUND,
        );
    }

    private function createMeditationCompletionLoggedResponse(): JsonResponse
    {
        return response()->json(
            [
                'message' => 'Meditation completion saved.',
            ],
            JsonResponse::HTTP_OK,
        );
    }
}
