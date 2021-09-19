<?php

namespace App\Repository;

use App\Models\User;
use Carbon\Carbon;

interface MeditationRepositoryInterface
{
    public function findTotalCompletedDurationByUser(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): int;
}
