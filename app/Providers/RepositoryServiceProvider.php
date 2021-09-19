<?php

namespace App\Providers;

use App\Models\Meditation;
use App\Models\UserMeditation;
use App\Repository\Eloquent\MeditationRepository;
use App\Repository\Eloquent\UserMeditationRepository;
use App\Repository\MeditationRepositoryInterface;
use App\Repository\UserMeditationRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserMeditationRepositoryInterface::class, function () {
            return new UserMeditationRepository(new UserMeditation());
        });

        $this->app->bind(MeditationRepositoryInterface::class, function () {
            return new MeditationRepository(new Meditation());
        });
    }
}
