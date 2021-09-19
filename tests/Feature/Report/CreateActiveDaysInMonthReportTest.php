<?php

namespace Tests\Feature\Report;

use App\Models\UserMeditation;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @internal
 * @covers \App\Http\Controllers\Report\ReportController
 */
class CreateActiveDaysInMonthReportTest extends TestCase
{
    public function test_report_successfully_created(): void
    {
        $currentDate = Carbon::create(2021, 06, 15);

        $meditation1 = $this->createMeditation();
        $meditation2 = $this->createMeditation();
        $meditation3 = $this->createMeditation();
        $meditation4 = $this->createMeditation();

        $validUser = $this->createAuthenticatedUserWithToken();
        $invalidUser = $this->createUser();

        $this->completeMeditation($meditation3, $invalidUser, $currentDate);
        $this->completeMeditation($meditation4, $validUser['user'], (clone $currentDate)->subDays(16));

        $expectedMeditations = [
            $this->completeMeditation($meditation1, $validUser['user'], (clone $currentDate)->subDay()),
            $this->completeMeditation($meditation2, $validUser['user'], (clone $currentDate)->subDays(2)),
        ];

        $response = $this->getReport($validUser['token'], $currentDate->year, $currentDate->month);

        $response->assertOk();
        $response->assertSimilarJson([
            'active_days' => array_map(static function (UserMeditation $meditation) {
                $completedAt = Carbon::parse($meditation->getAttribute('completed_at'));

                return $completedAt->toDateString();
            }, $expectedMeditations),
        ]);
    }

    private function getReport(string $token, ?int $year = null, ?int $month = null): TestResponse
    {
        $url = self::MONTHLY_DAY_REPORT_PATH;

        if ($year && $month) {
            $url .= sprintf('?year=%s&month=%s', $year, $month);
        }

        return $this->withToken($token)->get($url);
    }
}
