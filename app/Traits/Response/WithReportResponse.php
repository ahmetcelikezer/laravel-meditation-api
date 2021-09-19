<?php

namespace App\Traits\Response;

use App\Services\Report\ActiveDaysInMonthReport;
use App\Services\Report\DailyMeditationDurationReport;
use App\Services\Report\GeneralStatisticsReport;
use Illuminate\Http\JsonResponse;

trait WithReportResponse
{
    private function createActiveDaysInMonthResponse(ActiveDaysInMonthReport $activeDaysInMonthReport): JsonResponse
    {
        return response()->json([
            'active_days' => $activeDaysInMonthReport->activeDaysInMonth,
        ]);
    }

    private function createDailyMeditationDurationReportResponse(DailyMeditationDurationReport $dailyMeditationDurationReport): JsonResponse
    {
        return response()->json([
            'daily_duration' => $dailyMeditationDurationReport->dailyDuration,
        ]);
    }

    private function createGeneralStatisticsReportResponse(GeneralStatisticsReport $generalStatisticsReport): JsonResponse
    {
        return response()->json([
            'general_statistics' => [
                'total_meditation_count' => $generalStatisticsReport->totalMeditationCount,
                'total_meditation_duration' => $generalStatisticsReport->totalMeditationDuration,
                'longest_consecutive_days' => $generalStatisticsReport->longestConsecutiveDays,
            ],
        ]);
    }

    private function createInvalidReportTypeResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Invalid report type provided!',
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    private function createExceptionResponse(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}
