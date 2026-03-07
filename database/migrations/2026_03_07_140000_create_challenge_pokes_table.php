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
        Schema::create('challenge_pokes', function (Blueprint $table) {
            $table->id();
            $table->string('sender_id');
            $table->string('receiver_id');
            $table->string('user_daily_challenge_id');
            $table->string('challenge_id')->nullable();
            $table->string('type', 20)->default('boast');
            $table->string('message', 220)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('user_daily_challenge_id');
            $table->index(['receiver_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_pokes');
    }
};
