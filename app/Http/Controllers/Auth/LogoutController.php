<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Log the logout event
        \Log::info('User logged out', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Clear all session data
        Session::flush();

        // Logout the user
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to login page
        return redirect()->route('home')->with('message', 'You have been successfully logged out.');
    }
} 