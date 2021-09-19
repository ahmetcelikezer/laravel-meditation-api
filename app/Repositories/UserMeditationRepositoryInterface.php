<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserMeditation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface UserMeditationRepositoryInterface
{
    public function createIfNotExists(array $attributes): UserMeditation;

    public function findCompletedMeditationCountByUser(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): int;

    public function findLongestConsecutiveDaysCountByUser(User $user, Carbon $startDate, Carbon $endDate): int;

    public function findActiveDaysInMonthByUser(User $user, int $year, int $month): Collection;

    public function findDailyMeditationDurationByUser(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection;
}
