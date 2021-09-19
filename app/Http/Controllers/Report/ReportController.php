<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\Report\Exception\RequiredFilterNotProvidedException;
use App\Services\Report\MeditationReportService;
use App\Traits\Response\WithReportResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use WithReportResponse;

    public function __construct(private MeditationReportService $meditationReportService)
    {
    }

    public function createReport(Request $request, string $reportType): JsonResponse
    {
        if (MeditationReportService::DAILY_MEDITATION_DURATION === $reportType) {
            $dailyMeditationDurationReport = $this->meditationReportService->getDailyMeditationDurationReport($request->all());

            return $this->createDailyMeditationDurationReportResponse($dailyMeditationDurationReport);
        }

        if (MeditationReportService::ACTIVE_DAYS_IN_MONTH === $reportType) {
            try {
                $activeDaysInMonthReport = $this->meditationReportService->getActiveDaysInMonthReport($request->all());
            } catch (RequiredFilterNotProvidedException $exception) {
                return $this->createExceptionResponse($exception->getMessage());
            }

            return $this->createActiveDaysInMonthResponse($activeDaysInMonthReport);
        }

        if (MeditationReportService::GENERAL_STATISTICS === $reportType) {
            try {
                $generalStatisticsReport = $this->meditationReportService->getGeneralStatisticsReport($request->all());
            } catch (RequiredFilterNotProvidedException $exception) {
                return $this->createExceptionResponse($exception->getMessage());
            }

            return $this->createGeneralStatisticsReportResponse($generalStatisticsReport);
        }

        return $this->createInvalidReportTypeResponse();
    }
}
