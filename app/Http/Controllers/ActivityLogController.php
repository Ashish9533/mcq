<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Option;
use App\Models\Question;


class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        // Filter by user_id
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Search in details
        if ($request->filled('search')) { 
            $query->where('details', 'like', '%' . $request->search . '%');
        }

        // Get unique actions for filter dropdown
        $actions = ActivityLog::distinct()->pluck('action');

        // Get all users for filter dropdown
        $users = User::orderBy('name')->get();

        // Paginate results with 10 items per page
        $activities = $query->latest()->paginate(10)->withQueryString();

        return view('activity-log.index', compact('activities', 'users', 'actions'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by status (active/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('last_active_at', '>=', now()->subMinutes(5));
            } else {
                $query->where(function($q) {
                    $q->whereNull('last_active_at')
                      ->orWhere('last_active_at', '<', now()->subMinutes(5));
                });
            }
        }

        // Get users with pagination
        $users = $query->latest()->paginate(2)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('activity-log.users', compact('users'))->render(),
                'pagination' => view('components.pagination', ['paginator' => $users])->render()
            ]);
        }

        return view('activity-log.users', compact('users'));
    }


    public function userActivities(Request $request ,$user=null){
  


        $query = Activity::query();

        // Filter by user_id
        if (isset($user)) {
            $query->where('causer_id', $user);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('log_name', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Search in details
        if ($request->filled('search')) { 
            $query->where('details', 'like', '%' . $request->search . '%');
        }

        // Get unique actions for filter dropdown
        $actions = Activity::distinct()->pluck('log_name');

     

        // Paginate results with 10 items per page
        $activities = $query->latest()->paginate(10)->withQueryString();

        return view('activity-log.index', compact('activities', 'actions'));











    }



    public function logActivity(Request $request)
    {
        // Ensure the user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get message from the request
        $message = $request->input('message', 'No message provided');

        // Log the activity 
        activity('Suspicious Activity') // Use the default log name
            ->causedBy(auth()->user()) // Associate the activity with the logged-in user
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ])
            ->log($message);

        return response()->json(['success' => true, 'message' => 'Activity logged']);
    }




    // public function getExamResult(Request $request, $userId)
    // {
    //     // Fetch the latest exam attempt for the given user
    //     $examAttempt = ExamAttempt::where('user_id', 2)
    //         ->latest()
    //         ->first();
    
    //     if (!$examAttempt) {
    //         return response()->json(['message' => 'No exam attempt found'], 404);
    //     }
    
    //     // Fetch answers and related questions
    //     $examAnswers = ExamAnswer::where('exam_attempt_id', $examAttempt->id)
    //         ->with(['question.category']) // Load category details
    //         ->get();
    
    //     // Group answers by subject (category)
    //     $subjectResults = [];
    //     foreach ($examAnswers->groupBy('question.category.name') as $subjectName => $answers) {
    //         $totalQuestions = $answers->count();
    //         $correctAnswers = $answers->where('selected_option', '!=', null)
    //             ->where('question.options.is_correct', 1)
    //             ->count();
    //         $incorrectAnswers = $answers->where('selected_option', '!=', null)
    //             ->where('question.options.is_correct', 0)
    //             ->count();
    //         $marked = $answers->where('is_reviewed', true)->count();
    
    //         $subjectResults[] = [
    //             'name' => $subjectName,
    //             'total_questions' => $totalQuestions,
    //             'correct' => $correctAnswers,
    //             'incorrect' => $incorrectAnswers,
    //             'marked' => $marked,
    //             'pass_status' => $correctAnswers >= ($totalQuestions * 0.6) ? 'passed' : 'failed',
    //         ];
    //     }
    
    //     // Prepare overall result
    //     $resultData = [
    //         'overall' => [
    //             'score' => $examAttempt->score,
    //             'total_questions' => $examAttempt->total_questions,
    //             'correct' => $examAttempt->correct_answers,
    //             'incorrect' => $examAttempt->incorrect_answers,
    //             'marked' => $examAnswers->where('is_reviewed', true)->count(),
    //             'passing_percentage' => 60, // Set based on your requirements
    //         ],
    //         'subjects' => $subjectResults,
    //     ];

    //     dd($resultData);
    
    //     return view('mcq.result', compact('resultData'));
       
    // }
    


    public function getExamResult(Request $request, $userId)
{
    // Fetch the latest exam attempt for the given user
    $examAttempt = ExamAttempt::where('user_id', $userId) // Use dynamic userId
        ->latest()
        ->first();

    if (!$examAttempt) {
        return response()->json(['message' => 'No exam attempt found'], 404);
    }

    // Fetch answers and related questions
    $examAnswers = ExamAnswer::where('exam_attempt_id', $examAttempt->id)
        ->with(['question.category', 'question.options']) // Load options for each question
        ->get();

    // Group answers by subject (category)
    $subjectResults = [];
    foreach ($examAnswers->groupBy('question.category.name') as $subjectName => $answers) {
        $totalQuestions = $answers->count();

        $correctAnswers = $answers->filter(function ($answer) {
            return $answer->selected_option !== null && $answer->isCorrect();
        })->count();

        $incorrectAnswers = $answers->filter(function ($answer) {
            return $answer->selected_option !== null && !$answer->isCorrect();
        })->count();

        $marked = $answers->where('is_reviewed', true)->count();

        // Calculate pass status
        $passStatus = ($correctAnswers >= ($totalQuestions * 0.6)) ? 'passed' : 'failed';

        $subjectResults[] = [
            'name' => $subjectName,
            'total_questions' => $totalQuestions,
            'correct' => $correctAnswers,
            'incorrect' => $incorrectAnswers,
            'marked' => $marked,
            'pass_status' => $passStatus,
        ];
    }

    // Prepare overall result
    $resultData = [
        'overall' => [
            'score' => $examAttempt->score,
            'total_questions' => $examAttempt->total_questions,
            'correct' => $examAttempt->correct_answers,
            'incorrect' => $examAttempt->incorrect_answers,
            'marked' => $examAnswers->where('is_reviewed', true)->count(),
            'passing_percentage' => 60, // Set based on your requirements
        ],
        'subjects' => $subjectResults,
    ];

    // Return results or a view
    return view('mcq.result', compact('resultData'));
}

    



} 