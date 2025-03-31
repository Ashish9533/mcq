@extends('layouts.app')

@section('title', 'Exam Completed - Thank You')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header Section -->
            <div class="bg-indigo-600 px-8 py-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="inline-block bg-white/10 p-6 rounded-full mb-6">
                        <svg class="w-16 h-16 text-white mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold text-white mb-4">Exam Successfully Completed</h1>
                    <p class="text-indigo-100 text-lg">Thank you for participating in our assessment program</p>
                </div>
            </div>

          
        </div>
    </div>
</div>
@endsection