<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\UserMeditationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserMeditationRepository extends BaseRepository implements UserMeditationRepositoryInterface
{
    public function findCompletedMeditationCountByUser(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): int
    {
        $query = $this->model
            ->where('user_id', '=', $user->getKey())
        ;

        if ($startDate) {
            $query->where('completed_at', '>=', $startDate->toDateTimeString());
        }

        if ($endDate) {
            $query->where('completed_at', '<=', $endDate->toDateTimeString());
        }

        return $query->count();
    }

    public function findLongestConsecutiveDaysCountByUser(User $user, Carbon $startDate, Carbon $endDate): int
    {
        $query = '
            SELECT streak
                FROM (
                    SELECT IF(@prevDate + INTERVAL 1 DAY = current.date, @currentStreak := @currentStreak + 1, @currentStreak := 1) AS streak, @prevDate := current.date
                        FROM (
                            SELECT DATE(completed_at) AS date
                            FROM user_meditations
                            where user_id = "'.$user->getKey().'" AND
                            completed_at >= "'.$startDate->toDateTimeString().'" AND
                            completed_at <= "'.$endDate->toDateTimeString().'"
                            GROUP BY completed_at
                            ORDER BY completed_at
                        )
                    AS current
                INNER JOIN (SELECT @prevDate := NULL, @currentStreak := 1) AS vars
            ) AS _
            ORDER BY streak DESC LIMIT 1;';

        return DB::select(DB::raw($query))[0]->streak;
    }
}
