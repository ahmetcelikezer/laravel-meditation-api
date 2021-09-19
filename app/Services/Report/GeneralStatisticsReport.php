<?php

namespace App\Services\Report;

class GeneralStatisticsReport
{
    public function __construct(public int $totalMeditationCount, public int $totalMeditationDuration, public int $longestConsecutiveDays)
    {
    }
}
