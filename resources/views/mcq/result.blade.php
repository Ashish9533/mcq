@extends('layouts.app')

@section('title', 'User Results')

@section('content')
<div class="min-h-screen bg-gray-50 p-8">
    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">MCQ Examination Result</h1>
                    <p class="text-blue-100">Completed on {{ now()->format('d M Y') }}</p>
                </div>
                <div class="text-center bg-white/10 p-4 rounded-xl">
                    <span class="block text-3xl font-bold text-white">{{ $resultData['overall']['score'] }}%</span>
                    <span class="text-sm text-blue-100">Overall Score</span>
                </div>
            </div>
        </div>

        <!-- Result Summary Grid -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Correct Answers Card (Emerald) -->
                <div class="group relative bg-emerald-50 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-emerald-100/50 hover:border-emerald-200">
                    <div class="flex items-start justify-between">
                        <div class="text-emerald-600 relative">
                            <div class="absolute -left-2 -top-2 w-10 h-10 bg-emerald-100/30 rounded-full blur-md"></div>
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-emerald-700 bg-emerald-100/40 px-3 py-1 rounded-full">
                            +{{ number_format(($resultData['overall']['correct']/$resultData['overall']['total_questions'])*100, 1) }}%
                        </span>
                    </div>
                    <div class="mt-6">
                        <p class="text-sm text-slate-600 font-medium mb-2">Correct Answers</p>
                        <div class="flex items-center justify-between">
                            <p class="text-3xl font-bold text-slate-800">
                                {{ $resultData['overall']['correct'] }}
                                <span class="text-base font-normal text-slate-600">/{{ $resultData['overall']['total_questions'] }}</span>
                            </p>
                            <div class="w-20 h-2 bg-emerald-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 transition-all duration-500" 
                                     style="width: {{ ($resultData['overall']['correct']/$resultData['overall']['total_questions'])*100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Incorrect Answers Card (Rose) -->
                <div class="group relative bg-rose-50 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-rose-100/50 hover:border-rose-200">
                    <div class="flex items-start justify-between">
                        <div class="text-rose-600 relative">
                            <div class="absolute -left-2 -top-2 w-10 h-10 bg-rose-100/30 rounded-full blur-md"></div>
                            <i class="fas fa-times-circle text-3xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-rose-700 bg-rose-100/40 px-3 py-1 rounded-full">
                            -{{ number_format(($resultData['overall']['incorrect']/$resultData['overall']['total_questions'])*100, 1) }}%
                        </span>
                    </div>
                    <div class="mt-6">
                        <p class="text-sm text-slate-600 font-medium mb-2">Incorrect Answers</p>
                        <div class="flex items-center justify-between">
                            <p class="text-3xl font-bold text-slate-800">
                                {{ $resultData['overall']['incorrect'] }}
                                <span class="text-base font-normal text-slate-600">/{{ $resultData['overall']['total_questions'] }}</span>
                            </p>
                            <div class="w-20 h-2 bg-rose-100 rounded-full overflow-hidden">
                                <div class="h-full bg-rose-500 transition-all duration-500" 
                                     style="width: {{ ($resultData['overall']['incorrect']/$resultData['overall']['total_questions'])*100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Marked for Review Card (Indigo) -->
                <div class="group relative bg-indigo-50 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-indigo-100/50 hover:border-indigo-200">
                    <div class="flex items-start justify-between">
                        <div class="text-indigo-600 relative">
                            <div class="absolute -left-2 -top-2 w-10 h-10 bg-indigo-100/30 rounded-full blur-md"></div>
                            <i class="fas fa-flag text-3xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-indigo-700 bg-indigo-100/40 px-3 py-1 rounded-full">
                            {{ number_format(($resultData['overall']['marked']/$resultData['overall']['total_questions'])*100, 1) }}%
                        </span>
                    </div>
                    <div class="mt-6">
                        <p class="text-sm text-slate-600 font-medium mb-2">Marked for Review</p>
                        <div class="flex items-center justify-between">
                            <p class="text-3xl font-bold text-slate-800">
                                {{ $resultData['overall']['marked'] }}
                            </p>
                            <div class="w-20 h-2 bg-indigo-100 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 transition-all duration-500" 
                                     style="width: {{ ($resultData['overall']['marked']/$resultData['overall']['total_questions'])*100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Section Analysis -->
        <div class="p-6">
            <h2 class="text-xl font-bold mb-6">Subject-wise Performance</h2>
            
            <!-- Overall Progress -->
            <div class="mb-8">
                <div class="flex justify-between mb-3">
                    <span class="text-sm font-medium">Overall Progress</span>
                    <span class="text-sm text-gray-500">{{ $resultData['overall']['passing_percentage'] }}% Passing Requirement</span>
                </div>
                <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 relative" 
                         style="width: {{ $resultData['overall']['score'] }}%">
                        <div class="absolute right-0 -mr-2 top-0 w-4 h-4 bg-white rounded-full shadow"></div>
                    </div>
                </div>
            </div>

            <!-- Subject Breakdown -->
            <div class="space-y-6">
                @foreach($resultData['subjects'] as $subject)
                <div class="section-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold">Subject: {{ $subject['name'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $subject['total_questions'] }} Questions</p>
                        </div>
                        <span class="badge-{{ $subject['pass_status'] }}">{{ ucfirst($subject['pass_status']) }}</span>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-4 mt-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Attempted</p>
                            <p class="font-bold">{{ $subject['correct'] + $subject['incorrect'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Correct</p>
                            <p class="font-bold text-green-600">{{ $subject['correct'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Incorrect</p>
                            <p class="font-bold text-red-600">{{ $subject['incorrect'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Marked</p>
                            <p class="font-bold text-blue-600">{{ $subject['marked'] }}</p>
                        </div>
                    </div>
                    
                    @php
                        $subjectPercentage = ($subject['correct'] / $subject['total_questions']) * 100;
                    @endphp
                    <div class="mt-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $subject['pass_status'] === 'passed' ? 'green' : 'red' }}-500" 
                             style="width: {{ $subjectPercentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Legend & Actions -->
        <div class="bg-slate-50 p-6 border-t border-slate-100">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <!-- Legend with Stats -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-slate-600 mb-2">Result Summary</h4>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></div>
                                <span class="text-sm text-slate-700">Correct: {{ $resultData['overall']['correct'] }}</span>
                            </div>
                            <div class="flex items-center bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
                                <div class="w-2 h-2 bg-rose-500 rounded-full mr-2"></div>
                                <span class="text-sm text-slate-700">Incorrect: {{ $resultData['overall']['incorrect'] }}</span>
                            </div>
                            <div class="flex items-center bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
                                <i class="fas fa-clock text-sm text-amber-600 mr-2"></i>
                                <span class="text-sm text-slate-700">Time: 42m</span>
                            </div>
                        </div>
                    </div>
        
                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button class="group flex items-center px-4 py-2.5 bg-white border border-slate-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all duration-200">
                            <i class="fas fa-download text-slate-600 group-hover:text-blue-600 mr-2"></i>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-blue-700">PDF Report</span>
                        </button>
                        <button class="group flex items-center px-4 py-2.5 bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-redo text-white mr-2"></i>
                            <span class="text-sm font-medium text-white">Retake Test</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.result-card {
    @apply flex items-center p-4 rounded-xl space-x-4 transition-all duration-300 hover:shadow-md;
}

.section-card {
    @apply bg-white p-4 rounded-xl border hover:border-blue-200 transition-all duration-300 shadow-sm;
}

.badge-passed {
    @apply px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-medium;
}

.badge-failed {
    @apply px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm font-medium;
}

.action-btn {
    @apply px-4 py-2 text-white rounded-lg flex items-center transition-all duration-300 transform hover:scale-105;
}
</style>
   
<style>
    .result-card {
        @apply p-5 rounded-xl relative overflow-hidden transition-all duration-300 hover:shadow-sm;
    }
    
    .result-card::before {
        content: '';
        @apply absolute inset-0 border border-slate-200 rounded-xl pointer-events-none opacity-50;
    }
    
    .result-card:hover::before {
        @apply opacity-0;
    }
    </style>
@endsection