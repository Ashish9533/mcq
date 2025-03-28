<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\Facades\Activity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class MCQController extends Controller
{
    /**
     * Display the MCQ exam interface.
     */
    public function index()
    {
        // If exam authentication is required, redirect unauthenticated users.
        if (config('exam.require_authentication') && !auth()->check()) {
            return redirect()->route('login');
        }

        // If the user already has an active exam session, handle it.
        if (Session::has('exam_started')) {
            return $this->handleActiveSession();
        }

        // Retrieve active categories along with their active questions and options.
        $categories = Category::with([
            'questions' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('order')
                    ->with([
                        'options' => function ($query) {
                            $query->orderBy('order');
                        }
                    ]);
            }
        ])
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Randomize questions if configured.
        if (config('exam.randomize_questions')) {
            $categories->each(function ($category) {
                $category->questions = $category->questions->shuffle();
            });
        }

        // Count total active questions.
        $totalQuestions = Question::where('is_active', true)->count();

        // Initialize the exam session (this sets start time, device info, etc.).
        $this->initializeExamSession();

        // Prepare all questions data for JavaScript.
        $allQuestions = [];
        foreach ($categories as $category) {
            foreach ($category->questions as $question) {
                $allQuestions[] = [
                    'id' => $question->id,
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                    'question_text' => $question->question_text,
                    'options' => $question->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'text' => $option->option_text,
                        ];
                    }),
                ];
            }
        }

        // Retrieve the exam start time from the session.
        $examStartTime = session('exam_start_time');
        $examDuration = 120 * 60; // 120 minutes in seconds.
        $endTime = Carbon::parse($examStartTime)->addSeconds($examDuration);
        $remainingTime = $endTime->diffInSeconds(now(), false);

        return view('mcq.index', compact('categories', 'totalQuestions', 'allQuestions', 'remainingTime'));
    }

    /**
     * Initialize the exam session with security measures.
     */
    private function initializeExamSession()
    {
        $agent = new Agent();

        // Set exam session data.
        Session::put('exam_started', true);
        Session::put('exam_start_time', now());
        Session::put('exam_ip', request()->ip());
        Session::put('exam_user_agent', request()->userAgent());
        Session::put('exam_device_fingerprint', $this->generateDeviceFingerprint($agent));
        Session::put('last_activity', time()); // Using time() here is acceptable for tracking activity.
        Session::put('tab_switches', 0);
        Session::put('answers', []);
        Session::put('reviewed_questions', []);

        // Calculate exam end time using the stored start time.
        $examStartTime = session('exam_start_time');
        $examDuration = 120 * 60; // 120 minutes in seconds.
        $endTime = Carbon::parse($examStartTime)->addSeconds($examDuration);

        // Create an exam attempt record.
        ExamAttempt::create([
            'user_id' => auth()->id(),
            'start_time' => now(),
            'end_time' => $endTime,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_fingerprint' => $this->generateDeviceFingerprint($agent),
            'status' => 'in_progress'
        ]);
    }

    /**
     * Handle active exam session
     */
    private function handleActiveSession()
    {
        // Convert the stored exam end time to a Carbon instance.
        $examEndTime = Carbon::parse(Session::get('exam_end_time'));

        // Check if the exam has expired.
        if (now()->greaterThan($examEndTime)) {
            return $this->autoSubmitExam();
        }

        // Check for suspicious activity.
        if ($this->detectSuspiciousActivity()) {
            return $this->handleSuspiciousActivity();
        }

        // Continue with the exam.
        return $this->continueExam();
    }

    /**
     * Auto submit exam when expired or suspicious activity detected
     */
    private function autoSubmitExam()
    {
        try {
            // Begin transaction
            \DB::beginTransaction();

            // Get the active exam attempt
            $attempt = ExamAttempt::where('user_id', auth()->id())
                ->where('status', 'in_progress')
                ->latest()
                ->first();

            if (!$attempt) {
                throw new \Exception('No active exam session found');
            }

            // Get current answers from session
            $answers = Session::get('answers', []);
            $reviewedQuestions = Session::get('reviewed_questions', []);

            // Calculate time spent in seconds (ensure it's an integer)
            $startTime = Session::get('exam_start_time');
            $timeSpent = $startTime ? (int) (time() - $startTime) : 0;

            // Update attempt data
            $attempt->update([
                'end_time' => now(),
                'time_spent' => $timeSpent,
                'status' => 'completed',
                'answers' => $answers,
                'reviewed_questions' => $reviewedQuestions
            ]);

            // Calculate scores
            $correct = 0;
            $incorrect = 0;
            $unanswered = 0;

            foreach ($answers as $answer) {
                $question = Question::find($answer['question']);
                if (!$question)
                    continue;

                if (empty($answer['answer'])) {
                    $unanswered++;
                } else {
                    $isCorrect = $question->options()
                        ->where('id', $answer['answer'])
                        ->where('is_correct', true)
                        ->exists();

                    if ($isCorrect) {
                        $correct++;
                    } else {
                        $incorrect++;
                    }
                }
            }

            // Update attempt with scores
            $attempt->update([
                'score' => count($answers) > 0 ? ($correct / count($answers)) * 100 : 0,
                'total_questions' => count($answers),
                'correct_answers' => $correct,
                'incorrect_answers' => $incorrect,
                'unanswered_questions' => $unanswered
            ]);

            // Save individual answers
            foreach ($answers as $answer) {
                ExamAnswer::create([
                    'exam_attempt_id' => $attempt->id,
                    'question_id' => $answer['question'],
                    'selected_option' => $answer['answer'],
                    'is_reviewed' => in_array($answer['question'], $reviewedQuestions)
                ]);
            }

            // Clear session data
            Session::forget([
                'exam_started',
                'exam_start_time',
                'exam_end_time',
                'exam_ip',
                'exam_user_agent',
                'exam_device_fingerprint',
                'last_activity',
                'tab_switches',
                'answers',
                'reviewed_questions'
            ]);

            // Commit transaction
            \DB::commit();

            return redirect()->route('mcq.index')
                ->with('warning', 'Your exam has been automatically submitted due to time expiration or suspicious activity.');

        } catch (\Exception $e) {
            // Rollback transaction
            \DB::rollBack();

            // Log error
            \Log::error('Auto exam submission failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('mcq.index')
                ->with('error', 'There was an error submitting your exam. Please contact support.');
        }
    }

    /**
     * Continue with active exam
     */
    private function continueExam()
    {
        $categories = Category::with([
            'questions' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('order')
                    ->with([
                        'options' => function ($query) {
                            $query->orderBy('order');
                        }
                    ]);
            }
        ])->where('is_active', true)
            ->orderBy('order')
            ->get();

        $totalQuestions = Question::where('is_active', true)->count();

        return view('mcq.index', compact('categories', 'totalQuestions'));
    }

    /**
     * Get question details for AJAX request
     */
    public function getQuestion($id)
    {
        // Validate session
        if (!$this->validateSession()) {
            return response()->json(['error' => 'Invalid session'], 403);
        }

        // Check if question is part of current exam session
        $question = Question::with([
            'options' => function ($query) {
                $query->orderBy('order');
            }
        ])->findOrFail($id);

        // Verify question is active and belongs to an active category
        if (!$question->is_active || !$question->category->is_active) {
            return response()->json(['error' => 'Question not available'], 404);
        }

        // Prepare question data
        $questionData = [
            'id' => $question->id,
            'question' => $question->question_text,
            'category' => $question->category->name,
            'options' => $question->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'text' => $option->option_text
                ];
            })
        ];

        // Encrypt all data using Laravel's encryption
        $encryptedData = [
            'id' => Crypt::encryptString($questionData['id']),
            'question' => Crypt::encryptString($questionData['question']),
            'category' => Crypt::encryptString($questionData['category']),
            'options' => $questionData['options']->map(function ($option) {
                return [
                    'id' => Crypt::encryptString($option['id']),
                    'text' => Crypt::encryptString($option['text'])
                ];
            })
        ];

        // Log access for security monitoring
        \Log::info('Question accessed', [
            'user_id' => auth()->id(),
            'question_id' => $id,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return response()->json($encryptedData);
    }

    /**
     * Submit exam answers
     */
    public function submitExam(Request $request)
    {
        // Validate session
        if (!$this->validateSession()) {
            return response()->json(['error' => 'Invalid session'], 403);
        }

        // Get the exam session
        $examSession = ExamAttempt::where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        activity('Exam Submitted')
            ->causedBy(Auth::user())
            ->withProperties(['ip' => $request->ip(), 'email' => auth()->user()->email])
            ->log('Exam is submitted by users at ' . Carbon::now() . '' . $examSession->id);



        if (!$examSession) {
            return response()->json(['error' => 'No active exam session found'], 404);
        }

        // Calculate time spent (ensure it's an integer)
        $startTime = Session::get('exam_start_time');
        $timeSpent = $startTime ? (int) (time() - $startTime) : 0;

        // Update exam session
        $examSession->update([
            'status' => 'completed',
            'time_spent' => $timeSpent,
            'submitted_at' => now()
        ]);

        // Process answers
        $answers = $request->input('answers', []);
        foreach ($answers as $answer) {
            if (isset($answer['question']) && isset($answer['answer'])) {
                ExamAnswer::create([
                    'exam_attempt_id' => $examSession->id,
                    'question_id' => $answer['question'],
                    'selected_option' => $answer['answer'],
                    'is_reviewed' => in_array($answer['question'], $request->input('reviewed_questions', []))
                ]);
            }
        }

        // Clear session data
        Session::forget(['exam_start_time', 'exam_questions', 'current_question']);

        return response()->json([
            'success' => true,
            'message' => 'Exam submitted successfully',
            'score' => $examSession->score,
            'total_questions' => $examSession->total_questions,
            'correct_answers' => $examSession->correct_answers,
            'incorrect_answers' => $examSession->incorrect_answers,
            'unanswered_questions' => $examSession->unanswered_questions
        ]);
    }

    /**
     * Validate exam session
     */
    private function validateSession()
    {
        if (!Session::has('exam_started')) {
            return false;
        }

        // Check IP address
        if (config('exam.track_ip') && Session::get('exam_ip') !== request()->ip()) {
            return false;
        }

        // Check user agent
        if (config('exam.track_user_agent') && Session::get('exam_user_agent') !== request()->userAgent()) {
            return false;
        }

        // Check device fingerprint
        if (config('exam.track_device_fingerprint')) {
            $agent = new Agent();
            if (Session::get('exam_device_fingerprint') !== $this->generateDeviceFingerprint($agent)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generate device fingerprint
     */
    private function generateDeviceFingerprint($agent)
    {
        return md5(implode('|', [
            $agent->browser(),
            $agent->version($agent->browser()),
            $agent->platform(),
            $agent->version($agent->platform()),
            $agent->device(),
            $agent->robot(),
            $agent->isMobile(),
            $agent->isTablet(),
        ]));
    }

    /**
     * Detect suspicious activity
     */
    private function detectSuspiciousActivity()
    {
        // Check inactivity
        if (time() - Session::get('last_activity') > config('exam.max_inactive_time')) {
            return true;
        }

        // Check tab switches
        if (Session::get('tab_switches') > config('exam.max_tab_switches')) {
            return true;
        }

        return false;
    }

    /**
     * Handle suspicious activity
     */
    private function handleSuspiciousActivity()
    {
        // Log suspicious activity
        \Log::warning('Suspicious exam activity detected', [
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'inactive_time' => time() - Session::get('last_activity'),
            'tab_switches' => Session::get('tab_switches')
        ]);

        // Notify admin if configured
        if (config('exam.notify_admin_on_suspicious')) {
            // Send notification to admin
        }

        // Auto-submit exam if configured
        if (config('exam.auto_submit_on_suspicious')) {
            return $this->autoSubmitExam();
        }

        return redirect()->route('mcq.index')
            ->with('error', 'Suspicious activity detected. Your exam has been submitted.');
    }

    private function getActiveSession()
    {
        return ExamAttempt::where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();
    }
}