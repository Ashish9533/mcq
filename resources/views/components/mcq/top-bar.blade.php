@props(['totalQuestions','examDuration','remainingTime'])


<div class="bg-white shadow-lg rounded-lg p-4 md:p-6 mb-6 transition-all duration-300 hover:shadow-xl">
    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-lg font-semibold text-gray-700">Time Left:</div>
                <!-- Timer container where the formatted time will appear -->
                <div id="timer" class="text-2xl font-bold text-blue-600 font-mono"></div>
            </div>
        </div>
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-lg font-semibold text-gray-700">Progress:</div>
                <div class="w-48 bg-gray-200 rounded-full h-3">
                    <div id="progress-bar" class="bg-green-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <div id="progress-text" class="text-lg font-semibold text-gray-700">0/{{ $totalQuestions }}</div>
            </div>
            <button id="logout-btn" class="flex items-center space-x-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Logout</span>
            </button>
        </div>
    </div>
</div>