<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeditationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('meditations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->mediumInteger('duration');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meditations');
    }
}
