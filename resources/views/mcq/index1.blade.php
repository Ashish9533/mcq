@extends('layouts.app')

@section('title', 'Software Development MCQ')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Top Bar with Timer and Progress -->
    <div class="bg-white shadow-lg rounded-lg p-4 md:p-6 mb-6 transition-all duration-300 hover:shadow-xl">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-lg font-semibold text-gray-700">Time Left:</div>
                    <div id="timer" class="text-2xl font-bold text-blue-600 font-mono">02:00:00</div>
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

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4">Confirm Logout</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to logout? This will end your exam session and all progress will be lost.</p>
            <div class="flex justify-end space-x-3">
                <button id="cancel-logout" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Cancel
                </button>
                <button id="confirm-logout" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Logout
                </button>
            </div>
        </div>
    </div>

    <!-- Security Warning Modal -->
    <div id="security-warning-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4 text-red-600">Security Warning</h3>
            <p class="text-gray-600 mb-4" id="security-warning-message"></p>
            <div class="flex justify-end">
                <button id="acknowledge-warning" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Acknowledge
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-12rem)]">
        <!-- Left Section - Questions List -->
        <div class="w-full lg:w-1/3 bg-white shadow-lg rounded-lg overflow-hidden flex flex-col">
            <div class="p-4 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <button onclick="openCategoryModal()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h2 class="text-xl font-bold text-gray-800">Questions</h2>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span id="answered-count" class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-medium">0</span>
                        <span id="review-count" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full font-medium">0</span>
                        <button id="filter-toggle" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9M3 12h5"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Filter Options -->
                <div id="filter-options" class="hidden mt-4 p-3 bg-gray-50 rounded-lg">
                    <div class="space-y-3">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" id="show-reviewed" class="form-checkbox h-5 w-5 text-yellow-500 rounded">
                            <span class="text-gray-700">Show Reviewed Only</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" id="show-answered" class="form-checkbox h-5 w-5 text-green-500 rounded">
                            <span class="text-gray-700">Show Answered Only</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Questions List -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-4 space-y-6">
                    @foreach($categories as $category)
                    <div class="category-section">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 sticky top-0 bg-white py-2 px-2 rounded-lg shadow-sm">
                            {{ $category->name }}
                        </h3>
                        <div class="space-y-2">
                            @foreach($category->questions as $question)
                            <div class="question-item p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition-all duration-200 border border-gray-100" 
                                 data-question="{{ $question->id }}" 
                                 data-category="{{ $category->slug }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <span class="w-7 h-7 flex items-center justify-center bg-blue-100 text-blue-600 rounded-full font-semibold">
                                            {{ $loop->iteration }}
                                        </span>
                                        <p class="text-gray-700 line-clamp-2">{{ $question->question_text }}</p>
                                    </div>
                                    <span class="question-status w-3 h-3 rounded-full bg-gray-300 transition-all duration-300"></span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Section - Current Question -->
        <div class="w-full lg:w-2/3 bg-white shadow-lg rounded-lg overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Question <span id="current-question">1</span> of <span id="total-questions">{{ $totalQuestions }}</span></h2>
                        <p class="text-gray-600">Category: <span id="current-category" class="font-medium"></span></p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button class="flex items-center space-x-2 px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors duration-200" id="mark-review">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            <span>Review</span>
                        </button>
                        <button class="flex items-center space-x-2 px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200" id="mark-answered">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Mark Done</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <!-- Question Content -->
                <div class="mb-8">
                    @php $questionNumber = 1; @endphp
                    @foreach($categories as $category)
                        @foreach($category->questions as $question)
                            <div class="question-content {{ $questionNumber === 1 ? '' : 'hidden' }}" id="question-{{ $question->id }}">
                                <p class="text-lg text-gray-800 font-medium mb-6">{{ $question->question_text }}</p>
                                
                                <!-- Options -->
                                <div class="space-y-4">
                                    @foreach($question->options as $option)
                                        <div class="option-item flex items-center p-2 md:p-3 border rounded cursor-pointer transition-all duration-200">
                                            <input type="radio" 
                                                   name="answer-{{ $question->id }}" 
                                                   class="mr-2 md:mr-3" 
                                                   value="{{ $option->id }}" 
                                                   id="option-{{ $option->id }}">
                                            <label for="option-{{ $option->id }}" class="flex-1 text-sm md:text-base">
                                                {{ $option->option_text }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @php $questionNumber++; @endphp
                        @endforeach
                    @endforeach
                </div>

                <!-- Navigation and Actions -->
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 mt-8">
                    <div class="flex space-x-4">
                        <button class="flex items-center space-x-2 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 disabled:opacity-50" id="prev-btn" disabled>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span>Previous</span>
                        </button>
                        <button class="flex items-center space-x-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200" id="next-btn">
                            <span>Next</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex space-x-4">
                        <button class="px-6 py-2.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200" id="clear-answer">
                            Clear Answer
                        </button>
                        <button class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200" id="submit-exam">
                            Submit Exam
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.scrollbar-hide::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-hide::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.scrollbar-hide::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.scrollbar-hide::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.question-item {
    @apply transition-all duration-300 ease-in-out border-2;
}

.question-item.answered {
    @apply bg-green-50 border-green-500 shadow-md;
}

.question-item.reviewed {
    @apply bg-yellow-50 border-yellow-500 shadow-md;
}

.question-item.answered-review {
    @apply bg-red-50 border-red-500 shadow-md;
}

.question-status {
    @apply w-4 h-4 rounded-full transition-all duration-300;
}

.question-status.answered {
    @apply bg-green-500;
}

.question-status.review {
    @apply bg-yellow-500;
}

.question-status.answered.review {
    @apply bg-red-500;
}

.option-item {
    @apply p-4 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200;
}

.option-item:hover {
    @apply border-blue-300 bg-blue-50;
}

.option-item.selected {
    @apply border-blue-500 bg-blue-50;
    animation: selectPulse 0.3s ease-in-out;
}

@keyframes statusPulse {
    0% { transform: scale(0.8); opacity: 0.5; }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes statusBlink {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

@keyframes selectPulse {
    0% { transform: scale(0.98); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Mobile Optimizations */
@media (max-width: 640px) {
    .container {
        @apply px-3;
    }
    
    .question-item {
        @apply p-2;
    }
    
    .option-item {
        @apply p-3;
    }
}

* {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    -webkit-touch-callout: none;
}

input, textarea {
    -webkit-user-select: text;
    -moz-user-select: text;
    -ms-user-select: text;
    user-select: text;
}

.question-item.current {
    @apply ring-2 ring-blue-500;
}

/* Update existing hover state for better visibility */
.option-item:hover:not(.selected) {
    @apply border-blue-300 bg-blue-50/50;
}

.question-item {
    @apply transition-all duration-300 ease-in-out;
}

.question-item.ring-2 {
    @apply transform transition-transform duration-300;
}

.question-item:hover {
    @apply shadow-md;
}

/* Update the current question highlight */
.question-item.current {
    @apply ring-2 ring-blue-500 shadow-lg scale-105;
}
</style>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script> --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add these variables at the top of your script
    let currentQuestionIndex = 0;
    const questionItems = document.querySelectorAll('.question-item');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const currentQuestionSpan = document.getElementById('current-question');
    const currentCategorySpan = document.getElementById('current-category');
    const questionText = document.getElementById('question-text');
    const totalQuestionsSpan = document.getElementById('total-questions');
    const timerElement = document.getElementById('timer');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const markReviewBtn = document.getElementById('mark-review');
    const markAnsweredBtn = document.getElementById('mark-answered');
    const clearAnswerBtn = document.getElementById('clear-answer');
    const submitExamBtn = document.getElementById('submit-exam');
    const optionItems = document.querySelectorAll('.option-item');
    const reviewCount = document.getElementById('review-count');
    const filterToggle = document.getElementById('filter-toggle');
    const filterOptions = document.getElementById('filter-options');
    const showReviewed = document.getElementById('show-reviewed');
    const showAnswered = document.getElementById('show-answered');
    const answeredCount = document.getElementById('answered-count');

    // Add filter state variables
    let isFilterVisible = false;
    let showReviewedOnly = false;
    let showAnsweredOnly = false;

    let currentQuestion = null;
    const totalQuestions = questionItems.length;
    totalQuestionsSpan.textContent = totalQuestions;
    let answeredQuestions = new Set();
    let reviewedQuestions = new Set();
    let timeLeft = 7200; // 2 hours in seconds

    // Development mode flag
    const isDevelopment = true; // Set this to false in production

    // Enhanced security variables
    let securityWarnings = 0;
    const MAX_SECURITY_WARNINGS = 3;
    let lastWarningTime = Date.now();
    const WARNING_COOLDOWN = 60000; // 1 minute cooldown between warnings
    let lastActivity = Date.now();
    let isTabVisible = true;
    let warningShown = false;
    let submissionAttempted = false;
    let devToolsOpen = false;
    const INACTIVITY_TIMEOUT = 5 * 60 * 1000; // 5 minutes
    const WARNING_TIME = 5 * 60; // 5 minutes warning before auto-submit
    const MIN_ANSWERS_REQUIRED = 5;
    const DEVTOOLS_THRESHOLD = 160;

    // Add this at the top of your script with other variables
    let questionAnswers = new Map(); // Store answers for each question

    // Prevent accidental navigation/refresh
    if (!isDevelopment) {
        window.addEventListener('beforeunload', function(e) {
            if (examStarted && !submissionAttempted) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });
    }

    // Track tab visibility
    if (!isDevelopment) {
        document.addEventListener('visibilitychange', function() {
            isTabVisible = document.visibilityState === 'visible';
            if (!isTabVisible && examStarted) {
                showSecurityWarning('Please stay on the exam tab. Multiple tab switches may result in exam disqualification.');
            }
        });
    }

    // Track user activity
    function updateLastActivity() {
        lastActivity = Date.now();
    }

    if (!isDevelopment) {
        document.addEventListener('mousemove', updateLastActivity);
        document.addEventListener('keypress', updateLastActivity);
        document.addEventListener('click', updateLastActivity);
        document.addEventListener('scroll', updateLastActivity);
    }

    // Check for inactivity
    function checkInactivity() {
        if (isDevelopment || !examStarted || submissionAttempted) return;

        const inactiveTime = Date.now() - lastActivity;
        if (inactiveTime > INACTIVITY_TIMEOUT) {
            showWarning('You have been inactive for too long. The exam will be submitted automatically.');
            setTimeout(submitExam, 5000);
        }
    }

    // Show warning message
    function showWarning(message) {
        if (warningShown) return;
        
        const warningDiv = document.createElement('div');
        warningDiv.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
        warningDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(warningDiv);
        warningShown = true;

        setTimeout(() => {
            warningDiv.remove();
            warningShown = false;
        }, 5000);
    }

    // Validate answer selection
    function validateAnswer(questionNumber) {
        const questionItem = document.querySelector(`.question-item[data-question="${questionNumber}"]`);
        if (!questionItem) return false;

        const selectedOption = document.querySelector(`input[name="answer"]:checked`);
        return selectedOption !== null;
    }

   
    // Add event listeners for mark review and mark answered buttons
    markReviewBtn.addEventListener('click', () => {
        const currentQuestionItem = questionItems[currentQuestionIndex];
        if (currentQuestionItem) {
            const questionId = currentQuestionItem.dataset.question;
            toggleQuestionReview(questionId);
        }
    });

    markAnsweredBtn.addEventListener('click', () => {
        const currentQuestionItem = questionItems[currentQuestionIndex];
        if (currentQuestionItem) {
            const questionId = currentQuestionItem.dataset.question;
            toggleQuestionAnswered(questionId);
        }
    });

    // Function to toggle question review status
    function toggleQuestionReview(questionId) {
        console.log('Toggling review for question:', questionId); // Debug log
        
        if (reviewedQuestions.has(questionId)) {
            reviewedQuestions.delete(questionId);
            markReviewBtn.classList.remove('bg-yellow-200');
            markReviewBtn.classList.add('bg-yellow-100');
            console.log('Removed from reviewed questions'); // Debug log
        } else {
            reviewedQuestions.add(questionId);
            markReviewBtn.classList.remove('bg-yellow-100');
            markReviewBtn.classList.add('bg-yellow-200');
            console.log('Added to reviewed questions'); // Debug log
        }
        
        // Update review count
        reviewCount.textContent = reviewedQuestions.size;
        console.log('Updated review count:', reviewedQuestions.size); // Debug log
        
        // Update question status
        highlightQuestionStatus(questionId);
        
        // Apply filters if they are active
        if (isFilterVisible) {
            applyFilters();
        }
    }

    // Function to toggle question answered status
    function toggleQuestionAnswered(questionId) {
        if (answeredQuestions.has(questionId)) {
            answeredQuestions.delete(questionId);
            markAnsweredBtn.classList.remove('bg-green-200');
            markAnsweredBtn.classList.add('bg-green-100');
        } else {
            answeredQuestions.add(questionId);
            markAnsweredBtn.classList.remove('bg-green-100');
            markAnsweredBtn.classList.add('bg-green-200');
        }
        
        // Update answered count
        answeredCount.textContent = answeredQuestions.size;
        
        // Update progress bar
        updateProgress();
        
        // Update question status
        highlightQuestionStatus(questionId);
        
        // Apply filters if they are active
        if (isFilterVisible) {
            applyFilters();
        }
    }

    // Update the showQuestion function to handle selected options and button states
    function showQuestion(index) {
        // Hide all questions
        document.querySelectorAll('.question-content').forEach(q => q.classList.add('hidden'));
        
        // Show the selected question
        const questions = document.querySelectorAll('.question-content');
        if (questions[index]) {
            questions[index].classList.remove('hidden');
            
            // Update current question number
            currentQuestionSpan.textContent = index + 1;
            
            // Update category
            const questionItem = questionItems[index];
            if (questionItem) {
                const category = questionItem.dataset.category;
                currentCategorySpan.textContent = getCategoryName(category);
                
                // Remove highlight from all questions
                questionItems.forEach(item => {
                    item.classList.remove('current', 'ring-2', 'ring-blue-500', 'shadow-lg', 'scale-105');
                });
                
                // Add highlight to current question
                questionItem.classList.add('current', 'ring-2', 'ring-blue-500', 'shadow-lg', 'scale-105');
                
                // Update button states based on current question status
                const questionId = questionItem.dataset.question;
                updateButtonStates(questionId);
                
                // Update selected option styling
                const selectedRadio = document.querySelector(`input[name="answer-${questionId}"]:checked`);
                if (selectedRadio) {
                    const optionContainer = selectedRadio.closest('.option-item');
                    optionContainer.classList.add('selected');
                }
                
                // Scroll the current question into view
                questionItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
            
        // Update navigation buttons
        updateNavigationButtons();
    }

    // Function to update button states
    function updateButtonStates(questionId) {
        // Update mark review button
        if (reviewedQuestions.has(questionId)) {
            markReviewBtn.classList.remove('bg-yellow-100');
            markReviewBtn.classList.add('bg-yellow-200');
        } else {
            markReviewBtn.classList.remove('bg-yellow-200');
            markReviewBtn.classList.add('bg-yellow-100');
        }

        // Update mark answered button
        if (answeredQuestions.has(questionId)) {
            markAnsweredBtn.classList.remove('bg-green-100');
            markAnsweredBtn.classList.add('bg-green-200');
        } else {
            markAnsweredBtn.classList.remove('bg-green-200');
            markAnsweredBtn.classList.add('bg-green-100');
        }
    }

    // Update the highlightQuestionStatus function
    function highlightQuestionStatus(questionNumber) {
        const questionItem = document.querySelector(`.question-item[data-question="${questionNumber}"]`);
        if (questionItem) {
            const status = questionItem.querySelector('.question-status');
            
            // Reset all classes
            questionItem.classList.remove('answered', 'reviewed', 'answered-review');
            status.classList.remove('bg-gray-300', 'bg-green-500', 'bg-yellow-500', 'bg-red-500');
            
            const isAnswered = answeredQuestions.has(questionNumber);
            const isReviewed = reviewedQuestions.has(questionNumber);
            
            if (isAnswered && isReviewed) {
                questionItem.classList.add('answered-review');
                status.classList.add('bg-red-500');
            } else if (isAnswered) {
                questionItem.classList.add('answered');
                status.classList.add('bg-green-500');
            } else if (isReviewed) {
                questionItem.classList.add('reviewed');
                status.classList.add('bg-yellow-500');
            } else {
                status.classList.add('bg-gray-300');
            }
        }
    }

    // Update navigation buttons state
    function updateNavigationButtons() {
        const totalQuestions = document.querySelectorAll('.question-content').length;
        prevBtn.disabled = currentQuestionIndex === 0;
        nextBtn.disabled = currentQuestionIndex === totalQuestions - 1;
    }

    // Event listeners for navigation
    prevBtn.addEventListener('click', () => {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            showQuestion(currentQuestionIndex);
            updateLastActivity();
        }
    });

    nextBtn.addEventListener('click', () => {
        const totalQuestions = document.querySelectorAll('.question-content').length;
        if (currentQuestionIndex < totalQuestions - 1) {
            currentQuestionIndex++;
            showQuestion(currentQuestionIndex);
            updateLastActivity();
        }
    });

    // Question list click handler
    questionItems.forEach((item, index) => {
        item.addEventListener('click', () => {
            currentQuestionIndex = index;
            showQuestion(currentQuestionIndex);
            updateLastActivity();
        });
    });

    // Initialize with first question
    showQuestion(0);

    // Update submitExam function
    async function submitExam() {
        if (submissionAttempted) return;
        submissionAttempted = true;

        // Validate minimum answers
        if (answeredQuestions.size < MIN_ANSWERS_REQUIRED) {
            if (!confirm(`You have only answered ${answeredQuestions.size} questions. The minimum required is ${MIN_ANSWERS_REQUIRED}. Are you sure you want to submit?`)) {
                submissionAttempted = false;
                return;
            }
        }

        // Validate all answered questions have a selection
        const unansweredQuestions = Array.from(answeredQuestions).filter(q => !validateAnswer(q));
        if (unansweredQuestions.length > 0) {
            if (!confirm('Some of your answered questions do not have a selection. Are you sure you want to submit?')) {
                submissionAttempted = false;
                return;
            }
        }

        // Show submission confirmation
        if (confirm('Are you sure you want to submit the exam? This action cannot be undone.')) {
            try {
                const response = await fetch('/mcq/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        answers: Array.from(answeredQuestions).map(q => ({
                            question: q,
                            answer: document.querySelector(`input[name="answer"]:checked`)?.value
                        })),
                        timeSpent: 7200 - timeLeft,
                        reviewedQuestions: Array.from(reviewedQuestions)
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Disable all interactions
                    disableExamInterface();
                    
                    // Show submission success message
                    showSuccessMessage('Exam submitted successfully!');
                } else {
                    throw new Error('Submission failed');
                }
            } catch (error) {
                console.error('Error submitting exam:', error);
                showWarning('Error submitting exam. Please try again.');
                submissionAttempted = false;
            }
        } else {
            submissionAttempted = false;
        }
    }

    // Disable exam interface after submission
    function disableExamInterface() {
        questionItems.forEach(item => item.style.pointerEvents = 'none');
        optionItems.forEach(item => item.style.pointerEvents = 'none');
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        markReviewBtn.disabled = true;
        markAnsweredBtn.disabled = true;
        clearAnswerBtn.disabled = true;
        submitExamBtn.disabled = true;
    }

    // Show success message
    function showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
        successDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(successDiv);
    }

    // Enhanced timer function
    function updateTimer() {
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        
        // Show warning when time is running low
        if (timeLeft === WARNING_TIME) {
            showWarning(`Only ${WARNING_TIME / 60} minutes remaining!`);
        }
        
        if (timeLeft > 0) {
            timeLeft--;
            setTimeout(updateTimer, 1000);
        } else {
            submitExam();
        }
    }

    // Start exam
    function startExam() {
        if (!confirm('Are you ready to start the exam? Once started, you cannot navigate away or refresh the page.')) {
            return;
        }
        examStarted = true;
        initializeSecurity();
        updateTimer();
    }

    // Initialize exam
    startExam();

    // Timer function
    function updateTimer() {
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        
        if (timeLeft > 0) {
            timeLeft--;
            setTimeout(updateTimer, 1000);
        } else {
            submitExam();
        }
    }

    // Update progress
    function updateProgress() {
        const progress = (answeredQuestions.size / totalQuestions) * 100;
        progressBar.style.width = `${progress}%`;
        progressText.textContent = `${answeredQuestions.size}/${totalQuestions}`;
    }

    // Function to get category name from code
    function getCategoryName(code) {
        const categories = {
            'dsa': 'Data Structures & Algorithms',
            'system-design': 'System Design',
            'microservices': 'Microservices',
            'database': 'Database Design'
        };
        return categories[code] || code;
    }

    // Handle logout functionality
    document.getElementById('logout-btn').addEventListener('click', function() {
        const modal = document.getElementById('logout-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    document.getElementById('cancel-logout').addEventListener('click', function() {
        const modal = document.getElementById('logout-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    document.getElementById('confirm-logout').addEventListener('click', function() {
        // Submit exam before logging out
        submitExam().then(() => {
            // Create a form to submit the logout request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/logout';
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Add the form to the document and submit it
            document.body.appendChild(form);
            form.submit();
        });
    });

    document.getElementById('acknowledge-warning').addEventListener('click', function() {
        const modal = document.getElementById('security-warning-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    // Enhanced security checks
    function checkSecurity() {
        if (isDevelopment) return;

        // Check for multiple tabs
        if (!isTabVisible && examStarted) {
            showSecurityWarning('Multiple tabs detected. This is a security violation.');
        }

        // Check for developer tools
        checkDevTools();

        // Check for fullscreen mode
        if (document.fullscreenElement === null) {
            showSecurityWarning('Please maintain fullscreen mode during the exam.');
            requestFullscreen();
        }

        // Check window focus
        if (!document.hasFocus()) {
            showSecurityWarning('Please keep the exam window focused.');
        }

        // Check for keyboard shortcuts
        checkKeyboardShortcuts();

        // Check for copy/paste attempts
        checkCopyPaste();

        // Check for right-click
        checkRightClick();

        // Check for text selection
        checkTextSelection();

        // Check for iframe embedding
        checkIframeEmbedding();
    }

    // Security functions
    function checkDevTools() {
        if (isDevelopment) return;
        
        const widthThreshold = window.outerWidth - window.innerWidth > DEVTOOLS_THRESHOLD;
        const heightThreshold = window.outerHeight - window.innerHeight > DEVTOOLS_THRESHOLD;

        if (widthThreshold || heightThreshold) {
            if (!devToolsOpen) {
                devToolsOpen = true;
                showSecurityWarning('Developer tools detected. This action will be reported.');
                submitExam();
            }
        } else {
            devToolsOpen = false;
        }
    }

    function checkKeyboardShortcuts() {
        if (isDevelopment) return;
        
        document.addEventListener('keydown', function(e) {
            if (examStarted) {
                // Prevent common keyboard shortcuts
                if ((e.ctrlKey || e.metaKey) && (
                    e.key === 'c' || e.key === 'v' || e.key === 'p' || 
                    e.key === 's' || e.key === 'u' || e.key === 'r' ||
                    e.key === 'i' || e.key === 'j' || e.key === 'k'
                )) {
                    e.preventDefault();
                    showSecurityWarning('Keyboard shortcuts are disabled during the exam.');
                }

                // Prevent F12 and other developer tools shortcuts
                if (e.key === 'F12' || 
                    (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i')) || 
                    (e.ctrlKey && e.shiftKey && (e.key === 'J' || e.key === 'j')) || 
                    (e.ctrlKey && e.shiftKey && (e.key === 'C' || e.key === 'c')) || 
                    (e.ctrlKey && (e.key === 'U' || e.key === 'u'))) {
                    e.preventDefault();
                    showSecurityWarning('Developer tools shortcuts are disabled during the exam.');
                }
            }
        });
    }

    function checkCopyPaste() {
        if (isDevelopment) return;
        
        document.addEventListener('copy', function(e) {
            if (examStarted) {
                e.preventDefault();
                showSecurityWarning('Copying is not allowed during the exam.');
            }
        });

        document.addEventListener('paste', function(e) {
            if (examStarted) {
                e.preventDefault();
                showSecurityWarning('Pasting is not allowed during the exam.');
            }
        });

        document.addEventListener('cut', function(e) {
            if (examStarted) {
                e.preventDefault();
                showSecurityWarning('Cutting is not allowed during the exam.');
            }
        });
    }

    function checkRightClick() {
        if (isDevelopment) return;
        
        document.addEventListener('contextmenu', function(e) {
            if (examStarted) {
                e.preventDefault();
                showSecurityWarning('Right-click is disabled during the exam.');
            }
        });

        document.addEventListener('mousedown', function(e) {
            if (examStarted && e.button === 2) {
                e.preventDefault();
                showSecurityWarning('Right-click is disabled during the exam.');
            }
        });
    }

    function checkTextSelection() {
        if (isDevelopment) return;
        
        document.addEventListener('selectstart', function(e) {
            if (examStarted) {
                e.preventDefault();
                showSecurityWarning('Text selection is disabled during the exam.');
            }
        });
    }

    function checkIframeEmbedding() {
        if (isDevelopment) return;
        
        if (window.self !== window.top) {
            window.top.location.href = window.self.location.href;
            showSecurityWarning('The exam cannot be embedded in an iframe.');
        }
    }

    function requestFullscreen() {
        if (isDevelopment) return;
        
        const element = document.documentElement;
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
    }

    // Initialize security measures
    function initializeSecurity() {
        if (isDevelopment) return;
        
        // Start security checks
        setInterval(checkSecurity, 1000);
        setInterval(checkInactivity, 60000);
        
        // Request fullscreen mode
        requestFullscreen();
        
        // Initialize all security checks
        checkDevTools();
        checkKeyboardShortcuts();
        checkCopyPaste();
        checkRightClick();
        checkTextSelection();
        checkIframeEmbedding();
    }

    // Initialize security measures
    function disableRightClick() {
        if (isDevelopment) return;
        
        // Disable right click on the entire document
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            showSecurityWarning('Right-click is disabled during the exam.');
            return false;
        }, true);

        // Disable text selection
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
            return false;
        });

        // Disable copy
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            showSecurityWarning('Copying is not allowed during the exam.');
            return false;
        });

        // Disable cut
        document.addEventListener('cut', function(e) {
            e.preventDefault();
            showSecurityWarning('Cutting is not allowed during the exam.');
            return false;
        });

        // Disable paste
        document.addEventListener('paste', function(e) {
            e.preventDefault();
            showSecurityWarning('Pasting is not allowed during the exam.');
            return false;
        });

        // Disable save
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                showSecurityWarning('Saving is not allowed during the exam.');
                return false;
            }
        });
    }

    // Initialize basic security measures
    if (!isDevelopment) {
        disableRightClick();
        checkSecurity();
    }

    // Add event listeners for radio buttons
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const questionId = this.name.split('-')[1];
            const questionItem = document.querySelector(`.question-item[data-question="${questionId}"]`);
            
            if (questionItem) {
                // Add to answered questions if not already there
                if (!answeredQuestions.has(questionId)) {
                    answeredQuestions.add(questionId);
                    answeredCount.textContent = answeredQuestions.size;
                    updateProgress();
                }
                
                // Update question status with green highlight
                questionItem.classList.add('answered');
                const status = questionItem.querySelector('.question-status');
                status.classList.remove('bg-gray-300', 'bg-yellow-500', 'bg-red-500');
                status.classList.add('bg-green-500');
                
                // Add selected class to the option container
                const optionContainer = this.closest('.option-item');
                optionContainer.classList.add('selected');
                
                // Remove selected class from other options
                const otherOptions = optionContainer.parentElement.querySelectorAll('.option-item');
                otherOptions.forEach(opt => {
                    if (opt !== optionContainer) {
                        opt.classList.remove('selected');
                    }
                });

                // Update mark answered button state
                const currentQuestionItem = questionItems[currentQuestionIndex];
                if (currentQuestionItem && currentQuestionItem.dataset.question === questionId) {
                    markAnsweredBtn.classList.remove('bg-green-100');
                    markAnsweredBtn.classList.add('bg-green-200');
                }
                
                // Apply filters if they are active
                if (isFilterVisible) {
                    applyFilters();
                }
            }
        });
    });

    // Update the clearAnswerBtn click handler
    clearAnswerBtn.addEventListener('click', () => {
        const currentQuestionItem = questionItems[currentQuestionIndex];
        if (currentQuestionItem) {
            const questionId = currentQuestionItem.dataset.question;
            const questionContent = document.getElementById(`question-${questionId}`);
            
            // Clear radio selection
            const selectedRadio = questionContent.querySelector('input[type="radio"]:checked');
            if (selectedRadio) {
                selectedRadio.checked = false;
                
                // Remove selected class from all options
                questionContent.querySelectorAll('.option-item').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Remove from answered questions
                if (answeredQuestions.has(questionId)) {
                    answeredQuestions.delete(questionId);
                    answeredCount.textContent = answeredQuestions.size;
                    updateProgress();
                }
                
                // Remove highlight from question
                const questionItem = document.querySelector(`.question-item[data-question="${questionId}"]`);
                if (questionItem) {
                    questionItem.classList.remove('answered');
                    const status = questionItem.querySelector('.question-status');
                    status.classList.remove('bg-green-500', 'bg-yellow-500', 'bg-red-500');
                    status.classList.add('bg-gray-300');
                }

                // Update mark answered button state
                markAnsweredBtn.classList.remove('bg-green-200');
                markAnsweredBtn.classList.add('bg-green-100');
                
                // Apply filters if they are active
                if (isFilterVisible) {
                    applyFilters();
                }
            }
        }
    });

    // Add this function right after your DOMContentLoaded event listener
    function showSecurityWarning(message) {
        const modal = document.getElementById('security-warning-modal');
        const messageElement = document.getElementById('security-warning-message');
        
        messageElement.textContent = message;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        securityWarnings++;
        
        if (securityWarnings >= MAX_SECURITY_WARNINGS) {
            showWarning('Maximum security warnings reached. Your exam will be submitted automatically.');
            setTimeout(submitExam, 5000);
        }
        
        lastWarningTime = Date.now();
    }

    // Update the acknowledge warning handler
    document.getElementById('acknowledge-warning').addEventListener('click', function() {
        const modal = document.getElementById('security-warning-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Reset warning count if enough time has passed
        if (Date.now() - lastWarningTime > WARNING_COOLDOWN) {
            securityWarnings = 0;
        }
    });

    // Initialize the security measures
    disableRightClick();
    checkSecurity();

    // Add filter toggle functionality
    filterToggle.addEventListener('click', () => {
        isFilterVisible = !isFilterVisible;
        filterOptions.classList.toggle('hidden', !isFilterVisible);
        
        // Update filter toggle icon
        const filterIcon = filterToggle.querySelector('svg');
        if (isFilterVisible) {
            filterIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        } else {
            filterIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9M3 12h5"></path>';
        }
    });

    // Add filter checkbox event listeners
    showReviewed.addEventListener('change', () => {
        showReviewedOnly = showReviewed.checked;
        showAnsweredOnly = false; // Reset the other filter
        showAnswered.checked = false; // Uncheck the other checkbox
        applyFilters();
    });

    showAnswered.addEventListener('change', () => {
        showAnsweredOnly = showAnswered.checked;
        showReviewedOnly = false; // Reset the other filter
        showReviewed.checked = false; // Uncheck the other checkbox
        applyFilters();
    });

    // Function to apply filters
    function applyFilters() {
        // Process each question and category
        document.querySelectorAll('.category-section').forEach(section => {
            if (showReviewedOnly || showAnsweredOnly) {
                // When either filter is checked
                const questions = section.querySelectorAll('.question-item');
                let hasVisibleQuestions = false;
                
                questions.forEach(item => {
                    const questionId = item.dataset.question;
                    const isReviewed = reviewedQuestions.has(questionId);
                    const isAnswered = answeredQuestions.has(questionId);
                    
                    // Show question only if it matches the active filter
                    if ((showReviewedOnly && isReviewed) || (showAnsweredOnly && isAnswered)) {
                        item.style.display = 'block';
                        hasVisibleQuestions = true;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Hide the category header if no matching questions
                section.querySelector('h3').style.display = hasVisibleQuestions ? 'block' : 'none';
            } else {
                // When no filters are active, show everything
                section.style.display = 'block';
                section.querySelector('h3').style.display = 'block';
                section.querySelectorAll('.question-item').forEach(item => {
                    item.style.display = 'block';
                    highlightQuestionStatus(item.dataset.question);
                });
            }
        });
    }
});
</script>
@endsection