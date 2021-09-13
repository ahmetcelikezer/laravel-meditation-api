<?php

namespace Tests;

use App\Models\Meditation;
use App\Models\User;
use App\Models\UserMeditation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;

    protected const REGISTER_PATH = '/api/v1/auth/register';
    protected const LOGIN_PATH = '/api/v1/auth/login';
    protected const LOGOUT_PATH = '/api/v1/auth/logout';
    protected const COMPLETE_MEDITATION_PATH = '/api/v1/meditation/complete';
    protected const MONTHLY_DAY_REPORT_PATH = '/api/v1/meditation/report/active_days_in_month';

    protected function createUser(?string $email = null, ?string $password = null): User
    {
        if (!$email) {
            $email = $this->faker('tr_TR')->email();
        }

        if (!$password) {
            $password = $this->faker('tr_TR')->password(8);
        }

        return User::create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    protected function getTokenFromResponse(TestResponse $response): ?string
    {
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $content['access_token'] ?? null;
    }

    protected function loginUser(string $email, string $password): TestResponse
    {
        return $this->postJson(self::LOGIN_PATH, [
            'email' => $email,
            'password' => $password,
        ]);
    }

    protected function registerUser(string $email, string $password): TestResponse
    {
        return $this->postJson(self::REGISTER_PATH, [
            'email' => $email,
            'password' => $password,
        ]);
    }

    protected function logoutUser(string $token): TestResponse
    {
        return $this->withToken($token)->postJson(self::LOGOUT_PATH);
    }

    protected function createAuthenticatedToken(): string
    {
        $email = $this->faker('tr_TR')->email();
        $password = $this->faker('tr_TR')->password(8);

        $this->createUser($email, $password);

        $authenticatedUser = $this->loginUser($email, $password);

        return $this->getTokenFromResponse($authenticatedUser);
    }

    protected function createAuthenticatedUserWithToken(): array
    {
        $email = $this->faker('tr_TR')->email();
        $password = $this->faker('tr_TR')->password(8);

        $user = $this->createUser($email, $password);

        $authenticatedUser = $this->loginUser($email, $password);
        $token = $this->getTokenFromResponse($authenticatedUser);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    protected function createMeditation(?string $name = null, ?int $duration = null): Meditation
    {
        return Meditation::create([
            'title' => $name ?? (string) $this->faker('tr_TR')->words(2, true),
            'duration' => $duration ?? $this->faker->numberBetween(120, 5400),
        ]);
    }

    protected function completeMeditation(Meditation $meditation, User $user, ?Carbon $completedAt = null): UserMeditation
    {
        if (!$completedAt) {
            $completedAt = Carbon::now();
        }

        return UserMeditation::create([
            'user_id' => $user->getKey(),
            'meditation_id' => $meditation->getKey(),
            'completed_at' => $completedAt->format('Y-m-d H:i:s'),
        ]);
    }
}
