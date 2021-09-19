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

    private function createDailyMeditationDurationReportResponse(array $array): JsonResponse
    {
        return response()->json([
            'daily_duration' => $array,
        ]);
    }

    private function createGeneralStatisticsReportResponse(int $totalMeditationCount, int $totalMeditationDuration, int $longestConsecutiveDays): JsonResponse
    {
        return response()->json([
            'general_statistics' => [
                'total_meditation_count' => $totalMeditationCount,
                'total_meditation_duration' => $totalMeditationDuration,
                'longest_consecutive_days' => $longestConsecutiveDays,
            ],
        ]);
    }
}
