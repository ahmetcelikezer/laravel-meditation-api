<?php

namespace Tests\Feature\Meditation;

use App\Models\Meditation;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @internal
 * @covers \App\Http\Controllers\Meditation\MeditationController
 */
class CompleteMeditationTest extends TestCase
{
    public function test_complete_meditation_successfully(): void
    {
        /** @var Meditation $meditation */
        $meditation = Meditation::factory()->create();
        $token = $this->createAuthenticatedToken();

        $response = $this->completeMeditationWithToken($meditation->getKey(), $token);

        $this->assertDatabaseHas('user_meditations', [
            'meditation_id' => $meditation->getKey(),
        ]);
        $response->assertOk();
    }

    public function test_visitor_complete_meditation_failed(): void
    {
        /** @var Meditation $meditation */
        $meditation = Meditation::factory()->create();

        $response = $this->completeMeditationAsGuest($meditation->getKey());

        $response->assertUnauthorized();
    }

    private function completeMeditationWithToken(string $id, string $token): TestResponse
    {
        $url = sprintf('%s/%s', self::COMPLETE_MEDITATION_PATH, $id);

        return $this->withToken($token)->postJson($url);
    }

    private function completeMeditationAsGuest(string $id): TestResponse
    {
        $url = sprintf('%s/%s', self::COMPLETE_MEDITATION_PATH, $id);

        return $this->postJson($url);
    }
}
