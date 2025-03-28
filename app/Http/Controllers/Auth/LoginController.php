<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            activity('User Logged') // Use the default log name
            ->causedBy(auth()->user()) // Associate the activity with the logged-in user
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ])
            ->log("User Logged In");
            $request->session()->regenerate();
            return redirect()->intended('/mcq');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /** 
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {

        activity('User Log out') 
        ->causedBy(auth()->user()) 
        ->withProperties([
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ])
        ->log("User Logged Out");



        \Log::info('User logged out', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
} 