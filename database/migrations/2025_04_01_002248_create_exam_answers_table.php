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
       
            Schema::create('exam_answers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_attempt_id');
                $table->unsignedBigInteger('question_id');
                $table->string('selected_option')->nullable();
                $table->boolean('is_reviewed')->default(false);
                $table->integer('time_spent')->nullable(); // Optional: store time spent on the question
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                
                // Add deleted_at for soft deletes, defaulting to NULL
                $table->timestamp('deleted_at')->nullable();
    
                // Foreign key constraints
                $table->foreign('exam_attempt_id')
                      ->references('id')
                      ->on('exam_attempts')
                      ->onDelete('cascade');
    
                $table->foreign('question_id')
                      ->references('id')
                      ->on('questions')
                      ->onDelete('cascade');
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
