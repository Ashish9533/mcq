@extends('layouts.app')

@section('title', 'Exam Completed - Thank You')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4">
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg p-8 md:p-12 mx-4 transform transition-all duration-300 hover:shadow-xl">
        <!-- Checkmark Animation -->
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center animate-bounce">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-6 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Exam Submitted Successfully! 🎉
            </h1>
            
            <p class="text-lg text-gray-600 mb-8">
                Thank you for completing the exam. Your results are being processed and will be shared with you shortly.
            </p>

            <!-- Status Indicator -->
            <div class="inline-flex items-center bg-green-50 px-6 py-2 rounded-full">
                <span class="relative flex h-3 w-3 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-600"></span>
                </span>
                <span class="text-green-700 font-medium">Exam Completed</span>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="mt-10 flex justify-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-300 md:py-4 md:text-lg md:px-10 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>

        <!-- Additional Info -->
        <p class="mt-8 text-center text-sm text-gray-500">
            You will receive an email notification once your results are available. 
            For any queries, contact our support team.
        </p>
    </div>
</div>
@endsection