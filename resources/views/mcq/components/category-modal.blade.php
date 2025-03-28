<!-- Category Modal -->
<div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Categories</h3>
            <button onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mt-2">
            <div class="space-y-4">
                @foreach($categories as $category)
                <div class="category-item p-3 border rounded-lg hover:bg-gray-50 cursor-pointer" 
                     data-category-id="{{ $category->id }}"
                     data-category-name="{{ $category->name }}">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-base font-medium text-gray-900">{{ $category->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $category->active_questions_count }} questions</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">{{ $category->description }}</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function openCategoryModal() {
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('categoryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCategoryModal();
    }
});

// Handle category selection
document.querySelectorAll('.category-item').forEach(item => {
    item.addEventListener('click', function() {
        const categoryId = this.dataset.categoryId;
        const categoryName = this.dataset.categoryName;
        
        // Update the current category display
        document.getElementById('current-category').textContent = categoryName;
        
        // Filter questions for this category
        filterQuestionsByCategory(categoryId);
        
        // Close the modal
        closeCategoryModal();
    });
});

function filterQuestionsByCategory(categoryId) {
    document.querySelectorAll('.question-item').forEach(item => {
        if (item.dataset.categoryId === categoryId) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
}
</script> 