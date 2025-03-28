@extends('layouts.app')

@section('title', 'Software Development MCQ')

@section('content')

<nav class="bg-gradient-to-r from-blue-600 to-indigo-700 shadow-xl fixed w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <span class="text-white text-2xl font-bold tracking-wide">QuizMaster Pro</span>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="#client" class="text-blue-100 hover:text-white transition-colors">For Clients</a>
                <a href="#activity" class="text-blue-100 hover:text-white transition-colors">Activities</a>
                <a href="#" class="px-6 py-2 bg-white text-blue-600 rounded-full font-semibold hover:bg-opacity-90 transition-all">
                    Sign In
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="pt-32 pb-20 px-4">
    <div class="max-w-7xl mx-auto relative">
        <div class="absolute -top-16 right-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="text-center space-y-8">
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 leading-tight">
                Transform Your Assessments<br>
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    With AI-Powered MCQ
                </span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Create, manage, and analyze assessments with our intelligent platform. Perfect for educators, enterprises, and certification bodies.
            </p>
            <div class="space-x-4">
                <button class="bg-blue-600 text-white px-8 py-4 rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl">
                    Start Free Trial
                </button>
                <button class="border-2 border-blue-600 text-blue-600 px-8 py-4 rounded-xl font-semibold hover:bg-blue-50 transition-all">
                    Watch Demo
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="py-20 bg-white/50 backdrop-blur-lg">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-12">
        <div class="p-8 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Smart Question Bank</h3>
            <p class="text-gray-600">AI-powered question generation with dynamic difficulty adjustment and automatic tagging.</p>
        </div>

        <!-- Add more feature cards here -->
    </div>
</section>

<!-- Client Section -->
<section id="client" class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-12 text-white shadow-2xl">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold mb-6">For Organizations & Educators</h2>
                    <ul class="space-y-4">
                        <li class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white/10 rounded-full flex items-center justify-center">
                                ✓
                            </div>
                            <span>Bulk question import/export</span>
                        </li>
                        <!-- Add more list items -->
                    </ul>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-white/10 rounded-2xl backdrop-blur-lg"></div>
                    <img src="dashboard-preview.png" alt="Client Dashboard" class="relative z-10 rounded-2xl shadow-xl">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Activity Section -->
<section id="activity" class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-3xl shadow-xl p-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-12">Real-time Activity Monitoring</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="border-l-4 border-blue-600 pl-6">
                    <h3 class="text-2xl font-semibold mb-4">Live Proctoring</h3>
                    <p class="text-gray-600">Advanced AI monitoring with face recognition and activity logging.</p>
                </div>
                <!-- Add more activity features -->
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-4 gap-8">
        <div>
            <h4 class="text-lg font-semibold mb-4">QuizMaster Pro</h4>
            <p class="text-gray-400">Revolutionizing assessments through AI-powered technology.</p>
        </div>
        <!-- Add footer columns -->
    </div>
</footer>

<!-- Custom Animation -->
<style>
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
</style>
@endsection