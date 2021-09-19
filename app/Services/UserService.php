<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make(($data['password']));

        return $this->userRepository->create($data)->first();
    }

    public function getByEmail(string $email): ?User
    {
        return $this->userRepository->getByEmail($email);
    }
}
