<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the questions for the category.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the category's active questions.
     */
    public function activeQuestions()
    {
        return $this->questions()->active();
    }

    /**
     * Get the category's questions count.
     */
    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }

    /**
     * Get the category's active questions count.
     */
    public function getActiveQuestionsCountAttribute()
    {
        return $this->activeQuestions()->count();
    }
} 