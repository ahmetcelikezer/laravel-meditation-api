<?php

namespace App\Providers;

use App\Models\Meditation;
use App\Models\User;
use App\Models\UserMeditation;
use App\Repositories\Eloquent\MeditationRepository;
use App\Repositories\Eloquent\UserMeditationRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\MeditationRepositoryInterface;
use App\Repositories\UserMeditationRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
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

        $this->app->bind(UserRepositoryInterface::class, function () {
            return new UserRepository(new User());
        });
    }
}
