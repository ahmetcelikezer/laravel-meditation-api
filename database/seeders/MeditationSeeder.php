<?php

namespace Database\Seeders;

use App\Models\Meditation;
use Illuminate\Database\Seeder;

class MeditationSeeder extends Seeder
{
    public function run(): void
    {
        Meditation::factory(10)->create();
    }
}
