@props(['categories', 'totalQuestions'])

<div class="w-full lg:w-2/3 bg-white shadow-lg rounded-lg overflow-hidden flex flex-col">
    <div class="p-6 border-b border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Question <span id="current-question">1</span> of <span
                        id="total-questions">{{ $totalQuestions }}</span></h2>
                <p class="text-gray-600">Category: <span id="current-category" class="font-medium"></span></p>
            </div>
            <div class="flex items-center space-x-3">
                <button
                    class="flex items-center space-x-2 px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors duration-200"
                    id="mark-review">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    <span>Review</span>
                </button>
                <button
                    class="flex items-center space-x-2 px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200"
                    id="mark-answered">
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
            @foreach ($categories as $category)
                @foreach ($category->questions as $question)
                    <div class="question-content {{ $questionNumber === 1 ? '' : 'hidden' }}"
                        id="question-{{ $question->id }}">
                        <p class="text-lg text-gray-800 font-medium mb-6">{{ $question->question_text }}</p>

                        <!-- Options -->
                        <div class="space-y-4">
                            @foreach ($question->options as $option)
                                <div
                                    class="option-item flex items-center p-2 md:p-3 border rounded cursor-pointer transition-all duration-200">
                                    <input type="radio" name="answer-{{ $question->id }}" class="mr-2 md:mr-3"
                                    data-question-id="{{ $question->id }}"
                                        value="{{ $option->id }}" id="option-{{ $option->id }}">
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
                <button
                    class="flex items-center space-x-2 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 disabled:opacity-50"
                    id="prev-btn" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    <span>Previous</span>
                </button>
                <button
                    class="flex items-center space-x-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                    id="next-btn">
                    <span>Next</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            <div class="flex space-x-4">
                <button
                    class="px-6 py-2.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200"
                    id="clear-answer">
                    Clear Answer
                </button>
                <button
                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200"
                    id="submit-exam">
                    Submit Exam
                </button>
            </div>
        </div>
    </div>


    <!-- Submit Exam Modal (initially hidden) -->
    <div id="submit-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg relative w-96">
            <!-- Top Right Cross Icon -->
            <button id="modal-close" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <h2 class="text-xl font-bold mb-4">Submit Exam</h2>
            <p class="mb-2">
                You've answered <span id="modal-progress"></span> out of
                <span id="total-questions-modal"></span> questions. 
            </p>
            <p class="text-red-600 mb-4">
                Are you sure you want to submit the exam? After submission, you will not be able to take the exam again.
            </p>
            <input type="email" id="verification-email" placeholder="Enter your email for verification"
                class="w-full border border-gray-300 p-2 rounded mb-1" />
            <!-- Error message container for email validation/server errors -->
            <div id="email-error" class="text-red-600 text-sm mb-2 hidden"></div>

            <button id="modal-confirm" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Confirm and Submit Exam
            </button>
        </div>
    </div>
    


</div>
