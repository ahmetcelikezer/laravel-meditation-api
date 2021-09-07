<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(array $data): User
    {
        $data['password'] = Hash::make(($data['password']));

        return User::create($data)->first();
    }

    public function getByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
