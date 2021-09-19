<?php

namespace App\Services\Meditation;

use App\Models\User;
use App\Repository\MeditationRepositoryInterface;
use App\Repository\UserMeditationRepositoryInterface;
use App\Services\Authentication\AuthenticationService;
use App\Services\Meditation\Exception\InvalidReportType;
use App\Services\Meditation\Exception\RequiredFilterNotProvidedException;
use App\Traits\Response\WithReportResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class MeditationReportService
{
    use WithReportResponse;

    public const ACTIVE_DAYS_IN_MONTH = 'active_days_in_month';
    public const DAILY_MEDITATION_DURATION = 'daily_meditation_duration';
    public const GENERAL_STATISTICS = 'general_statistics';

    public function __construct(private AuthenticationService $authenticationService, private UserMeditationRepositoryInterface $userMeditationRepository, private MeditationRepositoryInterface $meditationRepository)
    {
    }

    /**
     * @throws InvalidReportType
     * @throws RequiredFilterNotProvidedException
     */
    public function getReport(string $reportType, array $filters): JsonResponse
    {
        $this->validateReportType($reportType);

        if (self::ACTIVE_DAYS_IN_MONTH === $reportType) {
            return $this->createActiveDaysInMonthResponse($this->createActiveDaysInMonthReport($filters));
        }

        if (self::DAILY_MEDITATION_DURATION === $reportType) {
            return $this->createDailyMeditationDurationReportResponse($this->createDailyMeditationDurationReport($filters));
        }

        if (self::GENERAL_STATISTICS === $reportType) {
            if (!array_key_exists('startDate', $filters)) {
                throw new RequiredFilterNotProvidedException();
            }

            if (!array_key_exists('endDate', $filters)) {
                throw new RequiredFilterNotProvidedException();
            }

            $startDate = Carbon::parse($filters['startDate']);
            $endDate = Carbon::parse($filters['endDate']);

            $totalMeditationCount = $this->getTotalMeditationCount($startDate, $endDate);
            $totalMeditationDuration = $this->getTotalMeditationDuration($startDate, $endDate);
            $longestConsecutiveDays = $this->getLongestConsecutiveDaysCount($startDate, $endDate);

            return $this->createGeneralStatisticsReportResponse(
                $totalMeditationCount,
                $totalMeditationDuration,
                $longestConsecutiveDays
            );
        }

        return response()->json([
            'message' => 'Unknown error occurred!',
        ], 500);
    }

    private function getTotalMeditationDuration(?Carbon $startDate = null, ?Carbon $endDate = null): int
    {
        $user = $this->getCurrentUser();

        return $this->meditationRepository->findTotalCompletedDurationByUser($user, $startDate, $endDate);
    }

    private function getTotalMeditationCount(?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $user = $this->getCurrentUser();

        return $this->userMeditationRepository->findCompletedMeditationCountByUser($user, $startDate, $endDate);
    }

    private function getLongestConsecutiveDaysCount(Carbon $startDate, Carbon $endDate)
    {
        $user = $this->getCurrentUser();

        return $this->userMeditationRepository->findLongestConsecutiveDaysCountByUser($user, $startDate, $endDate);
    }

    private function createDailyMeditationDurationReport(array $filters): array
    {
        $dates = [];

        $query = DB::table('user_meditations')
            ->selectRaw('DATE(completed_at) as date, SUM(meditations.duration) totalCount')
            ->leftJoin('meditations', 'meditations.id', '=', 'user_meditations.meditation_id')
            ->where('user_meditations.user_id', '=', $this->getCurrentUser()->getKey())
            ->groupByRaw('DATE(completed_at)')
        ;

        if (in_array('startDate', $filters, true)) {
            $query->where('user_meditations.completed_at', '>=', Carbon::parse($filters['startDate']));
        }

        if (in_array('endDate', $filters, true)) {
            $query->where('user_meditations.completed_at', '<=', Carbon::parse($filters['endDate']));
        }

        $durations = $query->get()->pluck('totalCount', 'date')->toArray();

        foreach ($durations as $date => $duration) {
            $dates[Carbon::parse($date)->toISOString()] = (int) $duration;
        }

        return $dates;
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
