<?php

namespace Tests;

use App\Models\User;
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

    protected function createUser(string $email, string $password): User
    {
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
}
