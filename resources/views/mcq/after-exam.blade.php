@extends('layouts.app')

@section('title', 'Exam Concluded')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white rounded-xl shadow-lg p-8 md:p-12 text-center">
            <!-- Warning Icon -->
            <div class="text-red-500 mx-auto mb-6">
                <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Exam Session Has Concluded
            </h1>

            <!-- Exam Timeline -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="font-semibold">Scheduled Start</p>
                        <p>{{ $start->format('M j, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Scheduled End</p>
                        <p>{{ $end->format('M j, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Duration</p>
                        <p>{{ $duration }}</p>
                    </div>
                </div>
            </div>

            <!-- Security Message -->
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-8 text-left">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-red-700">
                        All exam activities are now locked. Any attempts to access exam content
                        will be logged as security violations.
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors">
                        Logout
                    </button>
                </form>
                
           
            </div>

            <!-- Audit Footer -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-sm text-gray-600">
                <p>Session finalized at: {{ now()->format('h:i:s A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection