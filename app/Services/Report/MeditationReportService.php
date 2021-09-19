<?php

namespace App\Services\Report;

use App\Models\User;
use App\Repositories\MeditationRepositoryInterface;
use App\Repositories\UserMeditationRepositoryInterface;
use App\Services\Authentication\AuthenticationService;
use App\Services\Report\Exception\RequiredFilterNotProvidedException;
use Carbon\Carbon;

class MeditationReportService
{
    public const ACTIVE_DAYS_IN_MONTH = 'active_days_in_month';
    public const DAILY_MEDITATION_DURATION = 'daily_meditation_duration';
    public const GENERAL_STATISTICS = 'general_statistics';

    public function __construct(private AuthenticationService $authenticationService, private UserMeditationRepositoryInterface $userMeditationRepository, private MeditationRepositoryInterface $meditationRepository)
    {
    }

    /** @throws RequiredFilterNotProvidedException */
    public function getGeneralStatisticsReport(array $filters): GeneralStatisticsReport
    {
        if (!array_key_exists('startDate', $filters)) {
            throw new RequiredFilterNotProvidedException('Required filter "startDate" not provided.');
        }

        if (!array_key_exists('endDate', $filters)) {
            throw new RequiredFilterNotProvidedException('Required filter "endDate" not provided.');
        }

        $startDate = Carbon::parse($filters['startDate']);
        $endDate = Carbon::parse($filters['endDate']);

        $user = $this->getCurrentUser();

        $totalMeditationCount = $this->userMeditationRepository->findCompletedMeditationCountByUser(
            $user,
            $startDate,
            $endDate,
        );

        $totalMeditationDuration = $this->meditationRepository->findTotalCompletedDurationByUser($user, $startDate, $endDate);
        $longestConsecutiveDays = $this->userMeditationRepository->findLongestConsecutiveDaysCountByUser($user, $startDate, $endDate);

        return new GeneralStatisticsReport($totalMeditationCount, $totalMeditationDuration, $longestConsecutiveDays);
    }

    /** @throws RequiredFilterNotProvidedException */
    public function getActiveDaysInMonthReport(array $filters): ActiveDaysInMonthReport
    {
        if (!array_key_exists('year', $filters)) {
            throw new RequiredFilterNotProvidedException('Required filter "year" not provided.');
        }

        if (!array_key_exists('month', $filters)) {
            throw new RequiredFilterNotProvidedException('Required filter "month" not provided.');
        }

        $year = (int) $filters['year'];
        $month = (int) $filters['month'];

        $user = $this->getCurrentUser();

        $activeDaysInMonth = $this->userMeditationRepository->findActiveDaysInMonthByUser($user, $year, $month);

        $dates = $activeDaysInMonth->map(function ($item) {
            return Carbon::parse($item->date)->toDateString();
        });

        return new ActiveDaysInMonthReport($dates->all());
    }

    public function getDailyMeditationDurationReport(array $filters): DailyMeditationDurationReport
    {
        $startDate = Carbon::parse($filters['startDate']);
        $endDate = Carbon::parse($filters['endDate']);

        $user = $this->getCurrentUser();

        $durations = $this->userMeditationRepository->findDailyMeditationDurationByUser($user, $startDate, $endDate);

        $dates = [];
        foreach ($durations->toArray() as $date => $duration) {
            $dates[Carbon::parse($date)->toDateString()] = (int) $duration;
        }

        return new DailyMeditationDurationReport($dates);
    }

    private function getCurrentUser(): User
    {
        return $this->authenticationService->getCurrentUser();
    }
}
