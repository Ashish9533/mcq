<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $totalQuestions = Question::count();
        return view('home', compact('totalQuestions'));
    }
} 