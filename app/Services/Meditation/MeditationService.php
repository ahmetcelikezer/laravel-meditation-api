<?php

namespace App\Services\Meditation;

use App\Models\Meditation;

class MeditationService
{
    public function getById(string $id): ?Meditation
    {
        return Meditation::where([
            'id' => $id,
        ])->first();
    }
}
