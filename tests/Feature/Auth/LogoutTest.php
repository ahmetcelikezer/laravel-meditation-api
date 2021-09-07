<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

/**
 * @internal
 * @covers \App\Http\Controllers\Auth\LogoutController
 */
class LogoutTest extends TestCase
{
    public function test_logout_successfully(): void
    {
        $email = $this->faker('tr_TR')->email();
        $password = $this->faker('tr_TR')->password(8);

        $this->createUser($email, $password);

        $loginResponse = $this->loginUser($email, $password);
        $token = $this->getTokenFromResponse($loginResponse);

        $response = $this->logoutUser($token);

        $response->assertOk();
    }

    public function test_visitor_access_logout(): void
    {
        $response = $this->postJson(self::LOGOUT_PATH);

        $response->assertUnauthorized();
    }
}
