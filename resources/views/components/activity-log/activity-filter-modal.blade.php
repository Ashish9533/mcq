@props(['actions'])

<div id="filter-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Filter Activities</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeFilterModal()">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <form id="filter-form" class="px-6 py-4">
                <!-- Action Filter -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Action Type</label>
                    <select name="action" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500">From</label>
                            <input type="date" name="date_from" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500">To</label>
                            <input type="date" name="date_to" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Quick Filters -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Filters</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="quick_filters[]" value="today" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Today</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="quick_filters[]" value="week" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">This Week</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="quick_filters[]" value="month" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">This Month</span>
                        </label>
                    </div>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg">
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeFilterModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="button" onclick="applyFilters()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openFilterModal() {
    document.getElementById('filter-modal').classList.remove('hidden');
}

function closeFilterModal() {
    document.getElementById('filter-modal').classList.add('hidden');
}

function applyFilters() {
    const form = document.getElementById('filter-form');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }

    // Redirect with filter parameters
    window.location.href = `${window.location.pathname}?${params.toString()}`;
}

// Close modal when clicking outside
document.getElementById('filter-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFilterModal();
    }
});
</script> 