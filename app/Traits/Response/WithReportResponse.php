<?php

namespace App\Traits\Response;

use Illuminate\Http\JsonResponse;

trait WithReportResponse
{
    private function createActiveDaysInMonthResponse(array $days): JsonResponse
    {
        return response()->json([
            'active_days' => $days,
        ]);
    }
}
