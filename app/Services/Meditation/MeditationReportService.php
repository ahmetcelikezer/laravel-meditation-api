<?php

namespace App\Services\Meditation;

use App\Models\User;
use App\Services\Authentication\AuthenticationService;
use App\Services\Meditation\Exception\InvalidReportType;
use App\Traits\Response\WithReportResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class MeditationReportService
{
    use WithReportResponse;

    public const ACTIVE_DAYS_IN_MONTH = 'active_days_in_month';

    public function __construct(private AuthenticationService $authenticationService)
    {
    }

    /**
     * @throws InvalidReportType
     */
    public function getReport(string $reportType, array $filters): JsonResponse
    {
        $this->validateReportType($reportType);

        if (self::ACTIVE_DAYS_IN_MONTH === $reportType) {
            return $this->createActiveDaysInMonthResponse($this->createActiveDaysInMonthReport($filters));
        }

        return response()->json([
            'message' => 'Unknown error occurred!',
        ], 500);
    }

    private function createActiveDaysInMonthReport(array $filters): array
    {
        if (array_key_exists('year', $filters) && array_key_exists('month', $filters)) {
            $year = $filters['year'];
            $month = $filters['month'];
        }

        $monthlyDays = DB::table('user_meditations')
            ->distinct()
            ->selectRaw('cast(completed_at as date) as date')
            ->where(['user_id' => $this->getCurrentUser()->getKey()])
            ->whereMonth('completed_at', '=', $month ?? Carbon::now()->month)
            ->whereYear('completed_at', '=', $year ?? Carbon::now()->year)
            ->get()
        ;

        $dates = $monthlyDays->map(function ($item) {
            return Carbon::parse($item->date)->toISOString();
        });

        return $dates->all();
    }

    private function getCurrentUser(): User
    {
        return $this->authenticationService->getCurrentUser();
    }

    /** @throws InvalidReportType */
    private function validateReportType(string $reportType): void
    {
        $reflect = new ReflectionClass(get_class($this));
        $constants = $reflect->getConstants();

        if (!in_array($reportType, $constants, true)) {
            throw new InvalidReportType(sprintf('Report type "%s" is invalid!', $reportType));
        }
    }
}
