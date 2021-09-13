<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserMeditationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('user_meditations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('meditation_id');
            $table->foreignUuid('user_id');
            $table->timestamps();
            $table->timestamp('completed_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_meditations');
    }
}
