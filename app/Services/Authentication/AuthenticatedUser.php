<?php

namespace App\Services\Authentication;

use App\Models\User;

class AuthenticatedUser
{
    public function __construct(public User $user, public string $token)
    {
    }
}
