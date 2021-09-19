<?php

namespace Tests\Feature\Report;

use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @internal
 * @covers \App\Http\Controllers\Report\ReportController
 */
class CreateDailyMeditationDurationReportTest extends TestCase
{
    public function test_daily_meditation_duration_successfully_created(): void
    {
        $currentDate = Carbon::create(2021, 06, 15);

        $meditation1 = $this->createMeditation(duration: 300);
        $meditation2 = $this->createMeditation(duration: 325);
        $meditation3 = $this->createMeditation(duration: 200);
        $meditation4 = $this->createMeditation(duration: 250);
        $meditation5 = $this->createMeditation(duration: 300);

        $validUser = $this->createAuthenticatedUserWithToken();
        $invalidUser = $this->createUser();

        $this->completeMeditation($meditation1, $validUser['user'], (clone $currentDate)->subDays(7));
        $this->completeMeditation($meditation2, $validUser['user'], (clone $currentDate)->subDays(7));
        $this->completeMeditation($meditation3, $validUser['user'], (clone $currentDate)->subDays(8));
        $this->completeMeditation($meditation4, $invalidUser, (clone $currentDate)->subDays(6));
        $this->completeMeditation($meditation5, $validUser['user'], $currentDate);

        $expectedDurations = [
            'daily_duration' => [
                (clone $currentDate)->subDays(7)->toDateString() => $meditation1->getAttribute('duration') + $meditation2->getAttribute('duration'),
                $currentDate->toDateString() => $meditation5->getAttribute('duration'),
            ],
        ];

        $startDate = (clone $currentDate)->subDays(7);
        $endDate = $currentDate;

        $response = $this->getReport($validUser['token'], $startDate, $endDate);

        $response->assertOk();
        $response->assertSimilarJson($expectedDurations);
    }

    private function getReport(string $token, ?Carbon $startDate = null, ?Carbon $endDate = null): TestResponse
    {
        $url = self::DAILY_MEDITATION_DURATION;

        if ($startDate) {
            $url .= sprintf('?startDate=%s', $startDate->toISOString());
        }

        if ($endDate) {
            $url .= sprintf('%sendDate=%s', $startDate ? '&' : '?', $endDate->toISOString());
        }

        return $this->withToken($token)->get($url);
    }
}
