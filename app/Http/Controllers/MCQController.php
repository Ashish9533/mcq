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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Set a scheduled start time (IST) for the exam.
        $scheduledStartTime = Carbon::parse('2025-04-01 14:35:00', 'Asia/Kolkata');

        // Calculate exam duration and remaining time.
        $examDuration = 2 * 60; // 120 minutes in seconds.
        $endTime = $scheduledStartTime->copy()->addSeconds($examDuration);
        $remainingTime = (int) $endTime->diffInSeconds(now()->setTimezone('Asia/Kolkata'), absolute: true);
        $warningCount = Session::get('warning_count');

        // Optionally, check if it's not time to start the exam yet.
        if (now()->lt($scheduledStartTime)) {
            return view('mcq.waiting', compact('scheduledStartTime'));
        }

        if (now()->gt($endTime)) {
            return redirect()->route('exam.ended', [
                'start' => $scheduledStartTime->timestamp,
                'end' => $endTime->timestamp
            ]);
        }

        // Initialize the exam session (this sets start time, device info, etc.).
        $this->initializeExamSession($scheduledStartTime, $examDuration); // Pass scheduled start time.

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





        // dd($remainingTime );
        return view('mcq.index', compact(
            'categories',
            'totalQuestions',
            'allQuestions',
            'remainingTime',
            'examDuration',
            'warningCount'
        ));
    }

    /**
     * Initialize the exam session with security measures.
     */
    private function initializeExamSession($startTime = null, $examDuration = null)
    {
        $agent = new Agent();

        // Use the scheduled start time if provided; otherwise, use now().
        $examStartTime = $startTime ?: now();
        Session::put('exam_started', true);
        if (!Session::has('exam_start_time')) {
            Session::put('exam_start_time', $examStartTime);
        }

        Session::put('exam_ip', request()->ip());
        Session::put('exam_user_agent', request()->userAgent());
        Session::put('exam_device_fingerprint', $this->generateDeviceFingerprint($agent));
        Session::put('last_activity', now());
        Session::put('tab_switches', 0);
        Session::put('answers', []);
        Session::put('reviewed_questions', []);
        if (!Session::has('warning_count')) {
            Session::put('warning_count', 0);
        }




        $endTime = $examStartTime->copy()->addSeconds($examDuration);

        // Create an exam attempt record.
        ExamAttempt::create([
            'user_id' => auth()->id(),
            'start_time' => $examStartTime,
            'end_time' => $endTime,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_fingerprint' => $this->generateDeviceFingerprint($agent),
            'status' => 'in_progress'
        ]);
    }



    /**
     * Auto submit exam when expired or suspicious activity detected
     */
    private function autoSubmitExam(Request $request)
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
            $timeSpent = $startTime ? (int) now()->diffInSeconds($startTime) : 0;


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
                'reviewed_questions',
                'warning_count'
            ]);

            Session::flush();

            // Logout the user
            Auth::logout();

            // Invalidate the session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
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
     * Get question details for AJAX request
     */

    /**
     * Submit exam answers
     */
    public function submitExam(Request $request)
    {
        // Get the exam session
        $examSession = ExamAttempt::where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        activity('Exam Submitted')
            ->causedBy(Auth::user())
            ->withProperties(['ip' => $request->ip(), 'email' => auth()->user()->email])
            ->log('Exam is submitted by user at ' . Carbon::now() . ' - Session ID: ' . ($examSession ? $examSession->id : 'N/A'));

        if (!$examSession) {
            return response()->json(['error' => 'No active exam session found'], 404);
        }

        // Calculate time spent from the request value (adjusted as needed)
        $timeSpent = (int) $request->timeSpent - 1;

        // Start the transaction
        DB::beginTransaction();
        try {
            // Update exam session
            $examSession->update([
                'status' => 'completed',
                'time_spent' => $timeSpent,
                'submitted_at' => now()
            ]);

            // Process answers
            $answers = $request->input('answers', []);
            $reviewedQuestions = $request->input('reviewed_questions', []);

            
            $correct = 0;
            $incorrect = 0;
            $unanswered = 0;



            // Update attempt with scores



            foreach ($answers as $answer) {


                if (isset($answer['question']) && isset($answer['answer'])) {
                    ExamAnswer::create([
                        'exam_attempt_id' => $examSession->id,
                        'question_id' => $answer['question'],
                        'selected_option' => $answer['answer'],
                        'is_reviewed' => in_array($answer['question'], $reviewedQuestions)
                    ]);
                }


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


            $examSession->update([
                'score' => count($answers) > 0 ? ($correct / count($answers)) * 100 : 0,
                'total_questions' => count($answers),
                'correct_answers' => $correct,
                'incorrect_answers' => $incorrect,
                'unanswered_questions' => $unanswered
            ]);
         

            // Commit the transaction if everything is successful
            DB::commit();
        } catch (\Exception $e) {
            // Roll back the transaction on error
            DB::rollBack();

            // Log the error using an activity log
            activity('Exam Submission Error')
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'email' => auth()->user()->email,
                    'error' => $e->getMessage()
                ])
                ->log('Exam submission error occurred at ' . Carbon::now() . ' - Session ID: ' . $examSession->id);

            // Optionally, you can also log to the default logger
            Log::error('Exam submission failed', ['error' => $e->getMessage(), 'session_id' => $examSession->id]);

            return response()->json(['error' => 'Exam submission failed. Please try again.'], 500);
        }

        // Log out and clear session data
        Auth::logout();
        $request->session()->invalidate();
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
            'reviewed_questions',
            'warning_count'
        ]);
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
        ], 200);
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


    public function showEndedExam(Request $request)
    {
        $data = [
            'start' => Carbon::createFromTimestamp($request->start, 'Asia/Kolkata'),
            'end' => Carbon::createFromTimestamp($request->end, 'Asia/Kolkata'),
            'duration' => ($request->end - $request->start) / 60 . ' mins'
        ];

        return view('mcq.after-exam', $data);
    }




    public function thanksCandidate(Request $request)
    {


        return view('mcq.thanks');
    }



    public function beforeTimeSubmit(Request $request)
    {


        // First, verify that the email from the request matches the authenticated user's email
        $submittedEmail = $request->input('email');
        $authEmail = auth()->user()->email;

        if ($submittedEmail !== $authEmail) {
            return response()->json(['error' => 'Email verification failed.'], 422);
        }

        // Retrieve the active exam session for the user
        $examSession = ExamAttempt::where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        // Log the exam submission activity
        activity('Exam Submitted')
            ->causedBy(Auth::user())
            ->withProperties(['ip' => $request->ip(), 'email' => $authEmail])
            ->log('Exam is submitted by user at ' . Carbon::now() . ' - Session ID: ' . ($examSession ? $examSession->id : 'N/A'));

        if (!$examSession) {
            return response()->json(['error' => 'No active exam session found'], 404);
        }

        // Calculate time spent (adjusted as needed)
        $timeSpent = (int) $request->timeSpent - 1;

        // Start the transaction
        DB::beginTransaction();
        try {

            // Update exam session status and time spent
            $examSession->update([
                'status' => 'completed',
                'time_spent' => $timeSpent,
                'submitted_at' => now()
            ]);

            // Process answers
            $answers = $request->input('answers', []);
            $reviewedQuestions = $request->input('reviewed_questions', []);




            $correct = 0;
            $incorrect = 0;
            $unanswered = 0;



            // Update attempt with scores



            foreach ($answers as $answer) {


                if (isset($answer['question']) && isset($answer['answer'])) {
                    ExamAnswer::create([
                        'exam_attempt_id' => $examSession->id,
                        'question_id' => $answer['question'],
                        'selected_option' => $answer['answer'],
                        'is_reviewed' => in_array($answer['question'], $reviewedQuestions)
                    ]);
                }


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


            $examSession->update([
                'score' => count($answers) > 0 ? ($correct / count($answers)) * 100 : 0,
                'total_questions' => count($answers),
                'correct_answers' => $correct,
                'incorrect_answers' => $incorrect,
                'unanswered_questions' => $unanswered
            ]);

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Roll back on error
            DB::rollBack();

            // Log the error activity
            activity('Exam Submission Error')
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'email' => $authEmail,
                    'error' => $e->getMessage()
                ])
                ->log('Exam submission error occurred at ' . Carbon::now() . ' - Session ID: ' . $examSession->id);

            Log::error('Exam submission failed', [
                'error' => $e->getMessage(),
                'session_id' => $examSession->id
            ]);

            return response()->json(['error' => 'Exam submission failed. Please try again.'], 500);
        }

        // Log out and clear session data
        Auth::logout();
        $request->session()->invalidate();
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
            'reviewed_questions',
            'warning_count'
        ]);
        $request->session()->regenerateToken();

        return response()->json(['success' => true], 200);
    }



}


