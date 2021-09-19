<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface
{
    public function create(array $attributes): Model;

    public function findOneById(string $id): ?Model;

    public function findAll(): Collection;
}
