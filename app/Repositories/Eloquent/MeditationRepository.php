<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\MeditationRepositoryInterface;
use Carbon\Carbon;

class MeditationRepository extends BaseRepository implements MeditationRepositoryInterface
{
    public function findTotalCompletedDurationByUser(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): int
    {
        $query = $this->model
            ->leftJoin('user_meditations', 'user_meditations.meditation_id', '=', 'meditations.id')
            ->where('user_meditations.user_id', $user->getKey())
        ;

        if ($startDate) {
            $query->where('user_meditations.completed_at', '>=', $startDate->toDateTimeString());
        }

        if ($endDate) {
            $query->where('user_meditations.completed_at', '<=', $endDate->toDateTimeString());
        }

        return $query->sum('meditations.duration');
    }
}
