<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAnswer extends Model
{
    protected $fillable = [
        'exam_attempt_id',
        'question_id',
        'selected_option',
        'is_reviewed'
    ];

    protected $casts = [
        'is_reviewed' => 'boolean'
    ];

    /**
     * Get the exam attempt that owns the answer.
     */
    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    /**
     * Get the question that owns the answer.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Check if the answer is correct.
     */
    public function isCorrect()
    {
        if (!$this->question || !$this->selected_option) {
            return false;
        }

        return $this->question->correct_option === $this->selected_option;
    }

    /**
     * Get the explanation for the answer.
     */
    public function getExplanation()
    {
        return $this->question->explanation;
    }

    /**
     * Get the correct option for the question.
     */
    public function getCorrectOption()
    {
        return $this->question->correct_option;
    }

    /**
     * Get the selected option text.
     */
    public function getSelectedOptionText()
    {
        if (!$this->selected_option) {
            return null;
        }

        $option = $this->question->options->first(function ($option) {
            return $option->order === ord($this->selected_option) - 97;
        });

        return $option ? $option->option_text : null;
    }

    /**
     * Get the correct option text.
     */
    public function getCorrectOptionText()
    {
        $option = $this->question->options->first(function ($option) {
            return $option->order === ord($this->question->correct_option) - 97;
        });

        return $option ? $option->option_text : null;
    }

    /**
     * Get the points for this answer.
     */
    public function getPoints()
    {
        return $this->isCorrect() ? $this->question->points : 0;
    }

    /**
     * Get the time spent on this question.
     */
    public function getTimeSpent()
    {
        return $this->time_spent ?? 0;
    }

    /**
     * Get the difficulty level of the question.
     */
    public function getDifficultyLevel()
    {
        return $this->question->difficulty_level;
    }

    /**
     * Get the category of the question.
     */
    public function getCategory()
    {
        return $this->question->category;
    }

    /**
     * Get the category name of the question.
     */
    public function getCategoryName()
    {
        return $this->question->category->name;
    }

    /**
     * Get the category slug of the question.
     */
    public function getCategorySlug()
    {
        return $this->question->category->slug;
    }

    /**
     * Get the question text.
     */
    public function getQuestionText()
    {
        return $this->question->question_text;
    }

    /**
     * Get all options for the question.
     */
    public function getOptions()
    {
        return $this->question->options;
    }

    /**
     * Get the option text by order.
     */
    public function getOptionText($order)
    {
        $option = $this->question->options->first(function ($option) use ($order) {
            return $option->order === $order;
        });

        return $option ? $option->option_text : null;
    }

    /**
     * Get the option text by letter.
     */
    public function getOptionTextByLetter($letter)
    {
        return $this->getOptionText(ord($letter) - 97);
    }

    /**
     * Get the selected option letter.
     */
    public function getSelectedOptionLetter()
    {
        return $this->selected_option;
    }

    /**
     * Get the correct option letter.
     */
    public function getCorrectOptionLetter()
    {
        return $this->question->correct_option;
    }

    /**
     * Get the selected option order.
     */
    public function getSelectedOptionOrder()
    {
        return $this->selected_option ? ord($this->selected_option) - 97 : null;
    }

    /**
     * Get the correct option order.
     */
    public function getCorrectOptionOrder()
    {
        return ord($this->question->correct_option) - 97;
    }

    /**
     * Get the selected option object.
     */
    public function getSelectedOption()
    {
        if (!$this->selected_option) {
            return null;
        }

        return $this->question->options->first(function ($option) {
            return $option->order === ord($this->selected_option) - 97;
        });
    }

    /**
     * Get the selected option ID.
     */
    public function getSelectedOptionId()
    {
        $option = $this->getSelectedOption();
        return $option ? $option->id : null;
    }

    /**
     * Get the correct option ID.
     */
    public function getCorrectOptionId()
    {
        $option = $this->getCorrectOption();
        return $option ? $option->id : null;
    }
} 