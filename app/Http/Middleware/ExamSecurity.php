<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;

class ExamSecurity
{
    public function handle(Request $request, Closure $next)
    {
        // Check if exam is in progress
        if (!Session::has('exam_started')) {
            return redirect()->route('mcq.index')->with('error', 'Exam session not started.');
        }

        // Check exam timeout
        if (Session::has('exam_end_time') && time() > Session::get('exam_end_time')) {
            Session::forget(['exam_started', 'exam_end_time']);
            return redirect()->route('mcq.index')->with('error', 'Exam time has expired.');
        }

        // Validate session
        if (!$this->validateSession($request)) {
            return $this->handleInvalidSession($request);
        }

        // Track user activity
        $this->trackUserActivity($request);

        return $next($request);
    }

    private function validateSession(Request $request)
    {
        // Check IP address
        if (Session::has('exam_ip') && Session::get('exam_ip') !== $request->ip()) {
            return false;
        }

        // Check user agent
        if (Session::has('exam_user_agent') && Session::get('exam_user_agent') !== $request->userAgent()) {
            return false;
        }

        // Check device fingerprint
        $agent = new Agent();
        if (Session::has('exam_device_fingerprint')) {
            $currentFingerprint = $this->generateDeviceFingerprint($agent);
            if (Session::get('exam_device_fingerprint') !== $currentFingerprint) {
                return false;
            }
        }

        return true;
    }

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

    private function trackUserActivity(Request $request)
    {
        // Record last activity
        Session::put('last_activity', time());

        // Check for suspicious activity
        if (Session::has('last_activity')) {
            $inactiveTime = time() - Session::get('last_activity');
            if ($inactiveTime > config('exam.max_inactive_time', 300)) {
                $this->handleSuspiciousActivity($request);
            }
        }
    }

    private function handleSuspiciousActivity(Request $request)
    {
        // Log suspicious activity
        \Log::warning('Suspicious exam activity detected', [
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'inactive_time' => time() - Session::get('last_activity'),
        ]);

        // Force exam submission if configured
        if (config('exam.auto_submit_on_suspicious', true)) {
            $this->forceExamSubmission();
        }
    }

    private function handleInvalidSession(Request $request)
    {
        // Log invalid session attempt
        \Log::warning('Invalid exam session detected', [
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Force exam submission
        $this->forceExamSubmission();

        return redirect()->route('mcq.index')
            ->with('error', 'Invalid exam session detected. Your exam has been submitted.');
    }

    private function forceExamSubmission()
    {
        // Implement exam submission logic here
        // This should save the current answers and end the exam session
    }
} 