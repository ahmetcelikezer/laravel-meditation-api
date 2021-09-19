<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\Meditation\Exception\InvalidReportType;
use App\Services\Meditation\Exception\RequiredFilterNotProvidedException;
use App\Services\Meditation\MeditationReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private MeditationReportService $meditationReportService)
    {
    }

    public function createReport(Request $request, string $reportType): JsonResponse
    {
        try {
            $report = $this->meditationReportService->getReport($reportType, $request->all());
        } catch (InvalidReportType $exception) {
            return response()->json(
                ['message' => $exception->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST,
            );
        } catch (RequiredFilterNotProvidedException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $report;
    }
}
