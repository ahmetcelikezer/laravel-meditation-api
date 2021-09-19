<?php

namespace App\Services\Meditation;

use App\Models\Meditation;
use App\Repositories\MeditationRepositoryInterface;

class MeditationService
{
    public function __construct(private MeditationRepositoryInterface $meditationRepository)
    {
    }

    public function getById(string $id): ?Meditation
    {
        return $this->meditationRepository->findOneById($id);
    }
}
