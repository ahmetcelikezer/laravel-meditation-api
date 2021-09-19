<?php

namespace Tests\Feature\Report;

use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CreateGeneralStatisticsReportTest extends TestCase
{
    public function test_general_statistics_report_successfully_created(): void
    {
        $currentDate = Carbon::create(2021, 06, 15);

        $meditation1 = $this->createMeditation(duration: 300);
        $meditation2 = $this->createMeditation(duration: 325);
        $meditation3 = $this->createMeditation(duration: 200);
        $meditation4 = $this->createMeditation(duration: 250);
        $meditation5 = $this->createMeditation(duration: 300);
        $meditation6 = $this->createMeditation(duration: 330);

        $validUser = $this->createAuthenticatedUserWithToken();
        $invalidUser = $this->createUser();

        $this->completeMeditation($meditation1, $invalidUser, (clone $currentDate)->subDays(6));

        $this->completeMeditation($meditation2, $validUser['user'], (clone $currentDate)->subMonths(2));
        $this->completeMeditation($meditation3, $validUser['user'], (clone $currentDate)->subDays(2));
        $this->completeMeditation($meditation4, $validUser['user'], (clone $currentDate)->subDay());
        $this->completeMeditation($meditation5, $validUser['user'], $currentDate);
        $this->completeMeditation($meditation6, $validUser['user'], (clone $currentDate)->subDays(5));

        $startDate = (clone $currentDate)->subMonth();
        $endDate = $currentDate;

        $expectedResponse = [
            'general_statistics' => [
                'total_meditation_count' => 4,
                'total_meditation_duration' => 1080,
                'longest_consecutive_days' => 3,
            ],
        ];

        $response = $this->getReport($validUser['token'], $startDate, $endDate);

        $response->assertOk();
        $response->assertJson($expectedResponse);
    }

    private function getReport(string $token, Carbon $startDate, Carbon $endDate): TestResponse
    {
        $url = self::GENERAL_STATISTICS_REPORT_PATH;

        $url .= sprintf('?startDate=%s&endDate=%s', $startDate->toISOString(), $endDate->toISOString());

        return $this->withToken($token)->get($url);
    }
}
