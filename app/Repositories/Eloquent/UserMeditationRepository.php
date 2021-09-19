<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\UserMeditation;
use App\Repositories\UserMeditationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserMeditationRepository extends BaseRepository implements UserMeditationRepositoryInterface
{
    public function createIfNotExists(array $attributes): UserMeditation
    {
        return $this->model->firstOrCreate($attributes);
    }

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

        $result = DB::select(DB::raw($query));

        return $result[0]->streak ?? 0;
    }

    public function findActiveDaysInMonthByUser(User $user, int $year, int $month): Collection
    {
        $query = $this->model
            ->distinct()
            ->selectRaw('cast(completed_at as date) as date')
            ->where(['user_id' => $user->getKey()])
            ->whereMonth('completed_at', '=', $month ?? Carbon::now()->month)
            ->whereYear('completed_at', '=', $year ?? Carbon::now()->year)
        ;

        return $query->get();
    }

    public function findDailyMeditationDurationByUser(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = $this->model
            ->selectRaw('DATE(completed_at) as date, SUM(meditations.duration) totalCount')
            ->leftJoin('meditations', 'meditations.id', '=', 'user_meditations.meditation_id')
            ->where('user_meditations.user_id', '=', $user->getKey())
            ->groupByRaw('DATE(completed_at)')
        ;

        if ($startDate) {
            $query->where('user_meditations.completed_at', '>=', $startDate->toDateTimeString());
        }

        if ($endDate) {
            $query->where('user_meditations.completed_at', '<=', $endDate->toDateTimeString());
        }

        return $query->get()->pluck('totalCount', 'date');
    }
}
