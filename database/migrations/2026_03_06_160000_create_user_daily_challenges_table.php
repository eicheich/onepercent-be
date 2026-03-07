<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_daily_challenges', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->date('challenge_date');
            $table->string('title', 180);
            $table->text('content');
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'challenge_date']);
            $table->index('user_id');
            $table->index('challenge_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_daily_challenges');
    }
};
