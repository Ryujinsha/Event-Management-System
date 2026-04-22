<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->timestamp('checked_in_at')->useCurrent();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'training_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
