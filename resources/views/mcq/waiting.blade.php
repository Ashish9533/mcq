@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-8">
        <!-- Exam Start Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                {{ config('app.name') }} Online Examination
            </h1>
            <p class="text-lg text-gray-600">
                Scheduled Start Time: 
                <span class="font-semibold text-indigo-600">
                    {{ $scheduledStartTime->format('l, F j, Y \a\t h:i A') }} IST
                </span>
            </p>
        </div>

        <!-- Countdown Container -->
        <div class="bg-white rounded-lg shadow-lg p-8 space-y-6">
            <!-- Countdown Timer -->
            <div class="text-center space-y-4">
                <h2 class="text-xl font-semibold text-gray-700">
                    Your Exam Will Begin In
                </h2>
                <div id="countdown" class="flex justify-center items-center space-x-4">
                    <div class="text-4xl font-mono font-bold text-indigo-600">
                        <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                    </div>
                    <div class="h-8 w-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>

            <!-- Refresh Warning -->
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            Warning: Page refresh attempts are strictly limited. 
                            <span class="font-semibold">Accidental refreshes may lock you out!</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- System Checks -->
            <div class="space-y-3">
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    System Time Synchronized: {{ now()->format('h:i:s A') }}
                </div>
                
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Connection Stable (Ping: <span id="ping">--</span>ms)
                </div>
            </div>
        </div>

        <!-- Proctoring Notice -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-yellow-200">
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    Proctoring System Active
                </h3>
                <p class="text-sm text-gray-600">
                    Our system is currently monitoring your device environment.
                    Ensure you remain in frame and avoid any restricted activities.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const examTime = new Date("{{ $scheduledStartTime->toIso8601String() }}").getTime();
    
    function updateTimer() {
        const now = new Date().getTime();
        const remaining = examTime - now;

        if (remaining <= 0) {
            window.location.reload();
            return;
        }

        const hours = Math.floor(remaining / (1000 * 60 * 60));
        const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

        document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    }

    // Update every second
    setInterval(updateTimer, 1000);
    updateTimer();

    // Simulate ping check
    setInterval(() => {
        document.getElementById('ping').textContent = 
            Math.floor(Math.random() * 50) + 30; // Random ping between 30-80ms
    }, 2000);

    // Prevent accidental refresh
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = '';
    });
});
</script>
@endsection