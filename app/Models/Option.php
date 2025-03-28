<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_correct' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the question that owns the option.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Scope a query to only include correct options.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Get the option label (A, B, C, D).
     */
    public function getLabelAttribute()
    {
        $labels = ['A', 'B', 'C', 'D'];
        return $labels[$this->order - 1] ?? '';
    }

    /**
     * Check if this option is correct for its question.
     */
    public function isCorrect()
    {
        return $this->is_correct;
    }
}
