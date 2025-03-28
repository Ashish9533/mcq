<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'time_spent',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'status',
        'score',
        'total_questions',
        'correct_answers',
        'incorrect_answers',
        'unanswered_questions',
        'answers',
        'reviewed_questions',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'time_spent' => 'integer',
        'answers' => 'array',
        'reviewed_questions' => 'array',
        'score' => 'integer',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'incorrect_answers' => 'integer',
        'unanswered_questions' => 'integer'
    ];

    /**
     * Get the user that owns the exam attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the answers for the exam attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    /**
     * Calculate the score for this exam attempt.
     */
    public function calculateScore(): void
    {
        $this->score = $this->correct_answers;
        $this->save();
    }

    /**
     * Check if the exam attempt is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the exam attempt is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the exam attempt has expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Check if the exam attempt is disqualified.
     */
    public function isDisqualified(): bool
    {
        return $this->status === 'disqualified';
    }

    /**
     * Get the remaining time in seconds.
     */
    public function getRemainingTime(): int
    {
        if (!$this->isInProgress()) {
            return 0;
        }

        $maxDuration = config('exam.max_duration', 7200); // Default 2 hours
        $timeSpent = $this->time_spent;
        return max(0, $maxDuration - $timeSpent);
    }

    /**
     * Get the percentage of time spent.
     */
    public function getTimeSpentPercentage(): float
    {
        $maxDuration = config('exam.max_duration', 7200);
        return min(100, ($this->time_spent / $maxDuration) * 100);
    }

    /**
     * Get the number of questions answered.
     */
    public function getAnsweredCount(): int
    {
        return $this->correct_answers + $this->incorrect_answers;
    }

    /**
     * Get the number of questions reviewed.
     */
    public function getReviewCount(): int
    {
        return count($this->reviewed_questions ?? []);
    }

    /**
     * Get the completion percentage.
     */
    public function getCompletionPercentage(): float
    {
        if ($this->total_questions === 0) {
            return 0;
        }
        return ($this->getAnsweredCount() / $this->total_questions) * 100;
    }

    /**
     * Get the review percentage.
     */
    public function getReviewPercentage(): float
    {
        if ($this->total_questions === 0) {
            return 0;
        }
        return ($this->getReviewCount() / $this->total_questions) * 100;
    }

    /**
     * Get the accuracy percentage.
     */
    public function getAccuracyPercentage(): float
    {
        $answered = $this->getAnsweredCount();
        if ($answered === 0) {
            return 0;
        }
        return ($this->correct_answers / $answered) * 100;
    }

    /**
     * Complete the exam attempt.
     */
    public function complete(): void
    {
        $this->status = 'completed';
        $this->end_time = now();
        $this->calculateScore();
        $this->save();
    }

    /**
     * Expire the exam attempt.
     */
    public function expire(): void
    {
        $this->status = 'expired';
        $this->end_time = now();
        $this->calculateScore();
        $this->save();
    }

    /**
     * Disqualify the exam attempt.
     */
    public function disqualify(string $reason = null): void
    {
        $this->status = 'disqualified';
        $this->end_time = now();
        $this->notes = $reason;
        $this->save();
    }

    /**
     * Update the progress of the exam attempt.
     */
    public function updateProgress(int $correct, int $incorrect, int $unanswered): void
    {
        $this->correct_answers = $correct;
        $this->incorrect_answers = $incorrect;
        $this->unanswered_questions = $unanswered;
        $this->save();
    }

    /**
     * Update the time spent for the exam attempt.
     */
    public function updateTimeSpent(): void
    {
        if ($this->isInProgress()) {
            $this->time_spent = now()->diffInSeconds($this->start_time);
            $this->save();
        }
    }
} 