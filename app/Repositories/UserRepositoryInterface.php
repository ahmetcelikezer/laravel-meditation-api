<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getByEmail(string $email): ?User;
}
