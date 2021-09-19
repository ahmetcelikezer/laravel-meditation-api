<?php

namespace App\Repository;

use App\Models\User;
use Carbon\Carbon;

interface UserMeditationRepositoryInterface
{
    public function findCompletedMeditationCountByUser(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): int;

    public function findLongestConsecutiveDaysCountByUser(User $user, Carbon $startDate, Carbon $endDate): int;
}
