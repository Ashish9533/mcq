@props(['categories'])

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