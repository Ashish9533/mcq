<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'question_text',
        'explanation',
        'difficulty_level',
        'is_active',
        'order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the category that owns the question.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the options for the question.
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    /**
     * Get the correct option for the question.
     */
    public function correctOption()
    {
        return $this->hasOne(Option::class)->where('is_correct', true);
    }

    /**
     * Scope a query to only include active questions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include questions of a specific category.
     */
    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to only include questions of a specific difficulty level.
     */
    public function scopeDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    /**
     * Get the category name.
     */
    public function getCategoryNameAttribute()
    {
        return $this->category->name;
    }

    /**
     * Get the difficulty level name.
     */
    public function getDifficultyLevelNameAttribute()
    {
        $levels = [
            'easy' => 'Easy',
            'medium' => 'Medium',
            'hard' => 'Hard'
        ];

        return $levels[$this->difficulty_level] ?? $this->difficulty_level;
    }
}
