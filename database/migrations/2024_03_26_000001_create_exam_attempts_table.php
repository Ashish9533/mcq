<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->integer('time_spent')->default(0); // in seconds
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'expired', 'disqualified'])->default('in_progress');
            $table->integer('score')->nullable();
            $table->integer('total_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('incorrect_answers')->default(0);
            $table->integer('unanswered_questions')->default(0);
            $table->json('answers')->nullable(); // Store all answers as JSON
            $table->json('reviewed_questions')->nullable(); // Store reviewed questions as JSON
            $table->text('notes')->nullable(); // For any additional notes or comments
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
}; 