<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @internal
 * @covers \App\Http\Controllers\Auth\RegisterController
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const REGISTER_PATH = '/api/v1/auth/register';
    private const LOGIN_PATH = '/api/v1/auth/login';

    public function test_register_user_successfully(): void
    {
        $email = $this->faker('tr_TR')->email();
        $password = $this->faker('tr_TR')->password(8);

        $response = $this->registerUser($email, $password);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function test_register_user_with_duplicated_username(): void
    {
        $email = $this->faker('tr_TR')->email();
        $password = $this->faker('tr_TR')->password(8);

        $this->createUser($email, $password);

        $response = $this->registerUser($email, $password);

        $response->assertJsonValidationErrors('email');
    }

    public function test_register_with_token(): void
    {
        $email = $this->faker('tr_TR')->email();
        $password = $this->faker('tr_TR')->password(8);

        $response = $this->registerUser($email, $password);
        $token = $this->getTokenFromResponse($response);

        $result = $this->withToken($token)->postJson(self::REGISTER_PATH, [
            'email' => $email,
            'password' => $password,
        ]);

        $result->assertStatus(401);
    }

    /**
     * @dataProvider failCasesProvider
     */
    public function test_fail_cases(array $data, string $expectedField, int $expectedStatusCode): void
    {
        $response = $this->postJson(self::REGISTER_PATH, $data);

        $response->assertStatus($expectedStatusCode);
        $response->assertJsonValidationErrors($expectedField);
    }

    /**
     * @return array<string, array>
     */
    public function failCasesProvider(): array
    {
        return [
            'Empty email' => [
                ['email' => '', 'password' => $this->faker('tr_TR')->password(8)],
                'email',
                422,
            ],
            'Invalid email' => [
                ['email' => 'malformed_email', 'password' => $this->faker('tr_TR')->password(8)],
                'email',
                422,
            ],
            'Empty password' => [
                ['email' => $this->faker('tr_TR')->email(), 'password' => ''],
                'password',
                422,
            ],
            'Invalid password' => [
                ['email' => $this->faker('tr_TR')->email(), 'password' => $this->faker('tr_TR')->password(maxLength: 7)],
                'password',
                422,
            ],
        ];
    }

    private function registerUser(string $email, string $password): TestResponse
    {
        return $this->postJson(self::REGISTER_PATH, [
            'email' => $email,
            'password' => $password,
        ]);
    }

    private function getTokenFromResponse(TestResponse $response): ?string
    {
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $content['access_token'] ?? null;
    }
}
