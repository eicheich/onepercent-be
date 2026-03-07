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
        Schema::table('user_daily_challenges', function (Blueprint $table) {
            $table->string('challenge_id')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->index('challenge_id');
            $table->index(['user_id', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_daily_challenges', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_completed']);
            $table->dropIndex(['challenge_id']);
            $table->dropColumn(['challenge_id', 'is_completed', 'completed_at', 'expires_at']);
        });
    }
};
