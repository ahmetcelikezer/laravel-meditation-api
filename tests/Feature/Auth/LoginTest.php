<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

/**
 * @internal
 * @covers \App\Http\Controllers\Auth\LoginController
 */
class LoginTest extends TestCase
{
    public function test_login_successfully(): void
    {
        $email = $this->faker('tr_TR')->email();
        $password = $this->faker('tr_TR')->password(8);

        $this->createUser($email, $password);

        $response = $this->loginUser($email, $password);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'email',
                'created_at',
                'updated_at',
            ],
            'access_token',
        ]);
    }

    public function test_login_with_invalid_credentials(): void
    {
        $email = $this->faker('tr_TR')->email();
        $password = $this->faker('tr_TR')->password(8);

        $this->createUser($email, $password);

        $invalidPassword = $this->faker('tr_TR')->password(8);
        $response = $this->loginUser($email, $invalidPassword);

        $response->assertUnauthorized();
    }

    /**
     * @dataProvider failCasesProvider
     */
    public function test_fail_cases(array $data, string $expectedField, int $expectedStatusCode): void
    {
        $response = $this->postJson(self::LOGIN_PATH, $data);

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
}
