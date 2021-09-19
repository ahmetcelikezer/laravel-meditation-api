<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

interface MeditationRepositoryInterface
{
    public function findOneById(string $id): ?Model;

    public function findTotalCompletedDurationByUser(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): int;
}
